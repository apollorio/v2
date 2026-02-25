<?php

/**
 * Create Group Page — /criar-grupo
 *
 * Luxury form modal pattern (slide-in panel).
 * Apollo CDN + Design System.
 *
 * Registry: { slug: "criar-grupo", template: "create-group.php", type: "virtual", auth: true }
 *
 * @package Apollo\Groups
 * @since   3.0.0
 */

defined('ABSPATH') || exit;

if (! is_user_logged_in()) {
    wp_redirect(home_url('/acesso/'));
    exit;
}

$rest_url = rest_url('apollo/v1/groups');
$nonce    = wp_create_nonce('wp_rest');
$user_id  = get_current_user_id();

// Determine pre-selected type from query param
$preselect = sanitize_text_field($_GET['tipo'] ?? '');
?>
<!DOCTYPE html>
<html <?php language_attributes(); ?>>

<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Apollo · Criar Comuna ou Núcleo</title>

    <!-- Apollo CDN -->
    <script src="https://cdn.apollo.rio.br/v1.0.0/core.min.js?v=1.0.0" fetchpriority="high"></script>

    <!-- Navbar -->
    <?php if (defined('APOLLO_TEMPLATES_URL') && defined('APOLLO_TEMPLATES_VERSION')) : ?>
        <link rel="stylesheet" href="<?php echo esc_url(APOLLO_TEMPLATES_URL . 'assets/css/navbar.css'); ?>?v=<?php echo esc_attr(APOLLO_TEMPLATES_VERSION); ?>">
        <script src="<?php echo esc_url(APOLLO_TEMPLATES_URL . 'assets/js/navbar.js'); ?>?v=<?php echo esc_attr(APOLLO_TEMPLATES_VERSION); ?>" defer></script>
    <?php endif; ?>

    <!-- Google Fonts: Space Grotesk + Space Mono already loaded by CDN core.min.js -->

    <style>
        :root {
            --primary: #f45f00;
            --black-1: #121214;
            --white-1: #ffffff;
            --border: #e4e4e7;
            --text-muted: #94a3b8;
            --ff-main: 'Space Grotesk', system-ui, sans-serif;
            --ff-mono: 'Space Mono', monospace;
            --radius: 0px;
            --space-4: 24px;
            --ease-lux: cubic-bezier(0.16, 1, 0.3, 1);
            --z-modal: 9999;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: var(--ff-main);
            background: var(--white-1);
            color: var(--black-1);
            -webkit-font-smoothing: antialiased;
            min-height: 100vh;
        }

        ::selection {
            background: #FF8640;
            color: #fff;
        }

        /* ═══════════════════════════════════════ */
        /* HERO TRIGGER AREA                       */
        /* ═══════════════════════════════════════ */
        .create-hero {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            min-height: calc(100vh - 80px);
            padding: 40px 24px;
            text-align: center;
        }

        .create-hero-label {
            font-family: var(--ff-mono);
            font-size: 10px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: var(--primary);
            margin-bottom: 16px;
        }

        .create-hero-title {
            font-size: clamp(2rem, 5vw, 3.5rem);
            font-weight: 300;
            line-height: 1.1;
            color: var(--black-1);
            margin-bottom: 16px;
            max-width: 500px;
        }

        .create-hero-desc {
            font-size: 14px;
            color: var(--text-muted);
            max-width: 400px;
            margin-bottom: 40px;
            line-height: 1.6;
        }

        .create-trigger {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            background: var(--black-1);
            color: var(--white-1);
            border: none;
            padding: 16px 32px;
            font-family: var(--ff-mono);
            font-size: 12px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .create-trigger::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 0;
            height: 100%;
            background: var(--primary);
            transition: width 0.4s ease;
            z-index: 0;
        }

        .create-trigger:hover::before {
            width: 100%;
        }

        .create-trigger span,
        .create-trigger i {
            position: relative;
            z-index: 1;
        }

        .create-back {
            margin-top: 24px;
            font-family: var(--ff-mono);
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: var(--text-muted);
            text-decoration: none;
            transition: color 0.2s;
        }

        .create-back:hover {
            color: var(--primary);
        }

        /* ═══════════════════════════════════════ */
        /* LUXURY OVERLAY + MODAL                  */
        /* ═══════════════════════════════════════ */
        .form-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.4);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            opacity: 0;
            visibility: hidden;
            transition: all 0.6s var(--ease-lux);
            z-index: var(--z-modal);
        }

        .apollo-open .form-overlay {
            opacity: 1;
            visibility: visible;
        }

        .apollo-modal {
            position: fixed;
            bottom: 0;
            right: 0;
            width: 100%;
            max-width: 520px;
            height: 100vh;
            background: var(--white-1);
            border-left: 1px solid var(--border);
            padding: 60px 40px 40px;
            box-sizing: border-box;
            transform: translateX(100%);
            transition: transform 0.6s var(--ease-lux);
            z-index: calc(var(--z-modal) + 1);
            display: flex;
            flex-direction: column;
            overflow-y: auto;
        }

        .apollo-open .apollo-modal {
            transform: translateX(0);
        }

        .modal-close {
            position: absolute;
            top: 30px;
            right: 30px;
            background: none;
            border: none;
            color: var(--text-muted);
            font-size: 24px;
            cursor: pointer;
            transition: color 0.3s;
        }

        .modal-close:hover {
            color: var(--primary);
        }

        .modal-header h2 {
            font-family: var(--ff-mono);
            font-size: 12px;
            color: var(--primary);
            letter-spacing: 0.2em;
            text-transform: uppercase;
            margin-bottom: 10px;
        }

        .modal-header h1 {
            font-size: 32px;
            font-weight: 300;
            color: var(--black-1);
            margin: 0 0 40px 0;
            line-height: 1.1;
        }

        /* ═══════════════════════════════════════ */
        /* INPUTS (MINIMALIST)                     */
        /* ═══════════════════════════════════════ */
        .input-group {
            position: relative;
            margin-bottom: 35px;
        }

        .apollo-input {
            width: 100%;
            background: transparent;
            border: none;
            border-bottom: 1px solid var(--border);
            padding: 12px 0;
            font-family: var(--ff-main);
            font-size: 16px;
            color: var(--black-1);
            border-radius: 0;
            outline: none;
            transition: border-color 0.4s var(--ease-lux);
        }

        .apollo-input:focus {
            border-bottom-color: var(--primary);
        }

        textarea.apollo-input {
            resize: vertical;
            min-height: 60px;
        }

        .apollo-label {
            position: absolute;
            top: 12px;
            left: 0;
            font-family: var(--ff-mono);
            font-size: 12px;
            color: var(--text-muted);
            pointer-events: none;
            transition: all 0.4s var(--ease-lux);
            text-transform: uppercase;
        }

        .apollo-input:focus~.apollo-label,
        .apollo-input:not(:placeholder-shown)~.apollo-label {
            top: -10px;
            font-size: 10px;
            color: var(--primary);
        }

        /* SELECT */
        select.apollo-input {
            appearance: none;
            -webkit-appearance: none;
            cursor: pointer;
            border-radius: 0;
            color: var(--black-1);
        }

        select.apollo-input:invalid {
            color: transparent;
        }

        select.apollo-input:invalid~.apollo-label {
            top: 12px;
            font-size: 12px;
            color: var(--text-muted);
        }

        select.apollo-input:valid~.apollo-label {
            top: -10px;
            font-size: 10px;
            color: var(--primary);
        }

        select.apollo-input:valid {
            color: var(--black-1);
        }

        select.apollo-input option {
            background-color: var(--white-1);
            color: var(--black-1);
            font-family: var(--ff-main);
            font-size: 80%;
            padding: 15px;
        }

        .select-arrow {
            position: absolute;
            right: 0;
            bottom: 15px;
            pointer-events: none;
            color: var(--text-muted);
            font-size: 14px;
        }

        /* ═══════════════════════════════════════ */
        /* SUBMIT                                  */
        /* ═══════════════════════════════════════ */
        .submit-btn {
            margin-top: auto;
            background: transparent;
            border: 1px solid var(--border);
            color: var(--black-1);
            padding: 20px;
            font-family: var(--ff-mono);
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all 0.3s;
        }

        .submit-btn:hover {
            background: var(--black-1);
            color: var(--white-1);
            border-color: var(--black-1);
        }

        .submit-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        /* ═══════════════════════════════════════ */
        /* SUCCESS STATE                           */
        /* ═══════════════════════════════════════ */
        .success-view {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--white-1);
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 40px;
            box-sizing: border-box;
            opacity: 0;
            pointer-events: none;
            transition: opacity 0.5s;
        }

        .form-submitted .success-view {
            opacity: 1;
            pointer-events: all;
        }

        .success-icon {
            font-size: 4rem;
            color: var(--primary);
            margin-bottom: 20px;
        }

        .success-view h3 {
            font-size: 24px;
            margin: 0;
            font-weight: 400;
        }

        .success-view p {
            color: var(--text-muted);
            font-family: var(--ff-mono);
            font-size: 12px;
            margin-top: 10px;
        }

        /* ═══════════════════════════════════════ */
        /* ERROR                                   */
        /* ═══════════════════════════════════════ */
        .form-error {
            display: none;
            font-family: var(--ff-mono);
            font-size: 11px;
            color: #ef4444;
            padding: 10px 0;
            margin-bottom: 10px;
        }

        /* ═══════════════════════════════════════ */
        /* RESPONSIVE                              */
        /* ═══════════════════════════════════════ */
        @media (max-width: 640px) {
            .apollo-modal {
                max-width: 100%;
                padding: 50px 24px 24px;
            }

            .modal-header h1 {
                font-size: 24px;
            }
        }
    </style>
