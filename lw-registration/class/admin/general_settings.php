<?php
$args = array('post_type' => 'page', 'post_status' => 'publish', 'posts_per_page' => '-1', 'orderby' => 'title', 'order' => 'asc',);
$loop = new WP_Query($args);
$all_pages = array();
$i = 0;
foreach ($loop->posts as $single) {
    $all_pages[$i]['id'] = $single->ID;
    $all_pages[$i]['title'] = $single->post_title;
    $i++;
}

if (isset($_REQUEST['lw_general_settings_submit']) && $_REQUEST['lw_general_settings_submit'] !== "") {
    $settings = isset($_REQUEST['lw_general_settings']) ? $_REQUEST['lw_general_settings'] : array();
    // Normalize pronouns options from comma-separated string to array
    if (isset($settings['pronouns_options'])) {
        if (is_string($settings['pronouns_options'])) {
            $pronouns_arr = array_map('trim', explode(',', $settings['pronouns_options']));
            // Remove empty values and duplicates while preserving order
            $pronouns_arr = array_values(array_filter($pronouns_arr, function($v){ return $v !== ''; }));
            $seen = array();
            $normalized = array();
            foreach ($pronouns_arr as $p) {
                $key = strtolower($p);
                if (!isset($seen[$key])) {
                    $normalized[] = $p; // keep original case for display
                    $seen[$key] = true;
                }
            }
            $settings['pronouns_options'] = $normalized;
        }
    }
    // Enforce minimum pronouns count (>= 2) and default fallback
    $default_pronouns = array('She', 'Her');
    if (!isset($settings['pronouns_options']) || !is_array($settings['pronouns_options']) || count($settings['pronouns_options']) < 2) {
        $error_message = __('Please enter at least two pronouns. Default "She, Her" has been applied.', 'lw_registration');
        $settings['pronouns_options'] = $default_pronouns;
    }
    // Removed pronouns_max enforcement; no longer used
    update_option('lw_general_settings', $settings);
    $message= __('Settings successfully updated.', 'lw');
}
$lw_general_settings = get_option('lw_general_settings');
$lw_registration_email_settings  = get_option('lw_registration_email_settings');
// Ensure default pronouns exist for display if option is missing or invalid
$default_pronouns = array('She', 'Her');
if (!isset($lw_general_settings['pronouns_options']) || !is_array($lw_general_settings['pronouns_options']) || count($lw_general_settings['pronouns_options']) < 2) {
    $lw_general_settings['pronouns_options'] = $default_pronouns;
}


$emailList=array
(
	array('title'=>'LW Registration Invitation Known To Starlight','id'=>'lw_registration_invitation_known_to_starlight','dynamic_labels'=>array('first_name','last_name','email','site_url','site_name','registration_url')),
	array('title'=>'LW Registration Invitation Known To Wish Granting','id'=>'lw_registration_invitation_known_to_wish_granting','dynamic_labels'=>array('first_name','last_name','email','site_url','site_name','registration_url')),
	
	
	array('title'=>'LW Registration Form A','id'=>'lw_registration_form_a','dynamic_labels'=>array('first_name','last_name','email','site_url','site_name','username')),
	array('title'=>'LW Registration Form B','id'=>'lw_registration_form_b','dynamic_labels'=>array('first_name','last_name','email','site_url','site_name','username')),
	array('title'=>'LW Registration Form C','id'=>'lw_registration_form_c','dynamic_labels'=>array('first_name','last_name','email','site_url','site_name','username')),
	
);

if(isset($_POST['option_type']) && !empty($_POST['option_data'])){

	if($_POST['option_type']=='email_settings')
	{
		if(empty($lw_registration_email_settings[$_POST['option_type']]))
		{
			$lw_registration_email_settings[$_POST['option_type']]=$_POST['option_data'];
		}
		else
		{
			$lw_registration_email_settings[$_POST['option_type']]=array_merge($lw_registration_email_settings[$_POST['option_type']],$_POST['option_data']);
		}
	}
	else{
		$lw_registration_email_settings[$_POST['option_type']]=$_POST['option_data'];
	}
	update_option('lw_registration_email_settings',$lw_registration_email_settings);
}
global $wp_roles;

