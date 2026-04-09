<?php
$lw_general_settings = get_option('lw_general_settings');
?>
<div class="lw-width50 lw-white-bg">
   <div class="lw-forma-wrapper lw-form-frame" id="lw-rego-form-known-to-starlight">
      <div class="lw-form-frame-inner">
         <h2>Become a member</h2>
         <div class="lw-forma-wrapper">
            <form id="LwRegistrationForm" class="lw-form" method="post">
               <input type="hidden" name="lw_form_type" id="lw_form_type" value="form_a_direct" />
               <input type="hidden" name="lw_invitation_id" value="<?php echo isset($prePouplateData['id'])?$prePouplateData['id']:"" ?>" />
               <input type="hidden" name="action" value="lw_registration_action" />
               <div class="lw-tab2">
                  <div class="lw_tablinks active" id="step1" data-step="1">
                     <h2>Step 1</h2>
                     <span>About You</span> 
                  </div>
                  <div class="lw_tablinks" id="step2" data-step="2">
                     <h2>Step 2</h2>
                     <span>Emergency Contact</span> 
                  </div>
                  <div class="lw_tablinks" id="step3" data-step="3">
                     <h2>Step 3</h2>
                     <span>Your Account</span> 
                  </div>
               </div>
               <div class="lw_tabcontent">
                  <div class="lw-form-group">
                       <label>Your First Name<span class="lw_required_label">*</span><div class="lw-tooltip">?<span class="lw-tooltiptext">Please enter your legal name as it appears on your ID, and on your first login you can share more about yourself such as a preferred name</span></div></label>
                 
                     <input type="text" value="<?php echo isset($prePouplateData['first_name'])?stripcslashes($prePouplateData['first_name']):""; ?>"  placeholder="Your First Name"  class="required lettersonly"  name="lw_first_name" id="lw_first_name">
                  </div>
                  <div class="lw-form-group">
                     <label>Your Last Name<span class="lw_required_label">*</span><div class="lw-tooltip">?<span class="lw-tooltiptext">Please enter your legal name as it appears on your ID, and on your first login you can share more about yourself such as a preferred name</span></div></label>
                     <input type="text" value="<?php echo isset($prePouplateData['last_name'])?stripcslashes($prePouplateData['last_name']):""; ?>"  placeholder="Your Last Name" class="required lettersonly"  id="lw_last_name" name="lw_last_name">
                  </div>
                  <div class="lw-form-group">
                     <label>Your Pronouns <div class="lw-tooltip">?<span class="lw-tooltiptext">Pronouns are what we use to refer to a person other than their name, such as They, She and He. Feel free to share your pronouns here</span></div></label>
                     <select name="lw_registration_pronouns[]" class="lw-pronouns-select" multiple data-placeholder="Your Pronouns">
                        <?php 
                        $pronouns_options = (isset($lw_general_settings['pronouns_options']) && is_array($lw_general_settings['pronouns_options'])) ? $lw_general_settings['pronouns_options'] : array('She','Her','He','Him','They','Them');
                        foreach ($pronouns_options as $opt) {
                            echo '<option value="'.esc_attr($opt).'">'.esc_html($opt).'</option>';
                        }
                        ?>
                     </select>
                  </div>
				
                  <div class="lw-form-group">
                        <label>Your Birthday<span class="lw_required_label">*</span><div class="lw-tooltip">?<span class="lw-tooltiptext">Birthday is required</span></div></label>
                        <?php /*?><div class="lw_birthday-age-error">
                       
                         </div><?php */?>
                        <div class="lw-row birthday-block">
                           <div class="lw-width33 lw-col">
                              <label>Day</label>
                              <select name="lw_birthday_day" class="lw_birthday lw_birthday_day">
                              <option value="">Day</option>
                                 <?php for($i=1;$i<=31;$i++){
                                    $day = $i;
                                    if(strlen($i)==1){
                                    	$day = "0".$i;
                                    }
                                    ?>
                                 <option value="<?php echo $day; ?>"><?php echo $day; ?></option>
                                 <?php 		
                                    }?>
                              </select>
                           </div>
                           <div class="lw-width33 lw-col">
                              <label>Month</label>
                              <select name="lw_birthday_month" class="lw_birthday lw_birthday_month">
                              <option value="">Month</option>
                                 <?php for($i=1;$i<=12;$i++){
                                    $day = $i;
                                    if(strlen($i)==1){
                                    	$day = "0".$i;
                                    }
                                    ?>
                                 <option value="<?php echo $day; ?>"><?php echo $day; ?></option>
                                 <?php 		
                                    }?>
                              </select>
                           </div>
                           <div class="lw-width33 lw-col">
                              <label>Year</label>
                              <select name="lw_birthday_year" class="lw_birthday lw_birthday_year">
                              <option value="">Year</option>
                                 <?php 
								 	$prevYear = date('Y', strtotime('-21 year'));
									for($i=0;$i<10;$i++){
                                    	$year = $prevYear+$i;
                                    ?>
                                 <option value="<?php echo $year ; ?>"><?php echo $year ; ?></option>
                                 <?php 		
                                    }?>
                              </select>
                           </div>
                           <label class="birthday-error-all error"></label>
                        </div>
                     </div>
                     <div class="lw-form-group">
                     <label>Your Email Address<span class="lw_required_label">*</span><div class="lw-tooltip">?<span class="lw-tooltiptext">Please enter your personal email, if you have one, instead of your school email (so you can receive our messages)

