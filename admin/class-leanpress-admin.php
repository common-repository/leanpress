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
class LeanPress_Admin {

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
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

	}

	/**
	 * Register the stylesheets for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {
		global $wp_scripts;
		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */
		wp_enqueue_style('plugin_name-admin-ui-css',
                'http://ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/themes/ui-lightness/jquery-ui.min.css',
                false,
                1,
                false);
		wp_enqueue_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/leanpress-admin.css', array(), $this->version, 'all' );

	}

	/**
	 * Register the JavaScript for the admin area.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in Plugin_Name_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The Plugin_Name_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/leanpress-admin.js', array( 'jquery', 'jquery-ui-core', 'jquery-ui-datepicker' ), $this->version, false );
		wp_localize_script( $this->plugin_name,  'leanpress_ajax',
            		array( 'ajax_url' => admin_url( 'admin-ajax.php' )) );


	}


	/**
	 * Add menu page
	 *
	 * @since   1.0.0
	 */
	public function add_menu_page() {

		add_menu_page( 'LeanPress', 'LeanPress', 'manage_options', 'leanpress', array( $this, 'menu_page' ), 'dashicons-book', 50 );

		add_submenu_page( 'leanpress', 'LeanPress Settings', 'Settings', 'manage_options', 'leanpress_settings', array( $this, 'menu_page' ) );

		add_submenu_page( 'leanpress', 'LeanPress Test', 'Test', 'manage_options', 'leanpress_test', array( $this, 'submenu_page_test' ) );

		add_submenu_page( 'leanpress', 'LeanPress Books Import', 'Book Import', 'manage_options', 'leanpress_books', array( $this, 'submenu_page_books' ) );

		add_submenu_page( 'leanpress', 'LeanPress Coupons', 'Coupons', 'manage_options', 'leanpress_coupons', array( $this, 'submenu_page_coupons' ) );

	}

	/**
	 * Menu Page
	 *
	 * @return void
	 */
	public function menu_page() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/leanpress-admin-display.php';

	}

	/**
	 * Submenu Page Test
	 *
	 * @return void
	 */
	public function submenu_page_test() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/leanpress-admin-test.php';

	}

	/**
	 * Submenu Page Books
	 *
	 * @return void
	 */
	public function submenu_page_books() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/leanpress-admin-books.php';

	}


	/**
	 * Submenu Page Books
	 *
	 * @return void
	 */
	public function submenu_page_coupons() {

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/partials/leanpress-admin-coupons.php';

	}

	/**
	 * Defining the main settings
	 *
	 * @return void
	 */
	public function main_settings() {

		add_settings_section(
			'leanpress_main_section',
			__( 'General settings', 'leanpress' ),
			array( $this, 'leanpress_main_section_callback' ),
			'leanpress_settings'
		);

		add_settings_field(
			'leanpress_api_key',
			__( 'API key', 'leanpress' ),
			array( $this, 'leanpress_api_key_callback' ),
			'leanpress_settings',
			'leanpress_main_section'
		);

		add_settings_section(
			'leanpress_test_section',
			__( 'Test', 'leanpress' ),
			array( $this, 'leanpress_test_section_callback' ),
			'leanpress_test'
		);

		add_settings_field(
			'leanpress_book_slug',
			__( 'Book slug to test', 'leanpress' ),
			array( $this, 'leanpress_book_slug_callback' ),
			'leanpress_test',
			'leanpress_test_section'
		);

		add_settings_section(
			'leanpress_book_section',
			__( 'Books', 'leanpress' ),
			array( $this, 'leanpress_book_section_callback' ),
			'leanpress_books'
		);

		add_settings_field(
			'leanpress_books',
			__( 'Book slugs', 'leanpress' ),
			array( $this, 'leanpress_books_callback' ),
			'leanpress_books',
			'leanpress_book_section'
		);

		add_settings_field(
			'leanpress_books_import',
			__( 'Import Books', 'leanpress' ),
			array( $this, 'leanpress_books_import_callback' ),
			'leanpress_books',
			'leanpress_book_section'
		);
		register_setting( 'leanpress_test', 'leanpress_book_slug' );
		register_setting( 'leanpress_settings', 'leanpress_api_key' );
		register_setting( 'leanpress_books', 'leanpress_books' );
		register_setting( 'leanpress_books', 'leanpress_books_import' );


	}

	public function check_for_book_import( ) {

		if( isset( $_POST["leanpress_books_import"]) ){
			update_option( "blog_name", "LeanPress" );
		} else {
			update_option( "blog_name", "WordPress" );
		}
		 
 
	}


	/**
	 * Main Section callback function
	 *
	 * @return void
	 */
	public function leanpress_main_section_callback() {

		echo "<p>" . __( 'General LeanPub information', 'leanpress' ) . "</p>";

	}

	/**
	 * API Key html
	 *
	 * @return void
	 */
	public function leanpress_api_key_callback() {
		$leanpub_api = get_option( 'leanpress_api_key' );
		echo '<input name="leanpress_api_key" class="widefat" value="' . $leanpub_api . '" placeholder="Insert your LeanPub API key" />';
		echo '<p class="description">' . sprintf( __( 'More information about LeanPub API key can be found <a href="%s" target="_blank" >here</a>.', 'leanpress' ), 'https://leanpub.com/help/api' ). '</p>';

	}

	/**
	 * Test Section callback function
	 *
	 * @return void
	 */
	public function leanpress_test_section_callback() {

		echo "<p>" . __( 'Test LeanPub information', 'leanpress' ) . "</p>";

	}

	/**
	 * Book slug html
	 *
	 * @return void
	 */
	public function leanpress_book_slug_callback() {

		$book_slug = get_option( 'leanpress_book_slug' );
		echo '<input name="leanpress_book_slug" class="widefat" value="' . $book_slug . '" placeholder="Insert Book slug" />';
		echo '<div id="leanpress_book_slug_information" data-bookslug="' . $book_slug . '"></div>';
	

	}

	/**
	 * Book Section callback function
	 *
	 * @return void
	 */
	public function leanpress_book_section_callback() {

		echo "<p>" . __( 'Add new books slugs or delete previously added', 'leanpress' ) . "</p>";
		echo "<strong>" . __('How to import books', 'leanpress' ) . "</strong>";
		echo "<ol>";
			echo "<li>" . __( 'Add the book slug of your desired book', 'leanpress' ) . "</li>";
			echo "<li>" . __(' If you want to import more books, add other book slugs by clicking on the button Add new Slug', 'leanpress' ) . "</li>";
			echo "<li>" . __( 'When your are finished adding book slugs, saved them just to be sure', 'leanpress' ) . "</li>";
			echo "<li>" . __( 'If you are satisfied with your import list, check the box to Import Books and press Save Changes', 'leanpress' ) . "</li>";
		echo "</ol>";

	}

	/**
	 * Book slug html
	 *
	 * @return void
	 */
	public function leanpress_books_callback() {

		$book_slugs = get_option( 'leanpress_books' );

		if( is_array( $book_slugs ) && count( $book_slugs ) > 0 ){

			for( $i = 0; $i < count( $book_slugs ); $i++ ){
				echo '<p>';
				echo '<input name="leanpress_books[' . $i. ']" class="input" value="' . $book_slugs[ $i ] . '" placeholder="Insert Book slug" />';
				echo '<button class="button leanpress_delete_book_slug" data-delete="' . $i . '">' . __( 'Delete', 'leanpress' ) . '</button>'; 
				echo '</p>';
			}
			echo '<div class="leanpress_new_book_slug_container"></div>';
			echo '<p><button class="button button-primary leanpress_add_book_slug" data-add="' . count( $book_slugs ) . '">' . __( 'Add new slug', 'leanpress' ) . '</button></p>';

		} else {
			echo '<input name="leanpress_books[0]" class="widefat" value="" placeholder="Insert Book slug" />';
			echo '<div class="leanpress_new_book_slug_container"></div>';
			echo '<p><button class="button button-primary leanpress_add_book_slug" data-add="1">' . __( 'Add new slug', 'leanpress' ) . '</button></p>';
		}


		
	
	

	}



	/**
	 * Book slug Import checkbox html
	 *
	 * @return void
	 */
	public function leanpress_books_import_callback() {

		$book_slugs_import = get_option( 'leanpress_books_import' );

		
		echo '<input name="leanpress_books_import" class="checkbox" value="1" ' . checked( '1', $book_slugs_import, false ) . ' type="checkbox"/>';
		_e( 'If checked, all book slugs will be imported. The ones that are already added will be updated', 'leanpress');

		if( $book_slugs_import == "1" ){
			
			$this->import_book_slugs();

			update_option( 'leanpress_books_import' , '0');
			 

			
		}
			 

	}

	public function import_book_slugs() {

		$book_slugs_to_import = get_option( 'leanpress_books' );

		foreach ( $book_slugs_to_import as $slug ) {
			
			$leanpress_book = new LeanPress_CPT_LeanBook( $slug );
			if( $leanpress_book->exists() ) {

				$leanpress_book->update_from_leanpub();

			} else {

				$leanpress_book->create_from_leanpub();
				
			}
			 
		}

	   

	}

}
