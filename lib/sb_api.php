<?php

class SB_API{

	private static $url = SB_WP_URL;
	
	public function __construct() {
		/*add_action('load-media_page_springboard_video', array(&$this, 'sb_library_page_load'));
		add_action('admin_init', array(&$this, 'sb_add_settings'));
		add_action('admin_menu', array(&$this, 'sb_generate_menu'));
		add_action('admin_head', array(&$this, 'sb_scripts'));
		add_action('admin_notices', array(&$this, 'my_admin_notice'));*/
	}
	
	/*
	 * Register springboard actions
	 */
	public function sb_install() {
		if( defined('SPRINGBOARD_API_KEY')&& defined('SPRINGBOARD_PUB_ID') && defined('SPRINGBOARD_PLAYER')) {
			add_option('sb_player', SPRINGBOARD_PLAYER);
			add_option('sb_pub_id', SPRINGBOARD_PUB_ID);
			add_option('sb_api_key', SPRINGBOARD_API_KEY);
		}else{
			add_option('sb_player', 0);
			add_option('sb_pub_id', '');
			add_option('sb_api_key', '');
		}
	}
	
	public function sb_uninstall() {
		delete_option('sb_pub_id');
		delete_option('sb_api_key');
		delete_option('sb_player');
	}
	/*
	 * Enque thickbox js and css for loading
	 */
	public static function sb_library_page_load() {
		wp_enqueue_script('thickbox');
		wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
	}
	
	/*
	 * Add springboard video settings section
	 */
	public static function sb_add_settings() {
		global $pagenow;
		// will add button over the rich text editor
		add_filter("media_buttons", array('SB_API', 'sb_add_media_button')); 
		
	}
	
	/*
	 * Verify publisher id and api key
	 */
	public function sb_verify() {
		$html = "";	
		
		$html .= "<script type='text/javascript'>
					function verifyApiKey(){
						var pub_id = jQuery('#sb_pub_id').val();
						var api_key = jQuery('#sb_api_key').val();
						var params = {};
						
						params['pub_id'] = pub_id;
						params['api_key'] = api_key;
						params['sb_action'] = 'verifyKey';
						
						jQuery.ajax({
							type: 'POST',
							data: params,
							url: '".SB_PLUGIN_URL."/ajax.php?'+Math.floor((Math.random()*1000)+1),
							success: function(data){
								if(data == 'Success') {
									var params2 = {};
									params2['sb_action'] = 'getPartnerPlayers';
									params2['pub_id'] = pub_id;
									params2['api_key'] = api_key;
									jQuery.ajax({
										type: 'POST',
										data: params2,
										url: '".SB_PLUGIN_URL."/ajax.php',
										success: function(data) {
											var json_players = jQuery.parseJSON(data);
											for(var i = 0; i < json_players.length; i++) {
												var selected_val = json_players[i].WidgetPlayer.setAsDefaultPlayer == '1' ? 'selected' : '';
												jQuery('#sb_player').append('<option '+selected_val+' value=\"'+json_players[i].WidgetPlayer.player_id+'\">'+json_players[i].WidgetPlayer.player_id+' - ' +json_players[i].WidgetPlayer.description +'</option>');
											}
											jQuery('#default_player_row').show();
											jQuery('#sb_player').show();
											alert('Verified');
											document.forms[0].submit.click();
										}
									});
								} else {
									alert('Verification failed!');
								}
							},
							error: function(data){
								alert('There was an error, please try again');
							}
						});
					}
				</script>";
		
		$html .= "<input type='button' class='button-primary' onclick='verifyApiKey();' value='Verify API key'/>";
		echo $html;
	}
	/*
	 * Insert springboard video button over text editor
	 * @return link containing button
	 */
	public static function sb_add_media_button() {
		if(get_option('sb_pub_id') > 0 && get_option('sb_api_key') != ""){
			echo "<a href='media-upload.php?tab=sb_search&amp;TB_iframe=true&amp;width=800&amp;height=700' class='thickbox' title='Add Springboard Video' style='display: inline-block; margin-top: 5px;'><img src='".SB_PLUGIN_URL."/img/sb_logo_icon.png' alt='Add Springboard Video' /></a>";
		}
	}
	
	public function sb_settings() {
		
	}
	
	/*
	 * Insert field for inserting punblisher id in settings section
	 */
	public function sb_pub_id_setting() {
		$sb_pub_id = get_option('sb_pub_id');
		echo "<input name='sb_pub_id' id='sb_pub_id' type='text' value='$sb_pub_id' />";
	}
	
	/*
	 * Insert field for inserting sb api key in settings section
	 */
	public function sb_api_secret_setting() {
		$sb_api_key = get_option('sb_api_key');
		echo "<input name='sb_api_key' id='sb_api_key' type='text' value='$sb_api_key'/>";
	}
	
