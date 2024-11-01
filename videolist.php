<?php
require_once('const.php');
require_once('admin/config/config.php');
require_once('lib/sb_api.php');

$params['paged'] = isset($_GET['paged']) ? intval($_GET['paged']) : 1;
		$sb = new SB_API();
		$videos = $sb->call('search', $params, true);
		$page_links = paginate_links(
			array(
				'base' 			=> 'upload.php?page=springboard_video%_%',
				//'base' 			=> add_query_arg('paged', '%#%',SB_PLUGIN_DIR.'upload.php?page=springboard_video),
				'format' 		=> '&paged=%#%',
				'total' 	 	=> $videos[0]['Video']['videos_num'],
				'end_size'     => 1,
				'mid_size'     => 2,
				'current' 		=> isset($_GET['paged']) ? intval($_GET['paged']) : 1
			)
		);
		
		$html = "";
		if($videos != "") {
			foreach($videos as $key => $video) {
				$thumbnail = $video['Video']['image_lg'] ? "http://".$videos[0]['Video']['cname'].".".PRETTY_URL."".$videos[0]['Video']['domain'].$video['Video']['image_lg'] : SB_PLUGIN_URL."img/image_not_available.png";
				$html .= "<div id='video_div_".$video['Video']['id']."' class='sb_video_div'>
							
							<div id='sb_title_".$video['Video']['id']."' class='sb_title' align='center' title='".stripslashes(strip_tags($video['Video']['title']))."'>
								<input type=\"text\" class=\"inputbox\" style=\"width: 135px;margin-left: 3px;\" id=\"title_".$video['Video']['id']."\" value=\"".str_replace('"', '&quot;', stripslashes(strip_tags($video['Video']['title'])))."\">
							</div>
							<br />
							
							<div class='sb_snapshot'><a href='#TB_inline?width=1024&height=750' onclick='openUpdatePage(".$video['Video']['id'].")' class='thickbox' title='Edit video'><img width='115' height='86' src=".$thumbnail." ></a></div>
							
							<div class='sb_duration' >Duration: ".$sb->Sec2Time($video['Video']['length'])."</div>
							<div class='sb_update_thumbnail'><a href='#TB_inline?width=1024&height=750' onclick='openUpdatePage(".$video['Video']['id'].")' class='thickbox' title='Edit video'><img src='".SB_PLUGIN_URL."img/new_edit.png' style='margin-top:4px;' /></a></div>
							<div class='sb_delete_video' id=".$video['Video']['id']."><a href='javascript: void(0);' onclick='deleteVideo(".$video['Video']['id'].")'><img src='".SB_PLUGIN_URL."img/new_delete.png' /></a></div>
						</div>";
			}
			//$html .= "</ul>";
			$html .= "<script type='text/javascript'>";
			//$html .=    'jQuery(".sb_title").bind("click", addInput);';
			$html .=    'manualChange();';
			$html .= "</script>";
		} else {
			$html = "No videos found";
		}
		echo $html;
?>
<div id="test" style='display:none;'><?php echo $page_links; ?></div>
<script type='text/javascript'>
jQuery("#pagination_videoslist").html(jQuery('#test').html());

function openUpdatePage(videoId){
	var data = {
		action: 'updateThumbnail',
		video_id: videoId,
	};
	
	jQuery.post(ajaxurl, data, function(response) {
		jQuery('#TB_window').animate({width: '450px', height: '700px', marginLeft: '-200px', marginTop: '-350px'});
		jQuery('#TB_ajaxContent').animate({width: '450px'});
		jQuery('#TB_ajaxContent').html(response);
	});
}
</script>