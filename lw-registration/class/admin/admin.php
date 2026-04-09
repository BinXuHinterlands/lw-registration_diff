<?php

if (!class_exists('LW_REGISTRATION_ADMIN_CLASS')) {

    class LW_REGISTRATION_ADMIN_CLASS {
		
		public function __construct() {
		    add_action('admin_menu', array(&$this, 'lw_registration_admin_menu'));
			add_action( 'admin_enqueue_scripts', array(&$this,'lw_registration_admin_scripts'));
			
			$ajaxActionsArray = array('lw_invitation_admin_action','lw_invitation_admin_resend');
			
			foreach($ajaxActionsArray as $single){
				add_action("wp_ajax_".$single, array(&$this,strtolower($single)));	
				add_action("wp_ajax_nopriv_".$single, array(&$this,strtolower($single)));	
			}
			
			add_action( 'show_user_profile', array(&$this,'lw_registration_profile_fields' ));
			add_action( 'edit_user_profile', array(&$this,'lw_registration_profile_fields' ));
			add_action( 'user_new_form', array(&$this,'lw_registration_profile_fields' ));
			
			add_action( 'personal_options_update', array(&$this,'lw_profile_fields_callback' ));
			add_action( 'edit_user_profile_update', array(&$this,'lw_profile_fields_callback' ));
			add_action( 'edit_user_created_user', array(&$this,'lw_profile_fields_callback' ));
		

		}
        public function lw_registration_admin_menu() {
			   add_menu_page(__('LW Invitation', 'lw_registration'), __('LW Invitation', 'lw_registration'), 'manage_options', 'lw_invitation', array(&$this, 'lw_invitation'));
			   add_submenu_page('lw_invitation', 'Settings', 'Settings', 'manage_options', "lw_invitation_settings", array(&$this,'lw_invitation_general_settings'));	
		}
		public function lw_registration_profile_fields($user) {
			include(LW_REGISTRATION_CLASS.'/admin/user_info.php');
		}
		
		private function normalize_pronouns_input($input) {
			// Read allowed pronouns directly from LW General Settings (no defaults)
			$lw_general_settings = get_option('lw_general_settings');
			$pronouns_options_setting = (isset($lw_general_settings['pronouns_options']) && is_array($lw_general_settings['pronouns_options'])) ? $lw_general_settings['pronouns_options'] : array();
			$allowed = array_values(array_unique(array_filter(array_map(function($opt){ return strtolower(trim($opt)); }, $pronouns_options_setting), function($t){ return $t !== ''; })));
		
			// Normalize input to lowercase tokens
			if (is_array($input)) {
				$vals = array_map(function($v){ return strtolower(trim($v)); }, $input);
			} else {
				$vals = preg_split('/[\s,;\/]+/', strtolower(trim((string)$input)));
			}
			$vals = array_filter($vals, function($v) use ($allowed) { return in_array($v, $allowed, true); });
			$vals = array_values(array_unique($vals));
			// Limit to two tokens to match display style (e.g., "She Ze")
			$vals = array_slice($vals, 0, 2);
		
			// Debug logging for testing
			error_log('[LW Pronouns] Settings allowed=' . json_encode($allowed));
			error_log('[LW Pronouns] Input raw=' . json_encode($input));
			error_log('[LW Pronouns] Normalized tokens=' . json_encode($vals));
		
			// Use display casing
			$final = array_map(function($v){ return ucfirst($v); }, $vals);
			// Join with a space to align with Extended Profile and UI tokens
			$joined = implode(' ', $final);
			error_log('[LW Pronouns] Saved value=' . $joined);
			return $joined;
		}

		public function lw_profile_fields_callback($user_id){
			global $wpdb;
			$updatedMetaData = array(
				'lw_referral_source' => $_POST['lw_referral_source'],
				'lw_state' => $_POST['lw_state']
			);
			$lw_form_type = $_POST['lw_form_type'];

				if($lw_form_type=="form_a"){
					$today_birtday = $_POST['form_a_lw_registration_birthday'];
					
					$updatedMetaData = array_merge($updatedMetaData, 
						array(
												'lw_form_type'=>$_POST['lw_form_type'],
												'lw_registration_birthday'=>$_POST['form_a_lw_registration_birthday'],
												'lw_registration_pronouns'=>$this->normalize_pronouns_input(isset($_POST['form_a_lw_registration_pronouns'])?$_POST['form_a_lw_registration_pronouns']:''),
												'lw_area_code'=>$_POST['form_a_lw_area_code'],
												'lw_mobilephone'=>$_POST['form_a_lw_mobilephone'],
												'lw_emergency_area_code'=>$_POST['lw_emergency_area_code'],
												'lw_registration_guardian_first_name'=>$_POST['form_a_lw_registration_guardian_first_name'],
												'lw_registration_guardian_last_name'=>$_POST['form_a_lw_registration_guardian_last_name'],
												'lw_registration_guardian_email'=>$_POST['form_a_lw_registration_guardian_email'],
												'lw_registration_guardian_mobile_phone'=>$_POST['form_a_lw_registration_guardian_mobile_phone'],
												'lw_contact_you'=>$_POST['form_a_lw_contact_you']
						));
						
				}
				
				if($lw_form_type=="form_a_direct"){
				    $today_birtday = $_POST['form_a_lw_registration_birthday'];
				    $updatedMetaData = array_merge($updatedMetaData, 
				        array(
				            'lw_form_type'=>$_POST['lw_form_type'],
				            'lw_registration_birthday'=>$_POST['form_a_lw_registration_birthday'],
				            'lw_registration_pronouns'=>$this->normalize_pronouns_input(isset($_POST['form_a_lw_registration_pronouns'])?$_POST['form_a_lw_registration_pronouns']:''),
				            'lw_area_code'=>$_POST['form_a_lw_area_code'],
				            'lw_mobilephone'=>$_POST['form_a_lw_mobilephone'],
				            'lw_emergency_area_code'=>$_POST['lw_emergency_area_code'],
				            'lw_registration_guardian_first_name'=>$_POST['form_a_lw_registration_guardian_first_name'],
				            'lw_registration_guardian_last_name'=>$_POST['form_a_lw_registration_guardian_last_name'],
				            'lw_registration_guardian_email'=>$_POST['form_a_lw_registration_guardian_email'],
				            'lw_registration_guardian_mobile_phone'=>$_POST['form_a_lw_registration_guardian_mobile_phone'],
				            'lw_contact_you'=>$_POST['form_a_lw_contact_you']
				        )
				    );
				}
				
				if($lw_form_type=="form_b"){
					$today_birtday = $_POST['form_b_lw_registration_birthday'];
					
					$updatedMetaData = array('lw_form_type'=>$_POST['lw_form_type'],
												'lw_registration_birthday'=>$_POST['form_b_lw_registration_birthday'],
												'lw_registration_pronouns'=>$this->normalize_pronouns_input(isset($_POST['form_b_lw_registration_pronouns'])?$_POST['form_b_lw_registration_pronouns']:'')
						);
						
				}
						
						
				if($lw_form_type=="form_c"){
					$today_birtday = $_POST['form_c_lw_registration_birthday'];
					
					$updatedMetaData = array('lw_form_type'=>$_POST['lw_form_type'],
												'lw_registration_pronouns'=>$this->normalize_pronouns_input(isset($_POST['form_c_lw_registration_pronouns'])?$_POST['form_c_lw_registration_pronouns']:''),
												'lw_registration_birthday'=>$_POST['form_c_lw_registration_birthday'],
												'lw_sibling_spent_time'=>$_POST['form_c_lw_sibling_spent_time'],
												'lw_emergency_area_code'=>$_POST['lw_emergency_area_code'],
												'lw_area_code'=>$_POST['form_c_lw_area_code'],
												'lw_mobilephone'=>$_POST['form_c_lw_mobilephone'],
												'lw_registration_guardian_first_name'=>$_POST['form_c_lw_registration_guardian_first_name'],
												'lw_registration_guardian_last_name'=>$_POST['form_c_lw_registration_guardian_last_name'],
												'lw_registration_guardian_email'=>$_POST['form_c_lw_registration_guardian_email'],
												'lw_registration_guardian_mobile_phone'=>$_POST['form_c_lw_registration_guardian_mobile_phone'],
												'lw_contact_you'=>$_POST['form_c_lw_contact_you']
						);
					
				}
	
			foreach($updatedMetaData as $k=>$v){
							update_user_meta($user_id,$k,$v);
			}

			if ( function_exists('xprofile_set_field_data') ) {
				$pronouns_value    = isset($updatedMetaData['lw_registration_pronouns']) ? $updatedMetaData['lw_registration_pronouns'] : '';
				$pronouns_field_id = function_exists('xprofile_get_field_id_from_name') ? xprofile_get_field_id_from_name('Pronouns') : 0;
				if ( !empty($pronouns_field_id) ) {
					xprofile_set_field_data( $pronouns_field_id, $user_id, $pronouns_value );
				}
			}
		
			$birthday_string = '';
		
			$lw_registration_birthday = get_user_meta($user_id, 'lw_registration_birthday', TRUE);
			
			//stop updating birthday meta key if no birth data
			if(empty($lw_registration_birthday)){
				return;
			}
			
			//check if it has date format error
			$timestamp = strtotime($lw_registration_birthday);
	
			if($timestamp == FALSE||date('Y-m-d', $timestamp) !== $lw_registration_birthday){
				return;
			}
			
			if (empty($lw_registration_birthday)) {
				$result = $wpdb->query("delete from ".$wpdb->prefix."usermeta where meta_key LIKE 'birthmmdd%' and user_id= ".$user_id."");
				return $user_id;
			}

			$birthday_mmdd = explode("-", $lw_registration_birthday);
			$birthday_mmdd = $birthday_mmdd[1].$birthday_mmdd[2];
			error_log($birthday_mmdd);
			if(!empty($birthday_mmdd)){
				// $query = $wpdb->prepare("delete from ".$wpdb->prefix."usermeta where meta_key LIKE 'birthmmdd%' and meta_key != 'birthmmdd%s' and user_id = %d and meta_value != '%s'", $birthday_mmdd, $user_id, $birthday_mmdd);
				$query = $wpdb->prepare(
					"DELETE FROM {$wpdb->prefix}usermeta 
					WHERE meta_key LIKE 'birthmmdd%%' 
					AND meta_key != %s 
					AND user_id = %d 
					AND meta_value != %s", 
					'birthmmdd' . $birthday_mmdd, 
					$user_id, 
					$birthday_mmdd
				);
				$wpdb->query($query);

				update_user_meta($user_id, 'birthmmdd'. $birthday_mmdd, $birthday_mmdd);
			}
			$lwAdminUsers = new lwAdminUsers(); 
			$lwAdminUsers->processUserBirthday($user_id);
		}
		
		public function lw_invitation_general_settings() {
				require_once(LW_REGISTRATION_CLASS.'/admin/general_settings.php');
		}
		public function lw_invitation() {
				require_once(LW_REGISTRATION_CLASS.'/admin/lw_invitation.php');
		}
		public function lw_registration_admin_scripts() {
		   if(isset($_REQUEST['page']) && ($_REQUEST['page']=='lw_invitation' || $_REQUEST['page']=='lw_invitation_settings')){
				wp_enqueue_style( 'LW-style-admin-css', LW_REGISTRATION_ASSETS_URL . '/css/LW-style-admin.css');
				wp_enqueue_style( 'lw_registration_bootstrap-css', LW_REGISTRATION_ASSETS_URL . '/css/bootstrap.min.css');
				wp_enqueue_style( 'lw_registration_dataTables.bootstrap4.min-css', '//cdn.datatables.net/1.10.23/css/dataTables.bootstrap4.min.css');
				wp_enqueue_style( 'lw_registration_all.min.css', '//cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css');
				
				add_action('admin_print_footer_scripts', 'lw_print_pronouns_select2_init');
				
				
             }
		   
		   global $pagenow;
           if (in_array($pagenow, array('user-edit.php','profile.php','user-new.php','users.php'), true)) {
               $child_select2_css_nonmin = get_stylesheet_directory() . '/assets/css/select2.css';
               $child_select2_css_min    = get_stylesheet_directory() . '/assets/css/select2.min.css';
               $child_select2_js_nonmin  = get_stylesheet_directory() . '/assets/js/select2.js';
               $child_select2_js_min     = get_stylesheet_directory() . '/assets/js/select2.min.js';
               $select2_css_url = file_exists($child_select2_css_nonmin) ? get_stylesheet_directory_uri() . '/assets/css/select2.css'
                                   : (file_exists($child_select2_css_min) ? get_stylesheet_directory_uri() . '/assets/css/select2.min.css'
                                   : (file_exists(get_template_directory() . '/assets/css/vendors/select2.min.css') ? get_template_directory_uri() . '/assets/css/vendors/select2.min.css' : '//cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css'));
               $select2_js_url  = file_exists($child_select2_js_nonmin) ? get_stylesheet_directory_uri() . '/assets/js/select2.js'
                                   : ( file_exists($child_select2_js_min) ? get_stylesheet_directory_uri() . '/assets/js/select2.min.js'
                                   : ( file_exists(get_template_directory() . '/assets/js/vendors/select2.full.min.js') ? get_template_directory_uri() . '/assets/js/vendors/select2.full.min.js' : '//cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.min.js'));
               $select2_css_path = file_exists( $child_select2_css_nonmin ) ? $child_select2_css_nonmin
                                   : ( file_exists( $child_select2_css_min ) ? $child_select2_css_min
                                   : ( file_exists(get_template_directory() . '/assets/css/vendors/select2.min.css') ? get_template_directory() . '/assets/css/vendors/select2.min.css' : '' ) );
               $select2_js_path  = file_exists( $child_select2_js_nonmin ) ? $child_select2_js_nonmin
                                   : ( file_exists( $child_select2_js_min ) ? $child_select2_js_min
                                   : ( file_exists(get_template_directory() . '/assets/js/vendors/select2.full.min.js') ? get_template_directory() . '/assets/js/vendors/select2.full.min.js' : '' ) );
               $select2_css_version = !empty($select2_css_path) && file_exists( $select2_css_path ) ? filemtime( $select2_css_path ) : '4.0.13';
               $select2_js_version  = !empty($select2_js_path)  && file_exists( $select2_js_path )  ? filemtime( $select2_js_path )  : '4.0.13';
               wp_register_style( 'lw-select2-css', $select2_css_url, array(), strval($select2_css_version), 'all' );
wp_register_script( 'lw-select2', $select2_js_url, array( 'jquery' ), strval($select2_js_version), true );
wp_enqueue_style( 'lw-select2-css' );
wp_enqueue_script( 'lw-select2' );
// Enqueue admin interaction script (input trigger and selection sync)
// First, set up pronouns options global variable for JavaScript
$lw_general_settings = get_option('lw_general_settings');
$pronouns_options = (isset($lw_general_settings['pronouns_options']) && is_array($lw_general_settings['pronouns_options'])) ? $lw_general_settings['pronouns_options'] : array();
wp_add_inline_script('lw-select2', 'window.lwPronounsOptions = ' . wp_json_encode($pronouns_options) . ';', 'before');
wp_enqueue_script( 'lw-registration-admin-js', LW_REGISTRATION_ASSETS_URL . '/js/lw_registration_admin.js', array('jquery','lw-select2'), strval(filemtime(plugin_dir_path(__FILE__).'../../assets/js/lw_registration_admin.js')), true );
// Hide native pronouns <select> and its Select2 container until JS finishes initialization to avoid flash
wp_add_inline_style('lw-select2-css', 'select.lw-pronouns-select{opacity:0 !important; position:absolute !important; left:-9999px !important; width:1px !important; height:1px !important; pointer-events:none !important;} select.lw-pronouns-select + .select2-container{visibility:hidden !important;}');
// Initialize Select2 for pronouns (kept existing behavior)
$lw_general_settings = get_option('lw_general_settings');
wp_add_inline_script('lw-select2', "jQuery(function($){ if(!$.fn.select2){return;} function initPronouns(ctx){ var $sels=$(ctx).find('select.lw-pronouns-select'); $sels.each(function(){ var $el=$(this); if($el.data('select2')){return;} $el.select2({
 placeholder: $el.attr('data-placeholder')||'Your Pronouns',
 width:'100%',
 closeOnSelect:true,
 dropdownParent: $(document.body),
 sorter: function(data){
     if(!Array.isArray(data)) return data;
     var orderArr = " . json_encode(isset($lw_general_settings['pronouns_options']) && is_array($lw_general_settings['pronouns_options']) ? array_values(array_map(function($x){ return strtolower(trim($x)); }, $lw_general_settings['pronouns_options'])) : array()) . ";
     var orderMap = {}; for(var i=0;i<orderArr.length;i++){ orderMap[orderArr[i]] = i; }
     return data.slice().sort(function(a,b){
         if(!a || !b) return 0;
         var aid = (a.id!=null ? a.id : a.text!=null ? a.text : '');
         var bid = (b.id!=null ? b.id : b.text!=null ? b.text : '');
         aid = String(aid).toLowerCase();
         bid = String(bid).toLowerCase();
         var ai = orderMap.hasOwnProperty(aid) ? orderMap[aid] : 999;
         var bi = orderMap.hasOwnProperty(bid) ? orderMap[bid] : 999;
         return ai - bi;
     });
 }
});
 var $cont = $el.next('.select2, .select2-container');
 if(!$cont.length){ try{ $cont = $el.select2('container'); }catch(e){} }
 if($cont && $cont.length){ $cont.css('visibility','visible'); }
}); } initPronouns(document); try{ var mo=new MutationObserver(function(muts){ muts.forEach(function(m){ if(m.addedNodes){ m.addedNodes.forEach(function(n){ if(n.nodeType===1){ initPronouns(n); } }); } }); }); mo.observe(document.body,{childList:true,subtree:true}); }catch(e){} $('#lw_form_type').on('change', function(){ setTimeout(function(){ initPronouns(document); }, 0); }); });");
            }
		}
		public function lw_invitation_admin_action(){
			parse_str($_POST['data'], $postData);
			global $wpdb,$current_user;
			$checkRegistration = $wpdb->get_results('SELECT * FROM '.TABLES_LW_REGISTRATION_INVITATION.' WHERE  email="'.$postData['email'].'"',ARRAY_A);
			$is_exits = 0;
			if(!empty($checkRegistration)){
				$is_exits  = 1;
				$responseReturn['status'] =0;
				$responseReturn['message'] ='Each email address can only receive one invitation.';
				echo json_encode($responseReturn);
				exit;	
			}
			if($is_exits==0){
				$checkRegistration = get_user_by("email",$postData['email']);
				
				if(isset($checkRegistration->ID) && $checkRegistration->ID>0){
					$is_exits  = 1;
					$responseReturn['status'] =0;
						$responseReturn['message'] ='Sorry this email is already in use, if you previously set up an account with us email livewire@starlight.org.au to reactivate it, otherwise try another email address.						';
						echo json_encode($responseReturn);
						exit;	
				}
				if(!isset($checkRegistration->ID) ){
					$checkRegistration = get_user_by("login",$postData['email']);
					if(isset($checkRegistration->ID) && $checkRegistration->ID>0){
						$is_exits  = 1;
						$responseReturn['status'] =0;
						$responseReturn['message'] ='Sorry this email is already in use, if you previously set up an account with us email livewire@starlight.org.au to reactivate it, otherwise try another email address.						';
						echo json_encode($responseReturn);
						exit;	
					}
				}
			}
			if($is_exits ==0){
				$lw_general_settings = get_option('lw_general_settings');
				$data_array['user_id']=$current_user->ID;
				$data_array['first_name']=$postData['first_name'];
				$data_array['last_name']=$postData['last_name'];
				$data_array['email']=$postData['email'];
				$data_array['invitation_type']=$postData['invitation_type'];
				$data_array['expire_at']= date("Y-m-d H:i:s" ,strtotime("+".$lw_general_settings['token_expire_hours']." hours",strtotime(date("Y-m-d H:i:s"))));
				$wpdb->insert(TABLES_LW_REGISTRATION_INVITATION,$data_array); 
				$insertedId = $wpdb->insert_id;
				
				
				$tokenData = $_REQUEST['id']."::".$data_array['first_name']." ".$data_array['last_name']." ".$data_array['email'];
				$token = lw_registration_encrypt_decrypt('encrypt',$tokenData);
				$wpdb->update(TABLES_LW_REGISTRATION_INVITATION,array('token'=>$token),array('id'=>$insertedId));
				
				
				invitationEmailSend(array('first_name'=>$postData['first_name'],'last_name'=>$postData['last_name'],'email'=>$postData['email']
				,'token'=>$token,'invitation_type'=>$postData['invitation_type']));	
			
				$process='add';
				$message  = "Invitation successfully inserted.";
				$responseReturn['status'] =1;
			}else if($is_exits ==1){
				$process='exits';
				$message  = "Sorry this email is already in use, if you previously set up an account with us email livewire@starlight.org.au to reactivate it, otherwise try another email address.				";
				$responseReturn['status'] =0;
			}
			
				$responseReturn['message'] =$message;
				echo json_encode($responseReturn);
				exit;	
		}
		
		public function lw_invitation_admin_resend(){
			
			global $wpdb;
			$lw_general_settings = get_option('lw_general_settings');
				
			$checkRegistration = $wpdb->get_row('SELECT * FROM '.TABLES_LW_REGISTRATION_INVITATION.' WHERE  id="'.$_POST['id'].'"',ARRAY_A);
			$invitation_type=(int)$checkRegistration['invitation_type'];
			if(!empty($checkRegistration)){
				$responseReturn['status'] =1;
				$message = "Invitation has been successfully send.";
				$tokenData = $_POST['id']."::".$checkRegistration['first_name']." ".$checkRegistration['last_name']." ".$checkRegistration['email'];
				$token = lw_registration_encrypt_decrypt('encrypt',$tokenData);
				$data_array['expire_at']= date("Y-m-d H:i:s" ,strtotime("+".$lw_general_settings['token_expire_hours']." hours",strtotime(date("Y-m-d H:i:s"))));
				//extend 30 days if its wishgranting
				if($invitation_type==1){
						$data_array['expire_at']= date("Y-m-d H:i:s" ,strtotime("+30 days",strtotime(date("Y-m-d H:i:s"))));
				}

				$data_array['token']= $token;
				$data_array['status']= 0;
				
				invitationEmailSend(array('first_name'=>$checkRegistration['first_name'],'last_name'=>$checkRegistration['last_name'],'email'=>$checkRegistration['email']
				,'token'=>$token,'invitation_type'=>$invitation_type));	

				
				$wpdb->update(TABLES_LW_REGISTRATION_INVITATION,$data_array,array('id'=>$_POST['id']));
				
			}else{
				$message = "Sorry, Something worng. Please try again later.";
				$responseReturn['status'] =0;
					
			}
			
			$responseReturn['message'] =$message;
			echo json_encode($responseReturn);
			exit;	
		} 
	}
	
}
new LW_REGISTRATION_ADMIN_CLASS();

function lw_print_pronouns_select2_init(){
    $order = isset($lw_general_settings['pronouns_options']) && is_array($lw_general_settings['pronouns_options']) ? array_values(array_map(function($x){ return strtolower(trim($x)); }, $lw_general_settings['pronouns_options'])) : array();
    $order_js = json_encode($order);
    echo '<script>jQuery(function($){ if(!$.fn.select2){return;} function initPronouns(ctx){ var $sels=$(ctx).find("select.lw-pronouns-select"); $sels.each(function(){ var $el=$(this); if($el.data("select2")){return;} var orderArr = '.$order_js.'; var orderMap={}; for(var i=0;i<orderArr.length;i++){ orderMap[orderArr[i]] = i; } $el.select2({ placeholder: $el.attr("data-placeholder")||"Your Pronouns", width:"100%", closeOnSelect:true, sorter: function(data){ return data.sort(function(a,b){ var ai=orderMap[(a.id||a.text||"").toLowerCase()]; var bi=orderMap[(b.id||b.text||"").toLowerCase()]; if(typeof ai==="undefined") ai=999; if(typeof bi==="undefined") bi=999; return ai-bi; }); } }); }); } initPronouns(document); });</script>';
}