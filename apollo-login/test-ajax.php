<?php
/**
 * Simulate AJAX login request.
 * Access: http://localhost:10004/wp-content/plugins/apollo-login/test-ajax.php
 */

// Load WordPress.
require_once dirname( dirname( dirname( __DIR__ ) ) ) . '/wp-load.php';

header( 'Content-Type: text/html; charset=utf-8' );

// Simulate AJAX POST data
$_POST['log']                = 'root';
$_POST['pwd']                = 'root';
$_POST['rememberme']         = '1';
$_POST['apollo_login_nonce'] = wp_create_nonce( 'apollo_login_action' );
$_POST['action']             = 'apollo_ajax_login';

?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Apollo Login - AJAX Simulation Test</title>
	<style>
		body { font-family: monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; }
		.box { background: #252526; padding: 20px; margin: 20px 0; border-radius: 8px; border-left: 4px solid #007acc; }
		.success { border-left-color: #4ec9b0; }
		.error { border-left-color: #f48771; }
		h1 { color: #4ec9b0; }
		code { background: #1e1e1e; padding: 2px 6px; border-radius: 3px; color: #ce9178; }
		pre { background: #1e1e1e; padding: 15px; border-radius: 4px; overflow-x: auto; }
	</style>
</head>
<body>
	<h1>🔬 Apollo Login - AJAX Simulation Test</h1>

	<div class="box">
		<h2>Step 1: POST Data Simulation</h2>
		<pre><?php print_r( $_POST ); ?></pre>
	</div>

	<?php
	echo '<div class="box">';
	echo '<h2>Step 2: Calling apollo_ajax_login_handler()</h2>';

	// Buffer the output
	ob_start();

	// Check if function exists
	if ( ! function_exists( 'apollo_ajax_login_handler' ) ) {
		echo '<div class="error">✗ Function apollo_ajax_login_handler() not found!</div>';
		echo '<p>Loading function file...</p>';
		require_once __DIR__ . '/includes/functions.php';
	}

	// Call the function and catch output
	try {
		apollo_ajax_login_handler();
		echo '<div class="error">✗ Function did not exit (should call wp_send_json_*)</div>';
	} catch ( Exception $e ) {
		echo '<div class="error">✗ Exception: ' . $e->getMessage() . '</div>';
	}

	$output = ob_get_clean();

	// Try to decode as JSON
	$json = json_decode( $output, true );

	if ( $json ) {
		echo '<div class="' . ( isset( $json['success'] ) && $json['success'] ? 'success' : 'error' ) . '">';
		echo '<h3>JSON Response:</h3>';
		echo '<pre>' . json_encode( $json, JSON_PRETTY_PRINT ) . '</pre>';
		echo '</div>';
	} else {
		echo '<div class="box">';
		echo '<h3>Raw Output:</h3>';
		echo '<pre>' . htmlspecialchars( $output ) . '</pre>';
		echo '</div>';
	}

	echo '</div>';
	?>

	<div class="box">
		<h2>Step 3: Direct AJAX URL Test</h2>
		<p>Click the button to test real AJAX call:</p>
		<button id="test-ajax" style="background: #007acc; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer;">
			🚀 Send AJAX Request
		</button>
		<div id="ajax-result" style="margin-top: 20px;"></div>
	</div>

	<script>
	document.getElementById('test-ajax').addEventListener('click', async function() {
		const resultDiv = document.getElementById('ajax-result');
		resultDiv.innerHTML = '<p style="color: #4ec9b0;">⏳ Sending request...</p>';

		const formData = new FormData();
		formData.append('action', 'apollo_ajax_login');
		formData.append('log', 'root');
		formData.append('pwd', 'root');
		formData.append('rememberme', '1');
		formData.append('apollo_login_nonce', '<?php echo wp_create_nonce( 'apollo_login_action' ); ?>');

		try {
			const response = await fetch('/wp-admin/admin-ajax.php', {
				method: 'POST',
				body: formData,
				credentials: 'same-origin'
			});

			const data = await response.json();

			resultDiv.innerHTML = '<div class="' + (data.success ? 'success' : 'error') + '">' +
				'<h3>AJAX Response:</h3>' +
				'<pre>' + JSON.stringify(data, null, 2) + '</pre>' +
				'</div>';

		} catch (error) {
			resultDiv.innerHTML = '<div class="error">' +
				'<h3>Error:</h3>' +
				'<p>' + error.message + '</p>' +
				'</div>';
		}
	});
	</script>

	<div class="box">
		<h2>Step 4: Check Debug Log</h2>
		<?php
		$log_file = dirname( dirname( dirname( __DIR__ ) ) ) . '/debug.log';
		if ( file_exists( $log_file ) ) {
			$lines        = file( $log_file );
			$apollo_lines = array_filter(
				$lines,
				function ( $line ) {
					return stripos( $line, 'APOLLO' ) !== false;
				}
			);
			$apollo_lines = array_slice( $apollo_lines, -10 );

			if ( ! empty( $apollo_lines ) ) {
				echo '<pre>' . htmlspecialchars( implode( '', $apollo_lines ) ) . '</pre>';
			} else {
				echo '<p style="color: #858585;">No APOLLO logs found</p>';
			}
		} else {
			echo '<p style="color: #858585;">Debug log not found</p>';
		}
		?>
	</div>
</body>
</html>
