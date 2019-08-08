<?php

/**
 * Creates metaboxes for educational metadata
 *
 * Description. (use period)
 *
 * @link URL
 *
 * @package simple-metadata-lifecycle
 * @subpackage admin/init/metaboxes
 * @since x.x.x (when the file was introduced)
 */

use \vocabularies\SMDLC_Metadata_Lifecycle as lifecycle_meta;


defined ("ABSPATH") or die ("No script assholes!");

/**
* Function for producing metaboxes in all active locations.
*
* @since
*
*/

function smdlc_create_metaboxes() {
	//Don't display for blog id 1
	if (1 != get_current_blog_id() || !is_multisite()){

		// Can't use get_post_id() becouse it return void outside the loop of Wordpress
		$post_id = isset($_GET['post']) ? $_GET['post'] : '';

		//Don't show annotation metaboxes if the post type is not a Creative Work subtype
		if(!empty($post_id) && !smd_is_post_CreativeWork($post_id) && !is_plugin_active('pressbooks/pressbooks.php')){ //smd-general-function.php
			return;
		}

		//getting locations to place metaboxes
		$active_locations = get_option('smdlc_locations') ?: [];

		//for every active location initializaing lifecycle vocabulary to place metaboxes
		foreach ($active_locations as $location => $val) {
			new lifecycle_meta($location);
		}
	}

}


add_action( 'custom_metadata_manager_init_metadata', 'smdlc_create_metaboxes');
