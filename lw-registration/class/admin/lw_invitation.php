<?php
global $wpdb,$invitationTypes,$invitationStatus,$lw_general_settings;

$process="";

if(isset($_REQUEST['mode']) && $_REQUEST['mode']=='delete'  && !isset($_REQUEST['submit_btn']) ){

	$wpdb->delete(TABLES_LW_REGISTRATION_INVITATION, array( 'id' =>$_REQUEST['id']));    
	$process='delete';
}
?>

<div class="InvitationTitleHead"><?php echo __('Invite to Livewire', 'lw_registration'); ?></div>
<?php
if($process=='delete'){
 ?>

<div class="error notice notice-success mb-2" id="message" style="margin-left:0px">

  <p><?php echo __('Invitation successfully deleted.', 'lw_registration'); ?></p>

</div>

<?php
}else if($process=='update'){
?>
<div class="updated notice notice-success  mb-2" id="message" style="margin-left:0px">
  <p><?php echo __('Invitation successfully updated.', 'lw_registration'); ?></p>
</div>
<?php
}
?>
<div id="page_Settings">
<div class="tabs_outer">
<div id="listing_settings">
  <div class="row">
  <div class="col-md-12">
  <div class="InvitationPackageBody">

        <h4 class="InvitationPackageHeadingTitle d-flex align-items-baseline"><?php echo __('LW Invitation List', 'lw_registration'); ?>
        <a href="<?php echo site_url(); ?>/invite" target="_blank" class="button button-primary ml-1 font-weight-normal">ADD NEW</a>
       </h4>

     <?php

	   $results1 = $wpdb->get_results('SELECT * FROM '.TABLES_LW_REGISTRATION_INVITATION.' ORDER BY id DESC',ARRAY_A);

	

    $k=1;

    ?>
		<div class="table-responsive">
        
        <table id="invitation_table" class="table table-hover table-striped table-bordered table-advanced tablesorter display" role="grid">

          <thead>

            <tr>

              <td width="5%" align="center"><strong><?php echo __('Sr. ', 'lw_registration'); ?></strong></td>
              <td align="center"><strong><?php echo __('Sender’s Username', 'lw_registration'); ?></strong></td>
              <td align="center"><strong><?php echo __('Sender’s name', 'lw_registration'); ?></strong></td>
              <td align="center"><strong><?php echo __('First Name', 'lw_registration'); ?></strong></td>
              <td align="center"><strong><?php echo __('Last Name', 'lw_registration'); ?></strong></td>
              <td align="center"><strong><?php echo __('Email', 'lw_registration'); ?></strong></td>
              <td align="center"><strong><?php echo __('Invitation Type', 'lw_registration'); ?></strong></td>
              <td align="center"><strong><?php echo __('Status', 'lw_registration'); ?></strong></td>
              <td align="center"><strong><?php echo __('Created At', 'lw_registration'); ?></strong></td>
              <td align="center"><strong><?php echo __('Action', 'lw_registration'); ?></strong></td>

            </tr>

          </thead>

          <tbody>

            <?php foreach($results1 as $key=>$value){ 

  				$status =$value['status'];
				 
				if($value['status']==0 || $value['status']==1){
					
					if(strtotime($value['expire_at'])<=strtotime(date("Y-m-d"))){
						$status =1;
						
					}	
				}
				$senderName = "";
				if($value['user_id']>0){
					$userSender = get_user_by('ID',$value['user_id']);
					$senderName =$userSender->user_login; 	
				}
			  ?>

            <tr>

              <td align="center"><?php echo $k ;$k++?></td>
              
              <td align="center"><?php echo $senderName; ?></td>
              <td align="center"><?php echo $value['staff_name']; ?></td>

              <td align="center"><?php echo $value['first_name']; ?></td>
              <td align="center"><?php echo $value['last_name']; ?></td>
              <td align="center"><?php echo $value['email']; ?></td>
              <td align="center"><?php echo $invitationTypes[$value['invitation_type']]; ?></td>
              <td align="center"><span class="badge text-uppercase badge<?php echo $status; ?>"><?php echo $invitationStatus[$status]; ?></span></td>
              <td align="center"><?php echo $value['created_at']; ?></td>
              <td align="center">
              
              <?php
			   $registration_url= "";
			  if(isset($lw_general_settings['registration']) && $lw_general_settings['registration']>0 && $value['token']!=""){
				?>
                
              <div style="display:none;" id="RegistrationLink<?php echo $value['id']; ?>"><?php echo get_the_permalink($lw_general_settings['registration'])."/?token=".$value['token']; ?></div>
			 <?php /*?><a  title="<?php echo __('Registration Link', 'lw_registration'); ?>"  href="javascript:void(0)" onclick="copyToClipboard('RegistrationLink<?php echo $value['id']; ?>')" data-id="<?php echo $value['id'];?>" class="btn  btn-success btn-sm text-uppercase">Link Copy</a> 
             <?php */?>
                <?php   
				}
			   ?>
              
              <?php if( $value['status']==0 || $value['status']==1){?>
              <a  title="<?php echo __('Resend Email', 'lw_registration'); ?>"  href="javascript:void(0)" data-id="<?php echo $value['id'];?>" class="btn resend_invitation btn-info btn-sm text-uppercase">Resend Email</a> 
            	<?php  } ?>
              
              <?php /*?><a title="<?php echo __('Delete', 'lw_registration'); ?>" onClick="return confirm('<?php echo __('Are you sure you want to delete this?', 'lw_registration'); ?>');" href="admin.php?page=lw_invitation&id=<?php echo $value['id'];?>&mode=delete" class="btn btn-danger btn-sm">DELETE</a></td>
			<?php */?>
            </tr>

            <?php }?>

          </tbody>

        </table>
        </div>

  </div></div>

  </div>



