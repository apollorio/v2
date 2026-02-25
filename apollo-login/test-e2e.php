<?php
/**
 * Complete end-to-end login flow test.
 * Simulates exact button click -> AJAX -> Response
 */

// Load WordPress.
require_once dirname( dirname( dirname( __DIR__ ) ) ) . '/wp-load.php';

header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>E2E Login Test</title>
	<style>
		body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; }
		.step { background: #252526; padding: 20px; margin: 20px 0; border-left: 4px solid #007acc; }
		.success { border-left-color: #4ec9b0; background: #1e3a2f; }
		.error { border-left-color: #f48771; background: #3a1e1e; }
		code { background: #1e1e1e; padding: 2px 6px; color: #ce9178; }
		pre { background: #1e1e1e; padding: 15px; overflow-x: auto; }
		button { background: #007acc; color: white; border: none; padding: 12px 24px; cursor: pointer; font-size: 16px; }
		button:hover { background: #005a9e; }
	</style>
</head>
<body>
	<h1>🔬 E2E Login Flow Test</h1>

	<div class="step">
		<h2>Test Configuration</h2>
		<p>Username: <code>root</code></p>
		<p>Password: <code>root</code></p>
		<p>AJAX URL: <code><?php echo admin_url( 'admin-ajax.php' ); ?></code></p>
		<p>Nonce: <code><?php echo wp_create_nonce( 'apollo_login_action' ); ?></code></p>
	</div>

	<div class="step">
		<h2>Step 1: Check AJAX Hooks</h2>
		<?php
		global $wp_filter;
		$hooks_found = array();

		if ( isset( $wp_filter['wp_ajax_nopriv_apollo_login_ajax'] ) ) {
			$hooks_found[] = 'wp_ajax_nopriv_apollo_login_ajax';
		}
		if ( isset( $wp_filter['wp_ajax_apollo_login_ajax'] ) ) {
			$hooks_found[] = 'wp_ajax_apollo_login_ajax';
		}

		if ( ! empty( $hooks_found ) ) {
			echo '<div class="success">✓ Hooks registered:</div>';
			echo '<pre>' . print_r( $hooks_found, true ) . '</pre>';

			// Show callbacks
			foreach ( $hooks_found as $hook ) {
				echo '<p><strong>' . $hook . ' callbacks:</strong></p>';
				echo '<pre>';
				foreach ( $wp_filter[ $hook ]->callbacks as $priority => $callbacks ) {
					foreach ( $callbacks as $callback ) {
						print_r( $callback['function'] );
						echo "\n";
					}
				}
				echo '</pre>';
			}
		} else {
			echo '<div class="error">✗ No hooks found!</div>';
		}
		?>
	</div>

	<div class="step">
		<h2>Step 2: Simulate Button Click</h2>
		<button id="test-login">🚀 Click to Test Login (root/root)</button>
		<div id="result" style="margin-top: 20px;"></div>
	</div>

	<div class="step">
		<h2>Step 3: Server-Side Direct Call</h2>
		<?php
		// Simulate POST data exactly as form would send
		$_POST = array(
			'action'             => 'apollo_login_ajax',
			'log'                => 'root',
			'pwd'                => 'root',
			'rememberme'         => '1',
			'apollo_login_nonce' => wp_create_nonce( 'apollo_login_action' ),
			'redirect_to'        => home_url(),
		);

		echo '<p><strong>POST Data:</strong></p>';
		echo '<pre>' . print_r( $_POST, true ) . '</pre>';

		// Check if function exists
		if ( function_exists( 'apollo_ajax_login_handler' ) ) {
			echo '<div class="success">✓ Function exists (no namespace)</div>';
		} elseif ( function_exists( 'Apollo_Login\\apollo_ajax_login_handler' ) ) {
			echo '<div class="success">✓ Function exists in Apollo_Login namespace</div>';
		} else {
			echo '<div class="error">✗ Function not found</div>';
			echo '<p>Trying to load...</p>';
			require_once __DIR__ . '/includes/functions.php';

			if ( function_exists( 'Apollo_Login\\apollo_ajax_login_handler' ) ) {
				echo '<div class="success">✓ Loaded! Function found in Apollo_Login namespace</div>';
			}
		}

		// Try to call the function
		echo '<p><strong>Calling function...</strong></p>';
		ob_start();
		try {
			if ( function_exists( 'Apollo_Login\\apollo_ajax_login_handler' ) ) {
				\Apollo_Login\apollo_ajax_login_handler();
			} elseif ( function_exists( 'apollo_ajax_login_handler' ) ) {
				apollo_ajax_login_handler();
			}
		} catch ( Exception $e ) {
			echo '<div class="error">Exception: ' . $e->getMessage() . '</div>';
		}
		$output = ob_get_clean();

		echo '<p><strong>Output:</strong></p>';
		echo '<pre>' . htmlspecialchars( $output ) . '</pre>';

		// Try to decode JSON
		$json = json_decode( $output, true );
		if ( $json ) {
			echo '<div class="' . ( isset( $json['success'] ) && $json['success'] ? 'success' : 'error' ) . '">';
			echo '<p><strong>JSON Response:</strong></p>';
			echo '<pre>' . json_encode( $json, JSON_PRETTY_PRINT ) . '</pre>';
			echo '</div>';
		}
		?>
	</div>

	<script>
	document.getElementById('test-login').addEventListener('click', async function() {
		const result = document.getElementById('result');
		result.innerHTML = '<p style="color: #4ec9b0;">⏳ Sending AJAX request...</p>';

		const formData = new FormData();
		formData.append('action', 'apollo_login_ajax');
		formData.append('log', 'root');
		formData.append('pwd', 'root');
		formData.append('rememberme', '1');
		formData.append('apollo_login_nonce', '<?php echo wp_create_nonce( 'apollo_login_action' ); ?>');
		formData.append('redirect_to', '<?php echo home_url(); ?>');

		console.log('FormData entries:');
		for (let pair of formData.entries()) {
			console.log(pair[0] + ': ' + pair[1]);
		}

		try {
			const response = await fetch('<?php echo admin_url( 'admin-ajax.php' ); ?>', {
				method: 'POST',
				body: formData,
				credentials: 'same-origin'
			});

			const text = await response.text();
			console.log('Raw response:', text);

			try {
				const data = JSON.parse(text);
				console.log('Parsed JSON:', data);

				result.innerHTML = '<div class="' + (data.success ? 'success' : 'error') + '">' +
					'<h3>AJAX Response:</h3>' +
					'<pre>' + JSON.stringify(data, null, 2) + '</pre>' +
					'</div>';
			} catch (e) {
				result.innerHTML = '<div class="error">' +
					'<h3>Not valid JSON:</h3>' +
					'<pre>' + text + '</pre>' +
					'</div>';
			}

		} catch (error) {
			result.innerHTML = '<div class="error">' +
				'<h3>Network Error:</h3>' +
				'<p>' + error.message + '</p>' +
				'</div>';
		}
	});
	</script>
</body>
</html>
