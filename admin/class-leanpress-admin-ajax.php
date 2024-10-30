<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       http://example.com
 * @since      1.0.0
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Plugin_Name
 * @subpackage Plugin_Name/admin
 * @author     Your Name <email@example.com>
 */
class LeanPress_Admin_Ajax {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;



	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param string  $plugin_name The name of this plugin.
	 * @param string  $version     The version of this plugin.
	 */
	public function __construct() {


		add_action( 'wp_ajax_get_book_info', array( $this, 'get_book_info' ) );
		add_action( 'wp_ajax_get_book_coupons', array( $this, 'get_book_coupons' ) );

	}

	public function add_notice( $text, $class ){
		 ?>
		<div class="notice notice-<?php echo $class; ?>">
	        		<p><?php echo $text; ?></p>
	    	</div>
	    	<?php
	}

	/**
	 * Get book info from received data
	 * @return json 
	 */
	public function get_book_info() {
		$book_slug = "";
		$apiKey = "";



		if( isset( $_GET["book_slug"]) && $_GET["book_slug"] != "" ){

			$apiKey = get_option("leanpress_api_key");

			$book_slug = $_GET["book_slug"];

			if( $apiKey != "" ){
				$apiKey = "?api_key=".$apiKey;
			}

			$args = array( 'body' => true );
			$url = 'https://leanpub.com/' . $book_slug . '.json'.$apiKey;
			$response = wp_remote_get( $url, $args );

			echo $response["body"];
			die();

		}

		echo array( "error" => "Book Slug is empty" );
		die();
		

	}

	public function get_book_coupons() {

		$book_slug = "";

		if( isset( $_GET["book_slug"] ) && $_GET["book_slug"] != "" ) {

			$book_slug = $_GET["book_slug"];

			$coupons = new LeanPress_Coupons();

			// Get any existing copy of our transient data
			if ( false === ( $responseObject = get_transient( 'leanpress_coupons_' . $book_slug ) ) ) {
			     	// It wasn't there, so regenerate the data and save the transient
			     	$response =  $coupons->get_list_coupons( $book_slug );
		            	$responseObject = json_decode( $response );
		            	if( empty( $responseObject ) ) {
					$this->add_notice( __( "This is not your book or there are no coupons for this book.", 'leanpress' ), 'error' );
					die();
				}
			     	set_transient( 'leanpress_coupons_' . $book_slug, $responseObject, 6 * HOUR_IN_SECONDS );
			}
		            
			
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
		            		'valid' => __( 'Stil Valid', 'leanpress' ),
		            		'edit' => __( 'Edit', 'leanpress' )
		            	);
		            $exclude_data = array( 'created_at', 'updated_at','book_id', 'is_url', 'id');
		            echo '<table class="wp-list-table widefat fixed striped">';
				echo '<thead>';
					echo '<tr>';

						foreach ( $columns as $key => $value) {
							if( in_array( $key, $exclude_data ) ) {
								continue;
							}
							echo '<th>' . $value . '</th>';
						}

					echo '</tr>';

				echo '</thead>';

				echo '<tbody>';
					$today = time();

					foreach ( $responseObject as $couponObject ) {

						$date = new DateTime( $couponObject->end_date );
						$couponTimestamp = $date->getTimestamp();

						echo '<tr>';
						foreach ( $columns as $key => $value) {
							if( in_array( $key, $exclude_data ) ) {
								continue;
							}

							$theValue = "";

							if( $key != 'valid' && $key != 'edit'){
								$theValue = $couponObject->$key;	
							}
							
							if( $key == 'edit' ){
								$theValue = "<a class='button' href='admin.php?page=leanpress_coupons&book_slug=" .$book_slug . "&edit=" . $couponObject->id . "'>" . $value . "</a>";
							}
							

							if( is_bool( $theValue ) ){
								
								if( $theValue ) {
									$theValue = __( "Yes", 'leanpress');
								} else {
									$theValue = __("No", 'leanpress' );
								}
							}

							if( $key == 'max_uses' && is_null( $theValue ) ){
								$theValue = __( "No Limit", 'leanpress' );
							}
							if( $key == 'package_discounts' ){
								$packagesArray = $theValue;
								if( is_array( $packagesArray ) ){
									$count = count( $packagesArray );

									if( $count > 1 ){
										$theValue = "<ul>";
										for( $i = 0; $i < $count; $i++){
											$theValue .=  "<li>" . $packagesArray[ $i ]->package_slug . ": " . $packagesArray[ $i ]->discounted_price . "</li>";
										}
										$theValue .= "</ul>";
									} else {
										$theValue =  $packagesArray[ 0 ]->package_slug . ": " . $packagesArray[ 0 ]->discounted_price;
									}
									
								} else {
									$theValue = "";
								}

							}

							if( $key == 'valid' ){
								if( $today > $couponTimestamp ){
									$theValue = __( 'No', 'leanpress' );
								} else {
									$theValue = __( 'Yes', 'leanpress' );
								}


							}
							echo '<td>' . $theValue . '</td>';
						}
						echo '</tr>';
					}
					
				echo '</tbody>';
			echo '</table>';
		            die();
		}
		echo array( "error" => "Book Slug is empty" );
		die();

	}

	  

}
