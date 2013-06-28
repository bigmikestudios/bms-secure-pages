<?php
/**
 * @author Mike Lathrop
 * @version 0.0.1
 */
/*
Plugin Name: BMS Secure Pages
Plugin URI: http://bigmikestudios.com
Depends: 
Description: Adds a custom post type that is only viewable by logged in users.
Version: 0.0.1
Author URI: http://bigmikestudios.com
*/

// =============================================================================

add_action( 'init', 'register_cpt_secure_page' );

function register_cpt_secure_page() {

    $labels = array( 
        'name' => _x( 'Secure Pages', 'secure_page' ),
        'singular_name' => _x( 'Secure Page', 'secure_page' ),
        'add_new' => _x( 'Add New', 'secure_page' ),
        'add_new_item' => _x( 'Add New Secure Page', 'secure_page' ),
        'edit_item' => _x( 'Edit Secure Page', 'secure_page' ),
        'new_item' => _x( 'New Secure Page', 'secure_page' ),
        'view_item' => _x( 'View Secure Page', 'secure_page' ),
        'search_items' => _x( 'Search Secure Pages', 'secure_page' ),
        'not_found' => _x( 'No secure pages found', 'secure_page' ),
        'not_found_in_trash' => _x( 'No secure pages found in Trash', 'secure_page' ),
        'parent_item_colon' => _x( 'Parent Secure Page:', 'secure_page' ),
        'menu_name' => _x( 'Secure Pages', 'secure_page' ),
    );

    $args = array( 
        'labels' => $labels,
        'hierarchical' => true,
        
        'supports' => array( 'title', 'editor' ),
        
        'public' => true,
        'show_ui' => true,
        'show_in_menu' => true,
        
        
        'show_in_nav_menus' => false,
        'publicly_queryable' => true,
        'exclude_from_search' => true,
        'has_archive' => true,
        'query_var' => true,
        'can_export' => true,
        'rewrite' => true,
        'capability_type' => 'post'
    );

    register_post_type( 'secure_page', $args );
}

// =============================================================================

//////////////////////////
//
// ADD ATTACHMENTS PLUGIN STUFF
//
//////////////////////////

// Integrate with Attachments plugin

// only show this instance on pages using the rental template!

if (is_admin()) {
			
	function secure_attachments( $attachments )
	{
	  
	  $fields=array(
		array(
		  'name'      => 'title',                         // unique field name
		  'type'      => 'text',                          // registered field type
		  'label'     => __( 'Title', 'attachments' ),    // label to display
		  //'default'   => 'title',                         // default value upon selection
		),
	  );
	
	  $args = array(
	
		// title of the meta box (string)
		'label'         => 'Photos',
	
		// all post types to utilize (string|array)
		'post_type'     => array( 'secure_page'),
	
		// meta box position (string) (normal, side or advanced)
		'position'      => 'side',
	
		// meta box priority (string) (high, default, low, core)
		'priority'      => 'low',
	
		// allowed file type(s) (array) (image|video|text|audio|application)
		'filetype'      => array(),  // no filetype limit
	
		// include a note within the meta box (string)
		'note'          => 'Attach files here!',
	
		// by default new Attachments will be appended to the list
		// but you can have then prepend if you set this to false
		'append'        => true,
	
		// text for 'Attach' button in meta box (string)
		'button_text'   => __( 'Attach Files', 'attachments' ),
	
		// text for modal 'Attach' button (string)
		'modal_text'    => __( 'Attach', 'attachments' ),
	
		// which tab should be the default in the modal (string) (browse|upload)
		'router'        => 'browse',
	
		// fields array
		'fields'        => $fields,
	
	  );
	
	  $attachments->register( 'secure_attachments', $args ); // unique instance name
	}
	
	add_action( 'attachments_register', 'secure_attachments' );
}

// =============================================================================

//////////////////////////
//
// REDIRECT TO LOGIN IF USER IS NOT LOGGED IN
//
//////////////////////////

function block_secure_pages_from_non_users( $query ) {
    global $current_user;
	if (
		(count($current_user->roles) == 0) and (
		  ($query->query_vars['post_type'] == 'secure_page') or
		  ($query->query['post_type'] == 'secure_page') or 
		  ($query->queried_object->post_type == 'secure_page') or 
		  ($query->query_vars['post_type'] == 'attachment') or
		  ($query->query['post_type'] == 'attachment') or
		  ($query->queried_object->post_type == 'attachment') 
		)
	) {
		wp_redirect( home_url('/wp-login.php?redirect_to='.urlencode($_SERVER['REQUEST_URI'])) );
		exit;
	}
}
add_action( 'pre_get_posts', 'block_secure_pages_from_non_users' );