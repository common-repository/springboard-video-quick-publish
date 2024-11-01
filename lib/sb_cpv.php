<?php

class SbCpv {
private static $cpvVideos = null,$player_size=null;

public static function getCpvVideos($sb=null,$isCalledFromList=true) {
	if($sb === null){
		require_once(SB_PLUGIN_DIR.'/lib/sb_api.php');
		$sb = new SB_API();
	}
	$params['partner_id'] = get_option('sb_pub_id');
	$params['sb_player'] = get_option('sb_player');
	self::$cpvVideos = $sb->call('getCpvVideos', $params, true);
	if(!$isCalledFromList) {
	self::$player_size = $sb->call('getPlayerWH', $params, true);
	}
	return count(self::$cpvVideos);
}
static function displayCpv($isCalledFromList=true){
	$cpvVideos = self::$cpvVideos;
	if(!$isCalledFromList) {
		$player_size = self::$player_size;
	}
	 if(count($cpvVideos)) { ?>
		<style type="text/css">
		.cpv_header{
			text-align: center;
			color: white;
			font-family: Arial;
			font-size: 12px;
			height: 26px;
			line-height: 26px;
			background: rgb(41,171,226,1); /* Old browsers */
			background: -moz-linear-gradient(top, rgba(253,194,105,1) 0%, rgba(253,194,105,1) 50%, rgba(254,163,65,1) 51%, rgba(254,163,65,1) 100%); /* FF3.6+ */
			background: -webkit-linear-gradient(top, rgba(253,194,105,1) 0%,rgba(253,194,105,1) 50%,rgba(254,163,65,1) 51%,rgba(254,163,65,1) 100%); /* Chrome10+,Safari5.1+ */
			background: -o-linear-gradient(top, rgba(253,194,105,1) 0%,rgba(253,194,105,1) 50%,rgba(254,163,65,1) 51%,rgba(254,163,65,1) 100%); /* Opera 11.10+ */
			background: -ms-linear-gradient(top, rgba(253,194,105,1) 0%,rgba(253,194,105,1) 50%,rgba(254,163,65,1) 51%,rgba(254,163,65,1) 100%); /* IE10+ */
			background: linear-gradient(to bottom, rgba(253,194,105,1) 0%,rgba(253,194,105,1) 50%,rgba(254,163,65,1) 51%,rgba(254,163,65,1) 100%); /* W3C */
			filter: progid:DXImageTransform.Microsoft.gradient( startColorstr='#FDC269', endColorstr='#FEA341', GradientType=0 ); /* IE6-9 */
		}
		.cpvItem {
			width: 106px;
			height: 64px;
			margin: auto;
			margin-top: 3px;
			background-size: contain;
			}

		.cpvItemTitle {
			background: rgba(0, 0, 0, 0.8);
			width: 100%;
			height: 16px;
			font-family: Ariel;
			text-indent: 5px;
			color: white;
		}

		.cpvContent {
			border:1px solid #ECECEC;
		}
		</style>
		<div id='cpvVideos' style='float:left;width:130px;border:1px solid #ECECEC;'>
		<div class="cpv_header">Sponsored videos</div>
		<div class='cpvContent'>
		<?php 
		if(!$isCalledFromList) { ?>
			<input type="hidden" name="player_width" id="player_width" value="<?php print_r($player_size[0]['VideoPlayer']['width']); ?>" />
			<input type="hidden" name="player_height" id="player_height" value="<?php print_r($player_size[0]['VideoPlayer']['height']); ?>" />
		<?php 
		}
		?>
		<script type='text/javascript'>
			var SbCpvArray = [];
		</script>
		<?php  
		foreach($cpvVideos as $key => $value) {
			?>
				<script type='text/javascript'>
					SbCpvArray[<?php echo $value['Campaign']['id']; ?>] = '<?php echo $value['Campaign']['shortUrl']; ?>';
				</script>
				<?php if(!empty($value['Video'])) { ?>
					<div clsss='<?php echo "cpvVideo_".$value["Campaign"]["id"]; ?>' style='width:100%;margin-top:10px;' onclick="addCpvVideo('<?php echo $value["Campaign"]["shortUrl"] ?>')" >
						<div class='cpvItem' style="background-image:url('<?php echo $value['Video'][0]['thumb']; ?>');margin-bottom:5px;cursor:pointer;" title='Add video to post'>
							<div class='cpvItemTitle'><?php echo $value['Video'][0]['title']; ?></div>
						</div>
		
						
					</div>
				<?php } ?>
			<?php 
		} 
		?>
		</div>
	</div>
	<script type='text/javascript'>console.log(jQuery('div.cpvContent > div').attr('class'));
	function addCpvVideo(id){  
		create_quicktag(new Array(id), 'cpvVideo', '<?php echo get_option('sb_player'); ?>', 0);
	}
	</script>
	<?php
	}
	
	return $cpvVideos===null?0:count($cpvVideos);
	}

}

?>