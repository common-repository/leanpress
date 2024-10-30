<?php

class LeanPress_Shortcode{
	
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

	public $shortcodes = array();

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string    $plugin_name       The name of the plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		$this->shortcodes['leanpress_all'] = array( $this, 'all' ); 

	}

	public function add_shortcodes(){

		foreach ($this->shortcodes as $key => $value) {
			
			add_shortcode( $key, $value );

		}

	}

	public function all( $atts ){
		
		$posts = get_posts( array( 
			'post_type' => 'leanpress_books' ,
			'post_status' => 'publish',
			'posts_per_page' => -1
			));

		$output = "<ul class='leanpress_list'>";

		foreach ($posts as $post ) {
			$output .= '<li>';
				$output .= "<a href='" . get_post_meta( $post->ID, 'leanpress_url', true ) . "' target='_blank'>";
			$output .= get_the_post_thumbnail( $post->ID, 'full' );
			$output .= '</a>';
			$output .= '<ul class="leanpress_list_meta">';
				$output .= '<li><strong>Minimum Price: </strong> $' . get_post_meta( $post->ID, 'leanpress_minimum_price', true )  . " </li>";
				$output .= '<li><strong>Suggested Price: </strong> $' . get_post_meta( $post->ID, 'leanpress_suggested_price', true ) . '</li>';
			$output .= '</ul>';
			$output .= '<a href="' . get_post_meta( $post->ID, 'leanpress_url', true ) . '"  class="leanpress_btn" target="_blank">' . __( 'Buy Now', 'leanpress' ) . '</a>';
			$output .= '</li>';
		}

		$output .= "</ul>";
		return $output;
	}


}