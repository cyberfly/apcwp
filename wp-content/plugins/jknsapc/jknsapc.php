<?php

/*
Plugin Name: JKNS APC
Plugin URI: http://integrasolid.com/
Description: Declares a plugin that will create a custom post type displaying Award Recipients and Award Queues.
Version: 1.0
Author: Muhammad Fathur Rahman
Author URI: http://integrasolid.com/
License: GPLv2
*/

// init the plugin and create the custom post type award_recipients and award_entries

add_action( 'init', 'create_award_post_type' );

// change the custom post type award_recipients help text "Enter title here"

add_filter('gettext','custom_enter_title');

// create custom meta box for custom post type award_recipients

add_action( 'admin_init', 'award_recipients_admin' );

// save custom post type action

add_action( 'save_post', 'add_award_recipient_fields', 10, 2 );

// update award recipients table list column

add_filter( 'manage_edit-award_recipients_columns', 'my_columns' );

// populate table list columns

add_action( 'manage_posts_custom_column', 'populate_columns' );

// ajax request newest APC entry info

add_action( 'themify_layout_after', 'my_action_javascript' );

// action callback to get APC entry

add_action('wp_ajax_apc_award_info','my_apc_award_info_callback');
add_action('wp_ajax_nopriv_apc_award_info','my_apc_award_info_callback');

// create the custom post type award_recipients and award_entries

function create_award_post_type() {

    register_post_type( 'award_recipients',
        array(
            'labels' => array(
                'name' => 'Award Recipients',
                'singular_name' => 'Award Recipient',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Award Recipient',
                'edit' => 'Edit',
                'edit_item' => 'Edit Award Recipient',
                'new_item' => 'New Award Recipient',
                'view' => 'View',
                'view_item' => 'View Award Recipient',
                'search_items' => 'Search Award Recipients',
                'not_found' => 'No Award Recipients found',
                'not_found_in_trash' => 'No Award Recipients found in Trash',
                'parent' => 'Parent Award Recipient'
            ),

            'public' => true,
            'menu_position' => 1,
            'supports' => array( 'title', 'editor', 'thumbnail', 'custom-fields' ),
            'taxonomies' => array( '' ),
            'menu_icon' => 'dashicons-megaphone',
            'has_archive' => true
        )
    );

	register_post_type( 'award_entries',
        array(
            'labels' => array(
                'name' => 'Award Entries',
                'singular_name' => 'Award Entry',
                'add_new' => 'Add New',
                'add_new_item' => 'Add New Award Entry',
                'edit' => 'Edit',
                'edit_item' => 'Edit Award Entry',
                'new_item' => 'New Award Entry',
                'view' => 'View',
                'view_item' => 'View Award Entry',
                'search_items' => 'Search Award entries',
                'not_found' => 'No Award entries found',
                'not_found_in_trash' => 'No Award entries found in Trash',
                'parent' => 'Parent Award Entry'
            ),

            'public' => true,
            'menu_position' => 2,
            'supports' => array( 'title', 'editor', 'custom-fields' ),
            'taxonomies' => array( '' ),
            'menu_icon' => 'dashicons-megaphone',
            'has_archive' => true
        )
    );


}

function custom_enter_title( $input ) {

    global $post_type;

    if( is_admin() && 'Enter title here' == $input && 'award_recipients' == $post_type )
        return 'Enter Employee Name here';

    if( is_admin() && 'Enter title here' == $input && 'award_entries' == $post_type )
        return 'Enter Employee IC Number here';

    return $input;
}

// change the metabox position to appear before editor

/*function add_before_editor($post) {
  global $post;
  do_meta_boxes('award_recipients', 'pre_editor', $post);
}

add_action('edit_form_after_title','add_before_editor');*/

function award_recipients_admin() {

    add_meta_box( 'award_recipient_meta_box',
        'Award Recipient Details',
        'display_award_recipients_meta_box_ul',
        'award_recipients', 'normal', 'high'
    );
}

// Retrieve current data of Award Recipient based on Award Recipient ID

