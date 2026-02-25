<?php

/**
 * Apollo Sign — Signature Pad Shortcode Template
 * Usage: [apollo_signature_pad doc_id="123"]
 * Embeddable inline signing widget.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$doc_id = absint( $atts['doc_id'] ?? 0 );
$user   = wp_get_current_user();
?>
<div class="apollo-sign-pad" data-doc-id="<?php echo $doc_id; ?>" style="
	background: #1a1a1e;
	border: 1px solid rgba(255,255,255,.06);
	border-radius: 16px;
	padding: 20px;
	font-family: 'Space Grotesk', sans-serif;
	color: #e8e8ec;
	max-width: 460px;
">
	<div style="display:flex;align-items:center;gap:8px;margin-bottom:14px">
		<i class="ri-shield-keyhole-fill" style="color:#f45f00;font-size:20px"></i>
		<strong style="font-size:14px">Assinar Documento #<?php echo $doc_id; ?></strong>
	</div>

	<div style="background:rgba(244,95,0,.12);border-radius:8px;padding:8px 12px;margin-bottom:14px;font-size:11px;color:#f45f00;display:flex;align-items:center;gap:6px">
		<i class="ri-shield-check-fill"></i> Conformidade ICP-Brasil
	</div>

	<form class="apollo-sign-form" data-doc="<?php echo $doc_id; ?>">
		<div style="margin-bottom:10px">
			<label style="font-size:11px;color:#8a8a96;display:block;margin-bottom:3px">Certificado (.pfx / .p12)</label>
			<input type="file" name="certificate" accept=".pfx,.p12" required style="
				width:100%;height:38px;background:#222226;border:1px solid rgba(255,255,255,.06);
				border-radius:8px;padding:6px 10px;color:#e8e8ec;font-size:12px;cursor:pointer
			">
		</div>
		<div style="margin-bottom:12px">
			<label style="font-size:11px;color:#8a8a96;display:block;margin-bottom:3px">Senha do Certificado</label>
			<input type="password" name="password" placeholder="Senha..." required style="
				width:100%;height:38px;background:#222226;border:1px solid rgba(255,255,255,.06);
				border-radius:8px;padding:0 10px;color:#e8e8ec;font-size:12px;font-family:inherit
			">
		</div>
		<button type="submit" style="
			width:100%;height:40px;background:#f45f00;color:#fff;border:none;border-radius:10px;
			font-size:13px;font-weight:600;font-family:inherit;cursor:pointer;display:flex;
			align-items:center;justify-content:center;gap:6px
		">
			<i class="ri-shield-keyhole-fill"></i> Assinar
		</button>
	</form>

	<div class="apollo-sign-result" style="display:none;margin-top:12px;padding:10px;border-radius:8px;font-size:12px"></div>
</div>

<script>
	(function() {
		document.querySelectorAll('.apollo-sign-form').forEach(function(form) {
			form.addEventListener('submit', function(e) {
				e.preventDefault();
				var btn = form.querySelector('button[type="submit"]');
				var result = form.closest('.apollo-sign-pad').querySelector('.apollo-sign-result');
				btn.disabled = true;
				btn.innerHTML = '<i class="ri-loader-4-line" style="animation:spin .8s linear infinite"></i> Assinando...';

				var fd = new FormData(form);
				fd.append('action', 'apollo_sign_sign_document');
				fd.append('nonce', '<?php echo wp_create_nonce( 'apollo_sign_nonce' ); ?>');
				fd.append('doc_id', form.dataset.doc);

				// First create sig, then sign
				var fd2 = new FormData();
				fd2.append('action', 'apollo_sign_create_signature');
				fd2.append('nonce', '<?php echo wp_create_nonce( 'apollo_sign_nonce' ); ?>');
				fd2.append('doc_id', form.dataset.doc);

				fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
						method: 'POST',
						body: fd2,
						credentials: 'same-origin'
					})
					.then(function(r) {
						return r.json()
					})
					.then(function(data) {
						if (!data.success) {
							throw new Error(data.data?.message || 'Erro');
						}
						fd.append('signature_id', data.data.id);
						return fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
							method: 'POST',
							body: fd,
							credentials: 'same-origin'
						});
					})
					.then(function(r) {
						return r.json()
					})
					.then(function(data) {
						if (data.success) {
							result.style.display = 'block';
							result.style.background = 'rgba(34,197,94,.12)';
							result.style.color = '#22c55e';
							result.innerHTML = '<i class="ri-checkbox-circle-fill"></i> Documento assinado com sucesso!';
						} else {
							throw new Error(data.data?.message || 'Erro');
						}
					})
					.catch(function(err) {
						result.style.display = 'block';
						result.style.background = 'rgba(239,68,68,.12)';
						result.style.color = '#ef4444';
						result.innerHTML = '<i class="ri-error-warning-fill"></i> ' + err.message;
						btn.disabled = false;
						btn.innerHTML = '<i class="ri-shield-keyhole-fill"></i> Assinar';
					});
			});
		});
	})();
</script>
