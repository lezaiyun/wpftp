<?php
require_once 'api.php';


define( 'WPFTP_VERSION', 1.0 );  // 插件数据版本
define( 'WPFTP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );  // 插件路径
define( 'WPFTP_BASENAME', plugin_basename(__FILE__));
define( 'WPFTP_BASEFOLDER', plugin_basename(dirname(__FILE__)));

// 初始化选项
function wpftp_set_options() {
    $options = array(
	    'version' => WPFTP_VERSION,  # 用于以后当有数据结构升级时初始化数据
		'ftp_server' => "",
		'ftp_port' => 21,
		'ftp_user_name' => "",
		'ftp_user_pass' => "",
		'ftp_basedir' => "/",
		'no_local_file' => False,  # 不在本地保留备份
	    'ftp_url_path' => '',
	);
	$wpftp_options = get_option('wpftp_options');
	if(!$wpftp_options){
	    add_option('wpftp_options', $options, '', 'yes');
	};

	if ( isset($wpftp_options['ftp_url_path']) && $wpftp_options['ftp_url_path'] != '' ) {
		update_option('upload_url_path', $wpftp_options['ftp_url_path']);
	}
}


function wpftp_restore_options () {
	$wpftp_options = get_option('wpftp_options');
	$wpftp_options['ftp_url_path'] = get_option('upload_url_path');
	update_option('wpftp_options', $wpftp_options);
	update_option('upload_url_path', '');
}


/**
 * 删除本地文件
 * @param $file_path : 文件路径
 * @return bool
 */
function wpftp_delete_local_file($file_path) {
	try {
		# 文件不存在
		if (!@file_exists($file_path)) {
			return TRUE;
		}
		# 删除文件
		if (!@unlink($file_path)) {
			return FALSE;
		}
		return TRUE;
	} catch (Exception $ex) {
		return FALSE;
	}
}


/**
 * 文件上传功能基础函数，被其它需要进行文件上传的模块调用
 * @param $key  : 远端需要的Key值[包含路径]
 * @param $file_local_path : 文件在本地的路径。
 * @param bool $no_local_file : 如果为真，则不在本地保留附件
 *
 * @return bool  : 暂未想好如何与wp进行响应。

*/
function wpftp_file_upload($key, $file_local_path, $no_local_file = False) {
    $wpftp_options = get_option('wpftp_options');
	$wpftp = new FtpApi($wpftp_options);

	### 上传文件流
	  # 由于增加了独立文件名钩子对存储中同名文件的判断，避免同名文件的存在，因此这里直接覆盖上传。
    $wpftp->Upload(
        $key,
        $file_local_path
    );
    // 如果上传成功，且不再本地保存，在此删除本地文件
    if ($no_local_file) {
        wpftp_delete_local_file($file_local_path);
    }
}


/**
 * 删除远程附件（包括图片的原图）
 * @param $post_id
 */
function wpftp_delete_remote_attachment($post_id) {
	// 获取要删除的对象Key的数组
	$deleteObjects = array();
	$meta = wp_get_attachment_metadata( $post_id );

	if (isset($meta['file'])) {
		$attachment_key = $meta['file'];
		array_push($deleteObjects, array( 'Key' => $attachment_key, ));
	} else {
		$file = get_attached_file( $post_id );
		$attached_key = str_replace( wp_get_upload_dir()['basedir'] . '/', '', $file );  # 不能以/开头
		$deleteObjects[] = array( 'Key' => $attached_key, );
	}

	if (isset($meta['sizes']) && count($meta['sizes']) > 0) {
		foreach ($meta['sizes'] as $val) {
			$attachment_thumbs_key = dirname($meta['file']) . '/' . $val['file'];
			$deleteObjects[] = array( 'Key' => $attachment_thumbs_key, );
		}
	}

	if ( !empty( $deleteObjects ) ) {
		// 执行删除远程对象
		$wpftp = new FtpApi(get_option('wpftp_options'));
        //删除文件
        foreach ( $deleteObjects as $key) {
            $wpftp->Delete( $key );
        }
	}
}


/**
 * 上传图片及缩略图
 * @param $metadata: 附件元数据
 * @return array $metadata: 附件元数据
 * 官方的钩子文档上写了可以添加 $attachment_id 参数，但实际测试过程中部分wp接收到不存在的参数时会报错，上传失败，返回报错为“HTTP错误”
 */
function wpftp_upload_and_thumbs( $metadata ) {
	$wpftp_options = get_option('wpftp_options');
	$wp_uploads = wp_upload_dir();  # 获取上传路径

	if (isset( $metadata['file'] )) {
		# 1.先上传主图
		// wp_upload_path['base_dir'] + metadata['file']
		$attachment_key = $metadata['file'];  // 远程key路径
		$attachment_local_path = $wp_uploads['basedir'] . '/' . $attachment_key;  # 在本地的存储路径
		wpftp_file_upload($attachment_key, $attachment_local_path, $wpftp_options['no_local_file']);  # 调用上传函数
	}

	# 如果存在缩略图则上传缩略图
	if (isset($metadata['sizes']) && count($metadata['sizes']) > 0) {

		// 文件名可能相同，上传操作时会判断是否存在，如果存在则不会执行上传。
		foreach ($metadata['sizes'] as $val) {
			$attachment_thumbs_key = dirname($metadata['file']) . '/' . $val['file'];  // 生成object key
			$attachment_thumbs_local_path = $wp_uploads['basedir'] . '/' . $attachment_thumbs_key;  // 本地存储路径
			wpftp_file_upload($attachment_thumbs_key, $attachment_thumbs_local_path, $wpftp_options['no_local_file']);  //调用上传函数
		}
	}

	return $metadata;
}

/**
 * @param array  $upload {
 *     Array of upload data.
 *
 *     @type string $file Filename of the newly-uploaded file.
 *     @type string $url  URL of the uploaded file.
 *     @type string $type File type.
 * @return array  $upload
 */
function wpftp_upload_attachments ($upload) {
	$mime_types       = get_allowed_mime_types();
	$image_mime_types = array(
		// Image formats.
		$mime_types['jpg|jpeg|jpe'],
		$mime_types['gif'],
		$mime_types['png'],
		$mime_types['bmp'],
		$mime_types['tiff|tif'],
		$mime_types['ico'],
	);
	if ( ! in_array( $upload['type'], $image_mime_types ) ) {
		$key        = str_replace( wp_upload_dir()['basedir'] . '/', '', $upload['file'] );
		$local_path = $upload['file'];
		wpftp_file_upload( $key, $local_path, get_option('wpftp_options')['no_local_file'] );
	}

	return $upload;
}


/**
 * Filters the result when generating a unique file name.
 *
 * @since 4.5.0
 *
 * @param string        $filename                 Unique file name.

 * @return string New filename, if given wasn't unique
 *
 * 参数 $ext 在官方钩子文档中可以使用，部分 WP 版本因为多了这个参数就会报错。 返回“HTTP错误”
 */
function wpftp_unique_filename( $filename ) {
	$ext = '.' . pathinfo( $filename, PATHINFO_EXTENSION );
	$number = '';
	$wpftp = new FtpApi(get_option('wpftp_options'));
	while ( $wpftp->hasExist( wp_get_upload_dir()['subdir'] . "/$filename") ) {
		$new_number = (int) $number + 1;
		if ( '' == "$number$ext" ) {
			$filename = "$filename-" . $new_number;
		} else {
			$filename = str_replace( array( "-$number$ext", "$number$ext" ), '-' . $new_number . $ext, $filename );
		}
		$number = $new_number;
	}
	return $filename;
}


// 在导航栏“设置”中添加条目
function wpftp_add_setting_page() {
	if (!function_exists('wpftp_setting_page')) {
		require_once 'setting_page.php';
	}
	add_menu_page('WPFTP设置', 'WPFTP设置', 'manage_options', __FILE__, 'wpftp_setting_page');
}

// 在插件列表页添加设置按钮
function wpftp_plugin_action_links($links, $file) {
	if ($file == plugin_basename(dirname(__FILE__) . '/index.php')) {
		$links[] = '<a href="admin.php?page=' . WPFTP_BASEFOLDER . '/actions.php">设置</a>';
	}
	return $links;
}
