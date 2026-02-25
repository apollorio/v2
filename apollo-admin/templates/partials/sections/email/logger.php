<?php
/**
 * Email Section — Logger
 *
 * Page ID: page-email-logger
 *
 * @package Apollo\Admin
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<div class="page" id="page-email-logger">
	<div class="panel">
		<div class="spreadsheet-toolbar">
			<input type="text" class="spreadsheet-search" placeholder="<?php esc_attr_e( 'Search logs...', 'apollo-admin' ); ?>">
			<select class="select" style="height:32px;font-size:11px;width:120px">
				<option><?php esc_html_e( 'All', 'apollo-admin' ); ?></option>
				<option><?php esc_html_e( 'Sent', 'apollo-admin' ); ?></option>
				<option><?php esc_html_e( 'Failed', 'apollo-admin' ); ?></option>
				<option><?php esc_html_e( 'Queued', 'apollo-admin' ); ?></option>
			</select>
			<span class="spreadsheet-count"><?php esc_html_e( 'Last 500 entries', 'apollo-admin' ); ?></span>
		</div>
		<div style="overflow-x:auto">
			<table class="data-table">
				<thead>
					<tr>
						<th><?php esc_html_e( 'Timestamp', 'apollo-admin' ); ?></th>
						<th><?php esc_html_e( 'To', 'apollo-admin' ); ?></th>
						<th><?php esc_html_e( 'Subject', 'apollo-admin' ); ?></th>
						<th><?php esc_html_e( 'Template', 'apollo-admin' ); ?></th>
						<th><?php esc_html_e( 'Status', 'apollo-admin' ); ?></th>
					</tr>
				</thead>
				<tbody id="apollo-email-log-body">
					<tr>
						<td colspan="5" style="text-align:center;padding:40px;color:var(--c-muted)">
							<i class="ri-loader-4-line ri-spin" style="font-size:24px"></i><br>
							<?php esc_html_e( 'Loading logs...', 'apollo-admin' ); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div>
	</div>
</div>
