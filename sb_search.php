<?php
require_once(SB_PLUGIN_DIR.'/lib/sb_cpv.php');
$numCpvVideos = SbCpv::getCpvVideos();

if($numCpvVideos){
	?>
	<script type='text/javascript'>
	var sbUploadWidth = 780;
	</script>
	<style type='text/css' >
		.sbContentWrapper{
			clear:both;
			width:730px;
		}
		#cpvVideos {
			margin-left:25px;
		}
	</style>
	<?php
}
else {
	?>
	<script type='text/javascript'>
		var sbUploadWidth = 630;
	</script>
	<style type='text/css' >
		.sbContentWrapper{
			clear:both;
			width:607px;
		}
	</style>
	<?php
}
?>
<style type="text/css">
#pagination a.page-numbers {background-color: #FFFFFF; color: #000000; padding: 2px 5px;text-decoration:none;font-size:11px;}
#pagination .current{background-color:#464646; color: #86D6F8; padding: 2px 5px; text-decoration: none;font-size:11px;}

#sbSearchHeader{
width:567px;
margin-top:16px;

}

.sb_title_search_n {
font-family: Georgia;
    font-size: 11px;
    font-style: italic;
    height: 35px;
    overflow: hidden;
    width: 120px;
	color:#E5E5E5;
}
</style>
<?php

media_upload_header();

