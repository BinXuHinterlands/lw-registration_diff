<?php

if (!class_exists('LW_REGISTRATION_SHORTCODES'))

	{

	class LW_REGISTRATION_SHORTCODES

		{

		public



		function __construct()

			{

			

			$sortcodeArray=array('lw_registration','lw_invitation','lw_login','lw_today_birthday','lw_direct_starlight_registration');	

			foreach($sortcodeArray as $single)

			{

				add_shortcode($single, array(&$this,$single));	

			}

	}

	

	public function lw_login($att){
		if(!is_admin()){
			ob_start();
			include('shortcode-login.php');
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}
	}
	public function lw_registration($att){
		if(!is_admin()){
			ob_start();
			include('shortcode-registration.php');
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}
	}
	public function lw_direct_starlight_registration($att){
        if(!is_admin()){
            ob_start();
            include('shortcode-direct-starlight.php');
            $content = ob_get_contents();
            ob_end_clean();
            return $content;
        }
    }
	public function lw_invitation($att){
		if(!is_admin()){
			ob_start();
			include('shortcode-invitation.php');
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}
	}
	
	public function lw_today_birthday($att){
		if(!is_admin()){
			ob_start();
			include('shortcode-today-birthday.php');
			$content = ob_get_contents();
			ob_end_clean();
			return $content;
		}
	}
	

	

	

	

}

}



$GLOBALS['LW_REGISTRATION_SHORTCODES'] = new LW_REGISTRATION_SHORTCODES();