	/*
	 * Insert dropdown for selecting wordpress default player
	 */
	public function sb_player_setting() {
		$sb_api_key = get_option('sb_api_key');
		
		if($sb_api_key != "") {
			$players = $this->call('getPartnerPlayers', array(), true);
			echo '<select name="sb_player" id="sb_player">';
			foreach($players as $key => $player){
				if($player['WidgetPlayer']['player_id'] == get_option('sb_player') || $player['WidgetPlayer']['setAsDefaultPlayer'] == 1) {
					add_option('sb_player', $player['WidgetPlayer']['player_id']);
					$selected = "selected";
				} else {
					$selected = "";
				}
				echo '<option value="'.$player['WidgetPlayer']['player_id'].'" '.$selected.'>'.$player['WidgetPlayer']['player_id'].' - '.$player['WidgetPlayer']['description'].'</option>';
			}
			echo '</select>';
		} else {
			echo "<select name=\"sb_player\" id=\"sb_player\" style='display: none;'></select>";
			echo '<script type="text/javascript">';
			echo	'jQuery("#sb_player").parent().parent().attr("id", "default_player_row");';
			echo	'jQuery("#default_player_row").hide();';
			echo '</script>';
		}
	}
	
	/*
	 * Display warning if plugin is not activated
	 */
	public static function my_admin_notice() {
		$show_notice = false;
		if(get_option('sb_pub_id') != "") {
			$show_notice = self::call("action_allowed", array(), false);
		}

		if($show_notice || (get_option('sb_pub_id') == "" && !strpos($_SERVER["REQUEST_URI"],"page=sb_video"))) {
			echo '<div class="error"><p>To activate the plugin either sign up for an account on (<a href="'.get_option('siteurl').'/wp-admin/options-general.php?page=sb_video">Settings/Springboard page</a>)
			or enter your username and password on the same page to register if you already have an account in Springboard.</p></div>';
		}
	}
	
	/*
	 * Insert sb video link in media menu, Media->Springboard Video
	 */
	public static function sb_generate_menu() {
		/*$args = array('Springboard Video', 'Springboard Video', 10, 'springboard_video', array(&$this, 'springboard_admin_page'));
		$argsSettings = array('Springboard Video', 'Springboard Video', 10, 'sb_video', array(&$this, 'springboard_admin_settings_page'));
		
		//call_user_func_array("add_options_page", $argsSettings);
		//call_user_func_array("add_media_page", $args);
		*/
		add_options_page('Springboard Video', 'Springboard Video', 'administrator', 'sb_video', array('SB_API', 'springboard_admin_settings_page'));
		add_media_page('Springboard Video', 'Springboard Video', 'administrator', 'springboard_video', array('SB_API', 'springboard_admin_page'));
	}
	
	/* Script updates video data on ajax call
	*
	*/
	public static function updateVideoData_callback(){
		self::call($_POST['params']['sb_action'], $_POST['params'], false);
		die();
	}
	
	public static function updateThumbnail_callback(){
		require_once(SB_PLUGIN_DIR.'/update_thumbnail.php');
		die();
	}
	
	public static function loadPlayer_callback(){
		echo self::call($_POST['params']['sb_action'], $_POST['params'], false);
		die();
	}
	
	public static function create_snapshot_callback(){
		echo self::call($_POST['params']['sb_action'], $_POST['params'], false);
		die();
	}
	
	public static function editInputFields_callback(){
		echo self::call($_POST['params']['sb_action'], $_POST['params'], false);
		die();
	}
	
	public static function get_partner_playlists_callback(){
		echo self::call($_POST['params']['sb_action'], $_POST['params'], false);
		die();
	}
	
	public static function loadPlaylistVideos_callback(){
		echo self::call($_POST['params']['sb_action'], $_POST['params'], false);
		die();
	}
	
	public static function getPartnerChannels_callback(){
		echo self::call($_POST['params']['sb_action'], $_POST['params'], false);
		die();
	}
	
	public static function loadPlaylistVideosDynamic_callback(){
		echo self::call($_POST['params']['sb_action'], $_POST['params'], false);
		die();
	}
	
	public static function getPartnerPlayers_callback(){
		echo self::call($_POST['params']['sb_action'], $_POST['params'], false);
		die();
	}
	
	public static function search_callback(){
		echo self::call($_POST['params']['sb_action'], $_POST['params'], false);
		die();
	}
	
	public static function deleteVideo_callback(){
		echo self::call($_POST['params']['sb_action'], $_POST['params'], false);
		die();
	}
	
	public static function reconvertVideo_callback(){
		echo self::call($_POST['params']['sb_action'], $_POST['params'], false);
		die();
	}
	
	/*
	/*	Script returns video list on ajax call
	*/
	public static function getVideoList_callback(){
		require_once(SB_PLUGIN_DIR.'/videolist.php');
		die();
	}
	
