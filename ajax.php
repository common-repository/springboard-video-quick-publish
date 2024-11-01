<?php
	require_once('../../../wp-load.php');
	include(ABSPATH . 'wp-admin/includes/admin.php');
	
	$params = array();
	foreach ($_POST as $k => $v) {
		$params[$k] = $v;
	}
	
	if(isset($params['sb_pub_id'])) {
		$params['partner_id'] = $params['sb_pub_id'];
		unset($params['sb_pub_id']);
	}
	
	if(isset($params['pub_id'])) {
		$params['publisher_id'] = $params['pub_id'];
		unset($params['pub_id']);
	}

	print_r($sb->call($params['sb_action'], $params, false));
?>