<?php

/**
 * Main Plugin singleton — orchestrates all Apollo Email subsystems.
 *
 * @package Apollo\Email
 * @since   1.0.0
 */

declare(strict_types=1);

namespace Apollo\Email;

use Apollo\Email\Core\CPT;
use Apollo\Email\Core\Schema;
use Apollo\Email\Core\Cron;
use Apollo\Email\Mailer\Sender;
use Apollo\Email\Mailer\Queue;
use Apollo\Email\Template\TemplateEngine;
use Apollo\Email\Log\Logger;
use Apollo\Email\API\EmailController;
use Apollo\Email\Admin\AdminPage;

final class Plugin
{


    /** @var self|null */
    private static ?self $instance = null;

    /** @var bool */
    private bool $initialized = false;

    // ── Service containers ────────────────────────────────────────
    private Sender $sender;
    private Queue $queue;
    private TemplateEngine $templates;
    private Logger $logger;
    private CPT $cpt;
    private Cron $cron;

    private function __construct() {}
    private function __clone() {}
    public function __wakeup(): void
    {
        throw new \Exception('Cannot unserialize singleton');
    }

    /**
     * Get singleton instance.
     */
    public static function instance(): self
    {
        if (self::$instance === null) {
            self::$instance = new self();
            self::$instance->init();
        }
        return self::$instance;
    }

    /**
     * Initialize all plugin subsystems.
     */
    private function init(): void
    {
        if ($this->initialized) {
            return;
        }
        $this->initialized = true;

        // ── Core services ────────────────────────────────────────
        $this->logger    = new Logger();
        $this->templates = new TemplateEngine();
        $this->sender    = new Sender($this->templates, $this->logger);
        $this->queue     = new Queue($this->sender, $this->logger);
        $this->cpt       = new CPT();
        $this->cron      = new Cron($this->queue);

        // ── Register CPT ─────────────────────────────────────────
        add_action('init', array($this->cpt, 'register'), 5);

        // ── Cron hooks ───────────────────────────────────────────
        add_action(APOLLO_EMAIL_CRON_HOOK, array($this->queue, 'processNext'));

        // Ensure cron is always scheduled (resilience against dropped events)
        add_action('admin_init', array($this->cron, 'ensureScheduled'));

        // ── Admin ────────────────────────────────────────────────
        if (is_admin()) {
            $admin = new AdminPage($this);
            $admin->boot();
        }

        // ── REST API ─────────────────────────────────────────────
        add_action(
            'rest_api_init',
            function () {
                $controller = new EmailController($this);
                $controller->register_routes();
            }
        );

        // ── Shortcodes ───────────────────────────────────────────
        add_shortcode('apollo_email_prefs', array($this, 'renderEmailPrefsShortcode'));

        // ── Assets ───────────────────────────────────────────────
        add_action('admin_enqueue_scripts', array($this, 'enqueueAdminAssets'));

        // ── Cross-plugin hooks ───────────────────────────────────
        $this->registerHookIntegrations();

        // ── Newsletter (migrated from apollo-shortcodes) ────────
        Newsletter::init();

        /**
         * Fires after all Apollo Email services are initialized.
         *
         * @since 1.0.0
         */
        do_action('apollo/email/init', $this);
    }

    // ──────────────────────────────────────────────────────────────
    // SERVICE ACCESSORS
    // ──────────────────────────────────────────────────────────────

    public function sender(): Sender
    {
        return $this->sender;
    }
    public function queue(): Queue
    {
        return $this->queue;
    }
    public function templates(): TemplateEngine
    {
        return $this->templates;
    }
    public function logger(): Logger
    {
        return $this->logger;
    }
    public function cpt(): CPT
    {
        return $this->cpt;
    }
    public function cron(): Cron
    {
        return $this->cron;
    }

	// ──────────────────────────────────────────────────────────────
	// SETTINGS HELPERS
	// ──────────────────────────────────────────────────────────────

