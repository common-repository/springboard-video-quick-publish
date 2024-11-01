<?php
class Springboard_Client
{
	private static $req = null;

	static public function getReq() {
		if( self::$req === null )  {
			self::$req = self::FactoryReq();
			}
		return self::$req;
		}
	private static function FactoryReq() {
		if(function_exists("curl_init")) {
			require_once(SB_PLUGIN_DIR.'/lib/springboard_curl.php');
			return new Springboard_Curl();
			}
		else {
			require_once(SB_PLUGIN_DIR.'/lib/springboard_post.php');
			return new Springboard_Post();
			}
		}
	
	public function registerPartner($partner,$action) {
		$url = CMS_PATH."/wordpress/".$action;

		$response=self::getReq()->exec($url,$partner->toArray());
		return $response;
	}
}
?>