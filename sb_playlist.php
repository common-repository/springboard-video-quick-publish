<?php function sb_playlist(){
$params['paged'] = isset($_GET['paged']) ? $_GET['paged'] : 1;
$params['partner_id'] = get_option('sb_pub_id');
$params['search_value'] = isset($_POST['search_value']) ? $_POST['search_value'] : "";
	
//$sb = new SB_API();


$params['sb_player'] = get_option('sb_player');
$player_size = SB_API::call('getPlayerWH', $params, true);
?>
<script type="text/javascript">

	function loadPartnerPlaylists(partner_id) {
		var params = {};

		params['partner_id'] = partner_id;
		params['json'] = true;
		params['sb_action'] = "get_partner_playlists";
		
		var data = {
			action: 'get_partner_playlists',
			params: params,
		};
		
		jQuery.post(ajaxurl, data, function(response) {
			var json_data = JSON.parse(response);
				if( jQuery.isEmptyObject(json_data)) {
					return;
				}
				var html = '';
				jQuery('#select_playlists').html(function(){
					jQuery.each(json_data, function(i, val) {
						var selected_val = '';
						if(i == 0) {
							selected_val = 'selected';
						}
						html += '<option '+selected_val+' style="padding: 0px;" title='+json_data[i].playlists.id+' value='+json_data[i].playlists.id+'>'+json_data[i].playlists.title+'</option>';
					});
					return html;
				});
				loadPlaylistVideos(false, <?php echo get_option('sb_pub_id'); ?>, json_data[0].playlists.id);
		});

		/*jQuery.ajax({
			type: "POST",
			data: params,
			url: "<?php echo SB_PLUGIN_URL."/ajax.php"; ?>",
			success: function(data){
				var json_data = JSON.parse(data);
				if( jQuery.isEmptyObject(json_data)) {
					return;
				}
				var html = '';
				jQuery('#select_playlists').html(function(){
					jQuery.each(json_data, function(i, val) {
						var selected_val = '';
						if(i == 0) {
							selected_val = 'selected';
						}
						html += '<option '+selected_val+' style="padding: 0px;" title='+json_data[i].playlists.id+' value='+json_data[i].playlists.id+'>'+json_data[i].playlists.title+'</option>';
					});
					return html;
				});
				loadPlaylistVideos(false, <?php echo get_option('sb_pub_id'); ?>, json_data[0].playlists.id);
			}
		});*/
		return false;
	}
	
	loadPartnerPlaylists(<?php echo get_option('sb_pub_id'); ?>);
	
	function loadPlaylistVideos(channel_id, partner_id, playlist_id) {
		jQuery('#video_number').hide();
		var params = {};
		jQuery('#loading').show();
		jQuery('#videolist').hide();
		jQuery('#select_video').css('background', 'url(\'<?php echo SB_PLUGIN_URL;?>img/add_to_post_d.png\') repeat scroll 0 0 transparent');
		document.getElementById('dynamic_select_playlists').selectedIndex = -1;

		jQuery('#tags').hide();
		jQuery('#select_video').show();

		params['channel_id'] = channel_id;
		params['partner_id'] = partner_id;
		if(playlist_id != undefined) {
			params['playlist_id'] = playlist_id;
		} else {
			params['playlist_id'] = jQuery('#select_playlists').val();
		}
		params['json'] = true;
		params['sb_action'] = "loadPlaylistVideos";
		
		jQuery('#dyn_playlist_id').val("");
		jQuery('#playlist_id').val(params['playlist_id']);
		
		var data = {
			action: 'loadPlaylistVideos',
			params: params,
		};
		
		jQuery.post(ajaxurl, data, function(response) {
			var json_data = JSON.parse(response);
			if(typeof json_data.length !== 'undefined') {
				jQuery('#user_video_num').val(json_data.length);
			} 
			else jQuery('#user_video_num').val(0);
			jQuery('#loading').hide();
			var html = '<div>';
			jQuery('#videolist').html(function(){
				jQuery.each(json_data, function(i, val){
					html += '<div class="playlist_video_result" align="center" >';
					html += 	'<div style="width: 120px; height: 135px;">';
					html += 		'<div id="thumbnail_bg"><img class="thumbnail_search" width="115" height="86" src='+json_data[i].image+'></div>';
					html += 		'<div class="sb_title_search">'+ stripslashes(json_data[i].title) + '</div>';
					html += 	'</div>';
					html += '</div>';
				});
				return html + '</div>';
			});
			jQuery('#videolist').show();
			if(typeof json_data.length !== 'undefined')
				jQuery('#select_video').css('background', 'url(\'<?php echo SB_PLUGIN_URL;?>/img/add_to_post_d.png\') repeat scroll 0 -20px transparent');
		});
		
		
		/*jQuery.ajax({
			type: "POST",
			data: params,
			url: "<?php echo SB_PLUGIN_URL."/ajax.php"; ?>",
			success: function(data){
				var json_data = JSON.parse(data);
				if(typeof json_data.length !== 'undefined') {
					jQuery('#user_video_num').val(json_data.length);
				} 
				else jQuery('#user_video_num').val(0);
				jQuery('#loading').hide();
				var html = '<div>';
				jQuery('#videolist').html(function(){
					jQuery.each(json_data, function(i, val){
						html += '<div class="playlist_video_result" align="center" >';
						html += 	'<div style="width: 120px; height: 135px;">';
						html += 		'<div id="thumbnail_bg"><img class="thumbnail_search" width="115" height="86" src='+json_data[i].image+'></div>';
						html += 		'<div class="sb_title_search">'+ stripslashes(json_data[i].title) + '</div>';
						html += 	'</div>';
						html += '</div>';
					});
					return html + '</div>';
				});
				jQuery('#videolist').show();
				if(typeof json_data.length !== 'undefined')
					jQuery('#select_video').css('background', 'url(\'<?php echo SB_PLUGIN_URL;?>/img/add_to_post_d.png\') repeat scroll 0 -20px transparent');
			}
		});*/
		return false;
	}
	
	function getPartnerChannels(partner_id) {
		var params = {};

		params['partner_id'] = partner_id;
		params['json'] = true;
		params['sb_action'] = "getPartnerChannels";
		
		var data = {
			action: 'getPartnerChannels',
			params: params,
		};
		
		jQuery.post(ajaxurl, data, function(response) {
			var json_data = JSON.parse(response);
			var html = '<option style="padding: 0px;" value="v_10">Latest videos on site</option>';
			html += '<option style="padding:0px;" value="tag">Latest videos by tag</option>';
			jQuery('#dynamic_select_playlists').html(function(){
				jQuery.each(json_data, function(i, val){
					html += '<option style="padding: 0px;" value=c_'+json_data[i].Channel.id+'>Latest in '+json_data[i].Channel.channel_name+'</option>';
				});
				return html;
			});
		});
		
		
		/*jQuery.ajax({
			type: "POST",
			data: params,
			url: "<?php echo SB_PLUGIN_URL."/ajax.php"; ?>",
			success: function(data){
				var json_data = JSON.parse(data);
				var html = '<option style="padding: 0px;" value="v_10">Latest videos on site</option>';
				html += '<option style="padding:0px;" value="tag">Latest videos by tag</option>';
				jQuery('#dynamic_select_playlists').html(function(){
					jQuery.each(json_data, function(i, val){
						html += '<option style="padding: 0px;" value=c_'+json_data[i].Channel.id+'>Latest in '+json_data[i].Channel.channel_name+'</option>';
					});
					return html;
				});
			}
		});*/
		return false;
		
	}
	getPartnerChannels(<?php echo get_option('sb_pub_id'); ?>);
	
	function loadDynamicPlaylistVideos(channel_id, partner_id) {
		var params = {};
		var playlist_id = jQuery('#dynamic_select_playlists').val();
		
		if(playlist_id != 'tag'){
			jQuery('#loading').show();
			jQuery('#videolist').hide();
			jQuery('#select_video').css('background', 'url(\'<?php echo SB_PLUGIN_URL;?>img/add_to_post_d.png\') repeat scroll 0 0 transparent');
		}
		document.getElementById('select_playlists').selectedIndex = -1;
		
		jQuery('#video_number').show();
		jQuery('#tags').hide();
		jQuery('#select_video').show();

		params['channel_id'] = channel_id;
		params['partner_id'] = partner_id;
		params['playlist_id'] = playlist_id;
		params['json'] = true;
		params['sb_action'] = "loadPlaylistVideosDynamic";
		params['video_num'] = jQuery('#tag_videos_number').val();
		
		jQuery('#playlist_id').val("");
		jQuery('#dyn_playlist_id').val(playlist_id);
		
		if(playlist_id == 'tag') {
			show_dynamic_tag_inputs();
			jQuery('#tags').show();
			return false;
		}
		
		var data = {
			action: 'loadPlaylistVideosDynamic',
			params: params,
		};
		
		jQuery.post(ajaxurl, data, function(response) {
			var json_data = jQuery.parseJSON(response);
			var html = '<div>';
			jQuery('#loading').hide();
			jQuery('#videolist').html(function(){
				jQuery.each(json_data.VideoList, function(i, val){
					
					html += '<div class="playlist_video_result" align="center" style="margin-left: 40px;height: 145px;margin-top:14px; margin-bottom:14px;">';
					html += 	'<div style="width: 120px;height: 135px;">';
					html += 		'<div id="thumbnail_bg"><img class="thumbnail_search" width="115" height="86" src='+json_data.VideoList[i].Video.image+'></div>';
					html += 		'<div style="width: 115px;" class="sb_title_search" >'+ stripslashes(json_data.VideoList[i].Video.title) + '</div>';
					html += 	'</div>';
					html += '</div>';
				});
				return html + '</div>';
			});
			jQuery('#videolist').show();
			jQuery('#select_video').css('background', 'url(\'<?php echo SB_PLUGIN_URL;?>img/add_to_post_d.png\') repeat scroll 0 -20px transparent');
		});
		
		/*jQuery.ajax({
			type: "POST",
			data: params,
			url: "<?php echo SB_PLUGIN_URL."/ajax.php"; ?>",
			success: function(data){
				var json_data = jQuery.parseJSON(data);
				var html = '<div>';
				jQuery('#loading').hide();
				jQuery('#videolist').html(function(){
					jQuery.each(json_data.VideoList, function(i, val){
						
						html += '<div class="playlist_video_result" align="center" style="margin-left: 40px;height: 145px;margin-top:14px; margin-bottom:14px;">';
						html += 	'<div style="width: 120px;height: 135px;">';
						html += 		'<div id="thumbnail_bg"><img class="thumbnail_search" width="115" height="86" src='+json_data.VideoList[i].Video.image+'></div>';
						html += 		'<div style="width: 115px;" class="sb_title_search" >'+ stripslashes(json_data.VideoList[i].Video.title) + '</div>';
						html += 	'</div>';
						html += '</div>';
					});
					return html + '</div>';
				});
				jQuery('#videolist').show();
				jQuery('#select_video').css('background', 'url(\'<?php echo SB_PLUGIN_URL;?>/img/add_to_post_d.png\') repeat scroll 0 -20px transparent');
			}
		});*/
		return false;
	}
	
	function show_dynamic_tag_inputs(status) {
		
		if(status == 'submit') {
			var params = {};
			jQuery('#loading').show();
			jQuery('#videolist').hide();
			jQuery('#select_video').css('background', 'url(\'<?php echo SB_PLUGIN_URL;?>img/add_to_post_d.png\') repeat scroll 0 0 transparent');
			var playlist_id = jQuery('#dynamic_select_playlists').val();
			params['partner_id'] = <?php echo get_option('sb_pub_id'); ?>;
			params['playlist_id'] = jQuery('#dynamic_select_playlists').val();
			params['json'] = true;
			params['tag'] = jQuery('#dyn_playlist_tag').val();
			params['sb_action'] = "loadPlaylistVideosDynamic";
			params['video_num'] = jQuery('#tag_videos_number').val();
			params['tag'] = jQuery('#dyn_playlist_tag').val();
			
			var data = {
				action: 'loadPlaylistVideosDynamic',
				params: params,
			};
			
			jQuery.post(ajaxurl, data, function(response) {
				jQuery('#loading').hide();
					var json_data = JSON.parse(response);
					var html = '<div>';
					jQuery('#videolist').html(function(){
						jQuery.each(json_data.VideoList, function(i, val){
							html += '<div class="video_result" align="center" style="margin-left: 40px;height: 145px;margin-top:14px; margin-bottom:14px;">';
							html += 	'<div style="width: 120px;height: 135px;">';
							html +=			'<div id="thumbnail_bg"><img class="thumbnail_search" width="115" height="86" src='+json_data.VideoList[i].Video.image+'></div>';
							html += 		'<div style="width: 115px;" class="sb_title_search" >'+ stripslashes(json_data.VideoList[i].Video.title) + '</div>';
							html += 	'</div>';
							html += '</div>';
						});
						return html + '</div>';
					});
					jQuery('#videolist').show();
					jQuery('#select_video').css('background', 'url(\'<?php echo SB_PLUGIN_URL;?>/img/add_to_post_d.png\') repeat scroll 0 -20px transparent');
			});
			
			
			/*jQuery.ajax({
				type: "POST",
				data: params,
				url: "<?php echo SB_PLUGIN_URL."/ajax.php"; ?>",
				success: function(data){
					jQuery('#loading').hide();
					var json_data = JSON.parse(data);
					var html = '<div>';
					jQuery('#videolist').html(function(){
						jQuery.each(json_data.VideoList, function(i, val){
							html += '<div class="video_result" align="center" style="margin-left: 40px;height: 145px;margin-top:14px; margin-bottom:14px;">';
							html += 	'<div style="width: 120px;height: 135px;">';
							html +=			'<div id="thumbnail_bg"><img class="thumbnail_search" width="115" height="86" src='+json_data.VideoList[i].Video.image+'></div>';
							html += 		'<div style="width: 115px;" class="sb_title_search" >'+ stripslashes(json_data.VideoList[i].Video.title) + '</div>';
							html += 	'</div>';
							html += '</div>';
						});
						return html + '</div>';
					});
					jQuery('#videolist').show();
					jQuery('#select_video').css('background', 'url(\'<?php echo SB_PLUGIN_URL;?>/img/add_to_post_d.png\') repeat scroll 0 -20px transparent');
				}
			});*/
			return false;
		}
	}
	
	function get_quicktag() {
		var type = '';
		var videos = new Array();
		
		var value = jQuery('#dynamic_select_playlists').val();
		var videos_number = jQuery('#tag_videos_number').val();
		if(value){
			var dynamic_type = value.substr(0,1);
			if(dynamic_type == 'v'){
				type = 'latest';
				videos.push(0);
			}else if(dynamic_type == 'c'){
				type = 'channel';
				videos.push(value.substr(2));
			}else if(dynamic_type == 't'){
				type = 'tag';
				videos.push(jQuery('#dyn_playlist_tag').val());
			}
		}else{
			type = 'playlist';
			videos.push(jQuery('#select_playlists').val());
			var user_video_num = jQuery('#user_video_num').val();
			if(user_video_num) {
				videos_number = user_video_num;
			}
			else videos_number = 0;
		}
		create_quicktag(videos, type, '<?php echo get_option('sb_player'); ?>', videos_number);

	}
	
	function sbPlaylistCheckEnter(event) {
	if( (event.keyCode || event.which || event.charCode || 0) === 13) {
		show_dynamic_tag_inputs('submit');
		if(event.preventDefault) event.preventDefault();
		else event.returnValue = false;
	}
}
</script>
<script type="text/javascript" src="<?php echo SB_PLUGIN_URL; ?>/js/sb.js"></script>

<div id="background">
	<form action="<?php echo SB_PLUGIN_URL.'select_player.php'; ?>" style="float: left; margin-top: 12px; width: 100%; *padding-bottom: 25px" method="post" id="playlistForm" name="playlistForm">
		<div style="float: left; width: 220px; margin-left: 15px; margin-top: 20px;">
			<div style="font-size: 10px; font-weight: bold; width: 210px;">USER PLAYLISTS</div>
			<input type="hidden" name="player_width" id="player_width" value="<?php print_r($player_size[0]['VideoPlayer']['width']); ?>" />
			<input type="hidden" name="player_height" id="player_height" value="<?php print_r($player_size[0]['VideoPlayer']['height']); ?>" />
			<input type="hidden" name="user_video_num" id="user_video_num" value="10" />
			<select id="select_playlists" onchange="loadPlaylistVideos(false, <?php echo get_option('sb_pub_id'); ?>);" style="margin: 0; width: 210px;" size="10" name="select_playlists">
			</select>
			<div style="font-size: 10px; font-weight: bold; width: 210px; height: 14px; margin-top: 3px;">DYNAMIC PLAYLISTS</div>
			<div style="font-size: 10px; width: 210px; height: 16px;">POPULATED AUTOMATICALLY</div>
			<select id="dynamic_select_playlists" onclick="loadDynamicPlaylistVideos(false, <?php echo get_option('sb_pub_id'); ?>);" style="margin: 0; width: 210px;" size="9" name="dynamic_select_playlists">
			</select>
			<div id="dynamic_select_playlists_inputs" style="display: block; width: 210px;">
				<div style="display: none; width: 90px; float: left; margin-left: 39px; margin-top: 5px;" id="tags"> 
					<div style="float: left; padding: 3px;">Tag:</div>
					<input id="dyn_playlist_tag" name="dyn_playlist_tag" type="text" style="position: absolute; width: 55px; height: 21px; padding: 0; font-size: 12px; *height: 19px;" onkeypress="sbPlaylistCheckEnter(event);" />
				</div>
				<div id="video_number" style="display: none; float: right; margin-top: 5px;">
					<select id="tag_videos_number" name="tag_videos_number" style="height: 23px; float: left; padding: 0px; width: 40px; height: 21px;">
						<option style="padding:0px;" value="5">5</option>
						<option style="padding:0px;" value="10">10</option>
						<option style="padding:0px;" value="15">15</option>
						<option style="padding:0px;" value="25">25</option>
						<option style="padding:0px;" value="50">50</option>
					</select>
					<input type="button" value="GO" class="go_btn" onclick="show_dynamic_tag_inputs('submit');" style="float: left; margin-left: 8px; width: 27px; height: 21px;border: 0;cursor:pointer;" />
				</div>
			</div>
		</div>
		<div style="float: left; width: 420px; margin-left: 15px;">
			<div style="float: right; width: 420px;">
				<a href="javascript: void(0);" id="select_video" onclick="get_quicktag();" style="float: right; background: url('<?php echo SB_PLUGIN_URL; ?>img/add_to_post_d.png') repeat scroll 0 0 transparent; border-radius: 3px 3px 3px 3px;border-style: solid;border-width: 0px;cursor: pointer;width: 71px; height: 20px;"></a>
			</div>
			<div style="float: right; width: 420px;">
				<div id="videolist" style="float: left; padding-bottom: 0px; height: 352px; overflow: auto; width: 420px; margin-left: 0px; margin-top: 13px; display: none; background: url('<?php echo SB_PLUGIN_URL; ?>img/bg_gray_1.png') repeat scroll 0 0 transparent; *overflow: visible; width: 420px; *height: 100%; *padding-bottom: 14px;"></div>
			</div>
		</div>
		<!--<div id="loading"><img style="height:30px;margin-top: 20px;position: absolute;top: 200px;left: 400px;" src="<?php echo SB_PLUGIN_URL; ?>img/ajax-loader2.gif" /></div>-->
		<!--<a href="javascript: void(0);" id="select_video" class="next-button" onclick="get_quicktag();" style="margin-top: -10px;margin-right: 24px;" >
			<img src="<?php echo SB_PLUGIN_URL; ?>img/add_to_post.png" />
		</a>-->
		<input type="hidden" name="playlist" value="playlist"/>
		<input type="hidden" name="dyn_playlist" value="dyn_playlist"/>
		<input type="hidden" name="playlist_id[]" id="playlist_id"/>
		<input type="hidden" name="dyn_playlist_id[]" id="dyn_playlist_id"/>
	</form>
	
</div>
<?php 
}
sb_playlist();
//wp_iframe("sb_playlist");
?>
<script>
	var topWindow = getTopWindow();
	
	var width = jQuery(window).width();
	var height = jQuery(window).height();
	topWindow.animateModalSize(705, 550);

</script>
<?php require_once(SB_PLUGIN_DIR.'/sb_google_analytics.php') ;?>