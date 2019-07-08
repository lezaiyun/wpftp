<?php
if(!defined('WP_UNINSTALL_PLUGIN')){
	// 如果 uninstall 不是从 WordPress 调用，则退出
	exit();
}

// 从 options 表删除选项
delete_option('wpftp_options');

// 删除其他额外的选项和自定义表
update_option('upload_url_path', '');
