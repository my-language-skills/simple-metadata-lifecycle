<?php

/**
 * Simple Metadata - Life Cycle
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://github.com/my-language-skills/simple-metadata-lifecycle
 * @since             1.0
 * @package           simple-metadata-lifecycle
 *
 * @wordpress-plugin
 * Plugin Name:       Simple Metadata - Life Cycle
 * Plugin URI:        https://github.com/my-language-skills/simple-metadata-lifecycle
 * Description:       Simple Metadata add-on for life-cycle inforamtion of web-site content.
 * Version:           1.1
 * Author:            My Language Skills team
 * Author URI:        https://github.com/my-language-skills/
 * License:           GPL 3.0
 * License URI:       https://www.gnu.org/licenses/gpl-3.0.txt
 * Text Domain:       simple-metadata-lifecycle
 * Domain Path:       /languages
 */

defined ("ABSPATH") or die ("No script assholes!");

require_once( ABSPATH . '/wp-admin/includes/plugin.php' );

//we only enable plugin functionality if main plugin - Simple Metadata - is installed
if(is_plugin_active('simple-metadata/simple-metadata.php')){
	include_once plugin_dir_path( __FILE__ ) . "admin/vocabularies/smdlc-lifecycle-class.php";
	include_once plugin_dir_path( __FILE__ ) . "admin/smdlc-admin-settings.php";
	include_once plugin_dir_path( __FILE__ ) . "admin/smdlc-output.php";
	include_once plugin_dir_path( __FILE__ ) . "admin/smdlc-init-metaboxes.php";
	//loading network settings only for multisite installation
	if (is_multisite()){
		include_once plugin_dir_path( __FILE__ ) . "network-admin/smdlc-network-admin-settings.php";
	}

} else { //if Simple Metadata is not installed we show notice
	if (is_multisite()){ //notice for multisite installation
		add_action( 'network_admin_notices', function () {
			?>
    		<div class="notice notice-info is-dismissible">
        		<p><strong>'Simple Metadata Life Cycle'</strong> functionality is deprecated due to the following reason: <strong>'Simple Metadata'</strong> plugin is not installed or not activated. Please, install <strong>'Simple Metadata'</strong> in order to fix the problem.</p>
    		</div>
    	<?php
		});
	} else { //notice for single-site installation
		add_action( 'admin_notices', function () {
			?>
    		<div class="notice notice-info is-dismissible">
        		<p><strong>'Simple Metadata Life Cycle'</strong> functionality is deprecated due to the following reason: <strong>'Simple Metadata'</strong> plugin is not installed or not activated. Please, install <strong>'Simple Metadata'</strong> plugin in order to fix the problem.</p>
    		</div>
    	<?php
		});
	}
}


/**
 * Load plugin textdomain.
 */
function smlc_load_textdomain() {
  //load_plugin_textdomain( 'simple-metadata-lifecycle', false, basename( dirname( __FILE__ ) ) . '/languages' );
	load_plugin_textdomain( 'simple-metadata-lifecycle', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/'  );
}

add_action( 'plugins_loaded', 'smlc_load_textdomain' );
