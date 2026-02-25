<?php
// phpcs:ignoreFile

/**
 * Template Name: Cenario New Event
 * Description: Simplified event submission form for logged-in users (compliant with public form structure)
 */

defined('ABSPATH') || exit;

// Verificar se usuário está logado
if (! is_user_logged_in()) {
	wp_redirect(wp_login_url(get_permalink()));
	exit;
}

// Handle form submission (using public form processing function)
$submitted = false;
$error_message = '';
$submitted_nonce = isset($_POST['apollo_event_nonce'])
	? sanitize_text_field(wp_unslash($_POST['apollo_event_nonce']))
	: '';
if (isset($_POST['apollo_submit_event']) && wp_verify_nonce($submitted_nonce, 'apollo_public_event')) {
	$submitted = apollo_process_public_event_submission();
	if (is_wp_error($submitted)) {
		$error_message = $submitted->get_error_message();
		$submitted = false;
	}
}

get_header();
?>

<style>
/* Public Event Form Styles */
.apollo-form-title {
	font-size: 1.5rem;
	font-weight: 700;
	margin-bottom: 1rem;
	color: #333;
}

.apollo-form-helper {
	background: rgba(253, 92, 2, 0.1);
	border: 1px solid rgba(253, 92, 2, 0.2);
	border-radius: 8px;
	padding: 1rem;
	margin-bottom: 1.5rem;
	color: #666;
	font-size: 0.875rem;
}

.apollo-section {
	margin-bottom: 2rem;
}

.apollo-section-heading {
	font-size: 1.125rem;
	font-weight: 600;
	margin-bottom: 1rem;
	color: #333;
	display: flex;
	align-items: center;
	gap: 0.5rem;
}

.apollo-section-heading i {
	color: #fd5c02;
}

.apollo-inputs-row {
	display: grid;
	grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
	gap: 1rem;
	margin-bottom: 1rem;
}

.apollo-field-label {
	display: block;
	font-weight: 500;
	margin-bottom: 0.5rem;
	color: #333;
	font-size: 0.9375rem;
}

.apollo-field-label i {
	margin-right: 0.5rem;
	color: #fd5c02;
}

/* Upload Zone */
.apollo-upload-zone {
	border: 2px dashed #e0e2e4;
	border-radius: 8px;
	padding: 2rem;
	text-align: center;
	cursor: pointer;
	transition: all 0.2s ease;
	margin-bottom: 1.5rem;
	position: relative;
	overflow: hidden;
}

.apollo-upload-zone:hover {
	border-color: #fd5c02;
	background: rgba(253, 92, 2, 0.05);
}

.apollo-upload-preview {
	max-width: 100%;
	max-height: 200px;
	border-radius: 8px;
	display: none;
}

.apollo-upload-content {
	display: flex;
	flex-direction: column;
	align-items: center;
	gap: 0.5rem;
}

.apollo-upload-icon {
	font-size: 2rem;
	color: #666;
}

.apollo-upload-text {
	color: #666;
	font-weight: 500;
}

/* Combobox */
.apollo-combobox-wrapper {
	position: relative;
}

.apollo-combobox-dropdown {
	position: absolute;
	top: 100%;
	left: 0;
	right: 0;
	background: #fff;
	border: 1px solid #e0e2e4;
	border-radius: 8px;
	max-height: 200px;
	overflow-y: auto;
	z-index: 1000;
	display: none;
	box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}

.apollo-combobox-dropdown.apollo-active {
	display: block;
}

.apollo-combobox-option {
	padding: 0.75rem 1rem;
	cursor: pointer;
	transition: background 0.2s ease;
	border-bottom: 1px solid #e0e2e4;
}

.apollo-combobox-option:last-child {
	border-bottom: none;
}

.apollo-combobox-option:hover {
	background: #f5f5f5;
}

.apollo-combobox-option.apollo-hidden {
	display: none;
}

.apollo-action-row {
	display: flex;
	gap: 0.5rem;
	align-items: flex-start;
	margin-bottom: 1rem;
}

