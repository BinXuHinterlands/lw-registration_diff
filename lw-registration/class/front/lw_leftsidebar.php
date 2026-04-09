<?php 
global $lw_general_settings;
$postId= get_the_ID();

?>
<?php /*?><div class="lw-width50 lw-grey-bg">
			<div class="lw-livewire-wrapper">
				<div class="lw-livewire-logo">
					<a href="<?php echo site_url();?>"><img src="<?php echo LW_REGISTRATION_ASSETS_URL;?>/images/livewire.png" alt="logo"></a>
				</div>
				<div class="lw-livewire-links">
					<div class="lw-width50">
						<a href="<?php echo (isset($lw_general_settings['registration']) && $lw_general_settings['registration']==$postId)?"javascript:void(0)":get_the_permalink($lw_general_settings['registration']); ?>" class=" <?php echo (isset($lw_general_settings['registration']) && $lw_general_settings['registration']==$postId)?' active-livewire-link':""; ?>">Become a member</a>
					</div>
					<div class="lw-width50">
						<a href="<?php echo (isset($lw_general_settings['login']) && $lw_general_settings['login']==$postId)?"javascript:void(0)":get_the_permalink($lw_general_settings['login']); ?>" class="yp-text-right <?php echo (isset($lw_general_settings['login']) && $lw_general_settings['login']==$postId)?' active-livewire-link':""; ?>">or sign in</a>
					</div>
				</div>
				<div class="lw-livewire-content">
					<p class="info">To join the Livewire Community you must be between 12-20 living with a serious illness. disability or chronic health condition; or their sibling.</p>
				</div>
			</div>
		</div><?php */?>
		
        <div class="lw-width50 lw-grey-bg lw-registration-left">
        
        <div class="lw-responsive-circle"></div>
            <div class="lw-circle lw-white-bg lw-flex lw-column">
        	
            <div class="lw-livewire-logo">
              <a href="<?php echo site_url(); ?>"><img alt="logo" data-src="<?php echo LW_REGISTRATION_ASSETS_URL;?>/images/LW_LOGO_RGB_POS.svg" class="lazyloaded" src="<?php echo LW_REGISTRATION_ASSETS_URL;?>/images/LW_LOGO_RGB_POS.svg"><noscript><img
                    src="<?php echo LW_REGISTRATION_ASSETS_URL;?>/images/LW_LOGO_RGB_POS.svg"
                    alt="logo" /></noscript></a>
            </div>
           
            <div class="lw-livewire-content lw-flex lw-horizontal-center lw-vertical-center lw-column">
              <p class="welcome">
                Welcome to your community<span class="dot">.</span>
              </p>
              <p>We're so glad you're here</p>
              
              <?php 
			  // Check if it's a registration page or contains the registration-form-a-direct form
			  $is_registration_page = (isset($lw_general_settings['registration']) && $lw_general_settings['registration']==$postId);
			  $is_direct_form = false;
			  
			  // Check if the current file is included through shortcode-direct-starlight.php
			  $debug_backtrace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 10);
			  foreach($debug_backtrace as $trace) {
			    if(isset($trace['file']) && strpos($trace['file'], 'shortcode-direct-starlight.php') !== false) {
			      $is_direct_form = true;
			      break;
			    }
			  }
			  
			  if($is_registration_page || $is_direct_form){
				  ?>        <p>
						 				<span>Already a member?</span>
                    <a href="/wp-login.php">Log In<span></a>
            				</P>
                  <?php
				 }else{
					?>
            
                         		<a href="<?php echo (isset($lw_general_settings['registration']) && $lw_general_settings['registration']==$postId)?"javascript:void(0)":get_the_permalink($lw_general_settings['registration']); ?>" class=" <?php echo (isset($lw_general_settings['registration']) && $lw_general_settings['registration']==$postId)?' active-livewire-link':""; ?>">Become a member</a>
	
                    <?php  
					}
			  ?>
            
     			
            </div>
          </div>
          </div>