    /**
     * Get a plugin setting with optional default.
     *
     * Falls back to apollo-admin CPanel settings (apollo_admin_settings)
     * when a key is not set in apollo_email_settings.
     */
    public static function setting(string $key, mixed $default = null): mixed
    {
        $settings = get_option('apollo_email_settings', array());
        if (isset($settings[$key])) {
            return $settings[$key];
        }

        // Bridge: read from apollo-admin CPanel (email_ prefixed keys)
        static $admin_map = array(
            'from_name'     => 'email_from_name',
            'from_email'    => 'email_from_email',
            'smtp_host'     => 'email_smtp_host',
            'smtp_port'     => 'email_smtp_port',
            'smtp_username' => 'email_smtp_user',
            'smtp_password' => 'email_smtp_pass',
            'transport'     => 'email_transport',
            'track_opens'   => 'email_track_opens',
            'track_clicks'  => 'email_track_clicks',
            'brand_color'   => 'email_brand_color',
            'footer_text'   => 'email_footer_text',
        );

        if (isset($admin_map[$key])) {
            $admin = get_option('apollo_admin_settings', array());
            if (isset($admin[$admin_map[$key]]) && $admin[$admin_map[$key]] !== '') {
                return $admin[$admin_map[$key]];
            }
        }

        return $default;
    }

    /**
     * Update a single setting.
     */
    public static function updateSetting(string $key, mixed $value): void
    {
        $settings         = get_option('apollo_email_settings', array());
        $settings[$key] = $value;
        update_option('apollo_email_settings', $settings);
    }

    /**
     * Get from name for outgoing emails.
     */
    public static function fromName(): string
    {
        $name = apply_filters('apollo/email/from_name', self::setting('from_name', ''));
        return $name ?: get_bloginfo('name');
    }

    /**
     * Get from email for outgoing emails.
     */
    public static function fromEmail(): string
    {
        $email = apply_filters('apollo/email/from_address', self::setting('from_email', ''));
        return $email ?: get_bloginfo('admin_email');
    }

    /**
     * Get active transport name.
     */
    public static function activeTransport(): string
    {
        return self::setting('transport', 'wp_mail');
    }

	// ──────────────────────────────────────────────────────────────
	// SHORTCODES
	// ──────────────────────────────────────────────────────────────

    /**
     * Render the [apollo_email_prefs] shortcode.
     */
    public function renderEmailPrefsShortcode(array $atts = array()): string
    {
        if (! is_user_logged_in()) {
            return '<p class="apollo-email-login-required">' . esc_html__('Faça login para gerenciar suas preferências de email.', 'apollo-email') . '</p>';
        }

        $user_id = get_current_user_id();
        $prefs   = get_user_meta($user_id, '_apollo_email_prefs', true);
        if (! is_array($prefs)) {
            $prefs = array(
                'transactional' => true,
                'marketing'     => true,
                'digest'        => true,
            );
        }

        ob_start();
?>
        <form class="apollo-email-prefs-form" method="post" data-apollo-email-prefs>
            <?php wp_nonce_field('apollo_email_prefs', '_apollo_email_prefs_nonce'); ?>
            <h3><?php esc_html_e('Preferências de Email', 'apollo-email'); ?></h3>
            <label>
                <input type="checkbox" name="prefs[transactional]" value="1" <?php checked(! empty($prefs['transactional'])); ?> disabled>
                <?php esc_html_e('Transacionais (obrigatório)', 'apollo-email'); ?>
            </label>
            <label>
                <input type="checkbox" name="prefs[marketing]" value="1" <?php checked(! empty($prefs['marketing'])); ?>>
                <?php esc_html_e('Marketing e novidades', 'apollo-email'); ?>
            </label>
            <label>
                <input type="checkbox" name="prefs[digest]" value="1" <?php checked(! empty($prefs['digest'])); ?>>
                <?php esc_html_e('Resumo semanal', 'apollo-email'); ?>
            </label>
            <button type="submit" class="apollo-btn"><?php esc_html_e('Salvar Preferências', 'apollo-email'); ?></button>
        </form>
<?php
        return ob_get_clean();
    }

    // ──────────────────────────────────────────────────────────────
    // CROSS-PLUGIN HOOK INTEGRATIONS
    // ──────────────────────────────────────────────────────────────

