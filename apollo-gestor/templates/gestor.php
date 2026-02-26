<?php

/**
 * Gestor Apollo — Main admin template
 *
 * Converted from gestor.html prototype.
 * All data loaded via AJAX (ApolloGestor JS controller).
 *
 * @package Apollo\Gestor
 */

if (! defined('ABSPATH')) {
    exit;
}

$current_user = wp_get_current_user();
$is_admin     = current_user_can('manage_options');
$avatar_url   = get_avatar_url($current_user->ID, array('size' => 64));
$cdn_url      = defined('APOLLO_CDN_URL') ? APOLLO_CDN_URL : 'https://cdn.apollo.rio.br/v1.0.0/';
$assets_url   = 'https://assets.apollo.rio.br/i/';

// Query events for the selector
$events_query = new WP_Query(
    array(
        'post_type'      => 'event',
        'posts_per_page' => 50,
        'post_status'    => 'publish',
        'orderby'        => 'meta_value',
        'meta_key'       => '_event_start_date',
        'order'          => 'ASC',
    )
);

// Group events by status for the selector
$events_by_status = array(
    'preparing' => array(),
    'planning'  => array(),
    'live'      => array(),
    'done'      => array(),
    'cancelled' => array(),
);

foreach ($events_query->posts as $post) {
    $status = get_post_meta($post->ID, '_event_status', true) ?: 'planning';
    $date   = get_post_meta($post->ID, '_event_start_date', true);
    $fmt    = $date ? wp_date('d M', strtotime($date)) : '';

    $events_by_status[$status][] = array(
        'id'    => $post->ID,
        'title' => $post->post_title,
        'date'  => $fmt,
    );
}

$status_labels = array(
    'preparing' => 'Preparando',
    'planning'  => 'Planejando',
    'live'      => 'Ao Vivo',
    'done'      => 'Concluídos',
    'cancelled' => 'Cancelados',
);

$status_icons = array(
    'preparing' => '⬤',
    'planning'  => '○',
    'live'      => '◉',
    'done'      => '✓',
    'cancelled' => '✕',
);
?>
<script src="<?php echo esc_url($cdn_url . 'core.js'); ?>" fetchpriority="high"></script>

