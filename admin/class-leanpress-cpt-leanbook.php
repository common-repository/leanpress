<?php

class LeanPress_CPT_LeanBook {

	public $post_type = "leanpress_books";

	public $post_name = "";

	public $post_id = 0;

	private $wpdb;

	public $meta = array();

	public function __construct( $post_name = "" ) {
		global $wpdb;
		$this->wpdb = $wpdb;
		$this->post_name = $post_name;
	}

	public function create_cpt() {

		$labels = array(
			'name'               => _x( 'LeanPub Books', 'post type general name', 'your-plugin-textdomain' ),
			'singular_name'      => _x( 'LeanPub Book', 'post type singular name', 'your-plugin-textdomain' ),
			'menu_name'          => _x( 'LeanPub Books', 'admin menu', 'your-plugin-textdomain' ),
			'name_admin_bar'     => _x( 'LeanPub Book', 'add new on admin bar', 'your-plugin-textdomain' ),
			'add_new'            => _x( 'Add New', 'book', 'your-plugin-textdomain' ),
			'add_new_item'       => __( 'Add New Book', 'your-plugin-textdomain' ),
			'new_item'           => __( 'New Book', 'your-plugin-textdomain' ),
			'edit_item'          => __( 'Edit Book', 'your-plugin-textdomain' ),
			'view_item'          => __( 'View Book', 'your-plugin-textdomain' ),
			'all_items'          => __( 'LeanPub Books', 'your-plugin-textdomain' ),
			'search_items'       => __( 'Search Books', 'your-plugin-textdomain' ),
			'parent_item_colon'  => __( 'Parent Books:', 'your-plugin-textdomain' ),
			'not_found'          => __( 'No books found.', 'your-plugin-textdomain' ),
			'not_found_in_trash' => __( 'No books found in Trash.', 'your-plugin-textdomain' )
		);

		$args = array(
			'labels'             => $labels,
			'description'        => __( 'LeanPub Books Information.', 'leanpress' ),
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => 'leanpress',
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'leanpub_books' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor', 'thumbnail', 'excerpt')
		);

		register_post_type( $this->post_type, $args );

	}

	public static function get_all_books(){


		$posts = get_posts( array( 
			'post_type' => 'leanpress_books' ,
			'post_status' => 'publish',
			'posts_per_page' => -1
		));

		return $posts;
	}

	public function get_book_info_by_slug( $slug ) {

		if( $slug ){
			$apiKey = "";
			$apiKey = get_option("leanpress_api_key");

			if( $apiKey != "" ){
				$apiKey = "?api_key=".$apiKey;
			}


			$args = array( 'body' => true );
			$url = 'https://leanpub.com/' . $slug . '.json'.$apiKey;
			$response = wp_remote_get( $url, $args );

			return $response["body"];
		}

		return false;

	}

	public function exists() {

		$posts_table = $this->wpdb->posts;

		$sql = $this->wpdb->get_var("SELECT ID FROM $posts_table WHERE post_name = '$this->post_name' AND post_type='$this->post_type'");
		
		if( $sql ){
			$this->post_id = (int) $sql;
			return true;
		} else {
			return false;
		}
	}


	public function create_from_leanpub(){

		$data_from_leanpub = $this->get_book_info_by_slug( $this->post_name );

		if( $data_from_leanpub ){
			$data_from_leanpub = json_decode( $data_from_leanpub );
			  
			$data_to_insert = array(
				'post_type' => $this->post_type,
				'post_name' => $this->post_name,
				'post_title' => $data_from_leanpub->title,
				'post_status' => 'publish',
				'post_excerpt' => $data_from_leanpub->meta_description,
				'post_content' => $data_from_leanpub->about_the_book,
				);
			$this->post_id = wp_insert_post( $data_to_insert, false ); 

			if( $this->post_id ){
				$this->meta = $data_from_leanpub;
				$this->create_post_thumbnail();
				$this->add_post_meta();
			}

			echo "<div class='notice notice-success'>" . __( 'Successfully Imported', 'leanpress' ) . "</div>";
		}


	}

