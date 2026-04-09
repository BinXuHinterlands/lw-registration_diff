<?php 
global $invitationTypes,$lw_general_settings,$current_user;

$roles = $current_user->roles;
$access_page = 1;
if(empty($roles)){
	$access_page = 0;
}else{ 

	$access_page = 0;
 	foreach($roles as $single){
		if(in_array($single,$lw_general_settings['invitation_access'])){
				$access_page = 1;
				break;
		}		
	}
}
if($access_page==0){
	echo "<div class='not_access'>Sorry you don't have access this page.</div>";

}else{
?>
<div class="lw-wrapper">
		<div class="lw-container">
			<div class="lw-formc-wrapper">
				<form id="LwInvitationForm"  method="post">
					<input type="hidden" name="action" value="lw_invitation_registration_action" />
                    <div class="lw-form-group">
						<div class="lw-row">
							<div class="lw-width3-column lw-col">
								<label>First Name</label>
								<input type="text"  class="required lettersonly" placeholder="First Name"  name="first_name">
							</div>
							<div class="lw-width3-column lw-col">
								<label>Last Name</label>
								<input type="text" class="required lettersonly" placeholder="Last Name"  name="last_name">
							</div>
                            
							<div class="lw-width3-column lw-col">
								<label>Name of Starlight team member/s sending invite:</label>
								<input type="text" class="required" placeholder="Name of Starlight team member/s sending invite"  name="staff_name">
							</div>
                            
						</div>
					</div>
					
					<div class="lw-form-group">
						<div class="lw-row">
							<div class="lw-width50 lw-col">
								<label>Email</label>
								<input type="email" class="required validateEmail email" placeholder="Email Address"  name="email">
							</div>
                            <div class="lw-width50 lw-col">
								<label>Invitation Type</label>
								<select class="lw-w100 required" name="invitation_type" id="invitation_type">
                            		<option value="">Please select</option>
									<?php foreach($invitationTypes as $k=>$v){?>
                            			<option value="<?php echo $k; ?>" <?php echo (isset($_REQUEST['mode']) && $_REQUEST['mode']==$k)?"selected":"" ?>><?php echo $v; ?></option>	
                            		<?php }?>
                        		</select>
							</div>
							
						</div>
					</div>
                    
                    
                    
                    <div class="lw-form-group lw-checkbox-custome lw-checkbox-custome-tc">
                     <input type="checkbox" id="lw_generate_link" name="lw_generate_link" value="1" >
                     <label for="lw_generate_link"> Open the sign-up form on this device to complete side-by-side, instead of emailing it
</label>
                  </div>
                      <div class="lw-np-btn text-center yp-mt-0"> <input type="submit" value="Submit" name="submit">
               </div>
					
				</form>
			</div>
		</div>
	</div>
    
 <?php } ?>   