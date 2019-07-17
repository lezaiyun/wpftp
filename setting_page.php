<?php
/**
 *  插件设置页面
 */
function wpftp_setting_page() {
// 如果当前用户权限不足
	if (!current_user_can('manage_options')) {
		wp_die('Insufficient privileges!');
	}

	$wpftp_options = get_option('wpftp_options');
	if ($wpftp_options && isset($_GET['_wpnonce']) && wp_verify_nonce($_GET['_wpnonce']) && !empty($_POST)) {
		if($_POST['type'] == 'cos_info_set') {

            $wpftp_options['no_local_file'] = (isset($_POST['no_local_file'])) ? True : False;
            $wpftp_options['ftp_server'] = (isset($_POST['ftp_server'])) ? sanitize_text_field(trim(stripslashes($_POST['ftp_server']))) : '';
            $wpftp_options['ftp_user_name'] = (isset($_POST['ftp_user_name'])) ? sanitize_text_field(trim(stripslashes($_POST['ftp_user_name']))) : '';
            $wpftp_options['ftp_user_pass'] = (isset($_POST['ftp_user_pass'])) ? sanitize_text_field(trim(stripslashes($_POST['ftp_user_pass']))) : '';
            $wpftp_options['ftp_basedir'] = (isset($_POST['ftp_basedir'])) ? sanitize_text_field(trim(stripslashes($_POST['ftp_basedir']))) : '/';

			// 不管结果变没变，有提交则直接以提交的数据 更新wpftp_options
			update_option('wpftp_options', $wpftp_options);

			# 替换 upload_url_path 的值
			update_option('upload_url_path', esc_url_raw(trim(trim(stripslashes($_POST['upload_url_path'])))));

			?>
            <div style="font-size: 25px;color: red; margin-top: 20px;font-weight: bold;"><p>WPFTP插件设置保存完毕!!!</p></div>

			<?php

		}
}

?>

    <style>
        table {
            border-collapse: collapse;
        }

        table, td, th {border: 1px solid #cccccc;padding:5px;}
        .buttoncss {background-color: #4CAF50;
            border: none;cursor:pointer;
            color: white;
            padding: 15px 22px;
            text-align: center;
            text-decoration: none;
            display: inline-block;border-radius: 5px;
            font-size: 12px;font-weight: bold;
        }
        .buttoncss:hover {
            background-color: #008CBA;
            color: white;
        }
        input{border: 1px solid #ccc;padding: 5px 0px;border-radius: 3px;padding-left:5px;}
    </style>
<div style="margin:5px;">
    <h2>WordPress FTP（WPFTP）自建FTP空间存储设置</h2>
    <hr/>
    
        <p>WordPress FTP（简称:WPFTP），基于自建FTP空间存储与WordPress实现静态资源到FTP存储中。提高网站项目的访问速度，以及静态资源的安全存储功能。</p>
        <p>插件网站： <a href="https://www.laobuluo.com" target="_blank">老部落</a> / <a href="https://www.laobuluo.com/2599.html" target="_blank">WPFTP发布更新页面地址</a> / 站长创业交流QQ群： <a href="https://jq.qq.com/?_wv=1027&k=5gBE7Pt" target="_blank"> <font color="red">594467847</font></a>（宗旨：多做事，少说话）</p>
        <p>推荐文章： <a href="https://www.laobuluo.com/2113.html" target="_blank">新人建站常用的虚拟主机/云服务器 常用主机商选择建议</a></p>
        
   
      <hr/>
    <form action="<?php echo wp_nonce_url('./admin.php?page=' . WPFTP_BASEFOLDER . '/actions.php'); ?>" name="wpcosform" method="post">
        <table>
            <tr>
                 <td style="text-align:right;">
                    <b>FTP服务器IP地址：</b>
               </td>
                <td>
                    <input type="text" name="ftp_server" value="<?php echo esc_attr($wpftp_options['ftp_server']); ?>" size="50"
                           placeholder="FTP服务器IP地址"/>
                    <p>填写服务器/虚拟主机IP地址。示例：123.123.123.123</p>
                </td>
            </tr>
            <tr>
                <td style="text-align:right;">
                    <b>FTP用户名：</b>
                </td>
                <td>
                    <input type="text" name="ftp_user_name" value="<?php echo esc_attr($wpftp_options['ftp_user_name']); ?>" size="50"
                           placeholder="FTP用户名"/>

                    
                </td>
            </tr>
            <tr>
                <td style="text-align:right;">
                    <b>FTP密码：</b>
                 </td>
                <td><input type="text" name="ftp_user_pass" value="<?php echo esc_attr($wpftp_options['ftp_user_pass']); ?>" size="50" placeholder="FTP密码"/></td>
            </tr>
            <tr>
                <td style="text-align:right;">
                    <b>FTP空间绑定域名：</b>
                </td>
                <td>
                    <input type="text" name="upload_url_path" value="<?php echo esc_url(get_option('upload_url_path')); ?>" size="50"
                           placeholder="FTP空间绑定的域名"/>

                    <p><b>设置注意事项：</b></p>

                    <p>1. 一般我们是以：<code>http://{FTP空间绑定域名}</code>，同样不要用"/"结尾。</p>

                    <p>2. 示范： <code>http(s)://ftp.laobuluo.com</code></p>
                    
                </td>
            </tr>
            <tr>
                <td style="text-align:right;">
                    <b>FTP存储子目录(非必填,默认为/)：</b>
                </td>
                <td><input type="text" name="ftp_basedir" value="<?php echo esc_attr($wpftp_options['ftp_basedir']); ?>" size="50" placeholder="FTP存储子目录(非必填,默认为/)"/>
<p>这个是要相对我们FTP空间根目录的，一般云服务器创建的就按照默认，有些虚拟主机是需要单独设置子目录的，比如/wwwroot/</p>
                </td>
                
            </tr>
            <tr>
                <td style="text-align:right;">
                    <b>不在本地保存：</b>
                </td>
                <td>
                    <input type="checkbox"
                           name="no_local_file"
                        <?php
                            if ($wpftp_options['no_local_file']) {
                                echo 'checked="TRUE"';
                            }
					    ?>
                    />

                    <p>如果不想同步在服务器中备份静态文件就 "勾选"。我个人喜欢只存储在存储空间中，这样缓解服务器存储量。</p>
                </td>
            </tr>
            
            <tr>
                <th>
                    
                </th>
                <td><input type="submit" name="submit" value="保存WPFTP设置" class="buttoncss" /></td>

            </tr>
        </table>
        
        <input type="hidden" name="type" value="cos_info_set">
    </form>
    <p><b>WPFTP插件注意事项：</b></p>
    <p>1. 如果我们有多个网站需要使用WPFTP插件，需要给每一个网站独立不同的FTP空间。</p>
    <p>2. 使用WPFTP插件分离图片、附件文件，存储在WPFTP存储空间根目录，比如：2019、2018、2017这样的直接目录，不会有wp-content这样目录。</p>
    <p>3. 如果我们已运行网站需要使用WPFTP插件，插件激活之后，需要将本地wp-content目录中的文件对应时间目录上传至WPFTP存储空间中，且需要在数据库替换静态文件路径生效。</p>
    <p>4. 详细使用教程参考：<a href="https://www.laobuluo.com/2599.html" target="_blank">WPFTP发布页面地址</a>，或者加入QQ群： <a href="https://jq.qq.com/?_wv=1027&k=5gBE7Pt" target="_blank"> <font color="red">594467847</font></a>。</p>
</div>
<?php
}
?>