    private function registerHookIntegrations(): void
    {
        // Welcome email on user registration
        add_action('apollo/login/registered', array($this, 'onUserRegistered'), 10, 2);

        // Password reset email
        add_action('apollo/login/password_reset_requested', array($this, 'onPasswordResetRequested'), 10, 2);

        // Email verification
        add_action('apollo/login/verification_email', array($this, 'onVerificationEmail'), 10, 2);

        // Event reminder (from apollo-events cron)
        add_action('apollo/event/reminder', array($this, 'onEventReminder'), 10, 2);

        // Notification digest
        add_action('apollo/notif/digest', array($this, 'onNotifDigest'), 10, 2);

        // Segmented digests
        add_action('apollo/email/digest/notifications', array($this, 'onDigestNotifications'), 10, 2);
        add_action('apollo/email/digest/fav_events', array($this, 'onDigestFavEvents'), 10, 2);
        add_action('apollo/email/digest/event_match', array($this, 'onDigestEventMatch'), 10, 2);
        add_action('apollo/email/digest/chat', array($this, 'onDigestChat'), 10, 2);
        add_action('apollo/email/digest/comuna', array($this, 'onDigestComuna'), 10, 2);
        add_action('apollo/email/digest/news', array($this, 'onDigestNews'), 10, 2);
        add_action('apollo/email/digest/social', array($this, 'onDigestSocial'), 10, 2);

        // Membership achievement earned
        add_action('apollo/membership/achievement_earned', array($this, 'onAchievementEarned'), 10, 3);

        // Group invitation
        add_action('apollo/groups/user_invited', array($this, 'onGroupInvitation'), 10, 3);

        // New chat message (email notification if user offline)
        add_action('apollo/chat/message_sent', array($this, 'onChatMessage'), 10, 3);
    }

    /**
     * Send welcome email on user registration.
     */
    public function onUserRegistered(int $user_id, array $data = array()): void
    {
        $user = get_userdata($user_id);
        if (! $user) {
            return;
        }

        apollo_send_email(
            $user->user_email,
            __('Bem-vindo(a) ao Apollo Rio! 🎉', 'apollo-email'),
            'welcome',
            array(
                'user_name'   => $data['social_name'] ?? $user->display_name,
                'username'    => $user->user_login,
                'profile_url' => home_url('/id/' . $user->user_login),
                'site_name'   => get_bloginfo('name'),
                'site_url'    => home_url('/'),
            )
        );
    }

    /**
     * Send password reset email.
     */
    public function onPasswordResetRequested(int $user_id, string $reset_url): void
    {
        $user = get_userdata($user_id);
        if (! $user) {
            return;
        }

        apollo_send_email(
            $user->user_email,
            __('Sua nova chave de acesso — Apollo Rio', 'apollo-email'),
            'password-reset',
            array(
                'user_name'  => $user->display_name,
                'reset_url'  => $reset_url,
                'site_name'  => get_bloginfo('name'),
                'expires_in' => '1 hora',
            )
        );
    }

    /**
     * Send email verification.
     */
    public function onVerificationEmail(int $user_id, string $verify_url): void
    {
        $user = get_userdata($user_id);
        if (! $user) {
            return;
        }

        apollo_send_email(
            $user->user_email,
            __('Verifique seu email — Apollo Rio', 'apollo-email'),
            'verification',
            array(
                'user_name'  => $user->display_name,
                'verify_url' => $verify_url,
                'site_name'  => get_bloginfo('name'),
            )
        );
    }

    /**
     * Send event reminder.
     */
    public function onEventReminder(int $event_id, array $user_ids): void
    {
        $event = get_post($event_id);
        if (! $event) {
            return;
        }

        foreach ($user_ids as $uid) {
            $user = get_userdata($uid);
            if (! $user) {
                continue;
            }

            // Check user prefs
            $prefs = get_user_meta($uid, '_apollo_email_prefs', true);
            if (is_array($prefs) && empty($prefs['transactional'])) {
                continue;
            }

            apollo_queue_email(
                $user->user_email,
                sprintf(__('Lembrete: %s é hoje! 🎶', 'apollo-email'), $event->post_title),
                'event-reminder',
                array(
                    'user_name'   => $user->display_name,
                    'event_title' => $event->post_title,
                    'event_url'   => get_permalink($event_id),
                    'event_date'  => get_post_meta($event_id, '_event_start_date', true),
                    'event_time'  => get_post_meta($event_id, '_event_start_time', true),
                    'loc_name'    => '',
                    'site_name'   => get_bloginfo('name'),
                ),
                3 // high priority
            );
        }
    }

    /**
     * Send notification digest.
     */
    public function onNotifDigest(int $user_id, array $notifications): void
    {
        $this->queueDigestPart(
            $user_id,
            __('Seu digest de notificações — Apollo Rio', 'apollo-email'),
            __('Digest de Notificações', 'apollo-email'),
            $notifications,
            __('Confira suas notificações recentes.', 'apollo-email')
        );
    }

