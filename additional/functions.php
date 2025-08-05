<?php
// additional functions etc. e.g to replace native wordpress functions

function aplugin_dir_url($var=false) {
	return MonitorAit_settings::getPlugin_dir_url();
}

function aget_site_url() {
	return MonitorAit_settings::getSite_url();
}

function aget_current_user_id() {
	global $userData;
	return MonitorAit_settings::getCurrentUid($userData->ait_id);
}
?>