# Apollo Ecosystem — Auditoria de Formulários Frontend de Criação

> Inventário completo de todos os formulários frontend que **criam/adicionam** conteúdo novo  
> (CPTs ou registros em tabelas do banco). Exclui metaboxes wp-admin e formulários de edição/atualização.

---

## Resumo Rápido

| # | Plugin | Formulário | Cria | Tipo |
|---|--------|-----------|------|------|
| 1 | apollo-adverts | Novo Anúncio | CPT `classified` | `<form>` POST |
| 2 | apollo-groups | Criar Grupo | DB `apollo_groups` | JS → REST |
| 3 | apollo-sign | Assinatura Digital | DB `apollo_signatures` | `<form>` AJAX |
| 4 | apollo-sign | Upload PFX | DB `apollo_signatures` | `<form>` POST |
| 5 | apollo-shortcodes | Cena::Rio Evento | CPT `event_listing` | `<form>` POST |
| 6 | apollo-shortcodes | Newsletter | DB `apollo_newsletter_subscribers` | `<form>` POST |
| 7 | apollo-login | Registro (original) | User account | `<form>` AJAX |
| 8 | apollo-login | Registro (novo) | User account | `<form>` AJAX |
| 9 | apollo-social | Compose Box | Social post | JS → REST |
| 10 | apollo-comment | Depoimento | DB depoimento | JS → REST |
| 11 | apollo-chat | Nova Mensagem | DB chat messages | JS → REST |
| 12 | apollo-chat | Novo Thread | DB chat threads | JS → REST |
| 13 | apollo-docs | Upload Documento | CPT/attachment | JS → REST |
| 14 | apollo-templates | Sugestão de Evento | Event suggestion | `<form>` POST |
| 15 | apollo-admin | Modal Nova Entidade | CPTs variados (draft) | JS → AJAX |
| 16 | apollo-core | Report/Contact | Report/mensagem | JS → REST |
| 17 | apollo-gestor | Nova Tarefa | DB `apollo_gestor_tasks` | JS → REST |

---

## 1. apollo-adverts — Formulário de Novo Anúncio (Classified)