</span></div></label>
                     <input type="email" placeholder="Your Email Address" value="<?php echo isset($prePouplateData['email'])?stripcslashes($prePouplateData['email']):""; ?>" class="required email validateEmail validateEmailEdu"  id="lw_email_address" name="lw_registration_email_address">
                  </div>
                  <div class="lw-form-group">
                     <label>Who connected you to Livewire<span class="lw_required_label">*</span> <div class="lw-tooltip">?<span class="lw-tooltiptext">Letting us know who connected you with Livewire helps us set up your account and thank them.</span></div></label>
                     <input type="text" class="required lettersonly" placeholder="Who shared this form with you (name)" name="lw_referral_source">
                  </div>
                  <div class="lw-form-group">
                     <label>State / Location<span class="lw_required_label">*</span><div class="lw-tooltip">?<span class="lw-tooltiptext">your state / location helps us schedule any calls in your time zone.</span></div></label>
                     <select name="lw_state" class="required">
                        <option value="">Select State / Location</option>
                        <option value="NSW">NSW - New South Wales</option>
                        <option value="VIC">VIC - Victoria</option>
                        <option value="QLD">QLD - Queensland</option>
                        <option value="WA">WA - Western Australia</option>
                        <option value="SA">SA - South Australia</option>
                        <option value="TAS">TAS - Tasmania</option>
                        <option value="ACT">ACT - Australian Capital Territory</option>
                        <option value="NT">NT - Northern Territory</option>
                        <option value="NZ">New Zealand</option>
                     </select>
                  </div>
                  <div class="lw-form-group lw_mobilephone">
                 <label>Your Phone Number<span class="lw_required_label" style="visibility:hidden;">*</span><div class="lw-tooltip">?<span class="lw-tooltiptext">If you are under 18 you can choose to share your phone number with us. If you are over 18 we need this to contact you about your account</span></div></label>
                         <select name="lw_area_code" id="lw_area_code">
                     	<option value="+61">+61</option>
                        <option value="+64">+64</option>
                     </select>
                     <input type="text" placeholder="Your Phone Number" class="phone_number_validation_step_1 number" name="lw_mobilephone">
                  </div>
               </div>
               <div class="lw_tabcontent">
                  <div class="lw-form-group">
                     <label>Your Emergency Contact's First Name<span class="lw_required_label">*</span><div class="lw-tooltip">?<span class="lw-tooltiptext">If you are under 18 this must be your parent/guardian
If you are over 18 this is a trusted adult that we can reach in case of an emergency</span></div></label>
                     <input type="text" placeholder="Your Emergency Contact's First Name" class="required lettersonly"  name="lw_registration_guardian_first_name">
                  </div>
                  <div class="lw-form-group">
                     <label>Your Emergency Contact's Last Name<span class="lw_required_label">*</span><div class="lw-tooltip">?<span class="lw-tooltiptext">If you are under 18 this must be your parent/guardian
