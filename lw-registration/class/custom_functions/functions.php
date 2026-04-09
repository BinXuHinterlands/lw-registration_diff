<?php
function lw_registration_encrypt_decrypt($action, $string) {
    $output = false;
    $encrypt_method = "AES-256-CBC";
    // Update your secret key before use
    $secret_key = '$7PHKqGt$yRlPjyt89rds4ioSDsglpk/';
    // Update your secret iv before use
    $secret_iv = '$QG8$hj7TRE2allPHPlBbrthUtoiu23bKJYi/';
    // hash
    $key = hash('sha256', $secret_key);
    
    // iv - encrypt method AES-256-CBC expects 16 bytes - else you will get a warning
    $iv = substr(hash('sha256', $secret_iv), 0, 16);
    if ( $action == 'encrypt' ) {
        $output = openssl_encrypt($string, $encrypt_method, $key, 0, $iv);
        $output = base64_encode($output);
    } else if( $action == 'decrypt' ) {
        $output = openssl_decrypt(base64_decode($string), $encrypt_method, $key, 0, $iv);
    }
    return $output;
}

function invitationEmailSend($data=array()){
		
					
				//email setting
				$from = 'info@hinterland.com';
				$sender  = 'From:HinterLand <'.$from.'>' . "\r\n";
				$lw_registration_email_settings = get_option('lw_registration_email_settings');
				$siteUrl=site_url();
				$siteName=get_bloginfo('name');
				$adminEmail = get_option('admin_email');
				$lw_general_settings = get_option('lw_general_settings');
				
				
				
				if($data['invitation_type']==0){
					$adminDetail = $lw_registration_email_settings['email_settings']['lw_registration_invitation_known_to_starlight'];
				}else{
					$adminDetail = $lw_registration_email_settings['email_settings']['lw_registration_invitation_known_to_wish_granting'];
				}
				
				$admin_subject = stripslashes($adminDetail['subject']);
				$admin_content = stripslashes($adminDetail['html']);
				$array_find=array('%first_name%','%last_name%','%email%','%site_url%','%site_name%','%registration_url%');
				
			
				if(isset($lw_general_settings['registration']) && $lw_general_settings['registration']>0 && $data['token']!=""){
					$registration_url = get_the_permalink($lw_general_settings['registration'])."?token=".$data['token'];
				}
				$array_value=array($data['first_name'],$data['last_name'],$data['email'],site_url(),get_bloginfo(),$registration_url);
				
				if($admin_subject == ''){ $admin_subject = 'Registration Invitation';}
				if($admin_content != '')
				{
					$message = str_replace($array_find,$array_value,$admin_content);
					
					
					
					$from = get_option('admin_email');
					$headers = 'From:'.get_bloginfo().' <'.$from.'>' . "\r\n";
					$headers .= 'Content-Type: text/html; charset=UTF-8';
					
					$mail = wp_mail($data['email'] ,$admin_subject, $message, $headers );
					
				
				}
}

function registrationEmailSend($user_email,$data=array(),$cc_email_address=array()){
		
					
				//email setting
				$from = 'info@hinterland.com';
				$sender  = 'From:HinterLand <'.$from.'>' . "\r\n";
				$lw_registration_email_settings = get_option('lw_registration_email_settings');
				$siteUrl=site_url();
				$siteName=get_bloginfo('name');
				$adminEmail = get_option('admin_email');
				$lw_general_settings = get_option('lw_general_settings');
				
				
				
				$template_type = $data['lw_form_type'];
				if ($template_type == 'form_a_direct') {
					$template_type = 'form_a';
				}
				$adminDetail = $lw_registration_email_settings['email_settings']['lw_registration_'.$template_type];
				$admin_subject = stripslashes($adminDetail['subject']);
				$admin_content = stripslashes($adminDetail['html']);
				$array_find=array('%first_name%','%last_name%','%email%','%site_url%','%site_name%','%username%');
				
			
				$array_value=array($data['lw_first_name'],$data['lw_last_name'],$user_email,site_url(),get_bloginfo(),$data['lw_username']);
				
				if($admin_subject == ''){ $admin_subject = 'Registration Invitation';}
				if($admin_content != '')
				{
					$message = str_replace($array_find,$array_value,$admin_content);
					
					
					
					$from = get_option('admin_email');
					
					$headers[] = 'From:'.get_bloginfo().' <'.$from.'>';
					$headers[] = 'Content-Type: text/html; charset=UTF-8';
					if($cc_email_address!=""){
						$headers[] = 'Cc: '.$cc_email_address;	
					}
					if(!empty($cc_email_address)){
						foreach($cc_email_address as $cc_address){
							$headers[] = 'Cc: '.$cc_address;	
						}
					}
					$mail = wp_mail($user_email,$admin_subject, $message, $headers );
					
				
				}
}

