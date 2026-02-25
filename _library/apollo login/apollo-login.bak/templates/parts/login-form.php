<div class="apollo-login-form-wrapper">
	<form id="apollo-login-form" method="post" action="<?php echo esc_url( rest_url( APOLLO_LOGIN_REST_NAMESPACE . '/auth/login' ) ); ?>">

		<div class="apollo-form-field">
			<label for="username"><?php esc_html_e( 'Username or Email', 'apollo-login' ); ?></label>
			<input
				type="text"
				id="username"
				name="username"
				required
				autocomplete="username"
				placeholder="<?php esc_attr_e( 'Enter your username or email', 'apollo-login' ); ?>"
			/>
		</div>

		<div class="apollo-form-field">
			<label for="password"><?php esc_html_e( 'Password', 'apollo-login' ); ?></label>
			<input
				type="password"
				id="password"
				name="password"
				required
				autocomplete="current-password"
				placeholder="<?php esc_attr_e( 'Enter your password', 'apollo-login' ); ?>"
			/>
		</div>

		<div class="apollo-form-field apollo-checkbox">
			<label>
				<input type="checkbox" name="remember" value="1" />
				<?php esc_html_e( 'Remember me', 'apollo-login' ); ?>
			</label>
		</div>

		<div class="apollo-form-actions">
			<button type="submit" class="apollo-btn apollo-btn-primary">
				<?php esc_html_e( 'Login', 'apollo-login' ); ?>
			</button>
		</div>

		<div class="apollo-form-message" style="display:none;"></div>

	</form>
</div>

<script>
jQuery(document).ready(function($) {
	$('#apollo-login-form').on('submit', function(e) {
		e.preventDefault();

		const $form = $(this);
		const $message = $form.find('.apollo-form-message');
		const $button = $form.find('button[type="submit"]');

		$button.prop('disabled', true).text('<?php esc_html_e( 'Logging in...', 'apollo-login' ); ?>');
		$message.hide();

		$.ajax({
			url: $form.attr('action'),
			method: 'POST',
			data: {
				username: $('#username').val(),
				password: $('#password').val()
			},
			success: function(response) {
				if (response.success) {
					$message.removeClass('error').addClass('success').text(response.message).show();
					window.location.href = '<?php echo esc_url( home_url( '/mural/' ) ); ?>';
				}
			},
			error: function(xhr) {
				const error = xhr.responseJSON?.message || '<?php esc_html_e( 'Login failed. Please try again.', 'apollo-login' ); ?>';
				$message.removeClass('success').addClass('error').text(error).show();
				$button.prop('disabled', false).text('<?php esc_html_e( 'Login', 'apollo-login' ); ?>');
			}
		});
	});
});
</script>