<div class="gestor-wrap" id="gestorApp">

    <!-- LOADER -->
    <div class="loader" id="loader">
        <div class="loader-logo"><span class="gl">Gestor</span><span class="ga">apollo</span></div>
        <div class="loader-bar"></div>
    </div>

    <!-- ═══ HEADER ═══ -->
    <header class="hd">
        <div class="hd-logo"><span class="gl">Gestor</span><span class="ga">apollo</span></div>

        <div class="hd-evento-wrap">
            <select class="hd-evento-select" id="evSelect">
                <option value="overview" selected>Overview Eventos</option>
                <?php foreach ($events_by_status as $status => $evts) : ?>
                    <?php if (! empty($evts)) : ?>
                        <optgroup label="<?php echo esc_attr($status_labels[$status] ?? ucfirst($status)); ?>">
                            <?php foreach ($evts as $evt) : ?>
                                <option value="<?php echo esc_attr($evt['id']); ?>">
                                    <?php echo esc_html($status_icons[$status] . ' ' . $evt['title'] . ($evt['date'] ? ' · ' . $evt['date'] : '')); ?>
                                </option>
                            <?php endforeach; ?>
                        </optgroup>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
            <i class="ri-arrow-down-s-line hd-evento-arrow"></i>
        </div>

        <div class="hd-search">
            <i class="ri-search-line"></i>
            <input type="text" placeholder="Buscar…" id="gestorSearch">
            <span class="sc">⌘K</span>
        </div>

        <div class="hd-acts">
            <button class="icon-btn" data-tooltip="Notificações"><i class="ri-notification-3-line"></i><span class="dot"></span></button>
            <button class="icon-btn" data-tooltip="Ajuda"><i class="ri-question-line"></i></button>
            <button class="icon-btn" data-tooltip="Configurações"><i class="ri-settings-3-line"></i></button>
            <button class="icon-btn" data-tooltip="Novo Evento" id="btnNewEvent" style="background:var(--ink);color:#fff;border-radius:8px"><i class="ri-add-line"></i></button>
            <div class="user-av" data-tooltip="<?php echo esc_attr($current_user->display_name); ?>">
                <img src="<?php echo esc_url($avatar_url); ?>" alt="<?php echo esc_attr($current_user->display_name); ?>">
            </div>
        </div>
    </header>

    <!-- ═══ TABS ═══ -->
    <nav class="tabs">
        <button class="tab on" data-tab="overview" data-tooltip="Overview"
            data-icon-active="<?php echo esc_url($assets_url . 'gestor-s.svg'); ?>"
            data-icon-inactive="<?php echo esc_url($assets_url . 'gestor-v.svg'); ?>">
            <i data-apollo-icon="gestor" style="--apollo-mask:url('<?php echo esc_url($assets_url . 'gestor-s.svg'); ?>')!important"></i>
        </button>
        <button class="tab" data-tab="kanban" data-tooltip="Kanban"
            data-icon-active="<?php echo esc_url($assets_url . 'kanban-view-s.svg'); ?>"
            data-icon-inactive="<?php echo esc_url($assets_url . 'kanban-view-v.svg'); ?>">
            <i data-apollo-icon="kanban-view" style="--apollo-mask:url('<?php echo esc_url($assets_url . 'kanban-view-v.svg'); ?>')!important"></i>
            <span class="cnt" id="cntKanban">0</span>
        </button>
        <button class="tab" data-tab="equipe" data-tooltip="Time"
            data-icon-active="<?php echo esc_url($assets_url . 'team-s.svg'); ?>"
            data-icon-inactive="<?php echo esc_url($assets_url . 'team-v.svg'); ?>">
            <i data-apollo-icon="team" style="--apollo-mask:url('<?php echo esc_url($assets_url . 'team-v.svg'); ?>')!important"></i>
            <span class="cnt" id="cntTeam">0</span>
        </button>
        <button class="tab" data-tab="financeiro" data-tooltip="Financeiro"
            data-icon-active="<?php echo esc_url($assets_url . 'money-dollar-box-s.svg'); ?>"
            data-icon-inactive="<?php echo esc_url($assets_url . 'money-dollar-box-v.svg'); ?>">
            <i class="ri-money-dollar-box-line"></i>
            <span class="lock"><i class="ri-eye-line"></i></span>
        </button>
        <button class="tab" data-tab="fornecedores" data-tooltip="Fornecedores"
            data-icon-active="<?php echo esc_url($assets_url . 'contacts-book-3-s.svg'); ?>"
            data-icon-inactive="<?php echo esc_url($assets_url . 'contacts-book-3-v.svg'); ?>">
            <i data-apollo-icon="contacts-book-3" style="--apollo-mask:url('<?php echo esc_url($assets_url . 'contacts-book-3-v.svg'); ?>')!important"></i>
        </button>
        <button class="tab" data-tab="cronograma" data-tooltip="Cronograma"
            data-icon-active="<?php echo esc_url($assets_url . 'server-s.svg'); ?>"
            data-icon-inactive="<?php echo esc_url($assets_url . 'server-v.svg'); ?>">
            <i class="ri-server-line"></i>
        </button>
        <button class="tab" data-tab="arquivos" data-tooltip="Arquivos">
            <i class="ri-folder-3-fill"></i>
        </button>
    </nav>

    <!-- ═══ MAIN ═══ -->
    <div class="main">

        <!-- ═══ OVERVIEW PANEL ═══ -->
        <div class="panel on" id="p-overview">
            <div class="section">

                <!-- ALL EVENTS OVERVIEW -->
                <div id="overviewContent">
                    <div class="sec-head">
                        <div class="sec-title">
                            <i data-apollo-icon="gestor" style="--apollo-mask:url('<?php echo esc_url($assets_url . 'gestor-s.svg'); ?>')!important;width:18px;height:18px"></i>
                            Overview
                        </div>
                        <div class="sec-sub">Todos os eventos</div>
                    </div>

                    <!-- Stats — populated via AJAX -->
                    <div class="stats" id="overviewStats">
                        <div class="stat">
                            <div class="stat-icon"><i class="ri-calendar-event-line"></i></div>
                            <div class="stat-val" id="statEvents">–</div>
                            <div class="stat-label">Eventos ativos</div>
                        </div>
                        <div class="stat">
                            <div class="stat-icon"><i class="ri-check-double-line"></i></div>
                            <div class="stat-val" id="statDone">–</div>
                            <div class="stat-label">Tarefas prontas</div>
                        </div>
                        <div class="stat">
                            <div class="stat-icon"><i class="ri-error-warning-line"></i></div>
                            <div class="stat-val" id="statOverdue">–</div>
                            <div class="stat-label">Atrasadas</div>
                        </div>
                        <div class="stat">
                            <div class="stat-icon"><i class="ri-team-line"></i></div>
                            <div class="stat-val" id="statTeam">–</div>
                            <div class="stat-label">Time total</div>
                        </div>
                        <?php if ($is_admin) : ?>
                            <div class="stat" data-tooltip="Só Gestor + ADM">
                                <div class="stat-icon"><i class="ri-money-dollar-circle-line"></i></div>
                                <div class="stat-val" id="statBudget">–</div>
                                <div class="stat-label"><i class="ri-eye-line" style="font-size:9px;margin-right:2px"></i> Budget</div>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Event columns — populated via AJAX -->
                    <div class="sec-head" style="margin-top:8px">
                        <div class="sec-title" style="font-size:13px">Próximos Eventos</div>
                        <div class="sec-sub">clique para detalhes</div>
                        <span class="seeall" id="seeAllEvents">Ver todos →</span>
                    </div>
                    <div class="ov-cols" id="overviewCols">
                        <!-- JS populates event columns here -->
                        <div class="skeleton skeleton-card" style="width:200px;flex-shrink:0"></div>
                        <div class="skeleton skeleton-card" style="width:200px;flex-shrink:0"></div>
                        <div class="skeleton skeleton-card" style="width:200px;flex-shrink:0"></div>
                    </div>

                    <!-- Activity Feed -->
                    <div class="sec-head" style="margin-top:12px">
                        <div class="sec-title" style="font-size:13px"><i class="ri-history-line" style="margin-right:4px;font-size:14px"></i> Atividade Recente</div>
                    </div>
                    <div id="activityFeed" style="max-height:260px;overflow-y:auto;padding:0 2px">
                        <div class="skeleton skeleton-row"></div>
                        <div class="skeleton skeleton-row"></div>
                        <div class="skeleton skeleton-row"></div>
                    </div>
                </div>

                <!-- SINGLE EVENT VIEW (shown when specific event selected) -->
                <div id="singleContent" style="display:none">
                    <div class="sec-head">
                        <div class="sec-title">
                            <i data-apollo-icon="gestor" style="--apollo-mask:url('<?php echo esc_url($assets_url . 'gestor-s.svg'); ?>')!important;width:18px;height:18px"></i>
                            <span id="singleName">–</span>
                        </div>
                        <div class="sec-sub" id="singleDate">–</div>
                    </div>

                    <!-- Single event stats -->
                    <div class="stats">
                        <div class="stat">
                            <div class="stat-icon"><i class="ri-list-check-2"></i></div>
                            <div class="stat-val" id="sStat1">–</div>
                            <div class="stat-label">Total tarefas</div>
                        </div>
                        <div class="stat">
                            <div class="stat-icon"><i class="ri-check-double-line"></i></div>
                            <div class="stat-val" id="sStat2">–</div>
                            <div class="stat-label">Concluídas</div>
                        </div>
                        <div class="stat">
                            <div class="stat-icon"><i class="ri-time-line"></i></div>
                            <div class="stat-val" id="sStat3">–</div>
                            <div class="stat-label">Pendentes</div>
                        </div>
                        <div class="stat">
                            <div class="stat-icon"><i class="ri-team-line"></i></div>
                            <div class="stat-val" id="sStat4">–</div>
                            <div class="stat-label">Time</div>
                        </div>
                    </div>

                    <!-- Single event meta -->
                    <div style="display:flex;gap:16px;flex-wrap:wrap;margin-bottom:16px;padding:12px 14px;background:var(--sf);border-radius:12px;border:1px solid var(--brd)">
                        <div>
                            <div class="mono" style="font-size:8px;text-transform:uppercase;color:var(--mist);margin-bottom:2px">Status</div>
                            <div style="font-size:12px;font-weight:700;display:flex;align-items:center;gap:4px">
                                <span class="sdot" id="singleStatusDot"></span>
                                <span id="singleStatus">–</span>
                            </div>
                        </div>
                        <div>
                            <div class="mono" style="font-size:8px;text-transform:uppercase;color:var(--mist);margin-bottom:2px">Tarefas</div>
                            <div style="font-size:12px;font-weight:700" id="singleTasks">–</div>
                        </div>
                        <div>
                            <div class="mono" style="font-size:8px;text-transform:uppercase;color:var(--mist);margin-bottom:2px"><i class="ri-eye-line" style="font-size:9px"></i> Budget</div>
                            <div style="font-size:12px;font-weight:700" id="singleBudget">–</div>
                        </div>
                        <div>
                            <div class="mono" style="font-size:8px;text-transform:uppercase;color:var(--mist);margin-bottom:2px">Time</div>
                            <div style="font-size:12px;font-weight:700" id="singleTeam">–</div>
                        </div>
                    </div>

                    <!-- Task list (AJAX populated) -->
                    <div class="sec-head">
                        <div class="sec-title" style="font-size:13px"><i class="ri-list-check-2" style="font-size:15px"></i> Tarefas pendentes</div>
                    </div>
                    <div class="task-list" id="singleTasks_list">
                        <div class="skeleton skeleton-row"></div>
                        <div class="skeleton skeleton-row"></div>
                        <div class="skeleton skeleton-row"></div>
                    </div>

                    <!-- Add task footer -->
                    <div style="display:flex;gap:6px;margin-top:12px">
                        <input type="text" id="newTaskTitle" placeholder="Nova tarefa…" style="flex:1;height:32px;background:var(--sf);border:1px solid var(--brd);border-radius:var(--pill);padding:0 12px;font-size:11px;font-family:var(--ff)">
                        <button id="btnAddTask" class="icon-btn" style="background:var(--ink);color:#fff;border-radius:8px" data-tooltip="Adicionar"><i class="ri-add-line"></i></button>
                    </div>
                </div>

            </div>
        </div>

        <!-- ═══ KANBAN ═══ -->
        <div class="panel" id="p-kanban">
            <div class="section" style="padding-bottom:0">
                <div class="sec-head">
                    <div class="sec-title">
                        <i data-apollo-icon="kanban-view" style="--apollo-mask:url('<?php echo esc_url($assets_url . 'kanban-view-s.svg'); ?>')!important;width:18px;height:18px"></i>
                        Kanban
                    </div>
                    <div class="sec-sub">arraste para mudar status</div>
                </div>
            </div>
            <div class="ev-kanban" id="kanbanBoard">
                <!-- PLANEJANDO -->
                <div class="ev-col" data-status="planning">
                    <div class="ev-col-head">
                        <span class="ev-col-dot" style="background:var(--s-plan)"></span>
                        <span class="ev-col-name">Planejando</span>
                        <span class="ev-col-count">0</span>
                        <button class="ev-col-add" data-tooltip="Novo evento"><i class="ri-add-line"></i></button>
                    </div>
                    <div class="ev-list" data-status="planning"></div>
                </div>
                <!-- PREPARANDO -->
                <div class="ev-col" data-status="preparing">
                    <div class="ev-col-head">
                        <span class="ev-col-dot" style="background:var(--s-prep)"></span>
                        <span class="ev-col-name">Preparando</span>
                        <span class="ev-col-count">0</span>
                        <button class="ev-col-add" data-tooltip="Novo evento"><i class="ri-add-line"></i></button>
                    </div>
                    <div class="ev-list" data-status="preparing"></div>
                </div>
                <!-- AO VIVO -->
                <div class="ev-col" data-status="live">
                    <div class="ev-col-head">
                        <span class="ev-col-dot" style="background:var(--s-live);animation:pulse 1.5s infinite"></span>
                        <span class="ev-col-name">Ao Vivo</span>
                        <span class="ev-col-count">0</span>
                    </div>
                    <div class="ev-list" data-status="live"></div>
                </div>
                <!-- CONCLUÍDO -->
                <div class="ev-col" data-status="done">
                    <div class="ev-col-head">
                        <span class="ev-col-dot" style="background:var(--s-done)"></span>
                        <span class="ev-col-name">Concluído</span>
                        <span class="ev-col-count">0</span>
                    </div>
                    <div class="ev-list" data-status="done"></div>
                </div>
            </div>
        </div>

        <!-- ═══ TIME / STAFF ═══ -->
        <div class="panel" id="p-equipe">
            <div class="section">
                <div class="vis-bar">
                    <i class="ri-eye-line"></i><span>Quem vê:</span>
                    <div class="vis-roles">
                        <span class="perm perm-adm"><i class="ri-shield-star-line"></i> ADM</span>
                        <span class="perm perm-gestor"><i class="ri-user-star-line"></i> Gestor</span>
                        <span class="perm perm-tgestor"><i class="ri-user-settings-line"></i> T.Gestor</span>
                    </div>
                </div>
                <div class="sec-head">
                    <div class="sec-title">
                        <i data-apollo-icon="team" style="--apollo-mask:url('<?php echo esc_url($assets_url . 'team-s.svg'); ?>')!important;width:18px;height:18px"></i>
                        Time
                    </div>
                    <div class="sec-sub" id="teamSubtitle">Selecione um evento</div>
                    <button class="icon-btn" id="btnAddTeamMember" data-tooltip="Adicionar ao time" style="background:var(--ink);color:#fff;border-radius:8px;margin-left:auto"><i class="ri-user-add-line"></i></button>
                </div>
                <div class="staff-grid" id="staffGrid">
                    <!-- JS populates staff cards -->
                    <div class="skeleton skeleton-card"></div>
                    <div class="skeleton skeleton-card"></div>
                    <div class="skeleton skeleton-card"></div>
                </div>
            </div>
        </div>

        <!-- ═══ FINANCEIRO ═══ -->
        <div class="panel" id="p-financeiro">
            <div class="section">
                <div class="vis-bar restricted">
                    <i class="ri-eye-line" style="color:var(--p-urg)"></i>
                    <span style="color:var(--p-urg);font-weight:600">&nbsp;Dados sensíveis.&nbsp; podem ver este conteúdo:</span>
                    <div class="vis-roles">
                        <span class="perm perm-adm"><i class="ri-shield-star-line"></i> ADM</span>
                        <span class="perm perm-gestor"><i class="ri-user-star-line"></i> Gestor</span>
                    </div>
                </div>
                <div class="sec-head">
                    <div class="sec-title"><i class="ri-money-dollar-circle-line" style="font-size:18px"></i> Financeiro</div>
                    <div class="sec-sub" id="finSubtitle">Selecione um evento</div>
                </div>
                <div class="fin-grid">
                    <div style="overflow-x:auto">
                        <table class="fin-table">
                            <thead>
                                <tr>
                                    <th>Staff</th>
                                    <th>Função</th>
                                    <th>Valor</th>
                                    <th>PIX</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody id="finTableBody">
                                <tr>
                                    <td colspan="5" class="empty" style="padding:20px"><i class="ri-money-dollar-circle-line"></i>
                                        <p>Selecione um evento</p>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div>
                        <div class="fin-sum" id="finSummary">
                            <div class="mono" style="font-size:8px;text-transform:uppercase;letter-spacing:.1em;color:var(--mist);margin-bottom:10px">
                                <i class="ri-eye-line" style="font-size:10px;margin-right:2px"></i> Resumo
                            </div>
                            <div class="fin-sum-row"><span class="fin-sum-k">Budget total</span><span class="fin-sum-v" id="finBudget">–</span></div>
                            <div class="fin-sum-row"><span class="fin-sum-k"><i class="ri-team-line" style="font-size:11px;margin-right:3px"></i> Staff</span><span class="fin-sum-v" id="finStaff">–</span></div>
                            <div class="fin-sum-row"><span class="fin-sum-k"><i class="ri-store-2-line" style="font-size:11px;margin-right:3px"></i> Fornecedores</span><span class="fin-sum-v" id="finSuppliers">–</span></div>
                            <div class="fin-sum-row"><span class="fin-sum-k"><i class="ri-tools-line" style="font-size:11px;margin-right:3px"></i> Produção</span><span class="fin-sum-v" id="finProduction">–</span></div>
                            <div class="divider"></div>
                            <div class="fin-sum-row"><span class="fin-sum-k bold" style="color:var(--ink)">Já pago</span><span class="fin-sum-v" style="color:var(--s-live)" id="finPaid">–</span></div>
                            <div class="fin-sum-row"><span class="fin-sum-k bold" style="color:var(--ink)">Pendente</span><span class="fin-sum-v" style="color:var(--s-prep)" id="finPending">–</span></div>
                            <div class="fin-sum-row"><span class="fin-sum-k bold" style="color:var(--ink)">Atrasado</span><span class="fin-sum-v" style="color:var(--p-urg)" id="finLate">–</span></div>
                            <div class="divider"></div>
                            <div class="fin-sum-row"><span class="fin-sum-k bold" style="font-size:13px;color:var(--ink)">Saldo</span><span class="fin-sum-v big" style="color:var(--primary)" id="finBalance">–</span></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- ═══ FORNECEDORES ═══ -->
        <div class="panel" id="p-fornecedores">
            <!-- Permission Modal -->
            <div class="modal-overlay" id="permModal">
                <div class="modal-card">
                    <div class="modal-h">
                        <i class="ri-shield-keyhole-line" style="color:var(--primary)"></i>
                        Gerenciar Permissões
                        <button class="icon-btn" style="margin-left:auto" id="closePermModal"><i class="ri-close-line"></i></button>
                    </div>
                    <div style="padding:12px;background:var(--sf2);border-radius:8px;font-size:11px;color:var(--smoke);line-height:1.4">
                        <span class="bold" style="color:var(--ink)" id="permEventName">–</span>: Selecione membros da equipe para conceder acesso de visualização e edição aos orçamentos deste evento.
                    </div>
                    <div class="modal-select-wrap">
                        <select class="modal-select" id="permUserSelect">
                            <option value="" disabled selected>Selecionar usuário para incluir...</option>
                        </select>
                        <i class="ri-arrow-down-s-line modal-select-arrow"></i>
                    </div>
                    <div style="font-family:var(--mono);font-size:9px;color:var(--mist);text-transform:uppercase;margin-top:4px;letter-spacing:.05em">
                        Usuários com acesso (<span id="permCount">0</span>)
                    </div>
                    <div class="usr-list" id="permUserList">
                        <!-- JS populates -->
                    </div>
                    <button class="btn-perm-add" id="btnSavePerms"><i class="ri-check-line"></i> Salvar Alterações</button>
                </div>
            </div>

            <div class="section">
                <div class="sec-head">
                    <div>
                        <div class="sec-title">
                            <i data-apollo-icon="contacts-book-3" style="--apollo-mask:url('<?php echo esc_url($assets_url . 'contacts-book-3-s.svg'); ?>')!important;width:18px;height:18px"></i>
                            Fornecedores
                        </div>
                        <div class="sec-sub">Gestão de contratos e pagamentos</div>
                    </div>
                    <div style="margin-left:auto;display:flex;gap:8px">
                        <button class="vis-bar" id="btnOpenPerms" style="margin:0;cursor:pointer;transition:.2s">
                            <i class="ri-lock-2-line"></i><span>Acesso:</span>
                            <div class="vis-roles">
                                <span class="perm perm-adm">ADM</span>
                                <span class="perm perm-gestor">Gestor</span>
                            </div>
                            <i class="ri-settings-4-line" style="margin-left:6px;color:var(--primary)"></i>
                        </button>
                        <button class="icon-btn" data-tooltip="Novo fornecedor" style="background:var(--ink);color:#fff;border-radius:8px"><i class="ri-add-line"></i></button>
                    </div>
                </div>
                <div class="sup-list" id="supplierList">
                    <!-- JS populates supplier rows -->
                    <div class="skeleton skeleton-row"></div>
                    <div class="skeleton skeleton-row"></div>
                </div>
            </div>
        </div>

        <!-- ═══ CRONOGRAMA ═══ -->
        <div class="panel" id="p-cronograma">
            <div class="crono">

                <div class="sec-head">
                    <div class="sec-title"><i class="ri-timeline-view" style="font-size:18px"></i> Cronograma</div>
                    <div class="sec-sub">arraste fases · clique marcos</div>
                </div>

                <!-- Gantt Chart Container -->
                <div class="ms-container">
                    <div class="ms-toolbar">
                        <div style="display:flex;align-items:center;gap:8px">
                            <div class="icon-btn" style="background:var(--sf2);color:var(--ink)"><i class="ri-calendar-todo-line"></i></div>
                            <div>
                                <div style="font-size:12px;font-weight:700">Planejamento Mestre</div>
                                <div style="font-family:var(--mono);font-size:9px;color:var(--ghost)">Visualização Gantt · MS Project Mode</div>
                            </div>
                        </div>
                        <div style="display:flex;align-items:center;gap:6px">
                            <button class="vis-bar" style="margin:0;height:28px;padding:0 8px;cursor:pointer"><i class="ri-zoom-out-line"></i> <span>Zoom</span></button>
                            <button class="vis-bar" style="margin:0;height:28px;padding:0 8px;cursor:pointer"><i class="ri-filter-3-line"></i> <span>Filtro</span></button>
                            <div style="width:1px;height:20px;background:var(--brd);margin:0 4px"></div>
                            <button class="vis-bar" style="margin:0;height:28px;padding:0 8px;background:var(--ink);color:#fff;border-color:var(--ink);cursor:pointer"><i class="ri-save-3-line" style="color:#fff"></i> <span>Salvar</span></button>
                        </div>
                    </div>
                    <div class="ms-body">
                        <div class="ms-side" id="ganttSide">
                            <div class="ms-side-head"><span style="width:20px">#</span><span style="flex:1">Atividade</span><span>Resp.</span></div>
                            <!-- JS populates gantt task rows -->
                        </div>
                        <div class="ms-time-wrap" id="ganttTimeWrap">
                            <div class="ms-time-head" id="ganttTimeHead" style="width:800px">
                                <!-- JS populates day columns -->
                            </div>
                            <div id="ganttChartRows">
                                <!-- JS populates gantt bars -->
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Milestones -->
                <div class="ms-section" id="milestoneSection">
                    <div class="mono" style="font-size:8px;text-transform:uppercase;letter-spacing:.1em;color:var(--mist);margin-bottom:8px">Marcos</div>
                    <div id="milestoneList">
                        <!-- JS populates milestones -->
                        <div class="skeleton skeleton-row"></div>
                        <div class="skeleton skeleton-row"></div>
                    </div>
                    <div style="display:flex;gap:6px;margin-top:8px;align-items:center">
                        <input type="text" id="newMilestoneTitle" placeholder="Novo marco…" style="flex:1;background:var(--sf2);color:var(--smoke);border:1px solid var(--brd);border-radius:6px;padding:7px 10px;font-size:11px;font-family:var(--font);">
                        <input type="date" id="newMilestoneDue" style="background:var(--sf2);color:var(--smoke);border:1px solid var(--brd);border-radius:6px;padding:7px 10px;font-size:11px;font-family:var(--mono);width:130px;">
                        <button id="btnAddMilestone" style="background:var(--ink);color:#fff;border:none;border-radius:6px;padding:7px 10px;font-size:11px;cursor:pointer;white-space:nowrap;font-weight:600"><i class="ri-add-line"></i></button>
                    </div>
                </div>

                <div class="divider"></div>

                <!-- Phase progress bars (AJAX populated) -->
                <div id="cronoPhases">
                    <div class="skeleton skeleton-card" style="height:200px"></div>
                </div>

            </div>
        </div>

        <!-- ═══ ARQUIVOS (File Manager) ═══ -->
        <div class="panel" id="p-arquivos">
            <div class="section">
                <div class="sec-head">
                    <div>
                        <div class="sec-title"><i class="ri-folder-3-fill" style="font-size:18px;color:var(--primary)"></i> Arquivos do Evento</div>
                        <div class="sec-sub">Documentos, mídia, contratos e PDFs</div>
                    </div>
                    <div style="margin-left:auto;display:flex;gap:8px">
                        <button class="icon-btn gestor-fm-upload" data-tooltip="Upload" style="background:var(--ink);color:#fff;border-radius:8px"><i class="ri-upload-2-line"></i></button>
                        <button class="icon-btn gestor-fm-new-folder" data-tooltip="Nova pasta" style="border-radius:8px"><i class="ri-folder-add-line"></i></button>
                        <button class="icon-btn gestor-fm-new-doc" data-tooltip="Novo documento" style="border-radius:8px"><i class="ri-file-add-line"></i></button>
                    </div>
                </div>

                <!-- Toolbar -->
                <div class="gestor-fm-toolbar" style="display:flex;align-items:center;gap:8px;padding:12px 0;border-bottom:1px solid var(--brd)">
                    <div class="gestor-fm-breadcrumbs" style="display:flex;align-items:center;gap:4px;flex:1;font-size:11px;color:var(--mist)">
                        <span class="gestor-fm-crumb active" data-folder="0" style="color:var(--smoke);cursor:pointer">Todos</span>
                    </div>
                    <div style="display:flex;align-items:center;gap:4px;background:var(--sf2);border:1px solid var(--brd);border-radius:6px;padding:2px 8px">
                        <i class="ri-search-line" style="font-size:12px;color:var(--mist)"></i>
                        <input type="text" class="gestor-fm-search" placeholder="Buscar arquivos..." style="background:none;border:none;color:var(--smoke);font-size:11px;width:120px;outline:none">
                    </div>
                    <button class="icon-btn gestor-fm-view-toggle" data-mode="grid" data-tooltip="Visualização" style="font-size:14px"><i class="ri-grid-fill"></i></button>
                </div>

                <!-- File grid -->
                <div class="gestor-fm-files" id="gestorFiles" style="padding:16px 0;min-height:200px">
                    <div class="skeleton skeleton-card" style="height:200px"></div>
                </div>

                <!-- Hidden file input -->
                <input type="file" id="gestorFileInput" multiple style="display:none">
            </div>
        </div>

    </div><!-- end .main -->

    <!-- ═══ SLIDE-OVER ═══ -->
    <div class="so-bg" id="soBg"></div>
    <div class="so" id="so">
        <div class="so-head">
            <button class="so-close" data-tooltip="Fechar" id="soClose"><i class="ri-arrow-right-s-line"></i></button>
            <span class="so-head-id" id="soId">–</span>
            <div class="so-head-acts">
                <button class="icon-btn" data-tooltip="Editar"><i class="ri-edit-line"></i></button>
                <button class="icon-btn" data-tooltip="Compartilhar"><i class="ri-share-line"></i></button>
                <button class="icon-btn" data-tooltip="Mais opções"><i class="ri-more-2-fill"></i></button>
            </div>
        </div>
        <div class="so-body">
            <div class="so-title" id="soTitle">–</div>
            <div class="so-venue" id="soVenue"><i class="ri-map-pin-line"></i> <span>–</span></div>
            <div class="so-meta" id="soMeta">
                <div class="so-meta-k">Status</div>
                <div class="so-meta-v" id="soStatus">–</div>
                <div class="so-meta-k">Data</div>
                <div class="so-meta-v mono" style="font-size:11px" id="soDate">–</div>
                <div class="so-meta-k">Gestor</div>
                <div class="so-meta-v" id="soGestor">–</div>
                <div class="so-meta-k">Time</div>
                <div class="so-meta-v mono" style="font-size:11px" id="soTeamCount">–</div>
                <div class="so-meta-k"><i class="ri-eye-line" style="font-size:9px"></i> Budget</div>
                <div class="so-meta-v bold" id="soBudget">–</div>
            </div>
            <div class="so-desc" id="soDesc">–</div>
            <div class="so-sec" id="soChecklistLabel">Checklist</div>
            <div id="soChecklist">
                <!-- JS populates -->
            </div>
        </div>
        <div class="so-footer">
            <input type="text" placeholder="Adicionar tarefa…" id="soNewTask">
            <button class="send" data-tooltip="Enviar" id="soAddTask"><i class="ri-send-plane-fill"></i></button>
        </div>
    </div>

</div><!-- end .gestor-wrap -->