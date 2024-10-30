<?php

class LeanPress_Coupons {
	

	public function __construct(  ) {
		
		
	}

	public function get_coupon_info_by_slug( $slug ) {

		if( $slug ){
			$apiKey = "";
			$apiKey = get_option("leanpress_api_key");

			if( $apiKey != "" ){
				$apiKey = "?api_key=".$apiKey;
			}


			$args = array( 'body' => true );
			$url = 'https://leanpub.com/' . $slug . '/coupons.json'.$apiKey;
			$response = wp_remote_get( $url, $args );
			if( is_wp_error( $response ) ){
				echo $response->message;
				wp_die();
			}
			return $response["body"];
		}

		return false;

	}

	public function edit_coupon( $coupon_id, $book_slug ){

		if( isset( $_POST['leanpress_submit'] ) ) {
			 
			$apiKey = get_option("leanpress_api_key");
			if( isset( $_POST['suspended'] ) ){
				$_POST['suspended'] = true;
			} else {
				$_POST['suspended'] = false;
			}
			if( $apiKey != '' ){
				$url = 'https://leanpub.com/' . $book_slug . '/coupons/' . $_POST['coupon_code'] . '.json?api_key=' . $apiKey;
				$args = array(
					'method' => 'PUT',
					'headers' => array( "Content-type" => "application/json" ),
					'body' => json_encode( $_POST )
				);
				$response = wp_remote_request( 
						$url,
						$args );
				$responseBody = json_decode( wp_remote_retrieve_body( $response ) );
				delete_transient( 'leanpress_coupons_' . $book_slug );
				if( $responseBody->success == 'true' ){
					$this->add_notice( __( "Coupon successfully updated.", 'leanpress' ), 'success' );
				}

			}
			
		}
		
		// Get any existing copy of our transient data
		if ( false === ( $responseObject = get_transient( 'leanpress_coupons_' . $book_slug ) ) ) {
		     	// It wasn't there, so regenerate the data and save the transient
		     	$response =  $this->get_list_coupons( $book_slug );
	            	$responseObject = json_decode( $response );
		     	set_transient( 'leanpress_coupons_' . $book_slug, $responseObject, 6 * HOUR_IN_SECONDS );
		}

		if( empty( $responseObject ) ) {
				$this->add_notice( __( "This is not your book or there are no coupons for this book.", 'leanpress' ), 'error' );
		} else {

			$the_coupon = $this->get_the_coupon( $coupon_id, $responseObject );
			$couponArray = json_decode( json_encode( $the_coupon), true);
			$columns = array(
	            		'id' => __( 'ID', 'leanpress' ),
	            		'start_date' => __( 'Start Date', 'leanpress' ),
	            		'end_date' => __( 'End Date', 'leanpress' ),
	            		'coupon_code' => __( 'Coupon Code', 'leanpress' ),
	            		'book_id' => __( 'Book ID', 'leanpress' ),
	            		'created_at' => __( 'Created At', 'leanpress' ),
	            		'updated_at' => __( 'Updated At', 'leanpress' ),
	            		'num_uses' => __( 'Number of uses', 'leanpress' ),
	            		'max_uses' => __( 'Max Uses', 'leanpress' ),
	            		'suspended' => __( 'Suspended', 'leanpress' ),
	            		'note' => __( 'Note', 'leanpress' ),
	            		'is_url' => __( 'URL', 'leanpress' ),
	            		'package_discounts' => __( 'Package Discounts', 'leanpress' ),
	            		 
	            	);


			?>
			
			<form class="leanpress_coupon_edit" method="POST" action="">
				<div class="postbox">
					<div class="inside">
						<fieldset>
							<legend><?php _e( 'Non Editable Information', 'leanpress' ); ?></legend>
							<table class="form-table">
								<tr>
									<td class="label"><strong> <?php echo $columns['id']; ?></strong></td>
									<td class="value"><?php echo $couponArray['id']; ?></td>
								</tr>
								<tr>
									<td class="label"><strong> <?php echo $columns['coupon_code']; ?></strong></td>
									<td class="value">
										<?php echo $couponArray['coupon_code']; ?>
										<input type="hidden" name="coupon_code" value="<?php echo $couponArray['coupon_code']; ?>" />
									</td>
								</tr>
								<tr>
									<td class="label"><strong> <?php echo $columns['book_id']; ?></strong></td>
									<td class="value"><?php echo $couponArray['book_id']; ?></td>
								</tr>
								<tr>
									<td class="label"><strong> <?php echo $columns['created_at']; ?></strong></td>
									<td class="value"><?php echo $couponArray['created_at']; ?></td>
								</tr>
								<tr>
									<td class="label"><strong> <?php echo $columns['num_uses']; ?></strong></td>
									<td class="value"><?php echo $couponArray['num_uses']; ?></td>
								</tr>
							</table>
						</fieldset>

						<fieldset>
							<legend><?php _e( 'Editable Information', 'leanpress' ); ?></legend>
							<table class="form-table">
								<tr>
									<td class="label"><strong> <?php echo $columns['start_date']; ?></strong></td>
									<td class="value"><input class="widefat leanpress_datepicker" name="start_date" type="text" value="<?php echo $couponArray['start_date']; ?>" /></td>
								</tr>
								<tr>
									<td class="label"><strong> <?php echo $columns['end_date']; ?></strong></td>
									<td class="value"><input class="widefat leanpress_datepicker" name="end_date" type="text" value="<?php echo $couponArray['end_date']; ?>" /></td>
								</tr>
								<tr>
									<td class="label"><strong> <?php echo $columns['max_uses']; ?></strong></td>
									<td class="value"><input type="number" class="widefat" name="max_uses" value="<?php echo $couponArray['max_uses']; ?>" /></td>
								</tr>
								<tr>
									<td class="label"><strong> <?php echo $columns['suspended']; ?></strong></td>
									<td class="value"><input type="checkbox" name="suspended" value="1" <?php checked( $couponArray['suspended'], "1", true ); ?> /></td>
								</tr>
								<tr>
									<td class="label"><strong> <?php echo $columns['is_url']; ?></strong></td>
									<td class="value"><input type="checkbox" name="is_url" value="1" <?php checked( $couponArray['is_url'], "1", true ); ?> /></td>
								</tr>
								<tr>
									<td class="label"><strong> <?php echo $columns['note']; ?></strong></td>
									<td class="value"><textarea name="note"><?php echo $couponArray['note']; ?></textarea></td>
								</tr>
							</table>
						</fieldset>
						<fieldset>
							<legend><?php _e( 'Package Discounts', 'leanpress' ); ?></legend>
							<table class="form-table">
								<?php 
								if( count( $couponArray['package_discounts']) > 0 ) {
									foreach ($couponArray['package_discounts'] as $package ) {
										?>
										<tr>
											<td class="label"><strong> <?php echo $package['package_slug']; ?></strong></td>
											<td class="value"><input class="widefat" type="number" min="0" name="package[<?php echo $package['package_slug']; ?>]" type="text" value="<?php echo $package['discounted_price']; ?>" /></td>
										</tr>
										<?php
									}
								}
								?>
							</table>
						</fieldset>
						<a href="http://www.leanpub.com/<?php echo $book_slug; ?>/c/<?php echo $couponArray['coupon_code']; ?>" class="button"><?php _e( 'Go to Coupon', 'leanpress' ); ?></a>
						<button type="submit" name="leanpress_submit" class="button button-primary"><?php _e( 'Edit Coupon', 'leanpress' ); ?></button>
					</div>
				</div>
			</form>
		<?php
		}

	}