.apollo-btn-icon {
	padding: 0.5rem;
	border: 1px solid #e0e2e4;
	border-radius: 6px;
	background: #fff;
	color: #666;
	cursor: pointer;
	transition: all 0.2s ease;
	font-size: 0.875rem;
}

.apollo-btn-icon:hover {
	border-color: #fd5c02;
	color: #fd5c02;
}

/* Timetable */
.apollo-timetable-list {
	margin-top: 1rem;
}

.apollo-timetable-row {
	display: flex;
	align-items: center;
	gap: 1rem;
	padding: 1rem;
	background: #fff;
	border: 1px solid #e0e2e4;
	border-radius: 8px;
	margin-bottom: 0.5rem;
	transition: all 0.2s ease;
}

.apollo-timetable-row:hover {
	border-color: #fd5c02;
	box-shadow: 0 2px 8px rgba(253, 92, 2, 0.1);
}

.apollo-drag-handle {
	display: flex;
	flex-direction: column;
	gap: 0.25rem;
}

.apollo-drag-handle i {
	cursor: pointer;
	color: #666;
	transition: color 0.2s ease;
	font-size: 0.75rem;
}

.apollo-drag-handle i:hover {
	color: #fd5c02;
}

.apollo-dj-info {
	flex: 1;
}

.apollo-dj-name {
	font-weight: 600;
	color: #333;
	margin-bottom: 0.25rem;
}

.apollo-dj-meta {
	font-size: 0.875rem;
	color: #666;
}

.apollo-time-group {
	display: flex;
	align-items: center;
	gap: 0.5rem;
}

.apollo-time-input {
	width: 80px;
	padding: 0.25rem 0.5rem;
	border: 1px solid #e0e2e4;
	border-radius: 4px;
	font-size: 0.875rem;
	text-align: center;
}

.apollo-time-input.apollo-read-only {
	background: #f5f5f5;
	cursor: not-allowed;
}

.apollo-time-input.apollo-editable:focus {
	border-color: #fd5c02;
	outline: none;
}

.apollo-time-divider {
	color: #666;
	font-size: 0.875rem;
}

/* Modals */
.apollo-modal-overlay {
	position: fixed;
	top: 0;
	left: 0;
	right: 0;
	bottom: 0;
	background: rgba(0, 0, 0, 0.5);
	display: flex;
	align-items: center;
	justify-content: center;
	z-index: 10000;
	opacity: 0;
	visibility: hidden;
	transition: all 0.3s ease;
}

.apollo-modal-overlay.apollo-open {
	opacity: 1;
	visibility: visible;
}

.apollo-modal-card {
	background: #fff;
	border-radius: 12px;
	max-width: 500px;
	width: 90%;
	max-height: 90vh;
	overflow-y: auto;
	box-shadow: 0 20px 40px rgba(0, 0, 0, 0.2);
	transform: scale(0.9) translateY(20px);
	transition: all 0.3s ease;
}

.apollo-modal-overlay.apollo-open .apollo-modal-card {
	transform: scale(1) translateY(0);
}

.apollo-modal-header {
	padding: 1.5rem;
	border-bottom: 1px solid #e0e2e4;
	font-size: 1.25rem;
	font-weight: 600;
	color: #333;
	display: flex;
	justify-content: space-between;
	align-items: center;
}

.apollo-modal-close {
	background: none;
	border: none;
	font-size: 1.5rem;
	cursor: pointer;
	color: #666;
	padding: 0.25rem;
	border-radius: 4px;
	transition: all 0.2s ease;
}

.apollo-modal-close:hover {
	background: #f5f5f5;
	color: #333;
}

.apollo-form-group {
	padding: 1.5rem;
	border-bottom: 1px solid #e0e2e4;
}

.apollo-form-group:last-child {
	border-bottom: none;
}

.apollo-form-label {
	display: block;
	font-weight: 500;
	margin-bottom: 0.5rem;
	color: #333;
	font-size: 0.9375rem;
}

.apollo-lat-row {
	display: flex;
	gap: 0.5rem;
}

