<?php

if(WP_UNINSTALL_PLUGIN){
	delete_option('sb_pub_id');
	delete_option('sb_api_key');
	delete_option('sb_player');
}

?>