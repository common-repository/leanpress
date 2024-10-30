<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin/partials
 */
?>
<div class="wrap">
	<!-- This file should primarily consist of HTML with a little bit of PHP. -->
	<h1> <?php _e( 'LeanPress', 'leanpress' ); ?></h1>
	<h2> <?php _e( 'Use Leanpub with WordPress', 'leanpress' ); ?></h2>

	<form method="POST" action="options.php">
		<div class="postbox">
			<div class="inside">
			<?php
				settings_fields( 'leanpress_settings' );

				do_settings_sections( 'leanpress_settings' );
				submit_button();
			?>
			</div>
		</div>
	</form>
</div>