.apollo-lat-row input {
	flex: 1;
}

.apollo-modal-footer {
	padding: 1.5rem;
	border-top: 1px solid #e0e2e4;
	display: flex;
	justify-content: flex-end;
	gap: 0.75rem;
}

.apollo-btn {
	padding: 0.75rem 1.5rem;
	border-radius: 8px;
	font-weight: 500;
	cursor: pointer;
	transition: all 0.2s ease;
	border: none;
	font-size: 0.9375rem;
}

.apollo-btn-primary {
	background: #fd5c02;
	color: #fff;
}

.apollo-btn-primary:hover {
	background: #e54a00;
}

.apollo-btn-ghost {
	background: transparent;
	color: #666;
	border: 1px solid #e0e2e4;
}

.apollo-btn-ghost:hover {
	background: #f5f5f5;
}

.apollo-btn-block {
	width: 100%;
	margin-top: 1rem;
}

/* Base Apollo styles */
.apollo-input {
	width: 100%;
	padding: 0.625rem 0.875rem;
	border: 1px solid #e0e2e4;
	border-radius: 8px;
	font-size: 0.9375rem;
	transition: all 0.2s ease;
	background: #fff;
}

.apollo-input:focus {
	outline: none;
	border-color: #fd5c02;
	box-shadow: 0 0 0 3px rgba(253, 92, 2, 0.1);
}

.apollo-alert {
	padding: 1rem;
	border-radius: 8px;
	margin-bottom: 1.5rem;
	display: flex;
	align-items: center;
	gap: 0.75rem;
}

.apollo-alert-success {
	background: rgba(40, 167, 69, 0.1);
	border: 1px solid rgba(40, 167, 69, 0.2);
	color: #155724;
}

.apollo-alert-danger {
	background: rgba(220, 53, 69, 0.1);
	border: 1px solid rgba(220, 53, 69, 0.2);
	color: #721c24;
}

.apollo-alert i {
	font-size: 1.25rem;
}
</style>

