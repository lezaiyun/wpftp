<?php

class FtpApi {
	private $conn_id;
	public function __construct($option) {
		// 建立基础连接
		$this->conn_id = ftp_connect($option['ftp_server'], $option['ftp_port']);
		// 使用用户名和口令登录
		$login_result = ftp_login($this->conn_id, $option['ftp_user_name'], $option['ftp_user_pass']);
		// 检查是否成功
		if ((!$this->conn_id) || (!$login_result)) {
//			echo "FTP connection has failed!";
//			echo "Attempted to connect to $ftp_server for user $ftp_user_name";
			exit;
		}
	}


	public function Upload($key, $localFilePath) {
		// 上传文件
		$upload = ftp_put($this->conn_id, $key, $localFilePath, FTP_BINARY);
		return $upload;  // 成功时返回 TRUE， 或者在失败时返回 FALSE。
	}

	public function Delete($keys) {
		$this->client->delete($keys, True);  // 删除成功返回 true，否则 false
	}

	public function hasExist($key) {
		return $this->client->has( $key );
	}


	public function ftp_mksubdirs($ftpbasedir, $ftpath){
		@ftp_chdir($this->conn_id, $ftpbasedir); // /var/www/uploads
		$parts = explode('/',$ftpath); // 2013/06/11/username
		foreach($parts as $part){
			if(!@ftp_chdir($this->conn_id, $part)){
				ftp_mkdir($this->conn_id, $part);
				ftp_chdir($this->conn_id, $part);
				//ftp_chmod($this->conn_id, 0777, $part);
			}
		}
	}


	public function __destruct() {
		// TODO: Implement __destruct() method.
		ftp_close($this->conn_id);  // 关闭 FTP 流
	}
}