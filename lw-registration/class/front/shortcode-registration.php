<?php 
global $invitationTypes,$lw_general_settings,$current_user,$wpdb;

$roles = $current_user->roles;
$access_page = 1;


if($current_user->ID>0 ){
	$access_page = 0;
	if(isset($_REQUEST['token']) && $_REQUEST['token']!="" && (in_array("administrator",$current_user->roles) || in_array("editor",$current_user->roles))){
		$access_page = 1;
	}
}
if($access_page==0){
	echo "<div class='not_access'>Sorry you don't have access this page.</div>";
}else{
$prePouplateData = array();

	if(isset($_REQUEST['token']) && $_REQUEST['token']!=""){

		$checkToken = $wpdb->get_row("select *from ".TABLES_LW_REGISTRATION_INVITATION." where token='".$_REQUEST['token']."'",ARRAY_A);
		
		$expiryMessage = "Oops this link expires after 4 hours... but don't go anywhere!<br>If you're in hospital ask the Livewire crew or Captain Starlight to send you a new link, which gives you 24 hour access to the Livewire community 🙌<br>Or if you're at home <a href='/register/'><strong>become a member</strong></a> using the same email address you received this invite through. It won't give you immediate access but we will match your info so there's less steps to joining the Livewire community 😎";
		if($checkToken['invitation_type']==1){
			$expiryMessage = "Oops this link expires after 1 month... but don't go anywhere! <br>Ask your Wishgranter to send you a new link. <br>Or email us at livewire@starlight.org.au and we'll get one over to you 🙌";
			
		}
		
		$prePouplateData =$checkToken;
		

		if(!empty($checkToken)){
		
			if($checkToken['status']==2){
				echo "<script type='text/javascript'>document.addEventListener('DOMContentLoaded',function(){document.body.classList.add('register-link-invalid');})</script><div class='lw_not_access'><div class='not_access'>".$expiryMessage."</div> <a href='/'><button>Back to Homepage</button></a></div>";
			}else if(($checkToken['status']==0 || $checkToken['status']==1) && strtotime($checkToken['expire_at'])<=strtotime(date("Y-m-d H:i:s"))){
				echo "<script type='text/javascript'>document.addEventListener('DOMContentLoaded',function(){document.body.classList.add('register-link-invalid');})</script><div class='lw_not_access'><div class='not_access'>".$expiryMessage."</div> <a href='/'><button>Back to Homepage</button></a></div>";
			}else{
				
				if($checkToken['invitation_type']==0 || $checkToken['invitation_type']==2){
					echo "<div class='lw-wrapper register-page'>";
					include("lw_leftsidebar.php");
					include("registration-form-a.php");
					echo "</div>";		
				}
				if($checkToken['invitation_type']==1){
					echo "<div class='lw-wrapper register-page'>";
						include("lw_leftsidebar.php");
						include("registration-form-b.php");
					echo "</div>";	
				}
			}
		}else{
			echo "<div class='not_access'>Sorry Your verification link not valid.</div>";
		}
		echo "<script>document.body.classList.remove(\"page-id-2991\");</script>";
  	 }else{
		echo "<div class='lw-wrapper register-page'>";
			include("lw_leftsidebar.php");
			include("registration-form-c.php");
		echo "</div>";	
	}
	
?>

 <?php } ?>
