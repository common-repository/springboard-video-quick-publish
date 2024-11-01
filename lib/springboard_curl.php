<?php
require_once(SB_PLUGIN_DIR.'/lib/springboard_httprequest.php');
class Springboard_Curl implements Springboard_HttpRequest
{
	public function exec($url,$params) {
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url );
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_USERAGENT, '');
		$result = curl_exec($ch);
		$error = curl_error($ch);
		curl_close($ch);
		return array($result, $error);
	}
}

?>