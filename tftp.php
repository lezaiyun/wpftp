<?php
$ftp_server = '162.241.175.114';
$ftp_user_name = 'waf_yifenqian_info';
$ftp_user_pass = 'mX6krytwTbTeWp23';

// 建立基础连接
$conn_id = ftp_connect($ftp_server);

// 使用用户名和口令登录
$login_result = ftp_login($conn_id, $ftp_user_name, $ftp_user_pass);

// 检查是否成功
if ((!$conn_id) || (!$login_result)) {
    echo "FTP connection has failed!";
    echo "Attempted to connect to $ftp_server for user $ftp_user_name";
    exit;
}

// 开启被动模式，否则无法列出文件列表
ftp_pasv($conn_id, true);


// function
function ftp_mksubdirs($ftpcon,$ftpbasedir,$ftpath){
    @ftp_chdir($ftpcon, $ftpbasedir); // /var/www/uploads
    $parts = explode('/',$ftpath); // 2013/06/11/username
    foreach($parts as $part){
        if(!@ftp_chdir($ftpcon, $part)){
            ftp_mkdir($ftpcon, $part);
            ftp_chdir($ftpcon, $part);
            //ftp_chmod($ftpcon, 0777, $part);
        }
    }
}


// get contents of the root directory
// $contents = ftp_rawlist($conn_id, "/ftpcos/2019/05");

// $dir = ftp_mksubdirs($conn_id, '/', "ftp/2019/06"); // /var/www/uploads

// output $contents
// var_dump($dir);


$file = '/xos/readme.html';
$fp = fopen('readme.html', 'rb');
// // try to upload $file
// if (ftp_fput($conn_id, $file, $fp, FTP_BINARY)) {
    // echo "Successfully uploaded $file\n";
// } else {
    // echo "There was a problem while uploading $file\n";
// }
if (@ftp_chdir($conn_id, dirname($file))) {
    echo "Current directory is now: " . ftp_pwd($conn_id) . "\n";
} else {
    echo "Couldn't change directory\n";
}

// 关闭 FTP 流
ftp_close($conn_id);

fclose($fp);
?>