<div class="apollo-public-event-form-wrapper">
	<?php if ($submitted) : ?>
		<div class="apollo-alert apollo-alert-success">
			<i class="ri-checkbox-circle-line"></i>
			<p>
				<?php esc_html_e('O evento foi registrado internamente! Algum de nossos moderadores vão verificar a veracidade dos dados e já lançaremos aqui, vamos juntos?', 'apollo-events-manager'); ?>
			</p>
		</div>
	<?php else : ?>
		<?php if ($error_message) : ?>
			<div class="apollo-alert apollo-alert-danger">
				<i class="ri-error-warning-line"></i>
				<p><?php echo esc_html($error_message); ?></p>
			</div>
		<?php endif; ?>

		<h2 class="apollo-form-title"><?php esc_html_e('Criar Novo Evento', 'apollo-events-manager'); ?></h2>

		<form method="post" class="apollo-public-event-form" id="apolloPublicEventForm">
			<?php wp_nonce_field('apollo_public_event', 'apollo_event_nonce'); ?>

			<p class="apollo-form-helper">
				<?php esc_html_e('Todos os envios ficam pendentes até revisão da equipe Apollo.', 'apollo-events-manager'); ?>
			</p>

			<!-- Event Banner Upload -->
			<div class="apollo-upload-zone" onclick="document.getElementById('apollo-file-input').click()">
				<img id="apollo-preview-img" class="apollo-upload-preview" src="" alt="">
				<div class="apollo-upload-content" id="apollo-upload-placeholder">
					<div class="apollo-upload-icon"><i class="ri-camera-line"></i></div>
					<div class="apollo-upload-text">Capa do Evento (Destaque)</div>
				</div>
				<input type="file" id="apollo-file-input" name="_event_banner" accept="image/*" onchange="apolloPreviewFile('apollo-file-input','apollo-preview-img')" style="display:none">
			</div>

			<div class="apollo-form-field">
				<label for="event_name" class="apollo-field-label">
					<i class="ri-calendar-todo-line"></i>
					<?php esc_html_e('Nome do Evento', 'apollo-events-manager'); ?>
				</label>
				<input
					type="text"
					id="event_name"
					name="event_name"
					class="apollo-input"
					placeholder="<?php esc_attr_e('Nome do evento', 'apollo-events-manager'); ?>"
					required
				/>
			</div>

			<!-- Schedule Section -->
			<div class="apollo-section">
				<h3 class="apollo-section-heading">
					<i class="ri-time-line"></i>
					Cronograma
				</h3>

				<div class="apollo-inputs-row">
					<div class="apollo-form-field">
						<label for="event_date_start" class="apollo-field-label">
							Data Início
						</label>
						<input
							type="date"
							id="apollo-start-date"
							name="day_start"
							class="apollo-input"
							required
							min="<?php echo esc_attr(date('Y-m-d')); ?>"
							onchange="apolloAutoSetEndDate()"
						/>
					</div>
					<div class="apollo-form-field">
						<label for="event_start_time" class="apollo-field-label">
							Hora Início (Padrão 23h)
						</label>
						<input
							type="time"
							id="apollo-start-time"
							name="_event_start_time"
							class="apollo-input"
							step="900"
							onchange="apolloRecalculateTimeline()"
						/>
					</div>
				</div>

				<div class="apollo-inputs-row">
					<div class="apollo-form-field">
						<label for="event_date_end" class="apollo-field-label">
							Data Fim (Auto +1 dia)
						</label>
						<input
							type="date"
							id="apollo-end-date"
							name="day_end"
							class="apollo-input"
						/>
					</div>
					<div class="apollo-form-field">
						<label for="event_end_time" class="apollo-field-label">
							Hora Fim (Padrão 08h)
						</label>
						<input
							type="time"
							id="apollo-end-time"
							name="_event_end_time"
							class="apollo-input"
							step="900"
						/>
					</div>
				</div>
			</div>

			<!-- DJs/Lineup Section -->
			<div class="apollo-form-field">
				<label class="apollo-field-label">
					<i class="ri-music-line"></i>
					Line-up (DJs)
				</label>
				<div class="apollo-action-row">
					<div class="apollo-combobox-wrapper">
						<input type="text" class="apollo-input" id="apollo-dj-input" placeholder="Adicionar DJ..."
							   oninput="apolloFilterCombobox(this)" onfocus="apolloOpenCombobox(this)" onblur="apolloCloseCombobox(this)">
						<div class="apollo-combobox-dropdown" id="apollo-dj-dropdown">
							<div class="apollo-combobox-option" data-id="101" onmousedown="apolloAddDJ('DJ Alok', 101, this)">DJ Alok</div>
							<div class="apollo-combobox-option" data-id="102" onmousedown="apolloAddDJ('KVSH', 102, this)">KVSH</div>
							<div class="apollo-combobox-option" data-id="103" onmousedown="apolloAddDJ('Mochakk', 103, this)">Mochakk</div>
							<div class="apollo-combobox-option" data-id="104" onmousedown="apolloAddDJ('Vintage Culture', 104, this)">Vintage Culture</div>
						</div>
					</div>
					<button type="button" class="apollo-btn-icon" onclick="apolloOpenModal('apollo-dj-modal')">
						<i class="ri-add-line"></i>
					</button>
				</div>
				<div class="apollo-timetable-list" id="apollo-timetable-list"></div>
			</div>

			<!-- Location Section -->
			<div class="apollo-form-field">
				<label class="apollo-field-label">
					<i class="ri-map-pin-line"></i>
					Local (Único)
				</label>
				<div class="apollo-action-row">
					<div class="apollo-combobox-wrapper">
						<input type="text" class="apollo-input" id="apollo-local-input" placeholder="Buscar Local..."
							   oninput="apolloFilterCombobox(this)" onfocus="apolloOpenCombobox(this)" onblur="apolloCloseCombobox(this)">
						<div class="apollo-combobox-dropdown" id="apollo-local-dropdown">
							<div class="apollo-combobox-option" data-id="501" onmousedown="apolloSelectLocal('Fundição Progresso', 501, this)">Fundição Progresso</div>
							<div class="apollo-combobox-option" data-id="502" onmousedown="apolloSelectLocal('Circo Voador', 502, this)">Circo Voador</div>
						</div>
					</div>
					<button type="button" class="apollo-btn-icon" onclick="apolloOpenModal('apollo-local-modal')">
						<i class="ri-add-line"></i>
					</button>
				</div>
				<input type="hidden" name="local_write" id="apollo-local-hidden">
			</div>

			<div class="apollo-form-field">
				<label for="url_tickets" class="apollo-field-label">
					<i class="ri-ticket-line"></i>
					<?php esc_html_e('URL de Ingressos', 'apollo-events-manager'); ?>
				</label>
				<input
					type="url"
					id="url_tickets"
					name="url_tickets"
					class="apollo-input"
					placeholder="https://..."
				/>
			</div>

			<div class="apollo-form-field">
				<label for="coupon_apollo" class="apollo-field-label">
					<i class="ri-coupon-line"></i>
					<?php esc_html_e('Cupom Apollo', 'apollo-events-manager'); ?>
				</label>
				<input
					type="text"
					id="coupon_apollo"
					name="coupon_apollo"
					class="apollo-input"
					placeholder="<?php esc_attr_e('Ex: apollo25', 'apollo-events-manager'); ?>"
				/>
			</div>

			<button type="submit" name="apollo_submit_event" class="apollo-btn apollo-btn-primary apollo-btn-block">
				<i class="ri-add-circle-line"></i>
				<?php esc_html_e('Incluir Evento', 'apollo-events-manager'); ?>
			</button>
		</form>

		<!-- DJ Modal -->
		<div class="apollo-modal-overlay" id="apollo-dj-modal">
			<div class="apollo-modal-card">
				<div class="apollo-modal-header">Novo DJ</div>
				<button type="button" class="apollo-modal-close" onclick="apolloCloseModal('apollo-dj-modal')">
					<i class="ri-close-line"></i>
				</button>
				<div class="apollo-form-group">
					<label class="apollo-form-label">Nome</label>
					<input type="text" class="apollo-input" id="apollo-new-dj-name">
				</div>
				<div class="apollo-modal-footer">
					<button type="button" class="apollo-btn apollo-btn-primary" onclick="apolloSaveNewDJ()">Salvar</button>
				</div>
			</div>
		</div>

		<!-- Local Modal -->
		<div class="apollo-modal-overlay" id="apollo-local-modal">
			<div class="apollo-modal-card">
				<div class="apollo-modal-header">Novo Local</div>
				<button type="button" class="apollo-modal-close" onclick="apolloCloseModal('apollo-local-modal')">
					<i class="ri-close-line"></i>
				</button>
				<div class="apollo-form-group">
					<label class="apollo-form-label">Nome</label>
					<input type="text" class="apollo-input" id="apollo-new-local-name">
				</div>
				<div class="apollo-form-group">
					<label class="apollo-form-label">
						Endereço Completo
						<i id="apollo-geo-loading" class="ri-loader-line" style="display:none;"></i>
					</label>
					<input type="text" class="apollo-input" id="apollo-new-local-address" oninput="apolloDebounceGeo(this.value)">
				</div>
				<div class="apollo-form-group">
					<label class="apollo-form-label">Geo (Auto)</label>
					<div class="apollo-lat-row">
						<input type="text" class="apollo-input" id="apollo-local-lat" placeholder="LAT" readonly>
						<input type="text" class="apollo-input" id="apollo-local-lon" placeholder="LON" readonly>
						<button type="button" class="apollo-btn-icon" onclick="apolloTriggerManualGeo()">
							<i class="ri-refresh-line"></i>
						</button>
					</div>
				</div>
				<div class="apollo-modal-footer">
					<button type="button" class="apollo-btn apollo-btn-ghost" onclick="apolloCloseModal('apollo-local-modal')">Cancelar</button>
					<button type="button" class="apollo-btn apollo-btn-primary" onclick="apolloSaveNewLocal()">Salvar</button>
				</div>
			</div>
		</div>

		<script>
		document.addEventListener('DOMContentLoaded', function() {
			// Initialize defaults
			const startTimeInput = document.getElementById('apollo-start-time');
			const endTimeInput = document.getElementById('apollo-end-time');

			if(startTimeInput && !startTimeInput.value) startTimeInput.value = "23:00";
			if(endTimeInput && !endTimeInput.value) endTimeInput.value = "08:00";

			// Form submission handler
			const form = document.getElementById('apolloPublicEventForm');
			if(form) {
				form.addEventListener('submit', function(e) {
					// Serialize DJ data
					const djRows = document.querySelectorAll('.apollo-timetable-row');
					const djIds = [];
					const djSlots = [];

					djRows.forEach((row, index) => {
						const djId = row.dataset.djid;
						const startTime = row.querySelector('.apollo-row-start').value;
						const endTime = row.querySelector('.apollo-row-end').value;
						const djName = row.querySelector('.apollo-dj-name').textContent;

						if(djId && startTime && endTime) {
							djIds.push(parseInt(djId));
							djSlots.push({
								dj_id: parseInt(djId),
								dj_name: djName,
								start_time: startTime,
								end_time: endTime,
								order: index
							});
						}
					});

					// Add hidden inputs for DJ data
					if(djIds.length > 0) {
						const djIdsInput = document.createElement('input');
						djIdsInput.type = 'hidden';
						djIdsInput.name = '_event_dj_ids';
						djIdsInput.value = JSON.stringify(djIds);
						form.appendChild(djIdsInput);

						const djSlotsInput = document.createElement('input');
						djSlotsInput.type = 'hidden';
						djSlotsInput.name = '_event_dj_slots';
						djSlotsInput.value = JSON.stringify(djSlots);
						form.appendChild(djSlotsInput);
					}
				});
			}
		});

		// Auto-set end date (+1 day)
		function apolloAutoSetEndDate() {
			const startDate = document.getElementById('apollo-start-date').value;
			const endDateInput = document.getElementById('apollo-end-date');

			if (startDate) {
				const dateObj = new Date(startDate);
				dateObj.setDate(dateObj.getDate() + 1);
				const y = dateObj.getFullYear();
				const m = String(dateObj.getMonth() + 1).padStart(2, '0');
				const d = String(dateObj.getDate()).padStart(2, '0');
				endDateInput.value = `${y}-${m}-${d}`;
			}
		}

		// Timeline recalculation with 2-hour default slots
		function apolloRecalculateTimeline() {
			const rows = document.querySelectorAll('.apollo-timetable-row');
			const eventStartTimeInput = document.getElementById('apollo-start-time');

			if (!eventStartTimeInput.value) return;

			let currentTime = apolloDateFromTimeStr(eventStartTimeInput.value);

			rows.forEach((row) => {
				const startInput = row.querySelector('.apollo-row-start');
				const endInput = row.querySelector('.apollo-row-end');

				// Lock start time to previous end time
				startInput.value = apolloTimeStrFromDate(currentTime);

				let endTime;

				if (endInput.value) {
					let userEndDate = apolloDateFromTimeStr(endInput.value);
					// Handle midnight rollover
					if (userEndDate.getHours() < currentTime.getHours() && currentTime.getHours() > 12) {
						userEndDate.setDate(userEndDate.getDate() + 1);
					}

					// If manual time is behind start time, reset to default 2H
					if (userEndDate <= currentTime) {
						currentTime.setHours(currentTime.getHours() + 2);
						endTime = new Date(currentTime);
						endInput.value = apolloTimeStrFromDate(endTime);
					} else {
						endTime = userEndDate;
						currentTime = userEndDate;
					}
				} else {
					// Apply default 2H slot
					currentTime.setHours(currentTime.getHours() + 2);
					endTime = new Date(currentTime);
					endInput.value = apolloTimeStrFromDate(endTime);
				}
			});
		}

		// Add DJ to timetable
		function apolloAddDJ(name, id, el) {
			const container = document.getElementById('apollo-timetable-list');
			const input = document.getElementById('apollo-dj-input');
			if(input) { input.value = ""; input.blur(); }

			const row = document.createElement('div');
			row.className = 'apollo-timetable-row';
			row.dataset.djid = id;
			row.innerHTML = `
				<div class="apollo-drag-handle">
					<i class="ri-arrow-up-line" onclick="apolloMoveRow(this, -1)"></i>
					<i class="ri-arrow-down-line" onclick="apolloMoveRow(this, 1)"></i>
				</div>
				<div class="apollo-dj-info">
					<div class="apollo-dj-name">${name}</div>
					<div class="apollo-dj-meta">Set Time</div>
				</div>
				<div class="apollo-time-group">
					<input type="time" class="apollo-time-input apollo-read-only apollo-row-start" readonly tabindex="-1">
					<span class="apollo-time-divider"><i class="ri-arrow-right-line"></i></span>
					<input type="time" class="apollo-time-input apollo-editable apollo-row-end" onchange="apolloRecalculateTimeline()">
				</div>
				<i class="ri-close-line" style="cursor:pointer; font-size:14px; color:#f97316; opacity:0.8" onclick="this.parentElement.remove(); apolloRecalculateTimeline();"></i>
			`;
			container.appendChild(row);
			apolloRecalculateTimeline();
		}

		// Move timetable row
		function apolloMoveRow(btn, dir) {
			const row = btn.closest('.apollo-timetable-row');
			const parent = row.parentElement;
			if (dir === -1 && row.previousElementSibling) parent.insertBefore(row, row.previousElementSibling);
			else if (dir === 1 && row.nextElementSibling) parent.insertBefore(row.nextElementSibling, row);
			apolloRecalculateTimeline();
		}

		// Select local (single/unique)
		function apolloSelectLocal(name, id, el) {
			const input = document.getElementById('apollo-local-input');
			const hiddenInput = document.getElementById('apollo-local-hidden');

			input.value = name;
			hiddenInput.value = name; // Store as text for now
		}

		// Helper functions
		function apolloDateFromTimeStr(str) {
			const [h, m] = str.split(':').map(Number);
			const d = new Date();
			d.setHours(h, m, 0, 0);
			return d;
		}

		function apolloTimeStrFromDate(d) {
			return String(d.getHours()).padStart(2,'0') + ':' + String(d.getMinutes()).padStart(2,'0');
		}

		function apolloPreviewFile(inputId, imgId) {
			const input = document.getElementById(inputId);
			const preview = document.getElementById(imgId);
			if(input && input.files[0]) {
				const reader = new FileReader();
				reader.onload = () => {
					preview.src = reader.result;
					preview.style.display = 'block';
				};
				reader.readAsDataURL(input.files[0]);
			}
		}

		// UI utilities
		function apolloOpenCombobox(input) {
			input.nextElementSibling.classList.add('apollo-active');
		}

		function apolloCloseCombobox(input) {
			setTimeout(() => input.nextElementSibling.classList.remove('apollo-active'), 200);
		}

		function apolloFilterCombobox(input) {
			const filter = input.value.toLowerCase();
			const options = input.nextElementSibling.children;
			Array.from(options).forEach(option =>
				option.classList.toggle('apollo-hidden', option.innerText.toLowerCase().indexOf(filter) === -1)
			);
		}

		function apolloOpenModal(id) {
			document.getElementById(id).classList.add('apollo-open');
		}

		function apolloCloseModal(id) {
			document.getElementById(id).classList.remove('apollo-open');
		}

		// Geocoding
		let apolloGeoTimer;
		function apolloDebounceGeo(addr) {
			clearTimeout(apolloGeoTimer);
			document.getElementById('apollo-geo-loading').style.display = 'inline-block';
			apolloGeoTimer = setTimeout(() => apolloFetchCoordinates(addr), 1500);
		}

		async function apolloFetchCoordinates(addr) {
			try {
				const response = await fetch(`https://nominatim.openstreetmap.org/search?format=json&q=${encodeURIComponent(addr)}&limit=1`, {
					headers: {'User-Agent': 'Apollo/1.0'}
				});
				const data = await response.json();
				if(data.length) {
					document.getElementById('apollo-local-lat').value = data[0].lat;
					document.getElementById('apollo-local-lon').value = data[0].lon;
				}
			} catch(e) {
				console.error('Geocoding error:', e);
			} finally {
				document.getElementById('apollo-geo-loading').style.display = 'none';
			}
		}

		function apolloTriggerManualGeo() {
			apolloFetchCoordinates(document.getElementById('apollo-new-local-address').value);
		}

		function apolloSaveNewLocal() {
			const name = document.getElementById('apollo-new-local-name').value;
			if(name) {
				apolloSelectLocal(name, 999);
				apolloCloseModal('apollo-local-modal');
			}
		}

		function apolloSaveNewDJ() {
			const name = document.getElementById('apollo-new-dj-name').value;
			if(name) {
				apolloAddDJ(name, 888);
				apolloCloseModal('apollo-dj-modal');
			}
		}
		</script>
	<?php endif; ?>