    /**
     * Segmented digest: notifications.
     */
    public function onDigestNotifications(int $user_id, array $items): void
    {
        $this->queueDigestPart(
            $user_id,
            __('Seu digest de notificações — Apollo Rio', 'apollo-email'),
            __('Digest de Notificações', 'apollo-email'),
            $items,
            __('Confira suas notificações recentes.', 'apollo-email')
        );
    }

    /**
     * Segmented digest: favorited events updates.
     */
    public function onDigestFavEvents(int $user_id, array $items): void
    {
        $this->queueDigestPart(
            $user_id,
            __('Atualizações dos seus eventos salvos — Apollo Rio', 'apollo-email'),
            __('Digest de Eventos Salvos', 'apollo-email'),
            $items,
            __('Mudanças de status e informações dos eventos salvos por você.', 'apollo-email')
        );
    }

    /**
     * Segmented digest: event matchmaking by sound profile.
     */
    public function onDigestEventMatch(int $user_id, array $items): void
    {
        $this->queueDigestPart(
            $user_id,
            __('Eventos que combinam com seu som — Apollo Rio', 'apollo-email'),
            __('Digest de Match de Som', 'apollo-email'),
            $items,
            __('Novos eventos com match no seu perfil de sons.', 'apollo-email')
        );
    }

    /**
     * Segmented digest: chat activity.
     */
    public function onDigestChat(int $user_id, array $items): void
    {
        $this->queueDigestPart(
            $user_id,
            __('Seu digest de chat — Apollo Rio', 'apollo-email'),
            __('Digest de Chat', 'apollo-email'),
            $items,
            __('Resumo de mensagens e conversas recentes.', 'apollo-email')
        );
    }

    /**
     * Segmented digest: groups (comuna) activity.
     */
    public function onDigestComuna(int $user_id, array $items): void
    {
        $this->queueDigestPart(
            $user_id,
            __('Novidades das suas comunas — Apollo Rio', 'apollo-email'),
            __('Digest de Comunas', 'apollo-email'),
            $items,
            __('Atividades novas nas comunas das quais você participa.', 'apollo-email')
        );
    }

    /**
     * Segmented digest: Apollo news.
     */
    public function onDigestNews(int $user_id, array $items): void
    {
        $this->queueDigestPart(
            $user_id,
            __('Apollo News da semana — Apollo Rio', 'apollo-email'),
            __('Digest de Apollo News', 'apollo-email'),
            $items,
            __('Atualizações e destaques do ecossistema Apollo.', 'apollo-email')
        );
    }

    /**
     * Segmented digest: social updates.
     */
    public function onDigestSocial(int $user_id, array $items): void
    {
        $this->queueDigestPart(
            $user_id,
            __('Movimento social no seu perfil — Apollo Rio', 'apollo-email'),
            __('Digest Social', 'apollo-email'),
            $items,
            __('Quem visitou seu perfil e reações no seu conteúdo.', 'apollo-email')
        );
    }

    /**
     * Queue one digest part email.
     */
    private function queueDigestPart(int $user_id, string $subject, string $title, array $items, string $intro = ''): void
    {
        $user = get_userdata($user_id);
        if (! $user) {
            return;
        }

        $prefs = get_user_meta($user_id, '_apollo_email_prefs', true);
        if (is_array($prefs) && empty($prefs['digest'])) {
            return;
        }

        if (empty($items)) {
            return;
        }

        $rows = array();
        foreach ($items as $item) {
            if (is_array($item)) {
                $rows[] = array(
                    'heading' => (string) ($item['title'] ?? ''),
                    'message' => (string) ($item['message'] ?? ''),
                    'time'    => (string) ($item['time'] ?? ''),
                    'url'     => (string) ($item['url'] ?? ''),
                );
                continue;
            }

            $rows[] = array(
                'heading' => '',
                'message' => (string) $item,
                'time'    => '',
                'url'     => '',
            );
        }

        apollo_queue_email(
            $user->user_email,
            $subject,
            'digest',
            array(
                'user_name'       => $user->display_name,
                'digest_title'    => $title,
                'digest_intro'    => $intro,
                'digest_sections' => array(
                    array(
                        'title' => $title,
                        'items' => $rows,
                    ),
                ),
                'site_name'       => get_bloginfo('name'),
                'site_url'        => home_url('/'),
            ),
            7
        );
    }

