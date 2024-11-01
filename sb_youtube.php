<style type="text/css">
#sidemenu {
    float: left;
    margin: 3px 0 0 0;
    width: 795px;
    *width: 805px;
}

</style>

<?php 

function sb_fetch(){?>

<div id="sb_upload_wrapper" style="margin-top: 0px; height: 385px; float: left; width: 805px; margin-top: 20px;">

	<iframe width="100%" height='100%' frameBorder="0" style="float: left;" src='<?php echo CMS_PATH; ?>videos/add_youtube_video/<?php echo get_option('sb_pub_id'); ?>/<?php echo get_option('sb_api_key'); ?>'></iframe>

</div>

<?php 
}
sb_fetch();
?>
<script>
	var topWindow = getTopWindow();
	
	var width = jQuery(window).width();
	var height = jQuery(window).height();
	topWindow.animateModalSize(805, 600);
</script>
<?php require_once(SB_PLUGIN_DIR.'/sb_google_analytics.php') ;?>