<?php

/**
 * Unistall plugin
 *
 * Description. (use period)
 *
 * @link URL
 *
 * @package simple-metadata-lifecycle
 * @subpackage XXXXXX/XXXXXX
 * @since x.x.x (when the file was introduced)
 */

// If uninstall not called from WordPress, then exit.
if ( ! defined( 'WP_UNINSTALL_PLUGIN' ) ) {
	exit;
}

if(is_plugin_active('simple-metadata/simple-metadata.php')){
	//Simple metadata is installed
	//add dependency
	include_once   WP_PLUGIN_DIR . "/simple-metadata/inc/smd-uninstall-functions.php";
}
else{
	exit;
}

//get all the sites for multisite, if not a multisite, set blog id to 1
if (is_multisite()) {
	$blogs_ids = get_sites();
  smd_delete_network_options('smdlc_'); // see: simple metadata library
} else {
	$blogs_ids = [1];
}
smd_delete_local_options_and_post_meta($blogs_ids, 'smdlc_'); // see: simple metadata library