$lw_registration_email_settings  = get_option('lw_registration_email_settings');
?>

<div class="InvitationTitleHead"><?php echo __('LW General Settings', 'lw_registration'); ?></div>
<?php
if(isset($message) && $message!=""){
?>
<div class="updated notice notice-success  mb-2" id="message" style="margin-left:0px">
  <p><?php echo $message; ?></p>
</div>
<?php
}
// Show validation error if pronouns count was less than 2
if(isset($error_message) && $error_message!=""){
?>
<div class="notice notice-error mb-2" style="margin-left:0px">
  <p><?php echo esc_html($error_message); ?></p>
</div>
<?php
}
?>
<style>.panel.panel-primary {
	margin-bottom: 10px;
}</style>
<div id="page_Settings">
<div class="tabs_outer">
<div id="listing_settings">
  <div class="row mr-0">
  	<div class="col-md-4">
  		<div class="InvitationPackageBody">
		    <h4 class="InvitationPackageHeadingTitle d-flex align-items-baseline"><?php echo __('Settings', 'lw_registration'); ?></h4>
            
            <form role="form" id="general_settings" class="mt-4" enctype="multipart/form-data" method="post" action="">
			<div class="row">
        		
                <div class="col-md-12">
             		<div class="form-group">
						<label for="login"  class="invitation_label"><?php echo __('Login Page', 'lw_registration'); ?></label>
						 <select class="multiple-select required" style="max-width:100%; width:100%; " name="lw_general_settings[login]">

                            <option value=""><?php echo __('Select', 'lw_registration'); ?></option>

                            <?php foreach($all_pages as $single_page){ $selected='' ; if($lw_general_settings['login']==$single_page[ 'id']) { $selected="selected='selected'" ; } echo '<option value="'.$single_page[ 'id']. '" '.$selected. '>'.$single_page[ 'title']. ' (Page ID: '.$single_page[ 'id']. ')</option>'; } ?>

                        </select>
					</div>
          		</div>
                
                
                <div class="col-md-12">
             		<div class="form-group">
						<label for="login"  class="invitation_label"><?php echo __('After Login Redirect', 'lw_registration'); ?></label>
						 <select class="multiple-select required" style="max-width:100%; width:100%; " name="lw_general_settings[login_redirect]">

                            <option value=""><?php echo __('Select', 'lw_registration'); ?></option>

                            <?php foreach($all_pages as $single_page){ $selected='' ; if($lw_general_settings['login_redirect']==$single_page[ 'id']) { $selected="selected='selected'" ; } echo '<option value="'.$single_page[ 'id']. '" '.$selected. '>'.$single_page[ 'title']. ' (Page ID: '.$single_page[ 'id']. ')</option>'; } ?>

                        </select>
					</div>
          		</div>
                <div class="col-md-12">
             		<div class="form-group">
						<label for="first_name"  class="invitation_label"><?php echo __('Registrtion Page', 'lw_registration'); ?></label>
						 <select class="multiple-select required" style="max-width:100%; width:100%; " name="lw_general_settings[registration]">

                            <option value=""><?php echo __('Select', 'lw_registration'); ?></option>

                            <?php foreach($all_pages as $single_page){ $selected='' ; if($lw_general_settings['registration']==$single_page[ 'id']) { $selected="selected='selected'" ; } echo '<option value="'.$single_page[ 'id']. '" '.$selected. '>'.$single_page[ 'title']. ' (Page ID: '.$single_page[ 'id']. ')</option>'; } ?>

                        </select>
					</div>
          		</div>
                
                
                <div class="col-md-12">
             		<div class="form-group">
						<label for="first_name"  class="invitation_label"><?php echo __('Redirect Public Registrtion Page', 'lw_registration'); ?></label>
						 <select class="multiple-select required" style="max-width:100%; width:100%; " name="lw_general_settings[pending_registration]">

                            <option value=""><?php echo __('Select', 'lw_registration'); ?></option>

                            <?php foreach($all_pages as $single_page){ $selected='' ; if($lw_general_settings['pending_registration']==$single_page[ 'id']) { $selected="selected='selected'" ; } echo '<option value="'.$single_page[ 'id']. '" '.$selected. '>'.$single_page[ 'title']. ' (Page ID: '.$single_page[ 'id']. ')</option>'; } ?>

                        </select>
					</div>
          		</div>

				<div class="col-md-12">
					<div class="form-group">
						<label for="first_name" class="invitation_label"><?php echo __('Direct Starlight Registration Page', 'lw_registration'); ?></label>
						<select class="multiple-select required" style="max-width:100%; width:100%; " name="lw_general_settings[direct_starlight_registration]">
							<option value=""><?php echo __('Select', 'lw_registration'); ?></option>
							<?php foreach($all_pages as $single_page){ 
								$selected='' ; 
								if(isset($lw_general_settings['direct_starlight_registration']) && $lw_general_settings['direct_starlight_registration']==$single_page['id']) { 
									$selected="selected='selected'" ; 
								} 
								echo '<option value="'.$single_page['id'].'" '.$selected.'>'.$single_page['title'].' (Page ID: '.$single_page['id'].')</option>'; 
								} ?>
						</select>
					</div>
				</div>

				<div class="col-md-12">
					<div class="form-group">
						<label for="enable_direct_starlight_access" class="invitation_label"><?php echo __('Enable Direct Starlight Registration', 'lw_registration'); ?></label>
						<select class="form-control" name="lw_general_settings[enable_direct_starlight_access]">
							<option value="0" <?php echo (isset($lw_general_settings['enable_direct_starlight_access']) && $lw_general_settings['enable_direct_starlight_access'] == 0) ? 'selected' : ''; ?>><?php echo __('Disabled', 'lw_registration'); ?></option>
							<option value="1" <?php echo (isset($lw_general_settings['enable_direct_starlight_access']) && $lw_general_settings['enable_direct_starlight_access'] == 1) ? 'selected' : ''; ?>><?php echo __('Enabled', 'lw_registration'); ?></option>
						</select>
						<small class="form-text text-muted">Enable or disable the direct access to the Known to Starlight registration form. When disabled, the page will display a message indicating that the form is temporarily unavailable.</small>
					</div>
				</div>

				<div class="col-md-12">
					<div class="form-group">
						<label for="registration_disabled_page" class="invitation_label"><?php echo __('Registration Disabled Message Page', 'lw_registration'); ?></label>
						<select class="multiple-select" style="max-width:100%; width:100%; " name="lw_general_settings[registration_disabled_page]">
							<option value=""><?php echo __('Use Default Message', 'lw_registration'); ?></option>
							<?php foreach($all_pages as $single_page){ 
								$selected='' ; 
								if(isset($lw_general_settings['registration_disabled_page']) && $lw_general_settings['registration_disabled_page']==$single_page['id']) { 
									$selected="selected='selected'" ; 
								} 
								echo '<option value="'.$single_page['id'].'" '.$selected.'>'.$single_page['title'].' (Page ID: '.$single_page['id'].')</option>'; 
								} ?>
						</select>
						<small class="form-text text-muted">Select a page to display when Direct Starlight Registration is disabled. If no page is selected, the default message will be shown. You can create a new page with your custom content and select it here.</small>
					</div>
				</div>
                
                <div class="col-md-12">
             		<div class="form-group">
						<label for="first_name"  class="invitation_label"><?php echo __('Redirect Registrtion Known To Starlight Page', 'lw_registration'); ?></label>
						 <select class="multiple-select required" style="max-width:100%; width:100%; " name="lw_general_settings[redirect_registration_known_to_starlight]">

                            <option value=""><?php echo __('Select', 'lw_registration'); ?></option>

                            <?php foreach($all_pages as $single_page){ $selected='' ; if($lw_general_settings['redirect_registration_known_to_starlight']==$single_page[ 'id']) { $selected="selected='selected'" ; } echo '<option value="'.$single_page[ 'id']. '" '.$selected. '>'.$single_page[ 'title']. ' (Page ID: '.$single_page[ 'id']. ')</option>'; } ?>

                        </select>
					</div>
          		</div>
                
                 <div class="col-md-12">
             		<div class="form-group">
						<label for="first_name"  class="invitation_label"><?php echo __('Redirect Registrtion Known To Wish Granting Page', 'lw_registration'); ?></label>
						 <select class="multiple-select required" style="max-width:100%; width:100%; " name="lw_general_settings[redirect_registration_known_to_wish_granting]">

                            <option value=""><?php echo __('Select', 'lw_registration'); ?></option>

                            <?php foreach($all_pages as $single_page){ $selected='' ; if($lw_general_settings['redirect_registration_known_to_wish_granting']==$single_page[ 'id']) { $selected="selected='selected'" ; } echo '<option value="'.$single_page[ 'id']. '" '.$selected. '>'.$single_page[ 'title']. ' (Page ID: '.$single_page[ 'id']. ')</option>'; } ?>

                        </select>
					</div>
          		</div>
                
                <div class="col-md-12">
          			<div class="form-group">
						<label for="first_name"  class="invitation_label"><?php echo __('Invitation Page', 'lw_registration'); ?></label>
						 <select class="multiple-select required" style="max-width:100%; width:100%; " name="lw_general_settings[invitation]">

                          
                            <?php foreach($all_pages as $single_page){ $selected='' ; if($lw_general_settings['invitation']==$single_page[ 'id']) { $selected="selected='selected'" ; } echo '<option value="'.$single_page[ 'id']. '" '.$selected. '>'.$single_page[ 'title']. ' (Page ID: '.$single_page[ 'id']. ')</option>'; } ?>

                        </select>
					</div>
          		</div>
          		<div class="col-md-12">
              	<div class="form-group">
						<label for="token_expire_hours" class="invitation_label"><?php echo __('Who is access invite page?', 'lw_registration'); ?></label>
						<select class="form-control multiple-select required" style="max-width:100%;" multiple="multiple" name="lw_general_settings[invitation_access][]">

                          
                            <?php foreach($wp_roles->roles as $key=>$single){ 
							$selected='' ; 
							if(isset($lw_general_settings['invitation_access']) && in_array($key,$lw_general_settings['invitation_access'])) { 
								$selected="selected='selected'" ; 
							} 
							echo '<option value="'.$key. '" '.$selected. '>'.$single['name'].'</option>'; 
						} ?>

                        </select>
					</div>
          		</div>
                <div class="col-md-12">
           			<div class="form-group">
						<label for="token_expire_hours" class="invitation_label"><?php echo __('Token Expire Hours', 'lw_registration'); ?></label>
						<input type="number" name="lw_general_settings[token_expire_hours]" value="<?php echo isset($lw_general_settings['token_expire_hours'])?$lw_general_settings['token_expire_hours']:""; ?>" id="token_expire_hours"  class="form-control number required"/>
					</div>
          		</div>
                
                
                <div class="col-md-12">
           			<div class="form-group">
						<label for="token_expire_hours" class="invitation_label"><?php echo __('Number of Login Close', 'lw_registration'); ?></label>
						<input type="number" name="lw_general_settings[number_of_login_close]" value="<?php echo isset($lw_general_settings['number_of_login_close'])?$lw_general_settings['number_of_login_close']:""; ?>" id="number_of_login_close"  class="form-control number required"/>
					</div>
          		</div>
                
                <div class="col-md-12">
           			<div class="form-group">
						<label for="cc_cmail_recipient" class="invitation_label"><?php echo __('CC Email Recipient', 'lw_registration'); ?></label>
						<input type="email" name="lw_general_settings[cc_cmail_recipient]" value="<?php echo isset($lw_general_settings['cc_cmail_recipient'])?$lw_general_settings['cc_cmail_recipient']:""; ?>" id="cc_cmail_recipient"  class="form-control email"/>
					</div>
          		</div>

				<hr style="width: 100%; border-color: #ccc;">
          		
          		<!-- New: Pronouns configuration fields -->
          		<div class="col-md-12">
          			<div class="form-group">
          				<label class="invitation_label"><?php echo __('Pronouns Options', 'lw_registration'); ?></label>
          				<input type="text" name="lw_general_settings[pronouns_options]" value="<?php 
           					$pronouns_display = (isset($lw_general_settings['pronouns_options']) && is_array($lw_general_settings['pronouns_options'])) ? implode(', ', $lw_general_settings['pronouns_options']) : 'She, Her';
           					echo esc_attr($pronouns_display); ?>" class="form-control"/>
           				<small class="form-text text-muted"><?php echo __('Enter pronouns separated by commas (at least 2 required). Example: She, Her, He, Him, They, Them. If left empty or fewer than 2, default "She, Her" will be used.', 'lw_registration'); ?></small>
          			</div>
          		</div>
          		

            </div>
            
           <div class="row">
				<div class="col-md-12 text-center" >
                	<button type="submit" class="btn btn-dark" name="lw_general_settings_submit" value="save changes">SAVE CHANGES</button>
        		</div>
         	</div>      
        </form>
		  </div>
     </div>
     <div class="col-md-8">
  		<div class="InvitationPackageBody">
		    <h4 class="InvitationPackageHeadingTitle d-flex align-items-baseline"><?php echo __('Email Settings', 'lw_registration'); ?></h4>
    
    <div class="panel-group mt-3" id="accordion">
      
      <?php if(!empty($emailList)){
foreach($emailList as $single){	  
?>
      <div class="panel panel-primary">
        <div class="panel-heading">
          <h4 class="panel-title"> <a data-toggle="collapse" data-parent="#accordion" href="#collapse<?php echo $single['id'];?>"><span class="dashicons dashicons-email"></span> <?php echo $single['title'];?></a> </h4>
        </div>
        <div id="collapse<?php echo $single['id'];?>" class="panel-collapse collapse <?php if(isset($_POST['option_data'][$single['id']])){ echo 'in';}?> ">
          <div class="panel-body">
            <form role="form" class="txForm validateForm" method="post" action="">
              <div class="form-group">
                <label for="subject">Subject</label>
                <input type="text" class="form-control required"  name="option_data[<?php echo $single['id'];?>][subject]" placeholder="Enter email subject..." value="<?php echo isset($lw_registration_email_settings['email_settings'][$single['id']]['subject']) ?stripslashes($lw_registration_email_settings['email_settings'][$single['id']]['subject']):"";?>">
              </div>
              <div class="form-group">
                <label for="pwd" style="display:block; width:100%">Content</label>
                <?php foreach($single['dynamic_labels'] as $singleLabel){?>
                <div class="changable_string">%<?php echo $singleLabel;?>%</div>
                <?php }?>
                <!--<div class="changable_string">%siteUrl%</div>
    <div class="changable_string">%siteName%</div>  -->
                <textarea class="form-control required"  name="option_data[<?php echo $single['id'];?>][html]" placeholder="Enter email html..." style="height:200px !important"><?php echo isset($lw_registration_email_settings['email_settings'][$single['id']]['html']) ? stripslashes($lw_registration_email_settings['email_settings'][$single['id']]['html']):"";?></textarea>
              </div>
              <input type="hidden" name="option_type" value="email_settings" />
              <button type="submit" name="lw_email_settings_submit" class="btn btn-dark">Save</button>
            </form>
          </div>
        </div>
      </div>
      <?php }}?>
    </div>
 
</div></div>
	</div>
</div>
</div>
</div>

