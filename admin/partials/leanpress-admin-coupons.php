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
	<h1> <?php _e( 'LeanPress Coupons', 'leanpress' ); ?></h1>
	<?php 
		$leanpressCoupons = new LeanPress_Coupons();

		if( ! isset( $_GET['book_slug'] ) ) {
			$leanpressCoupons->list_all_books();
		} else {
			if( isset( $_GET['edit'] ) ){
				$leanpressCoupons->edit_coupon( $_GET['edit'], $_GET['book_slug'] );
			} else {
			?>
				<div id="leanpress_book_coupons" data-bookslug="<?php echo $_GET['book_slug'];?>"></div>
			<?php
			}
			

		}
	?>
</div>
