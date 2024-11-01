<?php
require_once(SB_PLUGIN_DIR.'/lib/sb_cpv.php');
$numCpvVideos = SbCpv::getCpvVideos(null,false);

if($numCpvVideos){
	?>
	<script type='text/javascript'>
	var sbUploadWidth = 990;
	</script>
	<style type='text/css' >
		.sbUploadWrapper{
			width:980px;
		}
	</style>
	<?php
}
else {
	?>
	<script type='text/javascript'>
		var sbUploadWidth = 805;
	</script>
	<style type='text/css' >
		.sbUploadWrapper{
			width:805px;
		}
	</style>
	<?php
}

?>
<style type="text/css">
#cpvVideos {
	margin-top:20px;
}
</style>

<?php 

function sb_upload_page(){?>
<div class='sbUploadWrapper'>
<div id="sb_upload_wrapper" style="margin-top: 0px; height: 480px; float: left; width: 805px;">

	<iframe width="805px" height='100%' frameBorder="0" style="float: left;" src='<?php echo CMS_PATH; ?>videos/upload_video/<?php echo get_option('sb_pub_id'); ?>/<?php echo get_option('sb_api_key'); ?>'></iframe>

</div>

<?php 
SbCpv::displayCpv(false);
?>
</div>
<?php 
}
sb_upload_page();
//wp_iframe("sb_upload_page");
?>
<script>
	var topWindow = getTopWindow();
	
	var width = jQuery(window).width();
	var height = jQuery(window).height();
	topWindow.animateModalSize(sbUploadWidth, 520);
</script>
<?php require_once(SB_PLUGIN_DIR.'/sb_google_analytics.php') ;?>