If you are over 18 this is a trusted adult that we can reach in case of an emergency
</span></div></label><input type="text" placeholder="Your Emergency Contact's Last Name" class="required lettersonly" name="lw_registration_guardian_last_name">
                  </div>
                  <div class="lw-form-group">
                     <label>Your Emergency Contact's Email<span class="lw_required_label">*</span><div class="lw-tooltip">?<span class="lw-tooltiptext">If you are under 18 this must be your parent/guardian
If you are over 18 this is a trusted adult that we can reach in case of an emergency
</span></div></label>
                     <input type="email"  placeholder="Your Emergency Contact's Email" class="email validateEmail required" name="lw_registration_guardian_email">
                  </div>
                  <div class="lw-form-group">
                     <label>Your Emergency Contact's Phone Number<span class="lw_required_label">*</span><div class="lw-tooltip">?<span class="lw-tooltiptext">If you are under 18 this must be your parent/guardian
If you are over 18 this is a trusted adult that we can reach in case of an emergency
</span></div></label>
			<select name="lw_emergency_area_code" id="lw_emergency_area_code">
                     	<option value="+61">+61</option>
                        <option value="+64">+64</option>
                     </select>
                     <input type="text" placeholder="Your Emergency Contact's Phone Number" class="required lw_emergency_phone_number phone_number_validation number" name="lw_registration_guardian_mobile_phone">
                  </div>
                  <div class="lw-form-group">
                     <label class="lw-line-break">Anything we need to know before we contact your emergency contact? <div class="lw-tooltip">?<span class="lw-tooltiptext">For example when we're talking with your parents, guardians or emergency contact
</span></div></label>
                     <textarea placeholder="Tell us a good time to call, or any other communication preferences " name="lw_contact_you"  maxlength="<?php echo isset($lw_general_settings['lw_contact_you'])?$lw_general_settings['lw_contact_you']:100; ?>"></textarea>
                  </div>
                  
               </div>
               <div class="lw_tabcontent">
                  <div class="lw-form-group">
                     <label>Create a username<span class="lw_required_label">*</span><div class="lw-tooltip">?<span class="lw-tooltiptext">This is the name that will appear within the Livewire community. Your username can use letters and up to five numbers. For safety reasons please don't include anything identifying such as part of your last name in your username</span></div></label>
                     <input type="text" placeholder="Livewire Username" class="required user_3_consecutive username_valid"  id="lw_username"   name="lw_username">
                  <?php /*?>   <small class="lw_notes">This is the name that will appear within the Livewire community. For safety please do not include part of your last name in your username. Your username can use letters and up to five numbers.</small><?php */?>
                  </div>
                  <div class="lw-form-group">
                     <label>Create a password<span class="lw_required_label">*</span><div class="lw-tooltip">?<span class="lw-tooltiptext">Passwords must be at least 12 characters, and must contain a number, a symbol (e.g. @,!,#), and a mix of upper/lower case letters. Passwords cannot contain sequential numbers or common words.</span></div></label>
                      <input type="password" placeholder="Password" class="required atleasteight"   name="lw_password" id="lw_password">
<!--                      <input type="password" placeholder="Password" oninput="checkCustomPasswordStrength()"  name="lw_password" id="lw_password">-->
                    <i class="far fa-eye" id="lwtogglePassword"></i>
                  <?php /*?>   <small  class="lw_notes">Password must be at least 12 characters, must contain a number, a symbol (ex @ ! #), and a mix of upper/lower case letters. Password cannot contain sequential numbers or common words.</small><?php */?>
                  </div>
                  <div class="lw-form-group lw-checkbox-custome lw-checkbox-custome-tc">
                     <input type="checkbox" id="lw_tc" name="lw_tc" value="1" class="chk_privacy_policy">
                                <label for="tc"> I agree to the Livewire <a href="<?php echo site_url().'/terms-conditions' ?>" target="_blank">Terms & Conditions </a> and <a href="<?php echo site_url().'/privacy' ?>" target="_blank">Privacy Policy</a></label>
        
                  </div>
               </div>
               <div class="lw-np-btn">
                  <a id="cancelBtn" href="/">Cancel</a>
                  <button type="button" id="LwprevBtn" onclick="nextPrev(-1)">Go back</button>
                  <button type="button" id="LwnextBtn" onclick="nextPrev(1)">Next step</button>
                  <button type="submit" id="LwsubmitBtn" >Register</button>
               </div>
            </form>
         </div>
      </div>
   </div>
</div>