require_once(ABSPATH . 'wp-admin/includes/admin.php');
require_once('springboardvideo.php');
require_once(SB_PLUGIN_DIR.'/lib/sb_cpv.php');
function sb_search(){
	$params['paged'] = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
	$params['partner_id'] = get_option('sb_pub_id');
	$params['search_value'] = (isset($_POST['search_value'])) ? $_POST['search_value'] : '' ;
	
	$sb = new SB_API();
	$videos = $sb->call('search', $params, true);
?>
<script language="javascript" type="text/javascript">
	function search(param) {
		var search_value = jQuery('#search_value').val();
		if(param && param == 'refreshVideos') {
			search_value = '';
		}
		
		if(search_value == 'Search Videos'){
			return false;
		}
		
		var params = {};

		var pretty_url = "<?php echo PRETTY_URL; ?>";
	
		params['search_value'] = encodeURIComponent(search_value);
		
		params['paged'] = 1;
		params['sb_action'] = "search";
		
		jQuery('#loading').show();
		
		var data = {
			action: 'search',
			params: params,
		};
		
		jQuery.post(ajaxurl, data, function(response) {
			jQuery('#loading').hide();
			jQuery('#pagination').hide();
			jQuery('#videolist').html(response);
			var json_data = jQuery.parseJSON(response);
			var html = '';
			jQuery('#videolist').html(function(){
				jQuery.each(json_data, function(i, val){
					html += '<div class="video_result" align="center" style="margin-top: 14px; margin-bottom: 14px;">';
					html += '<div style="list-style: none;width: 120px;height: 142px;z-index: 999px;margin: 0;">';
					html += '<div id="thumbnail_bg">';
					html += '<img width="115" height="86" class="thumbnail_search" src=http://'+json_data[0].Video.cname+'.'+pretty_url+''+json_data[0].Video.domain+''+json_data[i].Video.image_lg+'>';
					html += '</div>';
					html += '<div class="sb_title_search">'+ json_data[i].Video.title + '</div>';
					html += '</div>';
					html += '<label onclick="selectVideo();" class="label_check" for="embed_video_'+json_data[i].Video.id+'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
					html += '<input type="checkbox" class="select_video" id="embed_video_'+json_data[i].Video.id+'" name="embed_video[]" value='+ json_data[i].Video.id +'>';
					html += '</label>';
					html += '</div>';
				});
				return html;
			});
			jQuery("#select_player").show();
			if(search_value == ""){ 
				jQuery('#pagination').show();
			}
		});
		
		
		/*jQuery.ajax({
			type: "POST",
			data: params,
			url: "<?php echo SB_PLUGIN_URL."/ajax.php"; ?>",
			success: function(data){
				
				jQuery('#loading').hide();
				jQuery('#pagination').hide();
				jQuery('#videolist').html(data);
				var json_data = jQuery.parseJSON(data);
				var html = '';
				jQuery('#videolist').html(function(){
					jQuery.each(json_data, function(i, val){
						html += '<div class="video_result" align="center" style="margin-top: 14px; margin-bottom: 14px;">';
						html += '<div style="list-style: none;width: 120px;height: 142px;z-index: 999px;margin: 0;">';
						html += '<div id="thumbnail_bg">';
						html += '<img width="115" height="86" class="thumbnail_search" src=http://'+json_data[0].Video.cname+'.'+pretty_url+''+json_data[0].Video.domain+''+json_data[i].Video.image_lg+'>';
						html += '</div>';
						html += '<div class="sb_title_search">'+ json_data[i].Video.title + '</div>';
						html += '</div>';
						html += '<label onclick="selectVideo();" class="label_check" for="embed_video_'+json_data[i].Video.id+'">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
						html += '<input type="checkbox" class="select_video" id="embed_video_'+json_data[i].Video.id+'" name="embed_video[]" value='+ json_data[i].Video.id +'>';
						html += '</label>';
						html += '</div>';
					});
					return html;
				});
				jQuery("#select_player").show();
				if(search_value == ""){ 
					jQuery('#pagination').show();
				}
			}
		});*/
		return false;
	}

	function refreshVideos() {
		search('refreshVideos');
	}

	var d = document;
	var safari = (navigator.userAgent.toLowerCase().indexOf('safari') != -1) ? true : false;
	var chrome = (navigator.userAgent.toLowerCase().indexOf('chrome') != -1) ? true : false;
	var gebtn = function(parEl,child) { return parEl.getElementsByTagName(child); };
	selectVideo = function() {
		jQuery('#select_video').css('background', 'url(\'<?php echo SB_PLUGIN_URL;?>img/add_to_post_d.png\') repeat scroll 0 -21px transparent');
		
	    var body = gebtn(d,'body')[0];
	    body.className = body.className && body.className != '' ? body.className + ' has-js' : 'has-js';
	
	    if (!d.getElementById || !d.createTextNode) return;
	    var ls = gebtn(d,'label');
	    for (var i = 0; i < ls.length; i++) {
	        var l = ls[i];
	        if (l.className.indexOf('label_') == -1) continue;
	        var inp = gebtn(l,'input')[0];
	        if (l.className == 'label_check') {
	            l.className = (safari && inp.checked == true || inp.checked) ? 'label_check c_on' : 'label_check c_off';
	            l.onclick = check_it;
	        };
	    };
	};
	var check_it = function() {
	    var inp = gebtn(this,'input')[0];
	    if (this.className == 'label_check c_off' || (!safari && inp.checked)) {
	        this.className = 'label_check c_on';
	        jQuery('#select_video').css('background', 'url(\'<?php echo SB_PLUGIN_URL;?>img/add_to_post_d.png\') repeat scroll 0 -21px transparent');
	        if (safari) inp.click();
	        if(chrome) inp.checked = true;
	    } else {
	        this.className = 'label_check c_off';
	        if(chrome) inp.checked = false;
			if(jQuery("input:checked").length == 0) {
				jQuery('#select_video').css('background', 'url(\'<?php echo SB_PLUGIN_URL;?>img/add_to_post_d.png\') repeat scroll 0 0 transparent');
	        }
			if (safari) inp.click();
	        
	    };
	};
	
	function Searchclicked(){
		jQuery("#search_value").val("");
	}
	
	function get_quicktag(){
		var videos = new Array();
		var cpv_videos = new Array();
		var n = jQuery("input:checked").length;
		if(n < 1) return;
		jQuery("input:checked").each(function() {
			if(this.id.indexOf('cpv_video')==-1)
				videos.push(jQuery(this).val());
			else cpv_videos.push(jQuery(this).val());
		});
		if(videos.length)
			create_quicktag(videos, 'video', '<?php echo get_option('sb_player'); ?>', 0);
		if(cpv_videos.length)
			create_quicktag(cpv_videos, 'cpvVideo', '<?php echo get_option('sb_player'); ?>', 0);

	}

</script>
<?php 
$params['paged'] = (isset($_GET['paged']) && ($_GET['paged'] > 0)) ? intval($_GET['paged']) : 1;
$page_links = paginate_links( 
	array(
		'base' 			=> add_query_arg('paged', '%#%'),
		'format' 		=> '',
		'total' 	 	=> $videos[0]['Video']['videos_num'],
		'end_size'      => 1,
		'mid_size'      => 2,
		'current' 		=> $params['paged']
	)
);

$params['sb_player'] = get_option('sb_player');
$player_size = $sb->call('getPlayerWH', $params, true);
?>
<script type="text/javascript" src="<?php echo SB_PLUGIN_URL; ?>js/sb.js"></script>
<link rel="stylesheet" href="<?php echo SB_PLUGIN_URL; ?>css/sb.css" media="all" type="text/css" />
<form action="<?php echo SB_PLUGIN_URL.'select_player.php'; ?>" method="post" id="searchForm" name="searchForm" style="float: left;padding-left: 20px; *padding-bottom: 20px;">
	
	<div id="refresh header" style="width: 567px; float: left; height: 22px; margin-top: 16px;">
		<div style="float: left; width: 16px; height: 16px; margin-top: 3px;">
			<img alt="" border="" src="<?php echo SB_PLUGIN_URL;?>img/refresh_new.png" style="cursor: pointer;" onclick="refreshVideos();">
		</div>
		<div style="float: left; width: 270px; height: 22px; margin-left: 8px; color: #919191; font-size: 10px; line-height: 1.2em;">
			Please refresh and allow for a few minutes
			until any uploaded videos are ready to be inserted into this post.
		</div>
	</div>
	<div id='sbSearchHeader'>
		<div id="search" style="float: left;margin-bottom: 10px;margin-left: 0px; margin-top: 17px;">
			<input type="text" name="search_value" onclick="Searchclicked();" onkeypress="return search_keypress(event);" id="search_value" value="Search Videos" style="font-size: 13px;background-color: #"/>
			<input type="submit" id="search_button" onclick="search();return false;" value="" style="float: right;width: 17px;margin-top: 5px;"/>
		</div>
		<div id="pagination" align="center" style="float: left; margin-top: 22px; width: 300px; margin-left: 0px; *width: 285px;"><?php echo $page_links ?></div>
		<div style="float: right; width: 90px; height: 23px; margin-top: 21px;">
			<a href="javascript: void(0);" id="select_video" class="next-button" style="display: block;" onclick="get_quicktag();"></a>
		</div>
	</div>
	<input type="hidden" name="player_width" id="player_width" value="<?php print_r($player_size[0]['VideoPlayer']['width']); ?>" />
	<input type="hidden" name="player_height" id="player_height" value="<?php print_r($player_size[0]['VideoPlayer']['height']); ?>" />
	<div class='sbContentWrapper'>
		<div id="videolist" style="float: left;margin-top: 0px; margin-left: 0px;background: url('<?php echo SB_PLUGIN_URL; ?>img/bg_gray_1.png') repeat scroll 0 0 transparent; height: 438px; overflow: auto; width: 567px; padding-bottom: 0px; *overflow: visible; *width: 550px; *height: 100%; *padding-bottom: 14px;">
			<?php 
			if( count($videos) ) {
				foreach($videos as $key => $video){ 
					$sbActYear = substr($video['Video']['activation_date'],0,4);
					$sbActMounth = substr($video['Video']['activation_date'],5,2);
					$sbActDay = substr($video['Video']['activation_date'],8,2);
					$currentTime = time();
					$actDate = mktime(0,0,0,$sbActMounth,$sbActDay,$sbActYear);
					if( $currentTime < $actDate || $sbActYear == "0000" ) {
						$not_active = true;
					}
					else $not_active = false;
					?>
					<div class="video_result" align="center" style="margin-top: 14px; margin-bottom: 14px;">
						<div style="width: 120px; height: 142px; z-index: 999; margin: 0;">
							<div id="thumbnail_bg">
								<img class="thumbnail_search" width="115" height="86" src="<?php echo $video['Video']['image_lg'] ? "http://".$videos[0]['Video']['cname'].".".PRETTY_URL."".$videos[0]['Video']['domain'].$video['Video']['image_lg'] : SB_PLUGIN_URL."img/image_not_available.png"; ?>">
							</div>
							<?php
							if( $not_active )
								$titleClass = "sb_title_search_n";
							else $titleClass = "sb_title_search";
							?>
							<div class='<?php echo $titleClass; ?>'><?php echo stripslashes(strip_tags($video['Video']['title'])); ?></div>
						</div>
						<label class="label_check" onclick="selectVideo();" for="embed_video_<?php echo $video['Video']['id']?>">&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type="checkbox" class="select_video" id="embed_video_<?php echo $video['Video']['id']?>" name="embed_video[]" value='<?php echo $video['Video']['id']?>'></label>
					</div>
					<?php 
				}
			}
			?>
		</div>
		<?php SbCpv::displayCpv(true); ?>
	</div>

	<div style="display: none;position: absolute;background-color: white; opacity: 0.6;filter:alpha(opacity=60);top: 83px;left: 0px;z-index: 999;width: 600px;height: 470px; *height: 100%;" id="loading"></div>

</form>
<?php 
}
sb_search();
?>
<script>
	var topWindow = getTopWindow();
	
	var width = jQuery(window).width();
	var height = jQuery(window).height();
	topWindow.animateModalSize(sbUploadWidth, 565);
</script>
<?php require_once(SB_PLUGIN_DIR.'/sb_google_analytics.php') ;?>