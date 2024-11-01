<?php
require_once(SB_PLUGIN_DIR.'/lib/sb_cpv.php');
$numCpvVideos = SbCpv::getCpvVideos(null,false);

if($numCpvVideos){
	?>
	<script type='text/javascript'>
	var sbUploadWidth = 980;
	</script>
	<style type='text/css' >
		.sbUploadWrapper{
			width:980px;
		}
		.sbUploadWrapper #cpvVideos{
			margin-top:31px;
			margin-left:10px;
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
	margin-top:15px;
}
</style>

<?php 

function sb_clibrary(){ ?>
<div class='sbUploadWrapper'>
	<div id="sb_upload_wrapper" style="margin-top: 0px; height: 600px; float: left; width: 795px; margin-top: 10px;">

		<iframe width="100%" height='100%' frameBorder="0" style="float: left;" src='<?php echo CMS_PATH; ?>youtubes/index/<?php echo get_option('sb_pub_id'); ?>/<?php echo get_option('sb_api_key'); ?>'></iframe>

	</div>

<?php 
SbCpv::displayCpv(false);
?>
</div>
<?php
}
sb_clibrary();
?>
<script>
	var topWindow = getTopWindow();
	
	var width = jQuery(window).width();
	var height = jQuery(window).height();
	topWindow.animateModalSize(sbUploadWidth, 600);
	jQuery('#TB_iframeContent').css('overflow','hidden');
</script>
<?php require_once(SB_PLUGIN_DIR.'/sb_google_analytics.php');?>