<?php
/**
 * Plugin Name: Locations - AUSteve
 * Plugin URI: https://github.com/australiansteve/wp-plugin-austeve-locations
 * Description: Showcase a series of locations
 * Version: 0.0.1
 * Author: AustralianSteve
 * Author URI: http://AustralianSteve.com
 * License: GPL2
 */

include( plugin_dir_path( __FILE__ ) . 'admin.php');
include( plugin_dir_path( __FILE__ ) . 'shortcode.php');
include( plugin_dir_path( __FILE__ ) . 'widget.php');

/*
* Creating a function to create our CPT
*/

function austeve_create_locations_post_type() {

// Set UI labels for Custom Post Type
	$labels = array(
		'name'                => _x( 'Locations', 'Post Type General Name', 'austeve-locations' ),
		'singular_name'       => _x( 'Location', 'Post Type Singular Name', 'austeve-locations' ),
		'menu_name'           => __( 'Locations', 'austeve-locations' ),
		'parent_item_colon'   => __( 'Parent Location', 'austeve-locations' ),
		'all_items'           => __( 'All Locations', 'austeve-locations' ),
		'view_item'           => __( 'View Location', 'austeve-locations' ),
		'add_new_item'        => __( 'Add New Location', 'austeve-locations' ),
		'add_new'             => __( 'Add New', 'austeve-locations' ),
		'edit_item'           => __( 'Edit Location', 'austeve-locations' ),
		'update_item'         => __( 'Update Location', 'austeve-locations' ),
		'search_items'        => __( 'Search Location', 'austeve-locations' ),
		'not_found'           => __( 'Not Found', 'austeve-locations' ),
		'not_found_in_trash'  => __( 'Not found in Trash', 'austeve-locations' ),
	);
	
// Set other options for Custom Post Type
	
	$args = array(
		'label'               => __( 'Locations', 'austeve-locations' ),
		'description'         => __( 'Locations of any type', 'austeve-locations' ),
		'labels'              => $labels,
		// Features this CPT supports in Post Editor
		'supports'            => array( 'title', 'author', 'revisions', ),
		// You can associate this CPT with a taxonomy or custom taxonomy. 
		'taxonomies'          => array( 'location-type'),
		/* A hierarchical CPT is like Pages and can have
		* Parent and child items. A non-hierarchical CPT
		* is like Posts.
		*/	
		'hierarchical'        => false,
		'rewrite'           => array( 'slug' => 'locations' ),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_nav_menus'   => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 5,
		'can_export'          => true,
		'has_archive'         => true,
		'exclude_from_search' => false,
		'publicly_queryable'  => true,
		'capability_type'     => array( 'location' , 'locations' ),
        'map_meta_cap'        => true,
        'menu_icon'				=> 'dashicons-location',


	);
	
	// Registering your Custom Post Type
	register_post_type( 'austeve-locations', $args );


	$taxonomyLabels = array(
		'name'              => _x( 'Regions', 'taxonomy general name' ),
		'singular_name'     => _x( 'Region', 'taxonomy singular name' ),
		'search_items'      => __( 'Search Regions' ),
		'all_items'         => __( 'All Regions' ),
		'parent_item'       => __( 'Parent Region' ),
		'parent_item_colon' => __( 'Parent Region:' ),
		'edit_item'         => __( 'Edit Region' ),
		'update_item'       => __( 'Update Region' ),
		'add_new_item'      => __( 'Add New Region' ),
		'new_item_name'     => __( 'New Region Name' ),
		'menu_name'         => __( 'Regions' ),
	);

	$taxonomyArgs = array(

		'label'               => __( 'austeve_regions', 'austeve-locations' ),
		'labels'              => $taxonomyLabels,
		'show_admin_column'	=> false,
		'hierarchical' 		=> true,
		'rewrite'           => array( 'slug' => 'regions' ),
		'capabilities'		=> array(
							    'manage_terms' => 'edit_users',
							    'edit_terms' => 'edit_users',
							    'delete_terms' => 'edit_users',
							    'assign_terms' => 'edit_locations'
							 )
		);

	register_taxonomy( 'austeve_regions', 'austeve-locations', $taxonomyArgs );

}

/* Hook into the 'init' action so that the function
* Containing our post type registration is not 
* unnecessarily executed. 
*/

add_action( 'init', 'austeve_create_locations_post_type', 0 );

function location_include_template_function( $template_path ) {
    if ( get_post_type() == 'austeve-locations' ) {
        if ( is_single() ) {
            // checks if the file exists in the theme first,
            // otherwise serve the file from the plugin
            if ( $theme_file = locate_template( array ( 'single-locations.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/single-locations.php';
            }
        }
        else if ( is_archive() ) {
            // checks if the file exists in the theme first,
            // otherwise serve the file from the plugin
            if ( $theme_file = locate_template( array ( 'archive-locations.php' ) ) ) {
                $template_path = $theme_file;
            } else {
                $template_path = plugin_dir_path( __FILE__ ) . '/archive-locations.php';
            }
        }
    }
    return $template_path;
}
add_filter( 'template_include', 'location_include_template_function', 1 );

function location_filter_archive_title( $title ) {

    if( is_tax('austeve_regions' ) ) {

        $title = single_cat_title( '', false ) . ' locations';

    }
    else if ( is_post_type_archive('austeve-locations') ) {

        $title = post_type_archive_title( '', false );

    }

    return $title;

}

add_filter( 'get_the_archive_title', 'location_filter_archive_title');

function austeve_locations_enqueue_style() {
	wp_enqueue_style( 'austeve-locations', plugin_dir_url( __FILE__ ). '/style.css' , false , '4.6'); 
}

function austeve_locations_enqueue_script() {
	//wp_enqueue_script( 'my-js', 'filename.js', false );
}

add_action( 'wp_enqueue_scripts', 'austeve_locations_enqueue_style' );
add_action( 'wp_enqueue_scripts', 'austeve_locations_enqueue_script' );

?>