	/*
	 * Print sb video list on springboard video page
	 */
	public static function springboard_admin_page() {
		$sb_pub_id = get_option('sb_pub_id');
		if( $sb_pub_id === null || $sb_pub_id <= 0 ) {
			return;
		}
			echo '<div id="header_div">';
			echo '<div id="refresh_static" class="refresh" onclick="refreshVideoList();" style="cursor: pointer; background: url('.SB_PLUGIN_URL.'/img/refresh_.png) repeat-x scroll 0 0 transparent;"></div>';
			echo '<div id="refresh_active" class="refresh" style="display: none; background: url('.SB_PLUGIN_URL.'/img/refresh.png) repeat-x scroll 0 0 transparent;">';
			echo	'<img alt="" border="0" src="'.SB_PLUGIN_URL.'/img/refresh_b.gif" style="margin-top: 5px; margin-left: 14px;" />';
			echo '</div>';
			echo '<div id="pagination_videoslist" align="center"></div>';
			echo '</div>';
			
			$html = "<div id='loading' style='display: none;position: absolute;background-color: white; opacity: 0.6;filter:alpha(opacity=60);top: 55px;left: 0px;z-index: 999;width: 100%;height: 100%;'></div>";
			$html .= "<div style='margin-right: 15px; float: left;'>";
			$html .= "<script type='text/javascript'>";
			$html .= "function refreshVideoList() {
						//console.log('test');
						var params = {};
						var pretty_url = '".PRETTY_URL."';
	
						params['paged'] = ".(isset($_GET['paged']) ? $_GET['paged'] : "1").";
						params['sb_action'] = 'search';
						
						if(jQuery('.update-nag').length) {
							jQuery('#loading').css('top', '85px');
						} else {
							jQuery('#loading').css('top', '55px');
						}
						
						jQuery('#refresh_static').hide();
						jQuery('#refresh_active').show();
						jQuery('#loading').show();
						/*jQuery.ajax({
							type: 'GET',
							data: params,
							url: '".SB_PLUGIN_URL."/videolist.php?'+Math.floor((Math.random()*1000)+1) ,
							success: function(data) {
								//jQuery('#loading').hide();
								jQuery('#refresh_static').show();
								jQuery('#refresh_active').hide();
								jQuery('#loading').hide();
								jQuery('#videoitems').html(data);
							}
						});*/
						
						var data = {
							action: 'getVideoList',
							paged: '".(isset($_GET['paged']) ? $_GET['paged'] : '1')."',
						};
						
						jQuery.get(ajaxurl, data, function(response) {
							jQuery('#refresh_static').show();
							jQuery('#refresh_active').hide();
							jQuery('#loading').hide();
							jQuery('#videoitems').html(response);
						});
						
						return false;
					}
					
					
					";
			$html .= "</script>";
			$html .= "<div id='videoitems' style='margin-top: 0px;'>";
			
			$html .= "</div><script type='text/javascript'>refreshVideoList();</script>";
		
			$html .= "</div>";
			
			echo $html;
			