	public function get_the_coupon( $coupon_id, $array ) {
		if( empty( $array ) ) {
			return 0;
		}
		foreach ($array as $object) {
			if( $object->id == $coupon_id ){
				return $object;
			}
		}
	}

	public function add_notice( $text, $class ){
		 ?>
		<div class="notice notice-<?php echo $class; ?>">
	        		<p><?php echo $text; ?></p>
	    	</div>
	    	<?php
	}

	public function list_all_books(){

		$leanpressProducts = new LeanPress_CPT_LeanBook();
		$books = $leanpressProducts::get_all_books();

		if( count( $books ) > 0 ) {

			echo '<table class="wp-list-table widefat fixed striped">';
				echo '<thead>';
					echo '<tr><th>' . __( 'Book Title', 'leanpress' ) . '</th></tr>';

				echo '</thead>';

				echo '<tbody>';

					foreach ($books as $book) {
						echo '<tr><td><a href="admin.php?page=leanpress_coupons&book_slug=' . $book->post_name  . '"">' . $book->post_title . '</a></td></tr>';
					}
					
				echo '</tbody>';
			echo '</table>';

		} else {

			echo '<p>' . __('Please import some of your books.', 'leanpress') . '</p>';

		}

	}

	public function list_coupons( $book_slug ) {

		if( ! $book_slug ) { return; }

		$coupons = $this->get_coupon_info_by_slug( $book_slug );

		print_r( $coupons );

	}

	public function get_list_coupons( $book_slug ) {

		if( ! $book_slug ) { return; }

		$coupons = $this->get_coupon_info_by_slug( $book_slug );

		return $coupons;

	}


}
 