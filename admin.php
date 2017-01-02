<?php

function austeve_add_roles_on_taxonomy_creation($term_id, $tt_id, $taxonomy) {

	if ($taxonomy == 'austeve_regions')
	{
		$term = get_term($term_id, $taxonomy);
		$term_slug = $term->slug;

		add_role( 
		   	'austeve_'.$term_slug.'_role', 
		   	$term->name.' Administrator', 
		   	array(
		        'read'         => true,  // true allows this capability
		        'read_locations'   => true,
		        'edit_locations'   => true,
		        'edit_others_locations'   => true,
		        'edit_private_locations'   => true,
		        'edit_published_locations'   => true,
		        'publish_locations'   => true,
		        'delete_locations'   => true,
		        'delete_others_locations'   => true,
		        'delete_private_locations'   => true,
		        'delete_published_locations'   => true,
		        'delete_posts' => false, // Use false to explicitly deny
		        'delete_pages' => false, // Use false to explicitly deny
		    ) 
		);
	}

}
add_action( 'edit_term', 'austeve_add_roles_on_taxonomy_creation', 10, 3 );
add_action( 'create_term', 'austeve_add_roles_on_taxonomy_creation', 10, 3 );


function austeve_delete_roles_on_taxonomy_deletion($term_id, $tt_id, $taxonomy, $deleted_term, $object_ids ) {

	if ($taxonomy == 'austeve_regions')
	{
		$term_slug = $deleted_term->slug;

		if( get_role( 'austeve_'.$term_slug.'_role' ) ){
			remove_role( 'austeve_'.$term_slug.'_role' );
		}

	}
}

add_action( 'delete_term', 'austeve_delete_roles_on_taxonomy_deletion', 10, 5 );

function austeve_add_location_role_caps() {

	// Add the roles you'd like to administer the custom post types
	$roles = array('editor','administrator');

	//Get all regions
	$terms = get_terms( array(
	    'taxonomy' => 'austeve_regions',
	    'hide_empty' => false,
	) );

	// Loop through each region - adding the associated role to the list to update
	foreach($terms as $the_term) { 
		array_push($roles,  'austeve_'.$the_term->slug.'_role' );
	}

	// Loop through each role and assign capabilities
	foreach($roles as $the_role) { 

		$role = get_role($the_role);

		$role->add_cap( 'read' );
		$role->add_cap( 'read_locations');
		$role->add_cap( 'read_private_locations' );
		$role->add_cap( 'edit_locations' );
		$role->add_cap( 'edit_others_locations' );
		$role->add_cap( 'edit_private_locations' );
		$role->add_cap( 'edit_published_locations' );
		$role->add_cap( 'publish_locations' );
		$role->add_cap( 'delete_locations' );
		$role->add_cap( 'delete_others_locations' );
		$role->add_cap( 'delete_private_locations' );
		$role->add_cap( 'delete_published_locations' );

	}

}
 
add_action('admin_init','austeve_add_location_role_caps',999);


// Add Project Type column to admin header
function austeve_locations_columns_head($defaults) {
    $defaults['region'] = 'Region';

    return $defaults;
}
add_filter('manage_austeve-locations_posts_columns', 'austeve_locations_columns_head');
 
// Add Project Type column content to admin table
function austeve_locations_columns_content($column_name, $post_ID) {
    if ($column_name == 'project_types') {

    	$term_list = wp_get_post_terms($post_ID, 'austeve_regions', array("fields" => "all"));
    	$string_list = "";
		foreach($term_list as $term_single) {
			$string_list .= $term_single->name.", "; //do something here
		}

		echo substr($string_list, 0, -2);
    }
}
add_action('manage_austeve-locations_posts_custom_column', 'austeve_locations_columns_content', 10, 2);


?>