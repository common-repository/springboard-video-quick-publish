<?php
/*
	Plugin Name: Springboard Video
	Plugin URI: http://www.springboardplatform.com
	Description: Easily integrate your Springboard videos to your posts and manage them directly from WordPress.
	Author: Springboard Video
	Version: 0.2.10
	Author URI: http://wordpress.org/extend/plugins/springboard-video-quick-publish/
	Text Domain: springboard-video-quick-publish
	Domain Path: /lang
*/	
	require_once('const.php');
	require_once(SB_PLUGIN_DIR.'/admin/config/config.php');
	require_once(SB_PLUGIN_DIR.'/lib/sb_api.php');
	
	SB_API::register_actions();
	
	//$sbTabs = array('sb_search' => "Videos", 'insert_playlist'=> "Add playlist", 'sb_content_library'=>'Content Library','sb_upload' => "Upload","sb_fetch_url"=>"Fetch URL", "sb_youtube" => "Youtube");
	$sbTabs = array('sb_search' => "Videos", 'insert_playlist'=> "Add playlist", 'sb_content_library'=>'Content Library','sb_upload' => "Upload","sb_fetch_url"=>"Fetch URL");
	
	if(isset($_POST['sb_action'])) {
		if(isset($_POST['video_id'])) {
			$params['video_id'] = intval($_POST['video_id']);
		}
		
		switch($_POST['sb_action']) {
			case "updateTitle":
				if(isset($_POST['title'])) {
					$params['title'] = $_POST['title'];
				}
				if(isset($_POST['description'])) {
					$params['description'] = $_POST['description'];
				}
				if(isset($_POST['tags'])) {
					$params['tags'] = $_POST['tags'];
				}
				if(isset($_POST['channel'])) {
					$params['channel'] = $_POST['channel'];
				}
				SB_API::call("updateTitle", $params, false);
				break;
			case "verifyKey":
				$params['publisher_id'] = intval($_POST['pub_id']);
				$params['api_key'] = $_POST['api_key'];
				echo SB_API::call("verifyKey", $params, false);
				exit();
				break;
			case "deleteVideo": 
				$sb->call("deleteVideo", $params);
				break;
		}
	}
	
	add_shortcode('springboard', 'sb_shortcode_handler');
	
	//Posts->Add New
	if (isset($_GET['tab']) && in_array($_GET['tab'], array_keys($sbTabs))) {
		add_filter( 'media_upload_tabs', 'sb_add_tabs');
	}
	
	function sb_add_tabs($tabs) {
		//$tabs_new = array('sb_search' => "Videos", 'insert_playlist'=> "Add playlist", 'sb_content_library'=>'Content Library','sb_upload' => "Upload","sb_fetch_url"=>"Fetch URL", "sb_youtube" => "Youtube");
		$tabs_new = array('sb_search' => "Videos", 'insert_playlist'=> "Add playlist",'sb_content_library'=>'Content Library', 'sb_upload' => "Upload","sb_fetch_url"=>"Fetch URL");
		return $tabs_new;
	}
	
	add_action('media_upload_sb_search', 'media_upload_sb_search');
	add_action('media_upload_sb_upload', 'media_upload_sb_upload');
	add_action('media_upload_sb_fetch_url', 'media_upload_sb_fetch_url');
	add_action('media_upload_sb_content_library', 'media_upload_sb_content_library');
	add_action('media_upload_insert_playlist', 'media_upload_insert_playlist');
	//add_action('media_upload_sb_youtube', 'media_upload_sb_youtube');
	
	function media_upload_sb_search() {
		global $errors;
		return wp_iframe('sb_search_tab', $errors );
	}
	
	function media_upload_sb_youtube() {
		global $errors;
		return wp_iframe('sb_youtube', $errors );
	}
	
	function media_upload_insert_playlist() {
		global $errors;
		return wp_iframe('insert_playlist', $errors );
	}
	
	function media_upload_sb_upload() {
		global $errors;
		media_upload_header();
		return wp_iframe('sb_upload', $errors );
	}
	
	function sb_upload() {
		global $errors;
		sb_upload_tab();
	}
	
	function media_upload_sb_fetch_url() {
		global $errors;
		media_upload_header();
		return wp_iframe('sb_fetch_url', $errors );
	}
	
	function media_upload_sb_content_library() {
		global $errors;
		media_upload_header();
		return wp_iframe('sb_content_library', $errors );
	}
	
	function sb_fetch_url() {
		global $errors;
		sb_fetch_url_tab();
	}
	
	function sb_content_library() {
		global $errors;
		sb_content_library_tab();	
	}
	
	function sb_youtube() {
		global $errors;
		media_upload_header();
		sb_youtube_tab();
	}
	
	function sb_update_thumbnail(){
		global $errors;
		media_upload_header();
		
	}
	
	function insert_playlist() {
		global $errors;
		media_upload_header();
		sb_insert_playlist_tab();
	}
	
	function sb_shortcode_handler($atts) {
		$sb = new SB_API();
		$requestURI = $_SERVER["REQUEST_URI"];
 		
		extract( shortcode_atts( array(
			'type' => 'playlist',
			'id' => '0',
 			'width' => '640',
 			'height' => '360',
 			'partner_id' => get_option('sb_pub_id'),
 			'player' => get_option('sb_player'),
			'video_num' => 5,
		), $atts ) );
		if( ($strPos = strpos($id,"?")) !== false ) {
			$id = substr($id,0,$strPos);
		}
		$params['type'] = $type;
		$params['youtube_flag'] = 0;
		$params['id'] = $id;
		$params['width'] = $width;
		$params['height'] = $height;
		$params['player_id'] = $player;
		$params['partner_id'] = $partner_id;
		$params['video_num'] = $video_num;
		$params['xml_feed'] = XML_FEED_PATH.$partner_id."/0/0/0/0/".$video_num;
		$params['amp'] = 0;
		if($type == 'youtube') {
			$params['type'] = 'video';
			$params['id'] = 0;
			$params['youtube_flag'] = 1;
		}
		if( $sb->endsWith($requestURI , "/amp/") || $sb->endsWith($requestURI , "?amp=1") ) {
			$params['amp'] = 1;
			add_action( 'amp_post_template_head', 'amp_sb_script' );
		}
		$embedCode = $sb->call('getEmbedCode', $params, false);
	
		if($type == 'youtube') {
			preg_match('@name="movie" value="(.*)"@i', $embedCode, $matches);
			
			$new_path = preg_replace('@video@i', 'youtube', $matches[1]);
			$new_path = preg_replace('@'.$player.'(.*)@i', $player.'/'.$id.'/', $new_path);
			
			$embedCode = preg_replace('@name="movie" value="(.*)"@', 'name="movie" value="'.$new_path.'"', $embedCode);
			$embedCode = preg_replace('@embed src="([^"]+)"@', 'embed src="'.$new_path.'"', $embedCode);
		}
		return $embedCode;
	}
	
	function amp_sb_script() {
		?>
		<script async custom-element="amp-springboard-player" src="https://cdn.ampproject.org/v0/amp-springboard-player-0.1.js"></script>
		<?php
	}
	
	function sb_search_tab(){
		require_once("sb_search.php");
	}
	
	function sb_insert_playlist_tab(){
		require_once("sb_playlist.php");
	}
	
	function sb_upload_tab(){
		require_once("sb_upload.php");
	}
	
	function sb_fetch_url_tab(){
		require_once("sb_fetch_url.php");
	}
	function sb_content_library_tab(){
		require_once("sb_content_library.php");
	}
	function sb_youtube_tab() {
		require_once("sb_youtube.php");
	}
?>