	public function update_from_leanpub(){
		$data_from_leanpub = $this->get_book_info_by_slug( $this->post_name );
		 
		if( $data_from_leanpub ){
			$data_from_leanpub = json_decode( $data_from_leanpub );
			  
			$data_to_insert = array(
				'ID' => $this->post_id,
				'post_type' => $this->post_type,
				'post_name' => $this->post_name,
				'post_title' => $data_from_leanpub->title,
				'post_status' => 'publish',
				'post_excerpt' => $data_from_leanpub->meta_description,
				'post_content' => $data_from_leanpub->about_the_book,
				);
			$this->post_id = wp_update_post( $data_to_insert, false ); 

			if( $this->post_id ){
				$this->meta = $data_from_leanpub;
				$this->create_post_thumbnail();
				$this->add_post_meta();
			}

			echo "<div class='notice notice-success'>" . __( 'Successfully Imported', 'leanpress' ) . "</div>";
		}
	}

	public function create_post_thumbnail(){

		$url = $this->meta->image;

		$temporary = download_url( $url );


		$filepath = pathinfo( $temporary );

		$theFile = $filepath['dirname'] . "/" . $this->post_name . ".jpg";

		$fileArray = array();
		$fileArray['tmp_name'] =  $temporary;
		$fileArray['name'] = $this->post_name . ".jpg";
		$fileArray['type'] = 'image/jpeg';
		$fileArray['error'] = 0;
		$fileArray['size'] = filesize( $temporary );
		if( file_exists( $theFile ) ){
			unlink( $theFile );
		}
		$post_thumbnail_id = get_post_thumbnail_id( $this->post_id );

		if( $post_thumbnail_id ){
			wp_delete_attachment( $post_thumbnail_id, true );
		}
		// do the validation and storage stuff
		$att_id = media_handle_sideload( $fileArray, $this->post_id );             // $post_data can override the items saved to wp_posts table, like post_mime_type, guid, post_parent, post_title, post_content, post_status

		// If error storing permanently, unlink
		    if ( is_wp_error($att_id) ) {
		        @unlink($file_array['tmp_name']);   // clean up
		        echo $att_id->get_error_message();
		        return false; // output wp_error
		    }

		// set as post thumbnail if desired
		     
		set_post_thumbnail($this->post_id, $att_id);
		    

	}
	public function add_post_meta(){

		$do_not_save = array(
			'image', 
			'title_page_url',
			'about-the_book',
			'meta_description',
			'title'
		);



		foreach( $this->meta as $meta_key => $meta_value ){

			if( in_array( $meta_key, $do_not_save) ) continue;

			update_post_meta( $this->post_id, "leanpress_".$meta_key, $meta_value );

		}
		
	}

	public function add_meta_box() {
		add_meta_box(
		    'leanpress_book_info',      // Unique ID
		    esc_html__( 'Book Information', 'leanpress' ),    // Title
		    array( $this, 'the_meta_box' ),   // Callback function
		    $this->post_type,         // Admin page (or post type)
		    'normal',         // Context
		    'default'         // Priority
		  );
	}

	public function the_meta_box( $post, $box ) {

		$meta_keys = array(
			'subtitle' => 'Subtitle',
			'last_published_at' => 'Last Update',
			'total_copies_sold' => 'Total Copies Sold',
			'author_string' => 'Author',
			'url' => 'ULR',
			'minimum_price' => 'Minimum Price',
			'suggested_price' => 'Maximum Price',
			'meta_description' => 'Meta Description',
			'page_count' => 'Page Count',
			'page_count_published' => 'Page Count Published',
			'total_revenue' => "Total Revenue",
			'word_count' => 'Word Count',
			'word_count_published' => 'Word Count Published',
			'possible_reader_count' => 'Possible Reader Count',
			'pdf_preview_url' => 'PDF Preview URL',
			'epub_preview_url' => 'EPUB Preview URL',
			'mobi_preview_url' => 'MOBI Preview URL',
			'pdf_published_url' => 'PDF Published URL',
			'epub_published_url' => 'EPUB Published URL',
			'mobi_published_url' => 'MOBI Published URL'
		);

 

		?>
		<table class="table">
			<?php
			foreach ($meta_keys as $meta_key => $meta_title ) {

				$value = get_post_meta( $post->ID, 'leanpress_'.$meta_key, true );

				if( ! $value ) { continue; }
				echo '<tr>';

				echo '<th style="text-align:left;">' .$meta_title . '</th>';

				echo '<td>' . $value . '</td>';

				echo '</tr>';
			}
			?>
		</table>
		

		<?php

	}
	
}
 