function display_award_recipients_meta_box_ul( $award_recipient ) {

    $employee_ic = intval( get_post_meta( $award_recipient->ID, 'employee_ic', true ) );
    $employee_position = esc_html( get_post_meta( $award_recipient->ID, 'employee_position', true ) );
    $employee_grade = esc_html( get_post_meta( $award_recipient->ID, 'employee_grade', true ) );
    $employee_workplace = esc_html( get_post_meta( $award_recipient->ID, 'employee_workplace', true ) );

    ?>
    	<ul class="mb-list-entry-fields">
			<li class="row-award">
				<label class="field-label" for="award_info_award" style="display: inline-block; width: 400px; padding-right: 5px;">IC Number (No. Kad Pengenalan) :<span class="required">*</span></label>
				<div class="mb-right-column">
					<input type="text" name="employee_ic" class="mb-text-input mb-field" id="employee_ic" value="<?php echo $employee_ic; ?>" />
					<p class="description">To show this field data on APC Viewer, use class -&gt; apcdiv_icnumber</p>
				</div>
				<!-- .mb-right-column -->
			</li>
			<li class="row-award">
				<label class="field-label" for="award_info_award" style="display: inline-block; width: 400px; padding-right: 5px;">Employee Position (Jawatan) :<span class="required">*</span></label>
				<div class="mb-right-column">
					<input type="text" name="employee_position" class="mb-text-input mb-field" id="employee_position" value="<?php echo $employee_position; ?>" />
					<p class="description">To show this field data on APC Viewer, use class -&gt; apcdiv_position</p>
				</div>
				<!-- .mb-right-column -->
			</li>
			<li class="row-award">
				<label class="field-label" for="award_info_award" style="display: inline-block; width: 400px; padding-right: 5px;">Employee Grade (Gred) :<span class="required">*</span></label>
				<div class="mb-right-column">
					<input type="text" name="employee_grade" class="mb-text-input mb-field" id="employee_grade" value="<?php echo $employee_grade; ?>" />
					<p class="description">To show this field data on APC Viewer, use class -&gt; apcdiv_grade</p>
				</div>
				<!-- .mb-right-column -->
			</li>
			<li class="row-award">
				<label class="field-label" for="award_info_award" style="display: inline-block; width: 400px; padding-right: 5px;">Employee Workplace (Tempat bertugas) :<span class="required">*</span></label>
				<div class="mb-right-column">
					<input type="text" name="employee_workplace" class="mb-text-input mb-field" id="employee_workplace" value="<?php echo $employee_workplace; ?>" />
					<p class="description">To show this field data on APC Viewer, use class -&gt; apcdiv_workplace</p>
				</div>
				<!-- .mb-right-column -->
			</li>

		</ul>
    <?php
}

// save custom post type action

function add_award_recipient_fields( $award_recipient_id, $award_recipient ) {

    // Check post type for award_recipients
    if ( $award_recipient->post_type == 'award_recipients' ) {

    	// var_dump($_POST);exit;

        // Store data in post meta table if present in post data

        if ( isset( $_POST['employee_ic'] ) && !empty($_POST['employee_ic'])) {
            update_post_meta( $award_recipient_id, 'employee_ic', $_POST['employee_ic'] );
        }

        if ( isset( $_POST['employee_position'] ) && !empty($_POST['employee_position'])) {
            update_post_meta( $award_recipient_id, 'employee_position', $_POST['employee_position'] );
        }

        if ( isset( $_POST['employee_grade'] ) && !empty($_POST['employee_grade'])) {
            update_post_meta( $award_recipient_id, 'employee_grade', $_POST['employee_grade'] );
        }

        if ( isset( $_POST['employee_workplace'] ) && !empty($_POST['employee_workplace'])) {
            update_post_meta( $award_recipient_id, 'employee_workplace', $_POST['employee_workplace'] );
        }
    }
}

// update award recipients table list column

function my_columns( $columns ) {
    $columns['award_recipients_employee_ic'] = 'IC Number';
    $columns['award_recipients_employee_position'] = 'Employee Position';
    $columns['award_recipients_employee_grade'] = 'Employee Grade';
    $columns['award_recipients_employee_workplace'] = 'Employee Workplace';
    // unset( $columns['comments'] );
    return $columns;
}

// populate table list columns