add_action('buddyboss_theme_begin_content','lw_welcome_after_content');
function lw_welcome_after_content(){
	
$lw_login = get_option('lw_login');
global $current_user,$lw_general_settings;

$pageId = get_the_ID();
			
	

$lw_form_type = get_user_meta($current_user->ID,'lw_form_type',true);

$get_lw_login_time = get_user_meta($current_user->ID,'lw_login_time',true);

if($current_user->ID>0 && $pageId == $lw_general_settings['login_redirect']){	
	
echo '<div class="container">
<div class="lw-welcome-wrapper">';
		if(($lw_form_type=="form_a" || $lw_form_type=="form_a_direct") && is_user_pending($current_user->ID)){
		echo '<div class="lw-welcome-notice">
			<div class="lw-welcome-notice-icon">
				<span>🛈</span>
			</div>
			<div class="lw-welcome-notice-description">';
				echo '<span>Your seven day access has begun! Look out for our emails or phone calls to finish setting up your account, and access Livewire ongoing. You can also call us on AU: 02 8425 5971 or NZ: 0800 000 680.</span>
			</div>	
		</div>';
		}
		//if(isset($lw_login) && $lw_login=="yes") {


		if($lw_general_settings['number_of_login_close']>$get_lw_login_time){
				echo '<div class="lw-welcome-inner" id="welcomeframe">
			<div class="lw-width50">
				<div class="lw-title-inner">
				<div class="lw-welcome-close-mobile">
					<span>&times;</span>
				</div>

					<h1 class="lw-title">Welcome to Livewire</h1>';
					echo "<p class='description'>Now what? Here’s your go to guide on getting started… tick off<a href='".site_url()."/stuff-to-do/new-member-bingo/'>your New Member Bingo</a>as you explore to get your Livewire Prize Pack<span>🎁</span></p>
					<p class='description'>Check out<a href='".site_url()."/stuff-to-know'>Stuff to Know</a>and<a href='".site_url()."/stuff-to-do'>Stuff to do</a>to help you along the way.</p>
				</div>
			</div>";
			
			echo '<div class="lw-width50">
			<div class="lw-width50-wrapper">
				<div class="lw-welcome-close">

					<span> Close x</span>

				</div>
				<div class="lw-welcome-linkbox bg-pink">
					<div class="lw-welcome-linkbox-inner">
						<span>👀</span>
						<h4> <a href="'.site_url().'/stuff-to-know/livewire-community-guidelines" target="_blank">Watch our guidelines</a></h4>
						<p>They may be different to what you expect...</p><a href="'.site_url().'/stuff-to-know/badges-pins/" target="_blank">and you get a badge!</a>
					</div>
				</div>
				<div class="lw-welcome-linkbox bg-yellow">
					<div class="lw-welcome-linkbox-inner">
						<span>👋</span>
						<h4><a href="'.site_url().'/chat">Say hi in live chat</a></h4>
						<p>And see where Livewire comes to life...</p>
					</div>
				</div>	
				<div class="lw-welcome-linkbox bg-sky">
					<div class="lw-welcome-linkbox-inner">
						<span>🤩</span>
						<h4><a href="'.site_url().'/members/'.$current_user->user_nicename.'/profile/edit">Update your profile</a></h4>
						<p>Share about yourself and add more badges.</p>
					</div>
				</div>
			</div>
			</div>

		</div>';
	}


		//}
	echo '</div></div>';
		
		update_option('lw_login','no');	
	
	
}
}

