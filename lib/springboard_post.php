<?php
require_once(SB_PLUGIN_DIR.'/lib/springboard_httprequest.php');
class Springboard_Post implements Springboard_HttpRequest
{
	public function exec($url,$params) {
			$params_string = "";
			$http_result = "";
			foreach($params as $key=>$value){
				$params_string .= "&".$key."=".$value;
			}
			$params_string = substr($params_string,1);
			$http_data = array(
					'http' => array(
						'method' => 'POST',
						'header' => 'Content-type: application/x-www-form-urlencoded'."\r\n".
                                	'Content-length: ' . strlen($params_string),
                    	'content' => $params_string
				)
			);

			$stream = stream_context_create($http_data);
			$socket = fopen($url, 'r', false, $stream);
			if ($socket) {
				while (!feof($socket))
					$http_result .= fgets($socket, 4096);

				fclose($socket);
      		}
			return array($http_result, '');;
	}
}
?>