</div>

<style>
.apollo-public-event-form-wrapper {
	max-width: 600px;
	margin: 0 auto;
	padding: 30px;
	background: rgba(255, 255, 255, 0.7);
	backdrop-filter: blur(10px);
	border-radius: 16px;
	box-shadow: 0 4px 20px rgba(0, 0, 0, 0.1);
}
.apollo-form-title {
	margin: 0 0 24px 0;
	font-size: 1.75rem;
	font-weight: 700;
	text-align: center;
}
.apollo-public-event-form {
	display: flex;
	flex-direction: column;
	gap: 20px;
}
.apollo-form-field {
	display: flex;
	flex-direction: column;
	gap: 8px;
}
.apollo-form-helper {
	margin: 0;
	font-size: 0.95rem;
	color: var(--text-secondary, #4a5568);
}
.apollo-field-label {
	display: flex;
	align-items: center;
	gap: 8px;
	font-weight: 500;
	color: var(--text-primary, #1a1a1a);
}
.apollo-field-label i {
	font-size: 1.1em;
	color: var(--text-secondary, #666);
}
.apollo-input {
	width: 100%;
	padding: 12px 16px;
	border: 2px solid var(--border-color, #e2e8f0);
	border-radius: 8px;
	font-size: 1rem;
	transition: all 0.2s ease;
	box-sizing: border-box;
}
.apollo-input:focus {
	outline: none;
	border-color: var(--primary-color, #0078d4);
	box-shadow: 0 0 0 3px rgba(0, 120, 212, 0.1);
}
.apollo-btn {
	display: inline-flex;
	align-items: center;
	justify-content: center;
	gap: 8px;
	padding: 12px 24px;
	border: none;
	border-radius: 8px;
	font-size: 1rem;
	font-weight: 500;
	cursor: pointer;
	transition: all 0.2s ease;
	text-decoration: none;
}
.apollo-btn-primary {
	background: var(--primary-color, #0078d4);
	color: #fff;
}
.apollo-btn-primary:hover {
	background: var(--primary-color-dark, #0063a3);
	transform: translateY(-2px);
	box-shadow: 0 4px 12px rgba(0, 120, 212, 0.3);
}
.apollo-btn-block {
	width: 100%;
}
.apollo-alert {
	display: flex;
	align-items: flex-start;
	gap: 12px;
	padding: 16px;
	border-radius: 8px;
	margin-bottom: 20px;
}
.apollo-alert i {
	font-size: 1.5em;
	flex-shrink: 0;
}
.apollo-alert-success {
	background: #f0fdf4;
	border: 2px solid #86efac;
	color: #166534;
}
.apollo-alert-success i {
	color: #22c55e;
}
.apollo-alert-danger {
	background: #fef2f2;
	border: 2px solid #fecaca;
	color: #991b1b;
}
.apollo-alert-danger i {
	color: #ef4444;
}
</style>

<?php get_footer(); ?>
