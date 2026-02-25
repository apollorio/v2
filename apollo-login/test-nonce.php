<?php
/**
 * Test nonce in form submission
 */
require_once dirname( dirname( dirname( __DIR__ ) ) ) . '/wp-load.php';

header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Nonce Test</title>
	<style>
		body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; }
		.box { background: #252526; padding: 20px; margin: 20px 0; border-left: 4px solid #007acc; }
		code { background: #1e1e1e; padding: 2px 6px; color: #ce9178; }
		pre { background: #1e1e1e; padding: 15px; overflow-x: auto; }
		input, button { padding: 10px; margin: 5px 0; font-size: 14px; }
		button { background: #007acc; color: white; border: none; cursor: pointer; }
	</style>
</head>
<body>
	<h1>🔐 Nonce Test</h1>

	<div class="box">
		<h2>Current Nonce</h2>
		<p>Generated: <code><?php echo wp_create_nonce( 'apollo_login_action' ); ?></code></p>
		<p>Action: <code>apollo_login_action</code></p>
	</div>

	<div class="box">
		<h2>Test Form (Simulates /acesso)</h2>
		<form id="test-form">
			<?php wp_nonce_field( 'apollo_login_action', 'apollo_login_nonce' ); ?>
			<input type="text" name="log" value="root" placeholder="Username">
			<input type="password" name="pwd" value="root" placeholder="Password">
			<input type="hidden" name="rememberme" value="1">
			<button type="submit">Submit Form</button>
		</form>
		<div id="form-result"></div>
	</div>

	<div class="box">
		<h2>Test Manual AJAX</h2>
		<button id="test-ajax">Send AJAX with Nonce</button>
		<div id="ajax-result"></div>
	</div>

	<script>
	// Test 1: Form submission
	document.getElementById('test-form').addEventListener('submit', async function(e) {
		e.preventDefault();
		const resultDiv = document.getElementById('form-result');
		resultDiv.innerHTML = '<p style="color: #4ec9b0;">⏳ Sending...</p>';

		const formData = new FormData(this);
		formData.append('action', 'apollo_login_ajax');

		console.log('FormData from form:');
		for (let pair of formData.entries()) {
			console.log(pair[0] + ': ' + pair[1]);
		}

		try {
			const response = await fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
				method: 'POST',
				body: formData,
				credentials: 'same-origin'
			});

			const data = await response.json();
			console.log('Response:', data);

			resultDiv.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
		} catch (error) {
			resultDiv.innerHTML = '<p style="color: #f48771;">Error: ' + error.message + '</p>';
		}
	});

	// Test 2: Manual AJAX
	document.getElementById('test-ajax').addEventListener('click', async function() {
		const resultDiv = document.getElementById('ajax-result');
		resultDiv.innerHTML = '<p style="color: #4ec9b0;">⏳ Sending...</p>';

		const formData = new FormData();
		formData.append('action', 'apollo_login_ajax');
		formData.append('log', 'root');
		formData.append('pwd', 'root');
		formData.append('rememberme', '1');
		formData.append('apollo_login_nonce', '<?php echo wp_create_nonce( 'apollo_login_action' ); ?>');

		console.log('Manual FormData:');
		for (let pair of formData.entries()) {
			console.log(pair[0] + ': ' + pair[1]);
		}

		try {
			const response = await fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
				method: 'POST',
				body: formData,
				credentials: 'same-origin'
			});

			const data = await response.json();
			console.log('Response:', data);

			resultDiv.innerHTML = '<pre>' + JSON.stringify(data, null, 2) + '</pre>';
		} catch (error) {
			resultDiv.innerHTML = '<p style="color: #f48771;">Error: ' + error.message + '</p>';
		}
	});
	</script>
</body>
</html>