add_action('template_redirect', 'lw_template_redirect');
function lw_template_redirect(){
     global $wp_query,$lw_general_settings,$current_user;
	 
	if(isset($_REQUEST['token']) && $_REQUEST['token']!="" && $current_user->ID>0 && $lw_general_settings['registration']>0){
	 	wp_destroy_current_session();
     	wp_clear_auth_cookie();
		
		wp_redirect(get_the_permalink($lw_general_settings['registration'])."?token=".$_REQUEST['token']);
	 }
	
}


add_action('init', 'lw_add_ob_start');
function lw_add_ob_start(){
     ob_start("lw_push_callback");
	
}

function lw_push_callback($buffer){
     return $buffer;
}

add_action('wp_footer', 'lw_flush_ob_end');
function lw_flush_ob_end(){
     ob_end_flush();
}


//Disable the new user notification sent to the site admin
function lw_disable_new_user_notifications() {
//Remove original use created emails
remove_action( 'register_new_user', 'wp_send_new_user_notifications' );
remove_action( 'edit_user_created_user', 'wp_send_new_user_notifications', 10, 2 );

//Add new function to take over email creation
add_action( 'register_new_user', 'lw_send_new_user_notifications' );
add_action( 'edit_user_created_user', 'lw_send_new_user_notifications', 10, 2 );
}
function lw_send_new_user_notifications( $user_id, $notify = 'user' ) {
		return;
if ( empty($notify) || $notify == 'admin' ) {
return;
}elseif( $notify == 'both' ){
	return;
//Only send the new user their email, not the admin
$notify = 'user';
}
wp_send_new_user_notifications( $user_id, $notify );
}
add_action( 'init', 'lw_disable_new_user_notifications' );

