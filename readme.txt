=== WPFTP ===

Contributors: laobuluo
Donate link: https://www.laobuluo.com/donate/
Tags:WordPress对象存储,WordPress加速,WordPress FTP空间, FTP空间存储,自建云存储
Requires at least: 4.5.0
Tested up to: 5.2.2
Stable tag: 1.1
Requires PHP: 5.6
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

== Description ==

<strong>WordPress FTP（简称:WPFTP），基于自建FTP空间存储与WordPress实现静态资源到FTP存储中。提高网站项目的访问速度，以及静态资源的安全存储功能。</strong>

<strong>主要功能：</strong>

* 1、下载和激活【WPFTP】插件后，配置FTP空间账户信息。
* 2、可以选择只存储到FTP空间、也可以本地网站也同时备份。
* 3、对于FTP空间，我们可以服务器配置WEB空间，也可以使用虚拟主机空间。
* 4、WPFTP插件更多详细介绍和安装：<a href="https://www.laobuluo.com/2599.html" target="_blank" >https://www.laobuluo.com/2591.html</a>

<strong>支持网站平台：</strong>

* 1. 老蒋部落 <a href="https://www.itbulu.com" target="_blank" >https://www.itbulu.com</a>
* 2. 老部落 <a href="https://www.laobuluo.com" target="_blank" >https://www.laobuluo.com</a>

== Installation ==

* 1、把WPFTP文件夹上传到/wp-content/plugins/目录下<br />
* 2、在后台插件列表中激活WPFTP<br />
* 3、在左侧【WPFTP设置】菜单中输入FTP存储空间账户信息。<br />
* 4、设置可以参考：https://www.laobuluo.com/2599.html

== Frequently Asked Questions ==

* 1.当发现插件出错时，开启调试获取错误信息。
* 2.我们可以选择备份对象存储或者本地同时备份。
* 3.如果已有网站使用wpqiniu，插件调试没有问题之后，需要将原有本地静态资源上传到七牛云对象存储中，然后修改数据库原有固定静态文件链接路径。、
* 4.如果不熟悉使用这类插件的用户，一定要先备份，确保错误设置导致网站故障。

== Screenshots ==

1. screenshot-1.png

== Changelog ==

= 1.0 =
* 1. 在完成WPCOS、WPOSS等传统云存储插件之后，有网友呼吁开发利用FTP空间的自建存储-WPFTP。

= 1.1 =
* 1. 解决钩子重复提交的问题
* 2. 被动上传改成主动上传模式

== Upgrade Notice ==
* 