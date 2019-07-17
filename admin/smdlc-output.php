<?php

/**
 * Functionality for printing metatags
 *
 * Description. (use period)
 *
 * @link URL
 *
 * @package simple-metadata-lifecycle
 * @subpackage admin/output
 * @since x.x.x (when the file was introduced)
 */

use \vocabularies\SMDLC_Metadata_Lifecycle as lifecycle_meta;

/**
* Function for printing metatags in site front-end.
*
* @since
*
*/

function smdlc_print_tags ($type) {

	$locations = get_option('smdlc_locations');

	//Checking if we are executing Book Info or Site-Meta data for the front page - Site Level - Book Level
	if(!is_plugin_active('pressbooks/pressbooks.php')){
		$front_schema = 'site-meta';
	}else{
		$front_schema = 'metadata';
	}

	//recieving post type of current post
	$post_schema = get_post_type();

	//defining if page is post or front-page
	if ( is_front_page() ) {
		if (isset($locations[$front_schema]) && $locations[$front_schema]) {
			$lifecycle_meta = new lifecycle_meta($front_schema);
			echo $lifecycle_meta->smdlc_get_metatags($type);
		}
	} elseif (!is_home()){
		if (isset($locations[$post_schema]) && $locations[$post_schema]) {
			$lifecycle_meta = new lifecycle_meta($post_schema);
			echo $lifecycle_meta->smdlc_get_metatags($type);
		}
	}

}
