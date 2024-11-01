<?php 
wp_enqueue_script('jquery');
require_once(SB_PLUGIN_DIR.'/lib/sb_api.php'); 
$sb = new SB_API;
if(isset($_POST["sb_player"])) {
	$player = mysql_real_escape_string($_POST["sb_player"]);
	update_option('sb_player',$player);
}
?>
		<div class='w_message'>
		<div class='w_sub_message'>
			Your Springboard plugin is successfully installed.
		</div>
		</div>
		<div>
			<!-- <a href='#'>Click here to change player.</a> -->
			<div id='sbDefaultPlayer'>
				<form method='post' action='' id='sbPlayerForm'>
				<?php
				$sb->sb_player_setting();
				?>
				</form>
			</div>
		</div>
		<script type='text/javascript' >
			jQuery('#sb_player').change(function(){
				jQuery('#sbPlayerForm').submit();
			});
		</script>