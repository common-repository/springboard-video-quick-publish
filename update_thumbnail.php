<?php 
require_once('const.php');
require_once('admin/config/config.php');
require_once('lib/sb_api.php');

function update_thumbnail_frame(){
	$video_id = intval($_POST['video_id']);
	$params['video_id'] = $video_id;
	$params['player_id'] = get_option('sb_player');
	$sb = new SB_API();
	$video = $sb->call("getVideo", $params, true);
	$sb_time = time();
	$channels = $video["Channels"];
	$isExternal = false;
	if($video['Video']['external'] == 1) {
		$isExternal = true;	
	}
	$isEmbedded = false;
	if($video['Video']['flag_embedded'] == 1) {
		$isEmbedded = true;	
	}
	if(!$isExternal && !$isEmbedded){
	?>
<script type='text/javascript' src='<?php echo SB_PLUGIN_URL; ?>js/jquery.tools.min.js'></script>
<script type='text/javascript' src='<?php echo SB_PLUGIN_URL; ?>js/jquery-1.7.1.min.js'></script>
<link rel="stylesheet" href="css/sb.css" media="all" type="text/css" />
<script type='text/javascript' src='<?php echo CMS_PATH; ?>/js/global-0.54.js'></script>
<script language="javascript" type="text/javascript">
	jQuery(window).unload(function() {
		jQuery('object').hide();
		jQuery('.playerS').html('');
	});
	jQuery('body').css('min-width', '419px');

	var snapshotUrl = "<?php echo "http://".$video['Video']['cname'].".".PRETTY_URL."".$video['Video']['domain'].$video['Video']['image_lg']; ?>";

	function getLiteConfig() {
		var params = {};
		var SbPlayer = null;
		params['video_id'] = "<?php echo $video_id; ?>";
		params['sb_action'] = "loadPlayer";
	
		var mediaPlayerPath = '<?php echo SB_MEDIA_PLAYER ?>';
		
		var data = {
			action: 'loadPlayer',
			params: params,
		};
		
		jQuery.post(ajaxurl, data, function(response) {
			var SbPlayer = $f(<?php echo "player".$sb_time; ?>, mediaPlayerPath, response);
			SbPlayer.onBeforeLoad(function(){
				//Bind onClick for thumb and snapshot
				$.each("snapshots,vthumbs".split(","), function(i, el) {
					//Bind click on image only when player has done with loading
					$('#add_'+el+'_link').click(function (){
						alert('Player loading...');
					});
				});
			});
			SbPlayer.onLoad(function(){
				//Bind onClick for thumb and snapshot
				$.each("snapshots,vthumbs".split(","), function(i, el) {

					//Unbind onBeforePlayer load event
					$('#add_'+el+'_link').unbind('click');
					//Bind try again click
					$('#'+el+'_image_purge').click(function () {
						
						params['sb_action'] = "purge_thumb";
						params['type'] = el;
						 
						//Make JQ Ajax request object
						var jqXHR = $.ajax({
							  data: params,
							  url: "<?php echo SB_PLUGIN_URL; ?>ajax.php",
							  async: true,
							  beforeSend: function() {
									$('#loading').show(); //Show loading image
							  },
							  success: function(data, textStatus, jqXHR) { 	//Success
								alert(jqXHR);
								//First check is there any Akamai purge errors?
								if(data != 1)
								{
									$('#'+el+'_image_purge_error').show();
								}
								//Success
								else if(data == 1)
								{
									//Hide error if there is any
									$('#'+el+'_image_purge_error').hide();
									//Remov link url
									//Is it snapshot or thumbnail url
									
									var imageUrl = snapshotUrl;
									
									$('#'+el+'_image').attr('src',imageUrl+'?'+objDate.getTime()); //Reload image div
									//Reset old url path
									$('#add_'+el+'_url').val('').css('border','1px solid #C8E8F3');
								}
								else
								{	//Some error occured
									console.log('Error'+data);
								}
							  },
							  
							  complete: function (){
								$('#loading').hide(200);
							  },
							  error: function() {
								  console.log('Server error');
							  }
							});


						
					});
						
						//Bind click on image only when player has done with loading
						$('#add_'+el+'_link').click(function () {
							//Get current elapsed time from Player
							var currentVideoTime = parseInt(SbPlayer.getTime());
							
							var oldImageSrc = $('#'+el+'_image').attr('src');

							params['sb_action'] = "create_snapshot";
							params['cur_vid_time'] = currentVideoTime;
							params['type'] = el;
							
							var data = {
								action: 'create_snapshot',
								params: params,
							};
							//$('#loading').show(); //Show loading image
							//$('.sb_thumbnail_page').css({'background-color' : 'white', 'opacity' : '0.6', 'filter' : 'alpha(opacity=60)', 'width' : '100px;', 'height' : '300px', 'position' : 'relative'});
							jQuery.post(ajaxurl, data, function(response) {
								//$('#loading').hide(200);
								//$('.sb_thumbnail_page').css({'overflow' : 'hidden', 'position' : 'fixed', 'height' : '650px', 'background-color' : '', 'opacity' : '1'});
								
								//First check is there any Akamai purge errors?
								if(response != 1)
								{
									$('#'+el+'_image_purge_error').show();
								}
									//Success
								else if(response == 1)
								{
									//Hide error if there is any
									$('#'+el+'_image_purge_error').hide();
									//Remov link url
									//Is it snapshot or thumbnail url
									var imageUrl = snapshotUrl;
									var objDate = new Date(); //Generate date object to refresh img
									//
									$('#'+el+'_image').attr('src',imageUrl+'?'+objDate.getTime()); //Reload image div
								}
								else
								{	//Some error occured
									console.log('Error'+response);
								}
							});
						});
					});
				});
		});
	}
	getLiteConfig();
<?php } ?>
</script>
<div class="sb_thumbnail_page" style="overflow: hidden; position: fixed; height: 650px;">
	<div>
		<div style="float: left;margin-left: 10px;">
		<?php if(isset($video['Video']['embed_code'])) {
					$code = $video['Video']['embed_code'];
					$code = str_replace('></embed>', ' wmode="opaque"></embed>', $code);
					$code = preg_replace('/width="\d+"/', 'width="400"', $code);
					$code = preg_replace('/height="\d+"/', 'height="320"', $code);
					$code = preg_replace('/width:\d+/', 'width:400', $code);
					$code = preg_replace('/height:\d+/', 'height:320', $code);
				}; 
		?>
			<div id='<?php echo "player".$sb_time; ?>' style="display: block; width: 400px; height: 325px; margin-top: 10px;" class='playerS'></div>
			<div style="width: 400px; height: 265px;">
			
				<!-- change the urlto the thumbnail -->
				<div>
					<img class="sb_thumbnail" id="snapshots_image" width="121" height="89" src="<?php echo $video['Video']['image_lg'] ? "http://".$video['Video']['cname'].".".PRETTY_URL."".$video['Video']['domain'].$video['Video']['image_lg']."?".rand(0, 100) : SB_PLUGIN_URL."img/image_not_available.png"; ?>" />
				</div>
				
			<?php if($video['Video']['flag_youtube']==0){
				echo '<div id="add_snapshots_link"><img src="'.SB_PLUGIN_URL.'img/update.png"/></div>';
			 } else {
				echo '<div class="add_snapshots_youtube_link"><img src="'.SB_PLUGIN_URL.'img/update_dis.png"/></div>';
			}
			?>
			<div class='utVideoDerails'>
				<div style='color:#231F20;'>
					<font style="font-weight: bold; font-style: italic;">Duration:</font> <?php echo $sb->Sec2Time($video['Video']['length']); ?><br />
				</div>
				<div style='color:#231F20;'>
					<font style="font-weight: bold; font-style: italic;">File size:</font> <?php echo $sb->formatBytes($video['Video']['file_size']); ?><br />
				</div>
				<div>
					<font style="font-weight: bold; font-style: italic;">Mime type:</font> <?php echo $video['Video']['mime_type']; ?><br />
				</div>
				<div>
					<font style="font-weight: bold; font-style: italic;">Video info:</font> 
					<?php echo "(".$video['Video']['video_width']." x ".$video['Video']['video_height']."), ".$video['Video']['bitrate']." kb/s"; ?><br />
				</div>
			</div>
			<div id="sb_video_info">
				<font style="font-family:arial;color:#757575;">Title:</font>
				<div id="sb_title_<?php echo $video['Video']['id']; ?>" class="sb_title" style="padding-top: 0px; padding-left: 0px; height: 30px; width: 265px;">
					<input type="text" class="inputboxEdit" id="title_<?php echo $video['Video']['id'];?>_2" style="width: 257px; border: 1px solid #AFAFAF; border-radius: 0; margin: 0;font-family:georgia;color:#383838;font-style:italic;font-size:12px;" value="<?php echo str_replace('"', '&quot;', stripslashes(strip_tags($video['Video']['title'])));?>">
					<!--<font style="font-weight: normal; text-decoration: underline;" onClick="editInputValue()">save</font>-->
				</div>
				<font style="font-family:arial;color:#757575;">Description:</font>
				<div style="font-weight: normal; padding-top: 0px; padding-left: 0px; height: 55px; width: 265px; cursor: default;" id="sb_desc_<?php echo $video['Video']['id']; ?>" class="sb_title">
					<textarea class="inputboxTextEdit" id="description_<?php echo $video['Video']['id'];?>" wrap="on" style="padding: 3px; width: 257px; border: 1px solid #AFAFAF; border-radius: 0; margin: 0; height: 50px;font-family:georgia;color:#383838;font-style:italic;font-size:12px;"><?php echo stripslashes(strip_tags($video['Video']['blurb']));?></textarea>
					<!-- <font style="font-weight: normal; text-decoration: underline; cursor: pointer;" onClick="editTextAreaValue()">save</font> -->
				</div>
				<div>
					<font style="font-family:arial;color:#757575;">Tags:</font> <br />
					<?php
					if(is_array($video['Video']['tags']) && !empty($video['Video']['tags']))  {
						$tagsValue = stripslashes(strip_tags(implode(", ", $video['Video']['tags'])));
					}
					else $tagsValue = "";
					?>
					<input type='text' id="tags" wrap="on" style="padding: 3px; width: 257px; border: 1px solid #AFAFAF; border-radius: 0; margin-bottom: 5px; font-family:georgia;color:#383838;font-style:italic;font-size:12px;" value='<?php echo $tagsValue;?>'/>
					<br />
				</div>
				<font style="font-family:arial;color:#757575;">Channel:</font> <br />
				<?php
					if(is_array($channels) && !empty($channels))  {
						?>
						<select name='sb_channels' id='sb_channels'>
						<?php
						foreach($channels as $channel) {
							if($video['Video']['main_channel_id'] == $channel["Channel"]["id"])
								$selected = "selected";
							else $selected = "";
							echo '<option value="'.$channel["Channel"]["id"].'" '.$selected.'>'.$channel["Channel"]["channel_name"].'</option>';
						}
						?>
						</select>
						<?php
					}
					
					$pos = strpos($video['Video']['incoming_url'], "amazon");
					if($pos && !$isExternal) {
						$headers_array = get_headers($video['Video']['incoming_url']);
						$status_pos = strpos($headers_array[0], "200");
						if($status_pos != false) {
							$p["video_id"] = $video_id;
							$presets = $sb->call("getPartnersPresets", $p, true);
							?>
							<span style="font-family:arial;color:#757575;">Reconvert:</span> <br />
							<div id='presetH' style='display:none;height:20px;'><img src='<?php echo SB_PLUGIN_URL.'img/ajax-loader2.gif'; ?>' /></div>
							<select name='sb_presets' id='sb_presets' style='width:257px;border:1px solid #AFAFAF;'>
							<?php
							foreach($presets["presets"] as $preset) {
								echo '<option value="'.$preset["Preset"]["id"].'" '.$selected.'>'.$preset["Preset"]["name"].'</option>';
							}
							echo '</select>';
						}
					}
								
				?>
				<!--<div style='color:#231F20;'>
					<font style="font-weight: bold; font-style: italic;">Duration:</font> < echo $sb->Sec2Time($video['Video']['length']); ?><br />
				</div>
				<div style='color:#231F20;'>
					<font style="font-weight: bold; font-style: italic;">File size:</font> < echo $sb->formatBytes($video['Video']['file_size']); ?><br />
				</div>
				<div>
					<font style="font-weight: bold; font-style: italic;">Mime type:</font> < echo $video['Video']['mime_type']; ?><br />
				</div>
				<div>
					<font style="font-weight: bold; font-style: italic;">Video info:</font> 
					< echo "(".$video['Video']['video_width']." x ".$video['Video']['video_height']."), ".$video['Video']['bitrate']." kb/s"; ?><br />
				</div>-->
				<div style='position:relative;'>
					<img src='<?php echo SB_PLUGIN_URL.'img/save.png'; ?>' id='save_video'/>
				</div>
			</div>
			</div>
			<?php if(!$isEmbedded && !$isExternal){ ?>
				<!-- Image snapshot purge link -->
				<div id="snapshots_image_purge_error" style="display: none;float:left;width:100%; height:33px; background-color:#ff0000;color:#ffffff;font-size:14px;font-weight:bold; text-align:center; padding-top:15px;">
					<div style="float:left;padding-left:19px;">THUMBNAIL SAVE ERROR.&nbsp;</div><div style="float:left;cursor:pointer;text-decoration:underline;" id="snapshots_image_purge">CLICK TO TRY AGAIN.</div>
				</div>
			<?php } ?>
		</div>
	</div>
	<?php 
}
update_thumbnail_frame();
?>
<script type='text/javascript'>
jQuery('#save_video').click(function(){
	editInputFields();
});

jQuery("#sb_presets").bind("change", function(){
	var params = {};
	
	var $thisObject = jQuery(this);
	params["sb_action"] = 'reconvertVideo';
	params["sb_video_id"] = '<?php echo $video_id; ?>';
	params["sb_preset_id"] = $thisObject.val();
	var ajaxUrl = 'ajax.php';
	$thisObject.css('display','none');
	jQuery('#presetH').css('display','block');

	jQuery(this).hide('fast',function(){
	
	var data = {
		action: 'reconvertVideo',
		params: params,
	};
	
	jQuery.post(ajaxurl, data, function(response) {
		jQuery('#presetH').html('Video is being converted');
	});	
	});
});
</script>
<?php require_once(SB_PLUGIN_DIR.'/sb_google_analytics.php') ;?>