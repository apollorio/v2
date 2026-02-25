<?php
/**
 * Debug log viewer.
 * Access: http://localhost:10004/wp-content/plugins/apollo-login/debug-log.php
 */

// Load WordPress.
require_once dirname( dirname( dirname( __DIR__ ) ) ) . '/wp-load.php';

$log_file = dirname( dirname( dirname( __DIR__ ) ) ) . '/debug.log';

header( 'Content-Type: text/html; charset=utf-8' );
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<title>Apollo Login - Debug Log</title>
	<style>
		body { font-family: 'Consolas', 'Monaco', monospace; padding: 20px; background: #1e1e1e; color: #d4d4d4; }
		h1 { color: #4ec9b0; margin: 0 0 20px 0; }
		.log-container { background: #252526; padding: 20px; border-radius: 8px; max-height: 600px; overflow-y: auto; }
		.log-line { margin: 4px 0; padding: 4px 8px; border-left: 3px solid transparent; }
		.log-line.apollo { background: #1e3a5f; border-left-color: #4ec9b0; }
		.log-line.error { background: #5a1e1e; border-left-color: #f48771; }
		.timestamp { color: #858585; }
		.keyword { color: #4ec9b0; font-weight: bold; }
		.info { background: #264f78; padding: 12px; border-left: 4px solid #4ec9b0; margin: 20px 0; }
		button { background: #0e639c; color: white; border: none; padding: 10px 20px; border-radius: 4px; cursor: pointer; margin: 10px 0; }
		button:hover { background: #1177bb; }
	</style>
	<script>
		function refreshLog() {
			location.reload();
		}
		setInterval(refreshLog, 3000); // Auto-refresh every 3 seconds
	</script>
</head>
<body>
	<h1>🔍 Apollo Login - Debug Log (Auto-refresh: 3s)</h1>

	<button onclick="refreshLog()">🔄 Refresh Now</button>

	<?php if ( ! file_exists( $log_file ) ) : ?>
		<div class="info">
			<strong>Log file not found:</strong> <?php echo esc_html( $log_file ); ?><br>
			<small>Make sure WP_DEBUG_LOG is enabled in wp-config.php</small>
		</div>
	<?php else : ?>
		<div class="info">
			<strong>Log file:</strong> <?php echo esc_html( $log_file ); ?><br>
			<strong>Size:</strong> <?php echo esc_html( size_format( filesize( $log_file ) ) ); ?><br>
			<strong>Last modified:</strong> <?php echo esc_html( date( 'Y-m-d H:i:s', filemtime( $log_file ) ) ); ?>
		</div>

		<div class="log-container">
			<?php
			$lines = file( $log_file );
			$lines = array_slice( $lines, -100 ); // Last 100 lines
			$lines = array_reverse( $lines ); // Most recent first

			foreach ( $lines as $line ) {
				$line = htmlspecialchars( $line );

				// Highlight Apollo logs
				$class = '';
				if ( stripos( $line, 'APOLLO' ) !== false ) {
					$class = 'apollo';
					$line  = preg_replace( '/(APOLLO[^:]*:)/', '<span class="keyword">$1</span>', $line );
				} elseif ( stripos( $line, 'error' ) !== false || stripos( $line, 'warning' ) !== false ) {
					$class = 'error';
				}

				// Highlight timestamps
				$line = preg_replace( '/\[(.*?)\]/', '[<span class="timestamp">$1</span>]', $line );

				echo '<div class="log-line ' . $class . '">' . $line . '</div>';
			}
			?>
		</div>
	<?php endif; ?>
</body>
</html>
