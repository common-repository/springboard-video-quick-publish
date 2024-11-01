<?php 

function sb_fetch(){?>

<div id="sb_upload_wrapper" style="height: 600px; float: left; width: 800px; margin-top: 20px;">

	<iframe width="100%" height='100%' frameBorder="0" style="float: left;" src='<?php echo CMS_PATH; ?>videos/upload_url/<?php echo get_option('sb_pub_id'); ?>/<?php echo get_option('sb_api_key'); ?>'></iframe>

</div>

<?php 
}
sb_fetch();
?>
<script>
	var topWindow = getTopWindow();
	
	var width = jQuery(window).width();
	var height = jQuery(window).height();
	topWindow.animateModalSize(830, 700);
</script>
<?php require_once(SB_PLUGIN_DIR.'/sb_google_analytics.php') ;?>