<?php
if (!class_exists('lwAdminUsers')) {
class lwAdminUsers{

    const CHAT_TEMP_BLOCK_MINUTES = 15;

    public function __construct() 
    {
        add_action('init', array(&$this,'registerTaxonomy'));
        add_action('init', array(&$this,'registerTerms'));

        add_action('show_user_profile', array(&$this,'user_profile_field'));
        add_action('edit_user_profile', array(&$this,'user_profile_field'));
        add_action('personal_options_update', array(&$this,'user_profile_field_save'));
        add_action('edit_user_profile_update', array(&$this,'user_profile_field_save'));

        add_filter('manage_users_columns', array(&$this,'manage_users_columns') );
        add_action('manage_users_custom_column', array(&$this,'manage_users_custom_column'), 10, 3 );

        add_action('restrict_manage_users', array(&$this,'add_filter'));
        add_filter('pre_get_users', array(&$this,'process_filter'),1000);

        add_filter('cron_schedules',array(&$this,'cronSchedules'));

        //add_action('lw_cron_for_21_years_blocked_status',array(&$this,'cronToSetTwentyOneYearsUserBlockedStatus'));
        //add_action('lw_cron_for_set_user_age_taxonomy',array(&$this,'cronToSetUserAgeTaxonomy'));
        add_action('lw_cron_for_disable_access_after_24_hours',array(&$this,'cronToDisableAccessAfterTwentyFourHours'));
        add_action('lw_cron_for_chat_status_temp_blocked_clear',array(&$this,'cronToClearChatTempBlockedStatus'));         
                
        add_action('rest_api_init',array(&$this,'restAPiRoutes'));
    }

    public function activatePlugin()
    {
        $this->clearCronJobs(); 
        $this->setCronJobs();
    }

    public function deactivatePlugin()
    {
        $this->clearCronJobs(); 
    }

    public function cronSchedules($schedules)
    {
        if(!isset($schedules["5min"])){
            $schedules["5min"] = array(
                'interval' => 5*60,
                'display' => __('Once every 5 minutes'));
        }
        return $schedules;
    }

    private function setCronJobs()
    {
        /*
        $startTime = strtotime(date("Y-m-d 04:00:00",time()));        
        $cronDuration = 'daily';
        $cronKey = 'lw_cron_for_21_years_blocked_status';
        if (!wp_next_scheduled($cronKey)) {
            wp_schedule_event($startTime,$cronDuration,$cronKey);
        }

        $startTime = strtotime(date("Y-m-d 01:00:00",time()));
        $cronDuration = 'daily';
        $cronKey = 'lw_cron_for_set_user_age_taxonomy';
        if (!wp_next_scheduled($cronKey)) {
            wp_schedule_event($startTime,$cronDuration,$cronKey);
        }
        */

        $startTime = strtotime(date("Y-m-d 04:00:00",time()));
        $cronDuration = 'hourly';
        $cronKey = 'lw_cron_for_disable_access_after_24_hours';
        if (!wp_next_scheduled($cronKey)) {
            wp_schedule_event($startTime,$cronDuration,$cronKey);
        }

        $startTime = strtotime(date("Y-m-d 00:00:00",time()));
        $cronDuration = '5min';
        $cronKey = 'lw_cron_for_chat_status_temp_blocked_clear';
        if (!wp_next_scheduled($cronKey)) {
            wp_schedule_event($startTime,$cronDuration,$cronKey);
        }
    }

    private function clearCronJobs()
    {        
        wp_clear_scheduled_hook('lw_cron_for_21_years_blocked_status');
        wp_clear_scheduled_hook('lw_cron_for_disable_access_after_24_hours');
        wp_clear_scheduled_hook('lw_cron_for_set_user_age_taxonomy');
        wp_clear_scheduled_hook('lw_cron_for_chat_status_temp_blocked_clear');
    }

    private function createTaxList()
    {
        $tax = ['age_group'   => ['label'=>'Age Group',
                                  'values'=>['Less Than 18','18 And Above'],
                                  'edit'=>true
                                 ],
                'chat_status'  => ['label'=>'Chat Status',
                                  'values'=>['Chat Block Temporarily','Chat Block Permanently'],
                                  'edit'=>true
                                  ],
                ];

        return $tax;        
    }

    public function registerTaxonomy()
    {
        $taxs = $this->createTaxList();
        if(!empty($taxs)){
            foreach($taxs as $taxonomy=>$tax){
                $args = array(
                    'public' => true,
                    'label'  => $tax['label']
                );
                register_taxonomy( $taxonomy, 'user', $args );
            }
        }
    }

    public function registerTerms()
    {
        $taxs = $this->createTaxList();
        if(!empty($taxs)){
            foreach($taxs as $taxonomy=>$tax){
                $terms = $tax['values'];
                foreach ( $terms as $term ) {
                    if (!term_exists($term, $taxonomy)) {
                        wp_insert_term( $term, $taxonomy );
                    }
                }
            }            
        }
    }

    public function user_profile_field( $user ) 
    {
        $taxs = $this->createTaxList();
        if(!empty($taxs)){
            ?>
            <h3>Registration Taxonomy Fields</h3>
            <?php
            foreach($taxs as $taxonomy=>$tax){
                if(!$tax['edit']) continue;                              
                $terms = get_terms( $taxonomy, array( 'hide_empty' => false ) );
                $user_term = array_shift( wp_get_object_terms( $user->ID, $taxonomy, array( 'fields' => 'ids' ) ) );
                ?>                
                <table class="form-table">
                    <tr>
                        <th>
                            <label for="<?php echo $taxonomy;?>"><?php echo $tax['label'];?></label>
                        </th>
                        <td>
                            <select id="<?php echo $taxonomy;?>" name="<?php echo $taxonomy;?>">
                                <option>Please select</option>
                                <?php foreach( $terms as $term ) : ?>
                                    <option value="<?php echo $term->term_id; ?>" <?php selected( $term->term_id, $user_term ); ?>>
                                        <?php echo $term->name; ?>
                                    </option>
                                <?php endforeach; ?>
                            </select><br />
                            <span class="description">Please select <?php echo strtolower($tax['label']);?> here.</span>
                        </td>
                    </tr>
                </table>
                <?php
            }
        }
    }

    public function user_profile_field_save( $user_id )
    {
        if (!current_user_can( 'edit_user', $user_id ) ) {
            return false;
        }

        $taxs = $this->createTaxList();
        if(!empty($taxs)){
            foreach($taxs as $taxonomy=>$tax){
                if(!$tax['edit']) continue;

                if(isset($_POST[$taxonomy])){
                    self::assignTaxToUser($user_id,$taxonomy,'',$_POST[$taxonomy]);
                      if($taxonomy==='chat_status'){
                        $blocked = wp_get_post_terms($user_id, 'blocked_status')[0]->name;
                        if(!is_wp_error($blocked)&&$blocked==='Not Blocked'&&$_POST[$taxonomy]==='Please select'){
                           $data=array(
                            "uidsToActivate"=>[strval($user_id)],
                          );
                           $data=(object)$data;
                           if (function_exists('callCometChatProAPI')) {
                              $return_result=callCometChatProAPI("users",$data,"PUT");
                           }
                      }}
                }                
            }
        }        
    }

    public function manage_users_columns( $columns )
    {
        $columns['chat_status'] = 'Chat Status';
        return $columns;
    }

    public function manage_users_custom_column( $value, $column_name, $user_id )
    {
        if ( 'chat_status' == $column_name ) {
            $terms = wp_get_object_terms( $user_id, 'chat_status', array( 'fields' => 'names' ) );
            $value = ! is_wp_error( $terms ) && ! empty( $terms ) ? join( ', ', $terms ) : 'N/A';
        }
        return $value;
    }

    private function getTaxFilters()
    {
        $filters = ['activity_status'=>'Activity Status',
                    'blocked_status'=>'Block Status',  
                    'profile_type'=>'Verification Status',
                    'chat_status'=>'Chat Status',
                    'age_group'=>'Age Group',                    
                 ];
        return $filters;         
    }

    public function add_filter($which)
    {
		global $wpdb;
       
		if($which=='bottom'){
            return;
        }		

        echo '<div id="lw_user_filters">';
            $html='';
            $filters = $this->getTaxFilters();
            if(!empty($filters)){
                foreach($filters as $taxonomy_name=>$label){
                    $terms = get_terms( $taxonomy_name, array( 'hide_empty' => false ) );
                    if(!empty($terms) && !is_wp_error( $terms )){
                        $nameKey = 'lw_filter_'.$taxonomy_name.'_'.$which;
                        $html.= '<div class="frm-grp"><label>'.$label.'</label><select name="'.$nameKey.'" style="float:none;margin-right:10px;">';
                        $html.= '<option value="">Please Select</option>';
                        $selectedVal = (!empty($_GET[$nameKey]))?$_GET[$nameKey]:'';    
                        foreach($terms as $term){
                            $selected = ($selectedVal==$term->term_id)?'selected="selected"':'';
                            $html.= '<option value="'.$term->term_id.'" '.$selected.'>'.$term->name.'</option>';
                        }
                        $html.= '</select></div>';
                    }
                }
            }
            
            $bpmembertype = $wpdb->get_results("select *from ".$wpdb->prefix."posts where post_type='bp-member-type' and post_status='publish'",ARRAY_A);
			if(!empty($bpmembertype)){
				$html.= '<div class="frm-grp"><label>Profile Type</label><select name="lw-bp-member-type" style="float:none;margin-right:10px;">';
                        $html.= '<option value="">Please Select</option>';
                        
				foreach($bpmembertype as $single){
						   $selectedVal = (!empty($_GET['lw-bp-member-type']))?$_GET['lw-bp-member-type']:'';
						   $selected = ($selectedVal==$single['post_name'])?'selected="selected"':'';
                           $html.= '<option value="'.$single['post_name'].'" '.$selected.'>'.$single['post_title'].'</option>';
                }
				  $html.= '</select></div>';	
			}
			
            echo $html;
            echo '<div class="frm-grp">';
            submit_button(__( 'Filter' ), null, $which, false);
            echo '</div>';

        echo '</div>';
        ?>
        <style>
        #lw_user_filters { display: block; background: #ccc; padding: 15px; } 
        #lw_user_filters .frm-grp { display: inline-block; vertical-align:top } 
        #lw_user_filters .frm-grp label { display:block; font-weight:bold; margin-bottom:5px } 
        #lw_user_filters .frm-grp input[type="submit"]{margin-top: 22px;}
        </style>
        <script>
        jQuery( document ).ready(function() {
            try{
                jQuery("#lw_user_filters").insertBefore(".wp-list-table.users");
            }
            catch(err) {}
        });				
	    </script>
        <?php
    }

    public function process_filter($query)
    {
        global $pagenow;
        if(is_admin() && 'users.php' == $pagenow) {
            $finalIncludeUsers = [];
            $isTaxFiltered = false;
            $filters = $this->getTaxFilters();            
            if(!empty($filters)){
                $includeUsers = [];
                foreach($filters as $taxonomy_name=>$label){
                    $top = (!empty($_GET['lw_filter_'.$taxonomy_name.'_top']))?$_GET['lw_filter_'.$taxonomy_name.'_top']:'';
                    $bottom = (!empty($_GET['lw_filter_'.$taxonomy_name.'_bottom']))?$_GET['lw_filter_'.$taxonomy_name.'_bottom']:'';
                    if(!empty($top) OR !empty($bottom)){
                        $selection = !empty($top) ? $top : $bottom;
                        $user_ids = get_objects_in_term( $selection,$taxonomy_name);
                        $includeUsers[$taxonomy_name] = ['0'];
                        if(!empty($user_ids)){
                            $includeUsers[$taxonomy_name] = array_merge($includeUsers[$taxonomy_name],$user_ids);
                        }
                        $isTaxFiltered = true;
                    }
                }
                if(!empty($includeUsers)){
                    $firstStep = true;
                    foreach($includeUsers as $taxonomy_name=>$users){
                      if($firstStep){
                        $finalIncludeUsers = $users;
                      }
                      else{
                        $finalIncludeUsers = array_intersect($finalIncludeUsers,$users);
                      }                      
                      $firstStep = false;
                    }
                }
            }
        }

        if(isset($isTaxFiltered)  && $isTaxFiltered && empty($finalIncludeUsers)){
            $finalIncludeUsers = [0];
        }

        if(!empty($query->query_vars['include']) && !empty($finalIncludeUsers)){
            $finalIncludeUsers = array_intersect($finalIncludeUsers,$query->query_vars['include']);
        } 

        $isBpMemberFiltered = false;
        $bpMemberType = (!empty($_GET['lw-bp-member-type'])) ? $_GET['lw-bp-member-type'] : '';
        if(!empty($bpMemberType)){
            if(!empty(bp_get_member_type_object( $bpMemberType ))){
                $type = bp_get_term_by( 'slug', $bpMemberType, bp_get_member_type_tax_name() );
                if (!empty($type->term_id)){                    
					$user_ids = bp_get_objects_in_term( $type->term_id, bp_get_member_type_tax_name() );
                    $isBpMemberFiltered = true;
                    if($user_ids && !is_wp_error( $user_ids ) ) {   
                        if(!empty($finalIncludeUsers)){
                            $finalIncludeUsers = array_intersect($finalIncludeUsers,(array) $user_ids);
                        }   
                        else{
                            $finalIncludeUsers = (array) $user_ids;
                        }
                    }
                    else{
                        $finalIncludeUsers = [0]; 
                    }
				}
            }
        }

        if($isBpMemberFiltered && empty($finalIncludeUsers)){
            $finalIncludeUsers = [0];
        }

        if(!empty($finalIncludeUsers)){
            $query->set('include',$finalIncludeUsers);
        }
    }

    private function getDateByYearsOld($years)
    {
        $date = date('Y-m-d',strtotime('-'.$years.' year'));
        return $date;
    }    

    public function cronToSetTwentyOneYearsUserBlockedStatus()
    {
        global $wpdb;
        $date = $this->getDateByYearsOld('21');
        $userMetaTable = $wpdb->prefix . "usermeta";        
        $results = $wpdb->get_results( "SELECT DISTINCT user_id FROM ".$userMetaTable." WHERE meta_key='lw_registration_birthday' AND meta_value='".$date."'",ARRAY_A);
        if(!empty($results)){
            foreach($results as $result){
                self::assignTaxToUser($result['user_id'],'blocked_status','Blocked Graduated');
            }
        }       
    }

    public function cronToDisableAccessAfterTwentyFourHours()
    {
		global $lw_general_settings;
        $meta_query = array(
            'relation' => 'AND',
            array(
                'relation' => 'OR',
                array(
                    'key'     => 'lw_form_type',
                    'value'   => 'form_a',
                    'compare' => '='
                ),
                array(
                    'key'     => 'lw_form_type',
                    'value'   => 'form_a_direct',
                    'compare' => '='
                ),
            ),
            array(
                'key'     => 'lw_24_hours_access_blocked',
                'compare' => 'NOT EXISTS'
            ),               
        );
		
		$known_to_stl_registration_expired_hours = 24;
		if($lw_general_settings['known_to_stl_registration_expired_hours']>0){
			$known_to_stl_registration_expired_hours = $lw_general_settings['known_to_stl_registration_expired_hours'];	
		}
		$current_time = current_time('timestamp', true); // Get current server timestamp without considering WordPress time zone setting
        $date_query = array(array('before' => date('Y-m-d H:i:s', strtotime("-{$known_to_stl_registration_expired_hours} hours", $current_time)), 'inclusive' => false));

        // $date_query = array(array('before'  => $known_to_stl_registration_expired_hours.' hours ago', 'inclusive' => false));

        $args = array( 'role__in'   => array( 'subscriber' ),
                       'meta_query' => $meta_query,
                       'date_query' => $date_query,
                       'orderby'    => 'ID',
                       'order'      => 'DESC',
                       'fields'     => 'ID',
                      );
        $users = get_users($args);
        if(!empty($users)){
            foreach($users as $user_id){
                //check if user has verified taxonomy,hudson-start      
                $terms = wp_get_object_terms( $user_id, 'profile_type', array( 'fields' => 'names' ) );
                if ( ! is_wp_error( $terms ) && in_array( 'Verified', $terms ) ) {
                    continue;
                }
                //hudson-end        
                self::assignTaxToUser($user_id,'profile_type','Pending With No Access');
                self::assignTaxToUser($user_id,'blocked_status','Blocked Unverified');
                //call crm to update user
                do_action('lw_crm_sync_edit',$user_id);
                
               // if(class_exists('BP_Suspend_Member')){
                   // BP_Suspend_Member::suspend_user( $user_id );
                //}
                update_user_meta($user_id,'lw_24_hours_access_blocked','yes');
            }
        }
    }

    public function cronToClearChatTempBlockedStatus()
    {
          $currentTime = time();
          $meta_query = array(
            'relation' => 'AND',
            array(
                'key'     => 'lw_chat_status_temp_blocked_time',
                'value'   => $currentTime,
                'compare' => '<='
            ),
            array(
                'key'     => 'lw_chat_status_temp_blocked_time',
                'compare' => 'EXISTS'
            ),               
        );
        $args = array( 'meta_query' => $meta_query,
                       'orderby'    => 'ID',
                       'order'      => 'DESC',
                       'fields'     => 'ID',
                      );
        $users = get_users($args);
        if(!empty($users)){
            foreach($users as $user_id){
                self::removeTaxToUser($user_id,'chat_status','Chat Block Temporarily'); 
                delete_user_meta($user_id,'lw_chat_status_temp_blocked_time');
                 //re-activate the user in cometchat
                $data=array(
                    "uidsToActivate"=>[strval($user_id)],
                );
                $data=(object)$data;
                  if (function_exists('callCometChatProAPI')) {
                    $return_result=callCometChatProAPI("users",$data,"PUT");
                  }
                
            }
        }
    }    

    public static function isUserChatBlocked($user_id)
    {
        $return = false;
        $terms = wp_get_object_terms( $user_id, 'chat_status', array( 'fields' => 'names' ) );
        if(!is_wp_error($terms) && !empty($terms)){
            if(in_array('Chat Block Temporarily',$terms) || in_array('Chat Block Permanently',$terms)){
                $return = true;
            }
        }
        return $return;
    }

    public static function assignTaxToUser($user_id,$taxonomy,$tax_value='',$tax_value_id=0)
    {
        if(empty($tax_value_id)){
            $term = get_term_by('name',$tax_value , $taxonomy);
            if(!empty($term->term_id)){
                $tax_value_id = $term->term_id;            
            }
        }        

        if(!empty($tax_value_id)){
            wp_set_object_terms( $user_id,(int)$tax_value_id , $taxonomy, false );
            clean_object_term_cache( $user_id, $taxonomy );
        }
    }

    public static function removeTaxToUser($user_id,$taxonomy,$tax_value='',$tax_value_id=0)
    {
        if(empty($tax_value_id)){
            $term = get_term_by('name',$tax_value , $taxonomy);
            if(!empty($term->term_id)){
                $tax_value_id = $term->term_id;            
            }
        }       

        if(!empty($tax_value_id)){
            wp_remove_object_terms( $user_id,(int)$tax_value_id,$taxonomy);
            clean_object_term_cache( $user_id, $taxonomy );
        }
    }

    public function processUserBirthday($user_id,$force18Above=false)
    {
        $is18YearsOlder = false;
        if(!$force18Above){            
            $birthdate = get_user_meta($user_id,'lw_registration_birthday',true);
            if(!empty($birthdate)){
                $compareDate = $this->getDateByYearsOld(18);
                if($birthdate<=$compareDate){
                    $is18YearsOlder = true;  
                }
            }
        }
        else{
            $is18YearsOlder = true;
        }        
        $age_value = ($is18YearsOlder) ? '18 And Above' : 'Less Than 18';
        self::assignTaxToUser($user_id,'age_group',$age_value);     
    }
    
    public function cronToSetUserAgeTaxonomy()
    {
        $compareDate = $this->getDateByYearsOld(18);
        $meta_query = array(
            'relation' => 'AND',
            array(
                'key'     => 'lw_registration_birthday',
                'value'   => $compareDate,
                'compare' => '='
            )             
        );
        $args = array( 'role__in'   => array( 'subscriber' ),
                       'meta_query' => $meta_query,
                       'orderby'    => 'ID',
                       'order'      => 'DESC',
                       'fields'     => 'ID',
                      );
        $users = get_users($args);
        if(!empty($users)){
            foreach($users as $user_id){                
                $this->processUserBirthday($user_id,true);
            }
        }
    }

    public function restAPiRoutes()
    {
		
		
		global $invitationTypes,$lw_general_settings,$current_user;

		$roles = $current_user->roles;
		if(in_array("administrator",$roles) || in_array("editor",$roles)){
		
			//wp-json/cometchat/deactivate-temporarily/?user_id=123
			register_rest_route( 'cometchat', '/deactivate-temporarily', array(
				'methods' => 'GET',
				'callback' => array(&$this,'callbackToComechatDeactivateTempoararily'),
				'permission_callback' => '__return_true',
			));
	
			//wp-json/cometchat/deactivate-permanently/?user_id=123
			register_rest_route( 'cometchat', '/deactivate-permanently', array(
				'methods' => 'GET',
				'callback' => array(&$this,'callbackToComechatDeactivatePermanently'),
				'permission_callback' => '__return_true',
			));
		}
    }

    public function callbackToComechatDeactivateTempoararily(WP_REST_Request $request)
    {
        $responce = ['success'=>false];
        $user_id = $request->get_param( 'user_id' );
        if(!empty($user_id)){
            self::assignTaxToUser($user_id,'chat_status','Chat Block Temporarily');
            $chatTempBlockTill = strtotime('+'.self::CHAT_TEMP_BLOCK_MINUTES.' minutes');
            update_user_meta($user_id,'lw_chat_status_temp_blocked_time',$chatTempBlockTill);
            //call cometchat api
            if(function_exists('deactivate')){
                $return_result=deactive(strval($user_id));
                $response_code = wp_remote_retrieve_response_code( $result );
                if(200 <= $response_code && $response_code < 300){
                    $responce = ['success'=>true];  
                } 
            }
            
        }
        return new WP_REST_Response( $responce );      
    }

    public function callbackToComechatDeactivatePermanently(WP_REST_Request $request)
    {
        $responce = ['success'=>false];
        $user_id = $request->get_param( 'user_id' );

        if(!empty($user_id)){
            self::assignTaxToUser($user_id,'chat_status','Chat Block Permanently');
            //call cometchat api
            if(function_exists('deactivate')){
                $return_result=deactive(strval($user_id));
                $response_code = wp_remote_retrieve_response_code( $result );
                if(200 <= $response_code && $response_code < 300){
                    $responce = ['success'=>true];  
                }
            }
  
           
        }
        return new WP_REST_Response( $responce );
    }
}
new lwAdminUsers();
}
?>