<?php

if(!is_admin())
	die();

	$sb_pub_id = get_option("sb_pub_id");

	if(isset($_GET['login'])) {
		require_once('springboard_admin_login.php');
	}
	else if(!$sb_pub_id) {
		require_once('springboard_admin_register.php');
	}
	else {
		require_once('springboard_admin_user_info.php');
	}
?>