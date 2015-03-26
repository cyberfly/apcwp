<?php
/*
Plugin Name: WCK - Enable Unserialized Fields
Plugin URI: http://www.cozmoslabs.com
Description: Custom plugin that will enable unserialized fields for WCK. When enabled, besides the serialized array that is normally saved, each individual field will be saved in it's own meta, regardless if it's a repeater or single metabox. The meta key will have this form: $meta_name_arg . '_' . $field_slug .'_'. $n  * where $n starts at 1 and represents the number of the entry in the repeater metabox. For a single metabox it will be 1.
Author: Cristian Antohe
Version: 1.0
Author URI: http://www.cozmsolabs.com
*/


/* add_filter( 'wck-datepicker-args', 'wck_custom_datepicker_js');
function wck_custom_datepicker_js( $content ){
	return 'dateFormat : "yy-mm-dd",changeMonth: true,changeYear: true';
}
 */

 add_action( 'init', 'wck_unserialized_metabox' );
function wck_unserialized_metabox(){
	if ( !class_exists( 'Wordpress_Creation_Kit' ) ){
		return;
	}
	
	$fint = array( 
		array( 'type' => 'radio', 'title' => 'Enable Unserialized Fields', 'description' => 'By default WordPress Creation Kit saves the custom fields in a serialized array. 
				This enables you store each custom field in it\'s own meta entry.
				When enabled, besides the serialized array that is normally saved, each individual field will be saved in it\'s own meta, regardless if it\'s a repeater or single metabox. 
				<br/><br/><strong>The meta key will have this form: $meta_name_arg . \'_\' . $field_slug .\'_\'. $n  * where $n starts at 1 and represents the number of the entry in the repeater metabox. 
				</strong><br/>For a single metabox it will be 1. Ex: <strong>teacheratributes_field-of-study_1</strong>
				', 'options' => array( 'Yes', 'No'), 'default' => 'No' ), 
	);

	$args = array(
		'metabox_id' => 'wck_unserialized_metabox',
		'metabox_title' => 'Enable Unserialized Fields',
		'post_type' => 'wck-meta-box',
		'meta_name' => 'wck_enable_unserialized',
		'single' 	=> true,
		'sortable' 	=> false,
		'meta_array' => $fint	
	);

	new Wordpress_Creation_Kit( $args );
}

add_action ('init', 'wck_enable_unserialized_fields');
function wck_enable_unserialized_fields(){
	if ( !class_exists( 'Wordpress_Creation_Kit' ) ){
		return;
	}

	$cfc_metaboxes = get_posts( 'post_type=wck-meta-box&posts_per_page=-1' );
	foreach ( $cfc_metaboxes as $metabox ){
		$enable_unserialized = get_cfc_field( 'wck_enable_unserialized', 'enable-unserialized-fields', $metabox->ID );
		$meta_name = get_cfc_field( 'wck_cfc_args', 'meta-name', $metabox->ID );
		if ( $enable_unserialized == 'Yes' ){
			add_filter( 'wck_cfc_unserialize_fields_' . $meta_name, create_function('', 'return true;') );
		}
	}
}