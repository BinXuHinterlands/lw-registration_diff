<div class='lw-wrapper login-page'>
<?php 
global $invitationTypes,$lw_general_settings,$current_user,$wpdb;
if($current_user->ID>0){
	wp_redirect(get_the_permalink($lw_general_settings['login_redirect']));
	exit;
}
include("lw_leftsidebar.php"); ?>
					
<div class="lw-width50 lw-white-bg">
   <div class="lw-forma-wrapper lw-form-frame">
      <div class="lw-form-frame-inner">
         <div class="lw-forma-wrapper">
            <form id="LwLoginForm" class="lw-form" method="post">
               <h3>Log In</h3>
               <input type="hidden" name="action" value="lw_login_action" />
               <div class="lw-form-group">
                  <label>Username<span class="lw_required_label">*</span><div class="lw-tooltip">?<span class="lw-tooltiptext">Username is required</span></div></label>
                  <input type="text" placeholder="Username"  class="required"  name="lw_username">
               </div>
               <div class="lw-form-group">
                  <label>Password<span class="lw_required_label">*</span><div class="lw-tooltip">?<span class="lw-tooltiptext">Password is required</span></div></label>
                  <input type="password" placeholder="Password"    class="required" name="lw_password" id="lw_password">
                     <i class="far fa-eye" id="lwtogglePassword"></i>
               </div>
               <div class="lw-form-group lw_fogot-paassowd-block">
                  <a href="javascript:void(0)" class="lw_action_link" data-type="LwForgotPasswordForm">Forgot Password</a>
               
               </div>
               
               <div class="lw-np-btn yp-mt-0"> <a href="/">Cancel</a> <input type="submit" value="Log In" name="submit">
               </div>
            </form>
            <form id="LwForgotPasswordForm" style="display:none;" class="lw-form" method="post">
               <h3>Forgot Password</h3>
               <input type="hidden" name="action" value="lw_forgot_password_action" />
               <div class="lw-form-group">
                  <label>Email Address<span class="lw_required_label">*</span><div class="lw-tooltip">?<span class="lw-tooltiptext">Email Address is required</span></div></label>
                  <input type="text" placeholder="Email Address"  class="required"  name="lw_email_address">
               </div>
               <div class="lw-form-group lw_fogot-paassowd-block">
                  <a href="javascript:void(0)" class="lw_action_link" data-type="LwLoginForm">Back to Login</a>
               
               </div>
               
               <div class="lw-np-btn lw-form-group yp-mt-0"> <input type="submit" value="Submit" name="submit">
               </div>
            </form>
         </div>
      </div>
   </div>
</div>
</div>