    /**
     * Send achievement earned notification.
     */
    public function onAchievementEarned(int $user_id, int $achievement_id, string $achievement_title): void
    {
        $user = get_userdata($user_id);
        if (! $user) {
            return;
        }

        apollo_send_email(
            $user->user_email,
            sprintf(__('Conquista desbloqueada: %s 🏆', 'apollo-email'), $achievement_title),
            'notification',
            array(
                'user_name'   => $user->display_name,
                'title'       => $achievement_title,
                'message'     => sprintf(__('Parabéns! Você desbloqueou a conquista "%s".', 'apollo-email'), $achievement_title),
                'action_url'  => home_url('/minhas-conquistas'),
                'action_text' => __('Ver Conquistas', 'apollo-email'),
                'site_name'   => get_bloginfo('name'),
            )
        );
    }

    /**
     * Send group invitation email.
     */
    public function onGroupInvitation(int $user_id, int $group_id, int $invited_by): void
    {
        $user    = get_userdata($user_id);
        $inviter = get_userdata($invited_by);
        if (! $user || ! $inviter) {
            return;
        }

        apollo_send_email(
            $user->user_email,
            __('Convite para grupo — Apollo Rio', 'apollo-email'),
            'notification',
            array(
                'user_name'   => $user->display_name,
                'title'       => __('Convite para Grupo', 'apollo-email'),
                'message'     => sprintf(__('%s convidou você para participar de um grupo.', 'apollo-email'), $inviter->display_name),
                'action_url'  => home_url('/grupo/' . $group_id),
                'action_text' => __('Ver Grupo', 'apollo-email'),
                'site_name'   => get_bloginfo('name'),
            )
        );
    }

    /**
     * Send chat message notification (if user offline).
     */
    public function onChatMessage(int $thread_id, int $sender_id, int $recipient_id): void
    {
        $recipient   = get_userdata($recipient_id);
        $sender_user = get_userdata($sender_id);
        if (! $recipient || ! $sender_user) {
            return;
        }

        // Only send if user is offline
        $status = get_user_meta($recipient_id, '_apollo_chat_status', true);
        if ($status !== 'offline') {
            return;
        }

        // Check email prefs
        $prefs = get_user_meta($recipient_id, '_apollo_email_prefs', true);
        if (is_array($prefs) && empty($prefs['transactional'])) {
            return;
        }

        apollo_queue_email(
            $recipient->user_email,
            sprintf(__('Nova mensagem de %s — Apollo Rio', 'apollo-email'), $sender_user->display_name),
            'notification',
            array(
                'user_name'   => $recipient->display_name,
                'title'       => __('Nova Mensagem', 'apollo-email'),
                'message'     => sprintf(__('%s enviou uma mensagem para você.', 'apollo-email'), $sender_user->display_name),
                'action_url'  => home_url('/mensagens/' . $thread_id),
                'action_text' => __('Abrir Conversa', 'apollo-email'),
                'site_name'   => get_bloginfo('name'),
            ),
            4 // medium-high priority
        );
    }

    // ──────────────────────────────────────────────────────────────
    // ADMIN ASSETS
    // ──────────────────────────────────────────────────────────────

    public function enqueueAdminAssets(string $hook): void
    {
        if (! str_contains($hook, 'apollo-email')) {
            return;
        }

        wp_enqueue_style(
            'apollo-email-admin',
            APOLLO_EMAIL_URL . 'assets/css/admin-email.css',
            array(),
            APOLLO_EMAIL_VERSION
        );

        wp_enqueue_script(
            'apollo-email-admin',
            APOLLO_EMAIL_URL . 'assets/js/admin-email.js',
            array('jquery', 'wp-api-fetch'),
            APOLLO_EMAIL_VERSION,
            true
        );

        wp_localize_script(
            'apollo-email-admin',
            'apolloEmailAdmin',
            array(
                'restUrl'   => rest_url('apollo/v1/'),
                'nonce'     => wp_create_nonce('wp_rest'),
                'version'   => APOLLO_EMAIL_VERSION,
                'batchSize' => APOLLO_EMAIL_BATCH_SIZE,
                'pluginUrl' => APOLLO_EMAIL_URL,
                'adminUrl'  => admin_url(),
                'i18n'      => array(
                    'confirmDelete' => __('Tem certeza que deseja excluir?', 'apollo-email'),
                    'sending'       => __('Enviando...', 'apollo-email'),
                    'sent'          => __('Enviado!', 'apollo-email'),
                    'error'         => __('Erro ao enviar', 'apollo-email'),
                    'saved'         => __('Salvo!', 'apollo-email'),
                    'testSent'      => __('Email de teste enviado!', 'apollo-email'),
                ),
            )
        );
    }
}