			require_once(SB_PLUGIN_DIR.'/sb_google_analytics.php');
	}
	
	public static function springboard_admin_settings_page() {
		require_once(SB_PLUGIN_DIR.'/admin/springboard_admin_settings.php');
	}
	/*
	 * Insert springboard js and css files
	 */
	public static function sb_scripts() {
		$script = '<script type="text/javascript" src="'.plugins_url( 'js/sb.js' , dirname(__FILE__) ).'"></script>';
		$script .= '<link rel="stylesheet" href="'.plugins_url( 'css/sb.css' , dirname(__FILE__) ).'" media="all" type="text/css" />';
		$script .= '<script type="text/javascript">var plugin_path = "'.SB_PLUGIN_URL.'";</script>';
		echo $script;
	}
	
	/*
	 * Function used to make calls to sb video api
	 * @return json
	 */
	public static function call($action, $params, $json) {
		$pub_id = get_option('sb_pub_id');
		$api_key = get_option('sb_api_key');
		
		//build_query
		$build_query = '';
		if(!empty($params)) {
			foreach($params as $key=>$val) {
				$build_query .= $key.'='.$val.'&';
			}	
		}
		
		
		//Verification
		if($pub_id == '' && $api_key == '') {
			$url = self::$url.$action."?".$build_query;
		} else {
			$url = self::$url.$action."?publisher_id=".$pub_id."&api_key=".$api_key."&".$build_query;
		}
		//echo $url;
		//exit();
		
		if(function_exists('curl_init')) {
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
			curl_setopt($curl, CURLOPT_URL, $url);
			$response = curl_exec($curl);
			
			if($json){
				$response = utf8_encode($response);
				$response = json_decode($response, true);
			}else{
				$response = $response;
			}
		} else {
			if($json){
				$response = json_decode(file_get_contents($url), true);
			}else{
				$response = file_get_contents($url);
			}
		}
		return $response;
	}
	
	/*
	 * Convert numbers to kb, mb, gb, tb
	 * @return int kb, mb, gb, tb
	 */
	public function formatBytes($size, $precision = 2) {
	    $base = log($size) / log(1024);
	    $suffixes = array('', 'kb', 'MB', 'GB', 'TB');   
	
	    return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
	}
		
	
	/**
	 * Convert second number into valid mysql time field
	 * @since Version 1.0
	 * @param int $time
	 * @return string
	 */
	public function Sec2Time($time) {
		
		$out='';
	 	if(is_numeric($time)) {
    
    		if($time >= 31556926) { //years
     			$out.= floor($time/31556926).' y ';
      			$time = ($time%31556926);
    		}
    		if($time >= 86400) { //days
     			$out.= floor($time/86400).'d ';
      			$time = ($time%86400);
    		}
    		if($time >= 3600) { //hours
      			$out.= sprintf ("%02d",floor($time/3600)).':';
      			$time = sprintf ("%02d",($time%3600));
    		}
    		
    		if($time >= 60) { //mins
      			$out.= sprintf ("%02d",floor($time/60)).':';
      			$time = sprintf ("%02d", ($time%60));
    		}else
    		{
    			$out.= '00:';
    		}
		    //sec
			$out.= sprintf ("%02d", floor($time));
    		return $out;
	  	} else {
	    	return (bool) FALSE;
	    }
	}
	
	/*
	 * Encode json to array
	 * @return array $x;
	 */
	public function jsonSimpleDecode($json) {
	
		$len = strlen($json);
	    if(!empty($json) && $len>1) {
			$t = preg_match("/{(.*?)}/", $json,$matches);
			$jsonText = $matches[0];
			
			$len = strlen($json);
	     	
			$out = '$x=';
	   		for ($i=0; $i<$len; $i++) {
	            if ($json[$i] == '{')        $out .= ' array(';
	            else if ($json[$i] == '}')    $out .= ')';
	            else if ($json[$i] == ':')    $out .= '=>';
	            else if ($json[$i] == '[')    $out .= ' array(';
	            else if ($json[$i] == ']')    $out .= ')';
	            else                         $out .= $json[$i];           
		    }
			eval($out . ';');
		    return $x;
		}
	}
	
	/*
	 * Check if string ends with another string
	 * @return boolean
	 */
	function endsWith($haystack, $needle) {
	
		$length = strlen($needle);
		if ($length == 0) {
			return true;
		}
	
		return (substr($haystack, -$length) === $needle);
	}
	
	public static function register_actions(){
		register_activation_hook(SB_PLUGIN_MAIN_FILE, array( 'SB_API', 'sb_install'));
		register_deactivation_hook(SB_PLUGIN_MAIN_FILE, array( 'SB_API', 'sb_uninstall'));
		add_action('load-media_page_springboard_video', array('SB_API', 'sb_library_page_load'));
		add_action('admin_init', array('SB_API', 'sb_add_settings'));
		add_action('admin_menu', array('SB_API', 'sb_generate_menu'));
		add_action('admin_head', array('SB_API', 'sb_scripts'));
		add_action('wp_ajax_getVideoList', array('SB_API', 'getVideoList_callback'));
		add_action('wp_ajax_updateVideoData', array('SB_API', 'updateVideoData_callback'));
		add_action('wp_ajax_updateThumbnail', array('SB_API', 'updateThumbnail_callback'));
		add_action('wp_ajax_loadPlayer', array('SB_API', 'loadPlayer_callback'));
		add_action('wp_ajax_create_snapshot', array('SB_API', 'create_snapshot_callback'));
		add_action('wp_ajax_editInputFields', array('SB_API', 'editInputFields_callback'));
		add_action('wp_ajax_get_partner_playlists', array('SB_API', 'get_partner_playlists_callback'));
		add_action('wp_ajax_loadPlaylistVideos', array('SB_API', 'loadPlaylistVideos_callback'));
		add_action('wp_ajax_getPartnerChannels', array('SB_API', 'getPartnerChannels_callback'));
		add_action('wp_ajax_loadPlaylistVideosDynamic', array('SB_API', 'loadPlaylistVideosDynamic_callback'));
		add_action('wp_ajax_getPartnerPlayers', array('SB_API', 'getPartnerPlayers_callback'));
		add_action('wp_ajax_search', array('SB_API', 'search_callback'));
		add_action('wp_ajax_deleteVideo', array('SB_API', 'deleteVideo_callback'));
		add_action('wp_ajax_reconvertVideo', array('SB_API', 'reconvertVideo_callback'));
		add_action('admin_notices', array('SB_API', 'my_admin_notice'));
	}
}
?>