</div>





 

</div>

</div>

<script>
function copyToClipboard(elementId) {

  // Create a "hidden" input
  var aux = document.createElement("input");

  // Assign it the value of the specified element
  aux.setAttribute("value", document.getElementById(elementId).innerHTML);

  // Append it to the body
  document.body.appendChild(aux);

  // Highlight its content
  aux.select();

  // Copy the highlighted text
  document.execCommand("copy");

  alert("Link has been successfully copid!");
  // Remove it from the body
  document.body.removeChild(aux);

}
</script>
<div class="modal" id="LWRegistrationModal" tabindex="-1" role="dialog" aria-labelledby="LWRegistrationModalTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Add LW Invitation</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <form role="form" id="LwInvitationForm" enctype="multipart/form-data" method="post" action="">
			<div class="row">
        		<div class="col-md-6">
          			<div class="form-group">
						<label for="first_name"  class="invitation_label"><?php echo __('First Name', 'lw_registration'); ?></label>
						<input type="text" name="first_name" id="first_name" placeholder="<?php echo __('Enter First Name', 'lw_registration'); ?>" class="form-control required"/>
					</div>
          		</div>
          		<div class="col-md-6">
           			<div class="form-group">
						<label for="last_name" class="invitation_label"><?php echo __('Last Name', 'lw_registration'); ?></label>
						<input type="text" name="last_name" id="last_name" placeholder="<?php echo __('Enter Last Name', 'lw_registration'); ?>" class="form-control required"/>
					</div>
          		</div>
            </div>
            <div class="row">
        		<div class="col-md-6">
            		<div class="form-group">
						<label for="email" class="invitation_label"><?php echo __('Email', 'lw_registration'); ?></label>
						<input type="text" name="email"  id="email" placeholder="<?php echo __('Enter Email', 'lw_registration'); ?>" class="form-control required email"/>
					</div>
                </div>
                <div class="col-md-6">
              		<div class="form-group">
						<label for="invitation_type" class="invitation_label"><?php echo __('Invitation Type', 'lw_registration'); ?></label>
                        <select class="form-control required" name="invitation_type" id="invitation_type">
                            <?php foreach($invitationTypes as $k=>$v){
                            
                            ?>
                            <option value="<?php echo $k; ?>"><?php echo $v; ?></option>	
                            <?php }
                            ?>
                        </select>   
          			</div>
                </div>
            </div>
           <div class="row">
				<div class="col-md-12 text-center" >
                	<button type="submit" class="btn btn-dark">SUBMIT</button>
        		</div>
         	</div>      
        </form>
      </div>
    </div>
  </div>
</div>