function populate_columns( $column ) {
    if ( 'award_recipients_employee_ic' == $column ) {
        $employee_ic = esc_html( get_post_meta( get_the_ID(), 'employee_ic', true ) );
        echo $employee_ic;
    }
    elseif ( 'award_recipients_employee_position' == $column ) {
        $employee_position = get_post_meta( get_the_ID(), 'employee_position', true );
        echo $employee_position;
    }
    elseif ( 'award_recipients_employee_grade' == $column ) {
        $employee_grade = get_post_meta( get_the_ID(), 'employee_grade', true );
        echo $employee_grade;
    }
    elseif ( 'award_recipients_employee_workplace' == $column ) {
        $employee_workplace = get_post_meta( get_the_ID(), 'employee_workplace', true );
        echo $employee_workplace;
    }
}

// ajax request at homepage for APC latest entry data

function my_action_javascript() {

	?>
	<script type="text/javascript" >
	jQuery(document).ready(function($) {

		// ajax_apc_info();

		// execute at home only

		if ($( "body" ).hasClass( "home" )) {
			setInterval(ajax_apc_info, 5000);
		};


		function ajax_apc_info()
		{
			var data = {
				'action': 'apc_award_info'
			};

			$.ajax({
		        url: ajaxurl,
		        type: "post",
		        data: data,
		        dataType : 'json',
		        success: function(response){

		        	if (response.success) {

		        		var result = response.result;

		        		$.each (result, function (index) {
		        		    /*console.log (index);
		        		    console.log (result[index]);*/

		        		    // replace current data with new data

		        		    var new_data = result[index];

		        		    // console.log(new_data);

		        		    if (index=='apcdiv_staffimage') {
		        		    	jQuery('.'+index+' .image-wrap img').fadeOut().attr("src",new_data).fadeIn();
		        		    }
		        		    else
		        		    {
		        		    	jQuery('.'+index+' p').text(new_data);
		        		    }

		        		});

		        	};

		        },
		        error:function(response){

		        }
		    });


		}


	});
	</script> <?php
}

// ajax callback get APC latest entry

function my_apc_award_info_callback()
{
	$latest_award_entry = get_posts("post_type=award_entries&numberposts=1");

	$current_queue_IC = 0;

	if ($latest_award_entry) {

		$current_queue_IC = $latest_award_entry[0]->post_title;
	}

	// echo $current_queue_IC;

	if (!empty($current_queue_IC)) {

		// get the award recipient info

		$meta_key = 'employee_ic';

		$award_filter = array('meta_key'=> $meta_key, 'meta_value'=> $current_queue_IC, 'post_type'=>'award_recipients', 'numberposts'=>1);

		$latest_award_info = get_posts($award_filter);

		$award_id = 0;

		$award_view_data = array();

		if ($latest_award_info) {
			// var_dump($latest_award_info);

			$award_id = $latest_award_info[0]->ID;
			$award_recipient = $latest_award_info[0]->post_title;

			$award_meta = get_post_meta($award_id);

			// var_dump($award_meta['branch_branch_1'][0]);

			// predefine the default data needed

			$award_view_data['apcdiv_staffname'] =  $award_recipient;
			$award_view_data['apcdiv_staffimage'] =  $award_meta['post_image'][0];
			$award_view_data['apcdiv_workplace'] =  $award_meta['employee_workplace'][0];
			$award_view_data['apcdiv_grade'] =  $award_meta['employee_grade'][0];
			$award_view_data['apcdiv_position'] =  $award_meta['employee_position'][0];
			$award_view_data['apcdiv_ic'] =  $current_queue_IC;

			// load others addiotional data that may be useful

			foreach ($award_meta as $key => $value) {

				$award_view_data_key = "apcdiv_".$key;

				// only add not existing array key to prevent overwriting the array that was already define

				if (!array_key_exists($award_view_data_key, $award_view_data)) {
				    $award_view_data[$award_view_data_key] =  $value[0];
				}
			}

			echo json_encode(array('success' => true, 'result' => $award_view_data));
			// wp_send_json_success( $award_view_data );
		}
		else
		{
			wp_send_json_error();
		}

	}
	else
	{
		wp_send_json_error();
	}

	// echo $current_queue_IC;

	wp_die();
}


?>