<style>
	#custom_html-4 {
		display:  none;
	}
	#custom_html-4.active {
		display:  block;
	}
</style>

<?php if(is_user_logged_in()): ?>

	<?php
	// $today= date("m").date("d");
	$today = date_i18n('md');
	
	global $wpdb;
	$todayUsers = $wpdb->get_results('SELECT * FROM '.$wpdb->prefix.'usermeta WHERE meta_key="birthmmdd'.$today.'" ORDER BY umeta_id DESC limit 200',ARRAY_A);

	    ?>

	<div class="lw_todays_birthday_list">
	  <?php
		$counter  = 0; 
			if(count($todayUsers)>0){
			
		?>
	  <ul>
	    <?php 

			
					foreach($todayUsers as $user){
						$checkDobVisiable = xprofile_get_field_data(60,$user['user_id'] );
						if($checkDobVisiable=="Yes"){
						if($counter==20){break;}
						$checkUsers = $wpdb->get_row("SELECT * FROM `".$wpdb->prefix."bp_suspend` WHERE `item_id` = '".$user['user_id']."' AND `item_type` = 'user' AND `user_suspended` = 1",ARRAY_A) ;
						
						
						
	if(empty($checkUsers)){
						
						
						$userInfo = get_user_by('ID' ,$user['user_id']);
						
						
						$member_image =  bp_core_fetch_avatar(
				array(
					'item_id' => $user['user_id'],
					'type'    => 'thumb',
					'alt'     => sprintf( __( 'Profile photo of %s', 'buddyboss' ), $userInfo->data->user_nicename ),
					'css_id'  => false,
					'class'   => 'avatar',
					'width'   => false,
					'height'  => false,
					'email'   => $userInfo->data->user_email,
				)
			);
			
			$fullname = $userInfo->data->user_login;
					
			?>
	    <li <?php echo ($counter>3)?"class='hide_today_birthday'":""; ?>> <a href="<?php  echo bp_core_get_user_domain( $user['user_id'], $userInfo->data->user_nicename, $userInfo->data->user_login ) ; ?>" class="bp-tooltip" data-bp-tooltip-pos="left" data-bp-tooltip="<?php echo esc_attr( $fullname ); ?>"><?php echo $member_image;; ?><?php echo $fullname; ?></a> </li>
	    <?php 
			$counter++;
					}
						}
					}
			 ?>
	  </ul>
	  <?php 
		if($counter>4){
		?>
	  <div class="lw_see_all_birthday">
	    <button name="today_see_all_btn" class="today_see_all_btn">See All</button>
	  </div>
	  <?php }} ?>
	  
	</div>
	<script>
	;(function() {
			<?php if($counter != 0): ?>
					jQuery("#custom_html-4").addClass('active');
			<?php else: ?>
					jQuery(".today_see_all_btn").on("click",function(){
						jQuery('.lw_todays_birthday_list ul li').removeClass("hide_today_birthday");
						jQuery(".lw_see_all_birthday").hide();
					});
			<?php endif; ?>
	})();
	</script>
	<style>
	.lw_birthday_error{ color:red;}
	.hide_today_birthday{ display:none;}
	.lw_todays_birthday_list ul li img {
	  width: 45px;
	  margin-right: 10px;
	  margin-bottom: 10px;
	}
	.lw_todays_birthday_list ul li a {
	  font-size: 16px !important;
	  font-weight: 600;
	  color: #000 !important;
	}
	.lw_see_all_birthday {
		text-align: left;
		margin: 15px 0 0;
		height: auto;
		line-height: 1em;
	}
	.lw_see_all_birthday .today_see_all_btn {
		font-weight: 700;
		font-size: 12px;
		text-transform: uppercase;
		padding: 8px 16px;
		border-radius: var(--bb-button-radius);
		display: inline-block;
		background-color: var(--bb-secondary-button-background-regular);
		color: var(--bb-secondary-button-text-regular);
		border: 1px solid var(--bb-secondary-button-border-regular);
	}
	.lw_see_all_birthday .today_see_all_btn:hover {
		background-color: var(--bb-secondary-button-background-hover);
		color: var(--bb-secondary-button-text-hover);
		border: 1px solid var(--bb-secondary-button-border-hover);
	}
	</style>

<?php endif; ?>