- **Arquivo**: [apollo-adverts/templates/form.php](apollo-adverts/templates/form.php#L54-L113)
- **Campos definidos em**: [apollo-adverts/includes/form-fields.php](apollo-adverts/includes/form-fields.php#L150-L320)
- **Campo extra**: [apollo-adverts/includes/event-selector.php](apollo-adverts/includes/event-selector.php#L27-L47)
- **Handler API**: [apollo-adverts/src/API/ClassifiedsController.php](apollo-adverts/src/API/ClassifiedsController.php#L189)
- **Cria**: CPT `classified` (`APOLLO_CPT_CLASSIFIED`)
- **Rota**: `/novo-anuncio` ou `/criar-anuncio`
- **Tipo**: `<form>` tradicional com POST + nonce

### Campos (scheme "publish")

| Campo | `name` | Tipo | Obrigatório | Validação |
|-------|--------|------|-------------|-----------|
| Título | `post_title` | text | Sim | string_length 5-100 |
| Categoria | `classified_domain` (taxonomy) | select | Sim | — |
| Intenção | `classified_intent` (taxonomy) | select | Sim | — |
| Evento vinculado | `_classified_event_id` | event_search | Condicional* | — |
| Descrição | `post_content` | textarea | Sim | string_length 20-5000 |
| Valor de Referência | `_classified_price` | text | Não | is_numeric |
| Negociável | `_classified_negotiable` | checkbox | Não | — |
| Condição | `_classified_condition` | select | Não | — |
| Localização | `_classified_location` | text | Não | — |
| Telefone | `_classified_contact_phone` | text | Não | — |
| WhatsApp | `_classified_contact_whatsapp` | text | Não | — |
| Fotos | `_classified_gallery` | gallery (file upload) | Não | — |
| *Hidden*: edit_id | `edit_id` | hidden | — | — |
| *Hidden*: nonce | `apollo_classified_nonce` | hidden | — | — |
| *Submit* | `apollo_submit_classified` | submit | — | — |

> \*`_classified_event_id` é obrigatório via JS quando o domain é `repasse`, `ingresso` ou `ticket`.

---

## 2. apollo-groups — Criar Grupo

- **Arquivo**: [apollo-groups/templates/create-group.php](apollo-groups/templates/create-group.php#L507-L574)
- **Cria**: Registro na tabela `apollo_groups` via REST `apollo/v1/groups`
- **Rota**: `/criar-grupo`
- **Tipo**: JS → `fetch()` POST com JSON body

### Campos

| Campo | `name` / `id` | Tipo | Obrigatório | Validação |
|-------|---------------|------|-------------|-----------|
| Tipo de Grupo | `groupType` | select | Sim | Opções: `comuna`, `nucleo` |
| Nome do Grupo | `groupName` | text | Sim | maxlength=100 |
| Descrição | `groupDesc` | textarea | Não | — |
| Tags | `groupTags` | text (comma-separated) | Não | — |
| Regras | `groupRules` | textarea | Não | — |

---

## 3. apollo-sign — Signature Pad (Widget de Assinatura)

- **Arquivo**: [apollo-sign/templates/signature-pad.php](apollo-sign/templates/signature-pad.php#L34)
- **Cria**: Registro em `apollo_signatures` via AJAX `apollo_sign_create_signature` + `apollo_sign_sign_document`
- **Shortcode**: `[apollo_signature_pad doc_id="123"]`
- **Tipo**: `<form>` com AJAX (FormData)

### Campos

| Campo | `name` | Tipo | Obrigatório |
|-------|--------|------|-------------|
| Certificado PFX | `certificate` | file (.pfx/.p12) | Sim |
| Senha do certificado | `password` | password | Sim |
| *Hidden*: `doc_id` | `doc_id` | hidden (via JS) | — |
| *Hidden*: `signature_id` | `signature_id` | hidden (via JS) | — |
| *Hidden*: `nonce` | `apollo_sign_nonce` | hidden | — |

---

## 4. apollo-sign — Upload PFX (Signing Form)

- **Arquivo**: [apollo-sign/templates/parts/signing-form.php](apollo-sign/templates/parts/signing-form.php#L28-L52)
- **Cria**: Mesmo registro em `apollo_signatures`
- **Tipo**: `<form>` POST enctype multipart

### Campos

| Campo | `name` | Tipo | Obrigatório |
|-------|--------|------|-------------|
| Certificado | `certificate` | file | Sim |
| Senha | `password` | password | Sim |
| *Hidden*: Posição X | `sig_x` | hidden | — |
| *Hidden*: Posição Y | `sig_y` | hidden | — |
| *Hidden*: Largura | `sig_w` | hidden | — |
| *Hidden*: Altura | `sig_h` | hidden | — |
| *Hidden*: Página | `sig_page` | hidden | — |
| *Hidden*: Modo | `placement_mode` | hidden (default "auto_footer") | — |

---

## 5. apollo-shortcodes — Cena::Rio Event Submission

- **Arquivo**: [apollo-shortcodes/includes/class-cena-rio-submissions.php](apollo-shortcodes/includes/class-cena-rio-submissions.php#L444-L745)
- **Handler**: Método `create_cena_event()` → `wp_insert_post()` + `update_post_meta()`
- **Cria**: CPT `event_listing` com status `private`, meta `_apollo_source=cena-rio`, `_apollo_cena_status=expected`
- **Tipo**: `<form>` POST

### Campos

| Campo | `name` | Tipo | Obrigatório |
|-------|--------|------|-------------|
| Nome do Evento | `event_title` | text | Sim |
| Descrição | `event_description` | textarea | Sim |
| Data Início | `event_start_date` | date | Sim |
| Data Fim | `event_end_date` | date | Não |
| Hora Início | `event_start_time` | time | Não |
| Hora Fim | `event_end_time` | time | Não |
| Local | `event_venue` | text | Não |
| Latitude | `event_lat` | number | Não |
| Longitude | `event_lng` | number | Não |
| *Hidden*: action | `action` | hidden = `apollo_cena_submit_event` | — |
| *Hidden*: nonce | `apollo_cena_nonce` | hidden | — |

---

## 6. apollo-shortcodes — Newsletter Subscribe

- **Arquivo**: [apollo-shortcodes/includes/class-apollo-native-newsletter.php](apollo-shortcodes/includes/class-apollo-native-newsletter.php#L872-L940)
- **Cria**: Registro em `{prefix}_apollo_newsletter_subscribers`
- **Shortcode**: `[apollo_newsletter]`
- **Tipo**: `<form>` POST

### Campos

| Campo | `name` | Tipo | Obrigatório |
|-------|--------|------|-------------|
| Nome | `name` | text | Condicional (atributo `show_name`) |
| Email | `email` | email | Sim |
| GDPR consent | `gdpr` | checkbox | Condicional (se habilitado) |
| *Hidden*: nonce | `_wpnonce` | hidden | — |

---

## 7. apollo-login — Registro de Usuário (Original)

- **Arquivo**: [apollo-login/templates/parts/register-form.php](apollo-login/templates/parts/register-form.php#L1-L324)
- **Cria**: Conta WordPress (user account)
- **Tipo**: Multi-step `<form>` com AJAX

### Campos (7 etapas)

| Etapa | Campo | `name` | Tipo | Obrigatório |
|-------|-------|--------|------|-------------|
| 1 | Nome Social | `social_name` | text | Sim |
| 2 | Instagram | `instagram_username` | text | Sim |
| 2 | Username (= instagram) | `username` | hidden | — |
| 3 | Email | `email` | email | Sim |
| 4 | Senha | `password` | password | Sim |
| 4 | Confirmar Senha | `password_confirm` | password | Sim |
| 5 | Sons preferidos | `sounds[]` | checkboxes (1-5 seleções) | Sim |
| — | Quiz Token | `apollo_quiz_token` | hidden | — |
| — | Social Name Hidden | `social_name_hidden` | hidden | — |
| — | Instagram Hidden | `instagram_hidden` | hidden | — |

---

## 8. apollo-login — Registro de Usuário (Novo Design)

- **Arquivo**: [apollo-login/templates/parts/new_register-form.php](apollo-login/templates/parts/new_register-form.php#L1-L309)
- **Cria**: Conta WordPress (user account)
- **Tipo**: `<form>` com AJAX

### Campos

| Campo | `name` | Tipo | Obrigatório |
|-------|--------|------|-------------|
| Nome Social | `nome` | text | Sim |
| Instagram | `instagram` | text | Sim |
| Tipo Documento | `doc_type` | hidden (cpf/passport) | — |
| CPF | `cpf` | text (masked) | Condicional |
| Passaporte | `passport` | text | Condicional |
| País do Passaporte | `passport_country` | text | Condicional |
| Email | `email` | email | Sim |
| Senha | `senha` | password (min 8) | Sim |
| Sons preferidos | `sounds[]` | checkboxes | Sim |
| Termos aceitos | `terms_accepted` | hidden | — |
| Nonce | `apollo_register_nonce` | hidden | — |

---

## 9. apollo-social — Compose Box (Feed Post)

- **Arquivo**: [apollo-social/templates/parts/compose-box.php](apollo-social/templates/parts/compose-box.php)
- **Cria**: Post social no feed via JS → REST
- **Tipo**: Sem `<form>` — JS-driven (botão de submit)

### Campos

| Campo | `id` | Tipo | Validação |
|-------|------|------|-----------|
| Texto do post | `feed-compose-text` | textarea | maxlength=2000 |
| URL embed | `compose-url-input` | url | — |
| Mídia (SoundCloud/Spotify/YouTube/Evento) | botões de mídia | — | — |
| *Submit* | `feed-post-btn` | button | — |

---

## 10. apollo-comment — Formulário de Depoimento

- **Arquivo**: [apollo-comment/templates/depoimento-form.php](apollo-comment/templates/depoimento-form.php)
- **Cria**: Registro de depoimento (testemunho) via JS → REST
- **Tipo**: Sem `<form>` — JS click handler

### Campos

| Campo | `id` | Tipo | Validação |
|-------|------|------|-----------|
| Texto do depoimento | `depoimento-new-text` | textarea | maxlength=1000 |
| Post ID alvo | `data-post-id` | data attribute no wrapper | — |
| *Submit* | `depoimento-submit` | button | — |

---

## 11. apollo-chat — Enviar Mensagem

- **Arquivo**: [apollo-chat/templates/chat.php](apollo-chat/templates/chat.php#L180-L295)
- **Cria**: Mensagem de chat em thread existente via JS → REST
- **Tipo**: Sem `<form>` — JS-driven

### Campos

| Campo | `id` | Tipo |
|-------|------|------|
| Texto da mensagem | `ac-compose-input` | textarea |
| GIF picker | botão GIF | — |
| Emoji picker | botão emoji | — |

---

## 12. apollo-chat — Novo Thread (Conversa)

- **Arquivo**: [apollo-chat/templates/chat.php](apollo-chat/templates/chat.php#L180-L295)
- **Cria**: Thread de chat + primeira mensagem via JS → REST
- **Tipo**: Modal JS-driven

### Campos

| Campo | `id` | Tipo |
|-------|------|------|
| Buscar usuário | `ac-nt-search` | text (typeahead) |
| Mensagem inicial | `ac-nt-message` | textarea |
| *Submit* | `ac-nt-send` | button |

---

## 13. apollo-docs — Upload de Documento

- **Arquivo**: [apollo-docs/templates/frontend-documents.php](apollo-docs/templates/frontend-documents.php)
- **Cria**: Documento/attachment via JS → REST
- **Tipo**: Sem `<form>` — drag-and-drop + file input via JS

### Campos

| Campo | `id` | Tipo |
|-------|------|------|
| Upload de arquivo | `fm-file-input` | file (multiple) |
| Drag & Drop zone | — | — |
| Novo documento | `fm-btn-new-doc` | button |
| Nova pasta | `fm-sidebar-btn-folder` | button |

---

## 14. apollo-templates — Sugestão de Evento (Homepage Panel)

- **Arquivo**: [apollo-templates/templates/template-parts/new-home/panel-acesso.php](apollo-templates/templates/template-parts/new-home/panel-acesso.php#L50-L180)
- **Cria**: Sugestão de evento (provavelmente `event_listing` como sugestão)
- **Tipo**: `<form>` POST

### Campos

| Campo | `name` | Tipo | Obrigatório |
|-------|--------|------|-------------|
| Dia | `event_day` | number | Sim |
| Mês | `event_month` | number | Sim |
| Ano | `event_year` | number | Sim |
| Nome do Evento | `event_name` | text | Sim |
| URL do Ingresso | `event_ticket_url` | url | Sim |
| Local | `event_local` | text | Não |
| DJs | `event_djs` | text | Não |
| *Hidden*: nonce | `suggest_event_nonce` | hidden | — |

---

## 15. apollo-admin — Modal de Nova Entidade (Multi-CPT)

- **Arquivo**: [apollo-admin/templates/partials/modal-form.php](apollo-admin/templates/partials/modal-form.php#L1-L400)
- **Cria**: Múltiplos CPTs como **draft** via AJAX `wp_insert_post`
- **Tipo**: Modal slide-in JS-driven (submit via AJAX + FormData)
- **Nota**: Acessível via dashboard administrativo Apollo (frontend), NÃO é wp-admin

### Formulários definidos no objeto JS `FORMS`:

#### 15a. `new-event` → CPT `event`

| Campo | `name` | Tipo |
|-------|--------|------|
| Título | `title` | text |
| Data Início | `_event_start_date` | date |
| Data Fim | `_event_end_date` | date |
| Hora Início | `_event_start_time` | time |
| Hora Fim | `_event_end_time` | time |
| Local (ID) | `_event_loc_id` | text |
| URL Ingresso | `_event_ticket_url` | url |
| Preço Ingresso | `_event_ticket_price` | text |
| Privacidade | `_event_privacy` | select (public/private/members) |
| Conteúdo | `content` | textarea |

#### 15b. `new-dj` → CPT `dj`

| Campo | `name` | Tipo |
|-------|--------|------|
| Nome | `title` | text |
| Bio curta | `_dj_bio_short` | textarea |
| Instagram | `_dj_instagram` | text |
| SoundCloud | `_dj_soundcloud` | url |
| Spotify | `_dj_spotify` | url |
| YouTube | `_dj_youtube` | url |
| Website | `_dj_website` | url |
| User ID vinculado | `_dj_user_id` | text |

#### 15c. `new-hub` → CPT `hub`

| Campo | `name` | Tipo |
|-------|--------|------|
| Título | `title` | text |
| Conteúdo | `content` | textarea |

#### 15d. `new-local` → CPT `local`

| Campo | `name` | Tipo |
|-------|--------|------|
| Nome | `title` | text |
| Endereço | `_local_address` | text |
| Cidade | `_local_city` | text |
| Latitude | `_local_lat` | text |
| Longitude | `_local_lng` | text |
| Capacidade | `_local_capacity` | text |
| Telefone | `_local_phone` | text |
| Instagram | `_local_instagram` | text |
| Website | `_local_website` | url |
| Faixa de Preço | `_local_price_range` | select |

#### 15e. `new-classified` → CPT `classified`

| Campo | `name` | Tipo |
|-------|--------|------|
| Título | `title` | text |
| Preço | `_classified_price` | text |
| Condição | `_classified_condition` | select (novo/usado/recondicionado) |
| Conteúdo | `content` | textarea |

#### 15f. `report` → Formulário de Denúncia (sem CPT)

| Campo | `name` | Tipo |
|-------|--------|------|
| Nome | `name` | text |
| Email | `email` | email |
| Assunto | `subject` | select |
| Mensagem | `message` | textarea |

---

## 16. apollo-core — Report/Contact Modal

- **Arquivo**: [apollo-core/includes/report-modal.php](apollo-core/includes/report-modal.php#L324-L400)
- **Cria**: Mensagem de report/contato (REST ou Google Forms)
- **Tipo**: `<form>` JS submit

### Campos

| Campo | `name` | Tipo | Obrigatório |
|-------|--------|------|-------------|
| Nome | `name` | text | Sim |
| Email | `email` | email | Sim |
| Assunto | `subject` | select | Sim |
| Mensagem | `message` | text | Sim |

> Opções de `subject` variam por contexto: **Report** (Conteúdo impróprio / Spam ou fraude / Perfil falso / Outro) vs **Contato** (Parceria / Problema / Suporte / Elogio)

---

## 17. apollo-gestor — Nova Tarefa

- **Arquivo**: [apollo-gestor/templates/gestor.php](apollo-gestor/templates/gestor.php#L297-L301)
- **Cria**: Registro em `apollo_gestor_tasks` via JS → REST
- **Tipo**: Sem `<form>` — input inline + botão

### Campos

| Campo | `id` | Tipo |
|-------|------|------|
| Título da tarefa | `newTaskTitle` | text (placeholder: "Nova tarefa…") |
| *Submit* | `btnAddTask` | button |

> Também há botões `ev-col-add` para criar eventos no kanban e `btnAddTeamMember` para adicionar membros ao time, mas são modais JS sem campos fixos visíveis no template.

---

## Formulários que NÃO criam conteúdo (excluídos)

| Plugin | Arquivo | Motivo da exclusão |
|--------|---------|-------------------|
| apollo-users | `edit-profile.php` | Apenas **atualiza** user meta existente |
| apollo-users | `profile-edit-form.php` | Apenas **atualiza** user meta existente |
| apollo-hub | `edit-hub.php` | Apenas **edita** hub existente (auto-provisionado) |
| apollo-email | `Plugin.php` (shortcode) | Apenas **atualiza** preferências de email |
| apollo-templates | `edit-post.php` | Editor genérico — primariamente **edição** |
| apollo-login | `login-form.php` | Formulário de **login**, não criação |
| apollo-membership | `templates/admin/*` | Formulários **admin** (wp-admin) |
| apollo-seo | `Admin.php` | Formulário **admin** (wp-admin) |
| apollo-sheets | `Controller.php` | Formulários **admin** (wp-admin) |
| apollo-fav | `functions.php` | `$wpdb->insert` via botão toggle, sem formulário |
| apollo-wow | — | Sem formulários (reações via botão) |
| apollo-notif | `Admin.php` | Formulários **admin** (wp-admin) |

---

## Plugins sem formulários frontend de criação

`apollo-classifieds`, `apollo-coauthor`, `apollo-dashboard`, `apollo-djs`, `apollo-events`, `apollo-loc`, `apollo-mod`, `apollo-membership`, `apollo-pwa`, `apollo-runtime`, `apollo-seo`, `apollo-sheets`, `apollo-statistics`

> Esses plugins têm `wp_insert_post` / `$wpdb->insert` apenas em controllers REST API ou hooks internos — a criação é feita pelo **apollo-admin modal** (item 15) ou por automação interna.
