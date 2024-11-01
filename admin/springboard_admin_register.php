<?php
if(!is_admin())
	die();
	 
$completeRegistration = false;
$completeLogin = false;
if(isset($_POST['regFlag'])) {
	$postFields = array("first_name","last_name","user_name","email","website","country");
	require_once(SB_PLUGIN_DIR.'/lib/springboard_partner.php');
	$partner = new Springboard_Partner();
	foreach( $postFields as $field ){
		if(isset($_POST[$field]) && $_POST[$field]) {
			$partner->$field = $_POST[$field];
		}
		else {
			$errors[$field] = "<span style='color:red;'>*Required field</span>";
			}
		}
	if(!isset($_POST["agree_to_terms"])){
		$errors["agree_to_terms"] = "<span style='color:red;'>*You must agree to the Springboard Terms of Use</span>";
	}
	
	if(empty($errors)) {
		require_once(SB_PLUGIN_DIR.'/lib/springboard_client.php');
		$client = new Springboard_Client();
		$response = $client->registerPartner($partner,'createAccount');
		$response = json_decode($response[0],true);
		
		if(isset($response['success']) && isset($response["api_key"]) && isset($response["site_id"]) ){
			update_option('sb_pub_id',$response["site_id"]);
			update_option('sb_api_key',  $response["api_key"]);
			update_option('sb_player',  $response["player_id"]);
			$completeRegistration = true;
		}
		else if(!isset($response['error'])){
			$response['error'] = "Registration couldn't be completed.";
		}
		
	}
	else {
		unset($partner);
	}
}

	if(!$completeLogin && !$completeRegistration) {
	require_once(SB_PLUGIN_DIR."/admin/springboard_countries.php");
	
	?>
	<div class="wrap">
			<h2>Springboard</h2>
			<?php
			include(SB_PLUGIN_DIR.'/admin/springboard_admin_login.php'); 
			if(!(isset($response_login['success']) && count($response_login['success']))) {
			?>
			<h3 class='sb_registration'>Registration</h3>
			<?php 
			if( isset($response['error']) ){
				echo '<div class="springboardRegistrationError">'.$response['error'].'</div>';
			}
			
			
				?>
				<form name="registrationForm" method="post" id="registrationForm" />
					<table >
						<tr valign="top">
							<th class='regR'>First Name: *</th>
							<td><input type="text" id="first_name" name="first_name" value="<?php echo isset($_POST['first_name'])?$_POST['first_name']:""; ?>" class='reg_inputs'/>
								<?=isset($errors["first_name"])?$errors["first_name"]:""; ?>
							</td>
						</tr>
						<tr valign="top">
							<th>Last Name: *</th>
							<td>
								<input type="text" id="last_name" name="last_name" value="<?php echo isset($_POST['last_name'])?$_POST['last_name']:""; ?>" class='reg_inputs'/>
								<?=isset($errors["last_name"])?$errors["last_name"]:""; ?>
							</td>
						</tr>
						<tr valign="top">
							<th>Username: *</th>
							<td>
								<input type="text" id="user_name" name="user_name" value="<?php echo isset($_POST['user_name'])?$_POST['user_name']:""; ?>" class='reg_inputs'/>
								<?=isset($errors["user_name"])?$errors["user_name"]:""; ?>
							</td>
						</tr>
						<tr valign="top">
							<th>Email : *</th>
							<td><input type="text" id="email" name="email" value="<?php echo isset($_POST['email'])?$_POST['email']:""; ?>" class='reg_inputs' />
								<?=isset($errors["email"])?$errors["email"]:""; ?>
							</td>
						</tr>
						<tr valign="top">
							<th scope="row">Website: *</th>
							<td><input type="text" id="website" name="website" value="<?php echo isset($_POST['website'])?$_POST['website']:esc_url($_SERVER['HTTP_HOST']); ?>" class='reg_inputs' />
								<?=isset($errors["website"])?$errors["website"]:""; ?>
							</td>
						</tr>

						<tr valign="top">
							<th >Country: *</th>
							<td>
								<select id="country" name="country" class='reg_inputs'>
									<option value="">Please select...</option>
								<?php foreach($countries as $value => $name): ?>
									<option value="<?php echo $value; ?>" <?php echo (@$_POST['country'] == $value) ? ' selected="selected"' : ''; ?>><?php echo $name; ?></option>
								<?php endforeach; ?>
								</select>
								<?=isset($errors["country"])?$errors["country"]:""; ?>
							</td>
						</tr>
						<tr class="agree_to_terms">
							<th colspan="2">
								<input type="checkbox" name="agree_to_terms" id="agree_to_terms" value="1" <?php ((isset($_POST['agree_to_terms']) ? $_POST['agree_to_terms'] : '0') == '1') ? ' checked="checked"' : ''; ?> /> 
								<label for="agree_to_terms">I Accept </label><a href="http://www.evolvemediacorp.com/terms-of-service" target="_blank">Terms of Use</a> *
								<?=isset($errors["agree_to_terms"])?$errors["agree_to_terms"]:""; ?>
							</th>
						</tr>
						<tr>
							<th colspan="2">* Required fields</th>
						</tr>
					</table>
					
					<p class="submit" style="text-align: left; "><input type="submit" name="Submit" value="Complete installation" /></p>
								
					<input type="hidden" name="regFlag" value="1" />
				</form>
			</div>
			<?php
			}
		}
		else if($completeRegistration){
		?>
		<br><br>
		<div class='w_message'>
			<div class='w_sub_message'>
			Thank you for registering a Springboard account.<br>
			An email has been sent with a link to setup your password.
			</div>
		</div>
			<?php
		}