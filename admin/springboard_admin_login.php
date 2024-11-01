<?php 
if(!is_admin())
	die();
$completeLogin = false;	

if(isset($_POST["partnerApiKey"])){
	update_option('sb_pub_id',$_POST["partnerId"]);
	update_option('sb_api_key',$_POST["partnerApiKey"]);
	update_option('sb_player',$_POST["sb_palyer"]);
	$completeLogin = true;
	?>
	<div class='w_message'>
		<div class='w_sub_message'>
		Login successful.
		</div>
	</div>
	<?php
}
else {
	if(isset($_POST['login']))	{
		require_once(SB_PLUGIN_DIR.'/lib/springboard_partner.php');
		$partner = new Springboard_Partner();
		if( isset($_POST['sb_login_username']) && strlen($_POST['sb_login_username']) ) {
			$partner->username = $_POST['sb_login_username'];
		}
		else {
			$errors['sb_login_username'] = "<span style='color:red;'>*Required field</span>";;
		}
		
		if( isset($_POST['sb_login_password']) && strlen($_POST['sb_login_password']) ) {
			$partner->password = $_POST['sb_login_password'];
		}
		else {
			$errors['sb_login_password'] = "<span style='color:red;'>*Required field</span>";
		}

		
		if(empty($errors)) {
			require_once(SB_PLUGIN_DIR.'/lib/springboard_client.php');
			$client = new Springboard_Client();
			$response_login = $client->registerPartner($partner,'loginUser');
			
			$response_login = json_decode(stripslashes($response_login[0]),true);
			if(isset($response_login['success'])){
				$completeLogin = true;
			}
			
		}
		else {
			unset($partner);
		}
	}
	if( isset($response_login['error']) ){
		echo '<div class="springboardRegistrationError">'.$response_login['error'].'</div>';
	}

	if(!$completeLogin ) {
	?>
	<h3 class='sb_registration'>If you already have a Springboard account enter your username and password in the fields below to complete the installation process.</h3>
	<form method='POST'>
	<table border='0'>
	<tr>
		<td>Username:</td>
		<td><input type='text' name='sb_login_username' class='login_inputs' autocomplete='off' />
		<?=isset($errors["sb_login_username"])?$errors["sb_login_username"]:""; ?>
		</td>
	</tr>
	<tr>
		<td>Password:</td>
		<td><input type='password' name='sb_login_password' class='login_inputs'  autocomplete='off' />
		<?=isset($errors["sb_login_password"])?$errors["sb_login_password"]:""; ?>
		</td>
	</tr>
	<tr>
		<td colspan='2'>
			<input type="hidden" name="login" value="true" />
			<input type="submit" name="Submit" value="Complete instalatlion" />
		</td>
	</tr>
	</table>
	</form>
	<?php }
	else {
		if(count($response_login['success'])) {
			?>
			<!--<div class='sb_login_sp_form'>-->
				<img src='<?php echo SB_PLUGIN_URL."/img/choose_img.png"; ?>' style='margin-left: 3px;margin-top:20px;'><br>
				<form method='POST' action='<?php echo $_SERVER['PHP_SELF'] ?>?page=sb_video&login=true' id='partnersFrom'>
				<select name='partner' id='partner' style='width:200px;margin-bottom:3px;'>
				<?php
				$site_id = null;
				foreach($response_login['success'] as $partner){
					$domain = $partner["Partner"]["domain"];
					if($partner_id == null)
						$partner_id = $partner["Partner"]["id"];
					echo "<option value='".$domain."'>".$domain."</option>\n";
				}
				?>
				</select><br />
				<div id="refresh_active" class="refresh" style="display:none;position:absolute;repeat:no-repeat;"><img src='<?php echo SB_PLUGIN_URL.'/img/refresh.gif'; ?>' /><br /> </div>
				<div id='sb_player_holder' ></div>
				<input type='hidden' name='partnerApiKey' id='partnerApiKey' value='1' />
				<input type='hidden' name='partnerId' id='partnerId' value='1' />
				<input type='image' src='<?php echo SB_PLUGIN_URL.'/img/save_button.png';?>' id='sb_log_save_button' style='display:none;top:153px;padding-left:0px;'/>
				</form>
			<!--</div>-->
			<script type='text/javascript'>
				sb.login.partners = eval('<?php echo json_encode($response_login["success"]);?>');
				jQuery(document).ready(function(){
				sb.plugin_url = '<?php echo SB_PLUGIN_URL; ?>';
						jQuery("#partner").change(function(){
						domain = jQuery(this).val();
						for(i in sb.login.partners ) {
							if(sb.login.partners[i].Partner.domain == domain) {
								sb.login.showPartnersPlayer(sb.login.partners[i].Partner.id,sb.login.partners[i].Partner.wp_api_key);
							}
						}
					});
					
					jQuery("#partnersFrom").submit(function(){
						var domain = jQuery("#partner").val();
						for(i in sb.login.partners ) {
							if(sb.login.partners[i].Partner.domain == domain) {
								jQuery("#partnerApiKey").val(sb.login.partners[i].Partner.wp_api_key);
								jQuery("#partnerId").val(sb.login.partners[i].Partner.id);
								return true;
							}
						}
						return false;
					});
					if( typeof sb != 'undefined' && typeof sb.login != 'undefined' && typeof sb.login.partners != 'undefined') {
							sb.login.showPartnersPlayer(sb.login.partners[0].Partner.id,sb.login.partners[0].Partner.wp_api_key);
						}
				});
			</script>
			<?php

		}
					
	}

}