function lw_get_client_ip() {
    $ipaddress = '';
    if (isset($_SERVER['HTTP_CLIENT_IP']))
        $ipaddress = $_SERVER['HTTP_CLIENT_IP'];
    else if(isset($_SERVER['HTTP_X_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_X_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_X_FORWARDED'];
    else if(isset($_SERVER['HTTP_FORWARDED_FOR']))
        $ipaddress = $_SERVER['HTTP_FORWARDED_FOR'];
    else if(isset($_SERVER['HTTP_FORWARDED']))
        $ipaddress = $_SERVER['HTTP_FORWARDED'];
    else if(isset($_SERVER['REMOTE_ADDR']))
        $ipaddress = $_SERVER['REMOTE_ADDR'];
    else
        $ipaddress = 'UNKNOWN';
    return $ipaddress;
}



add_action( 'xprofile_updated_profile', 'lw_profile_sync_wp_profile', 1000, 5 );

function lw_append_pronouns_to_displayname($display_name, $user_id){
    return $display_name;
}

function lw_profile_sync_wp_profile( $user_id, $posted_field_ids, $errors, $old_values, $new_values ) {
	$nickname_id  = bp_xprofile_nickname_field_id();
	global $current_user;

	foreach ( $new_values as $field_id => $new_value ) {
	    if ( $field_id == $nickname_id ) {
	        // Use the BuddyPress nickname field value for the edited user, not the current logged-in user
	        $nick = '';
	        if ( is_array( $new_value ) && isset( $new_value['value'] ) ) {
	            $nick = $new_value['value'];
	        } else {
	            $nick = $new_value;
	        }
	        if ( is_array( $nick ) ) { $nick = reset( $nick ); }
	        $nick = sanitize_text_field( (string) $nick );
	        if ( $nick !== '' ) {
	            bp_update_user_meta( $user_id, 'nickname', $nick );
	        }
	    }
	
	    $pronouns_field_id = function_exists('xprofile_get_field_id_from_name') ? xprofile_get_field_id_from_name('Pronouns') : 0;
	    $pronouns_field_id = (int) $pronouns_field_id;
	
	    if ( !empty($pronouns_field_id) && (int)$field_id === $pronouns_field_id ) {
	        $raw = isset($new_value['value']) ? $new_value['value'] : '';
	        $tokens = is_array($raw) ? $raw : preg_split('/[\s,;\/]+/', strtolower(trim((string)$raw)));
	        $tokens = array_map('strtolower', $tokens);
	        $lw_general_settings = get_option('lw_general_settings');
	        $pronouns_options_setting = (isset($lw_general_settings['pronouns_options']) && is_array($lw_general_settings['pronouns_options'])) ? $lw_general_settings['pronouns_options'] : array();
	        $allowed = array_values(array_unique(array_filter(array_map(function($opt){ return strtolower(trim($opt)); }, $pronouns_options_setting), function($t){ return $t !== ''; })));
	        if (empty($allowed)) { $allowed = array('she','her','he','him','they','them'); }
	        $tokens = array_values(array_unique(array_filter($tokens, function($t) use ($allowed){ return in_array($t, $allowed, true); })));
	        $tokens = array_slice($tokens, 0, 2);
	        $normalized = implode(' ', array_map(function($t){ return ucfirst($t); }, $tokens));
	        update_user_meta( $user_id, 'lw_registration_pronouns', $normalized );
	    }
	}
}
function update_user_birthday($user_id,$birthday){
	global $wpdb;
	if(!empty($user_id) && !empty($birthday) && preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/",$birthday)){
		$lw_registration_birthday = get_user_meta( $user_id, 'lw_registration_birthday', true);
		if(!empty($lw_registration_birthday)){
			$old_birthdate = explode("-",$lw_registration_birthday);
			$wpdb->query("delete from ".$wpdb->prefix."usermeta where meta_key='".$old_birthdate[1].$old_birthdate[2]."'");
		}
		update_user_meta($user_id,'lw_registration_birthday',$birthday);
		$today_birthday = explode("-",$birthday);
		update_user_meta($user_id,$today_birthday[1]."".$today_birthday[2],$today_birthday[1]."".$today_birthday[2]);
	}
}

add_action('wp_enqueue_scripts', 'enqueue_wp_password_strength_script');

function enqueue_wp_password_strength_script() {
    wp_enqueue_script('password-strength-meter');
}

if(is_admin()) {
	

	add_action( 'pre_user_query', function( $uqi ) {
    global $wpdb;

    $search = '';
	 if ( isset( $uqi->query_vars['search'] ) )
        $search = trim( $uqi->query_vars['search'] );

    if ( $search ) {
        $search = trim($search, '*');
        $the_search = '%'.$search.'%';

        $search_meta = $wpdb->prepare("
        ID IN ( SELECT user_id FROM {$wpdb->usermeta}
        WHERE ( ( meta_key='first_name' OR meta_key='last_name' )
            AND {$wpdb->usermeta}.meta_value LIKE '%s' )
        )", $the_search);

        $uqi->query_where = str_replace(
            'WHERE 1=1 AND (',
            "WHERE 1=1 AND (" . $search_meta . " OR ",
            $uqi->query_where );
    }
});
}


function is_user_pending($user_id) {
    $taxonomy = 'profile_type';
    // get terms
    $terms = wp_get_object_terms( $user_id, $taxonomy );
    // check terms
    if ( is_wp_error( $terms ) || empty( $terms ) ) {
        return false;
    }
    $term_names = wp_list_pluck( $terms, 'name' );
    return implode(', ', $term_names) == 'Verified'?false:true;
}
?>