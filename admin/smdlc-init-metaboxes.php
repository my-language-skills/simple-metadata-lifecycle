<?php

//creating metaboxes for educational metadata

use \vocabularies\SMDLC_Metadata_Lifecycle as lifecycle_meta;


defined ("ABSPATH") or die ("No script assholes!");

/**
 * Function for producing metaboxes in all active locations
 */
function smdlc_create_metaboxes() {

	if (1 != get_current_blog_id() || !is_multisite()){

		//getting locations to place metaboxes
		$active_locations = get_option('smdlc_locations') ?: [];

		//for every active location initializaing lifecycle vocabulary to place metaboxes
		foreach ($active_locations as $location => $val) {
			new lifecycle_meta($location);
		}
		
	}

}


add_action( 'custom_metadata_manager_init_metadata', 'smdlc_create_metaboxes');