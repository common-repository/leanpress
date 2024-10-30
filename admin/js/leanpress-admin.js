(function( $ ) {
	'use strict';

	String.prototype.capitalize = function() {
	    return this.charAt(0).toUpperCase() + this.slice(1);
	}

	/**
	 * All of the code for your admin-facing JavaScript source
	 * should reside in this file.
	 *
	 * Note: It has been assumed you will write jQuery code here, so the
	 * $ function reference has been prepared for usage within the scope
	 * of this function.
	 *
	 * This enables you to define handlers, for when the DOM is ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * When the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and/or other possibilities.
	 *
	 * Ideally, it is not considered best practise to attach more than a
	 * single DOM-ready or window-load handler for a particular page.
	 * Although scripts in the WordPress core, Plugins and Themes may be
	 * practising this, we should strive to set a better example in our own work.
	 */
	
	$( function() {



		var book_slug_container = $( "#leanpress_book_slug_information" );
		var book_slug_add_button = $(".leanpress_add_book_slug");
		var book_slug_delete_button = $(".leanpress_delete_book_slug");
		var book_coupons = $("#leanpress_book_coupons");
		var book_coupons_edit = $(".leanpress_coupon_edit");

		if( book_coupons_edit.length > 0 ){
			$(".leanpress_datepicker").datepicker(
				{
					dateFormat: "yy-mm-dd"
				});
		}

		if( book_slug_container.length > 0 ) {

			var bookSlug = book_slug_container.attr( "data-bookslug" );
			if( "" !== bookSlug ) {
				leanpress_get_book_info( bookSlug );
			} 
		}

		if( book_coupons.length > 0 ) {

			var bookSlug = book_coupons.attr( "data-bookslug" );
			if( "" !== bookSlug ) {
				leanpress_get_coupons_for_book( bookSlug );
			} 
		}

		if( book_slug_add_button.length > 0 ){

			
			var book_new_container = $( ".leanpress_new_book_slug_container" );
			book_slug_add_button.on( "click", function( e ){
				e.preventDefault();
				var new_index = $( this ).attr( 'data-add' );
				var html =  '<p>';
				html += '<input name="leanpress_books[' + new_index + ']" class="input" value="" placeholder="Insert Book slug" />';
				html += '</p>';

				book_new_container.append( html );

			});
			

		}

		if( book_slug_delete_button.length > 0 ){

			leanpress_bind_delete_button();

		}

		//DELETE NAPRAVITI
		function leanpress_bind_delete_button() {
			book_slug_delete_button.unbind( "click", leanpress_delete_book );
			book_slug_delete_button.bind( "click", leanpress_delete_book );
		}

		function leanpress_delete_book( e ) {
			e.preventDefault();
			$( this ).parent( "p" ).remove();
		}

		function leanpress_get_book_info( book_slug ) {
		 	
		 	var data = {
		 		action: 'get_book_info',
		 		book_slug: book_slug
		 	}
		 	
		 	book_slug_container.html( "Getting info..." );
		 	jQuery.getJSON( leanpress_ajax.ajax_url, data, leanpress_show_book_info );

		 }

		 function leanpress_get_coupons_for_book( book_slug ) {
		 	var data = {
		 		action: 'get_book_coupons',
		 		book_slug: book_slug
		 	};

		 	book_coupons.html( "Getting coupons..." );
		 	jQuery.get( leanpress_ajax.ajax_url, data, leanpress_show_coupon_info );
		 }

		function leanpress_show_coupon_info( response ) {
  
		 	book_coupons.html( "" );
		 	console.log( response );
		 	if( response ){

		 		book_coupons.html( response );

		 	}
		 	

		 }

		 function leanpress_show_book_info( response ) {
  
		 	book_slug_container.html( "" );
		 	if( response ){

		 		var html =  '<ul class="leanpress_ul_nostyle">' ;
		 		for( var x in response ){
		 			
		 			html +=  leanpress_generate_formed_data( "li", x, response[ x ] );
		 			
		 		}
		 		
		 		html += "</ul>";

		 		

		 		book_slug_container.append( html );

		 	}
		 	

		 }

		 function leanpress_generate_formed_data( html, key, data ) {

		 	var output = "<"+html+">";

		 	switch( key ){
		 		case 'image':
		 		output += "<img src='" + data + "' />";
		 		break;
		 		default:
		 		output += "<strong>" + key.replace( "_", " " ).replace( "_", " " ).capitalize() + "</strong> " + data;
		 		break;
		 	}

		 	output += "</"+html+">";

		 	return output;

		 }

	 
	});

})( jQuery );

