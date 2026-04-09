<?php

/*

 * Plugin Name:       LW Registration

 * Description:       LW Registration

 * Version:           1.1.1

 * Author:           Cwiser 

 * Author URI:        

 * License:           GPL-2.0+

 */

if (!defined('ABSPATH'))

{

	exit; // Exit if accessed directly

}

global $wpdb,$invitationTypes,$invitationStatus,$lw_general_settings;
$uploads = wp_upload_dir();

$lw_general_settings = get_option('lw_general_settings');
 
$invitationTypes[0] = "In-Hospital Referral";
$invitationTypes[1] = "Wishgranting Referral";

$invitationStatus[0] = "Invitation Sent";
$invitationStatus[1] = "Expire";
$invitationStatus[2] = "Confirm";

add_action( 'plugins_loaded', 'lw_registration_load_textdomain' );

function lw_registration_load_textdomain() {

    $plugin_lw_path =basename( dirname( __FILE__ ) ) . '/languages'; /* Relative to WP_PLUGIN_DIR */
	load_plugin_textdomain( 'lw_registration', false, $plugin_lw_path );
}

require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
register_activation_hook( __FILE__, 'lw_registration_active_plugin');
register_deactivation_hook( __FILE__, 'lw_registration_deactive_plugin' );

// One-time migration: wipe existing pronoun data upon deployment
add_action('plugins_loaded','lw_registration_wipe_pronouns_once');
function lw_registration_wipe_pronouns_once(){
	if (get_option('lw_pronouns_wipe_done') === 'yes') {
		return;
	}
	global $wpdb;
	$wpdb->query("DELETE FROM {$wpdb->usermeta} WHERE meta_key='lw_registration_pronouns'");
	update_option('lw_pronouns_wipe_done','yes');
}


function lw_registration_deactive_plugin(){
	$lwAdminUsers = new lwAdminUsers();
	$lwAdminUsers->deactivatePlugin();
}
function lw_registration_active_plugin(){
	global $wpdb;
	
	$create_tables[] = "CREATE TABLE `".TABLES_LW_REGISTRATION_INVITATION."` (
					  `id` int(11) NOT NULL,
					  `user_id` int(11) NOT NULL,
					  `first_name` varchar(255) NOT NULL,
					  `last_name` varchar(255) NOT NULL,,
					  `staff_name` varchar(255) NOT NULL,
					  `email` varchar(255) NOT NULL,
					  `invitation_type` enum('0','1','2') NOT NULL DEFAULT '0' COMMENT '0 - Known to STL, 1- Known to WG (active), 2 - Known to WG(inactive))',
					  `status` enum('0','1','2') NOT NULL COMMENT '0 - Invitation Send, 1- expire, 2 - Confirm',
					  `token` text NOT NULL,
					  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
					  `expire_at` timestamp NOT NULL DEFAULT current_timestamp()
					) ENGINE=InnoDB DEFAULT CHARSET=latin1 AUTO_INCREMENT=1;";

	require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
	
	foreach ($create_tables as $key => $query) {
		dbDelta($query);
	}
		
		$wpdb->query("ALTER TABLE `".TABLES_LW_REGISTRATION_INVITATION."` ADD PRIMARY KEY (`id`)");
		$wpdb->query("ALTER TABLE `".TABLES_LW_REGISTRATION_INVITATION."` CHANGE `id` `id` INT(11) NOT NULL AUTO_INCREMENT");
		//$wpdb->query("ALTER TABLE `".TABLES_LW_REGISTRATION_INVITATION."` ADD `staff_name` VARCHAR(255) NOT NULL AFTER `last_name`");
		
		$lwAdminUsers = new lwAdminUsers();
		$lwAdminUsers->activatePlugin();


}



define ( 'LW_REGISTRATION_PATH', plugin_dir_path ( __FILE__ ) );
define ( 'LW_REGISTRATION_URL', plugins_url ( '', __FILE__ ) );
define ( 'LW_REGISTRATION_DIR_URL',  plugin_dir_url( __FILE__ ) );
define ( 'LW_REGISTRATION_ASSETS_URL', plugins_url ( 'assets/', __FILE__ ) );
define ( 'LW_REGISTRATION_ASSETS_PATH', LW_REGISTRATION_PATH . 'assets' );
define ( 'LW_REGISTRATION_CLASS', LW_REGISTRATION_PATH . 'class' );
define ( 'LW_REGISTRATION_GLOBALS', LW_REGISTRATION_PATH . 'globals' );
define ( 'TABLES_LW_REGISTRATION_INVITATION',$wpdb->prefix.'lw_registration_invitation');

define ( 'LW_REGISTRATION_ATTACH_URL',$uploads['baseurl'].'/lw_registration/');

define ( 'LW_REGISTRATION_ATTACH_PATH',$uploads['basedir'].'/lw_registration/');


if (!file_exists(LW_REGISTRATION_ATTACH_PATH)) {

    mkdir(LW_REGISTRATION_ATTACH_PATH, 0777, true);

}

require_once (LW_REGISTRATION_CLASS.'/custom_functions/functions.php');
require_once (LW_REGISTRATION_CLASS.'/front/front.php');
require_once (LW_REGISTRATION_CLASS.'/front/shortcodes.php');
require_once (LW_REGISTRATION_CLASS.'/admin/admin.php');
require_once (LW_REGISTRATION_CLASS.'/admin/users.php');