</head>

<body>

    <?php
    if (function_exists('apollo_render_navbar')) {
        apollo_render_navbar();
    }
    ?>

    <!-- ═══ HERO TRIGGER ═══ -->
    <div class="create-hero" id="createHero">
        <div class="create-hero-label">Novo</div>
        <h1 class="create-hero-title">Crie sua<br>comunidade</h1>
        <p class="create-hero-desc">Monte uma comuna aberta ou um núcleo de trabalho para conectar a galera da cena carioca.</p>

        <button class="create-trigger" id="createTrigger">
            <span>Começar agora</span>
            <i class="ri-arrow-right-line"></i>
        </button>

        <a href="<?php echo esc_url(home_url('/grupos')); ?>" class="create-back">
            <i class="ri-arrow-left-line"></i> Voltar para o diretório
        </a>
    </div>

    <!-- ═══ OVERLAY ═══ -->
    <div class="form-overlay" id="apolloOverlay"></div>

    <!-- ═══ MODAL ═══ -->
    <div class="apollo-modal" id="apolloModal">
        <button class="modal-close" id="apolloClose">
            <i class="ri-close-line"></i>
        </button>

        <div class="modal-header">
            <h2>Criar</h2>
            <h1>Nova Comuna<br>ou Núcleo</h1>
        </div>

        <!-- Success View -->
        <div class="success-view">
            <i class="ri-checkbox-circle-line success-icon"></i>
            <h3>Criado com sucesso!</h3>
            <p>Você será redirecionado em instantes.</p>
        </div>

        <!-- Form -->
        <form id="apolloForm" autocomplete="off">

            <!-- Type -->
            <div class="input-group">
                <select class="apollo-input" id="groupType" required>
                    <option value="" disabled <?php echo empty($preselect) ? 'selected' : ''; ?>></option>
                    <option value="comuna" <?php echo $preselect === 'comuna' ? 'selected' : ''; ?>>Comuna — Comunidade aberta</option>
                    <option value="nucleo" <?php echo $preselect === 'nucleo' ? 'selected' : ''; ?>>Núcleo — Equipe de trabalho</option>
                </select>
                <label class="apollo-label">Tipo</label>
                <i class="ri-arrow-down-s-line select-arrow"></i>
            </div>

            <!-- Name -->
            <div class="input-group">
                <input type="text" class="apollo-input" id="groupName" placeholder=" " required maxlength="100">
                <label class="apollo-label">Nome</label>
            </div>

            <!-- Description -->
            <div class="input-group">
                <textarea class="apollo-input" id="groupDesc" placeholder=" " rows="3"></textarea>
                <label class="apollo-label">Descrição</label>
            </div>

            <!-- Tags -->
            <div class="input-group">
                <input type="text" class="apollo-input" id="groupTags" placeholder=" ">
                <label class="apollo-label">Tags (separadas por vírgula)</label>
            </div>

            <!-- Rules -->
            <div class="input-group">
                <textarea class="apollo-input" id="groupRules" placeholder=" " rows="3"></textarea>
                <label class="apollo-label">Regras (uma por linha)</label>
            </div>

            <!-- Error -->
            <div class="form-error" id="formError"></div>

            <!-- Submit -->
            <button type="submit" class="submit-btn" id="submitBtn">
                <span>Criar Agora</span>
                <i class="ri-send-plane-fill"></i>
            </button>

        </form>
    </div>

    <!-- ═══ SCRIPTS ═══ -->
    <script>
        (function() {
            'use strict';

            const REST = '<?php echo esc_url($rest_url); ?>';
            const NONCE = '<?php echo esc_js($nonce); ?>';

            const body = document.body;
            const trigger = document.getElementById('createTrigger');
            const closeBtn = document.getElementById('apolloClose');
            const overlay = document.getElementById('apolloOverlay');
            const modal = document.getElementById('apolloModal');
            const form = document.getElementById('apolloForm');
            const errDiv = document.getElementById('formError');
            const submitBtn = document.getElementById('submitBtn');

            /* ── Open modal ── */
            trigger.addEventListener('click', () => {
                body.classList.add('apollo-open');
                trigger.style.opacity = '0';
                trigger.style.pointerEvents = 'none';
            });

            /* ── Close modal ── */
            function closeForm() {
                body.classList.remove('apollo-open');
                trigger.style.opacity = '1';
                trigger.style.pointerEvents = 'all';

                setTimeout(() => {
                    modal.classList.remove('form-submitted');
                    form.reset();
                    errDiv.style.display = 'none';
                }, 600);
            }

            closeBtn.addEventListener('click', closeForm);
            overlay.addEventListener('click', closeForm);

            /* Escape key */
            document.addEventListener('keydown', (e) => {
                if (e.key === 'Escape' && body.classList.contains('apollo-open')) {
                    closeForm();
                }
            });

            /* Auto-open if ?tipo= is set */
            <?php if (! empty($preselect)) : ?>
                document.addEventListener('DOMContentLoaded', () => {
                    setTimeout(() => trigger.click(), 300);
                });
            <?php endif; ?>

            /* ── Submit ── */
            form.addEventListener('submit', async (e) => {
                e.preventDefault();

                const type = document.getElementById('groupType').value;
                const name = document.getElementById('groupName').value.trim();
                const desc = document.getElementById('groupDesc').value.trim();
                const tags = document.getElementById('groupTags').value.trim();
                const rules = document.getElementById('groupRules').value.trim();

                if (!name) {
                    errDiv.textContent = 'Nome é obrigatório';
                    errDiv.style.display = '';
                    return;
                }

                if (!type) {
                    errDiv.textContent = 'Selecione o tipo';
                    errDiv.style.display = '';
                    return;
                }

                submitBtn.disabled = true;
                submitBtn.querySelector('span').textContent = 'Criando...';
                errDiv.style.display = 'none';

                try {
                    const res = await fetch(REST, {
                        method: 'POST',
                        headers: {
                            'X-WP-Nonce': NONCE,
                            'Content-Type': 'application/json'
                        },
                        credentials: 'same-origin',
                        body: JSON.stringify({
                            name: name,
                            type: type,
                            description: desc,
                            tags: tags,
                            rules: rules
                        })
                    });

                    const data = await res.json();

                    if (res.ok && (data.id || data.slug)) {
                        /* Success state */
                        modal.classList.add('form-submitted');

                        /* Redirect after 2s */
                        const slug = data.slug || data.id;
                        setTimeout(() => {
                            window.location.href = '<?php echo esc_url(home_url('/grupo/')); ?>' + slug;
                        }, 2000);
                    } else {
                        errDiv.textContent = data.message || data.error || 'Erro ao criar. Tente novamente.';
                        errDiv.style.display = '';
                        submitBtn.disabled = false;
                        submitBtn.querySelector('span').textContent = 'Criar Agora';
                    }
                } catch (err) {
                    errDiv.textContent = 'Erro de conexão. Verifique sua internet.';
                    errDiv.style.display = '';
                    submitBtn.disabled = false;
                    submitBtn.querySelector('span').textContent = 'Criar Agora';
                }
            });
        })();
    </script>

</body>

</html>