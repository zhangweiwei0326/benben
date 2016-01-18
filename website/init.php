<?php
header ( "content-Type: text/html; charset=Utf-8" );

// change the following paths if necessary
$yii = dirname ( __FILE__ ) . '/../framework/yii.php';
$config = dirname ( __FILE__ ) . '/admin/config/main.php';
$params = dirname ( __FILE__ ) .'/admin/config/params.php';
require_once ($yii);
require_once ($params);

// 数据库连接
$dns = 'mysql:host='.$dbhost.';dbname='.$dbname.';charset='.$charset.'';
$connection = new CDbConnection ( $dns, $username, $password );
$connection->active = true;
// 表前缀
// $tablePrefix = 't_';

$sql1 = "CREATE TABLE IF NOT EXISTS `user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `username` varchar(45) DEFAULT NULL COMMENT '用户名',
  `password` varchar(45) DEFAULT NULL COMMENT '用户密码',
  `role` int(11) DEFAULT '0' COMMENT '用户角色',
  `created_time` int(11) DEFAULT NULL COMMENT '创建时间',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台用户表';";

$sql2 = "INSERT INTO `user`(`id`,`username`,`password`,`role`,`created_time`)
VALUES ('1', 'admin', '" . md5 ( '111111' ) . "', '1'," . time () . ");";

// $sql3 = "CREATE TABLE IF NOT EXISTS`menu` (
//   `id` int(11) NOT NULL AUTO_INCREMENT,
//   `name` varchar(255) DEFAULT NULL COMMENT '菜单名',
//   `href` varchar(255) DEFAULT NULL COMMENT '菜单链接',
//   `parent_id` int(11) DEFAULT NULL COMMENT '菜单级数',
//   `priority` int(11) DEFAULT '100' COMMENT '优先级',
//   `created_time` int(11) DEFAULT NULL COMMENT '创建时间',
//   PRIMARY KEY (`id`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台菜单表';";

// $sql4 = "INSERT INTO `menu` (`id`, `name`, `href`, `parent_id`, `priority`, `created_time`)
// VALUES ('1', '系统管理', '/system', '0', '1', " . time () . "), ('2', '用户管理', '/system/user', '1', '2', " . time () . "), 
// 		('3', '权限管理', '/system/right', '1', '1', " . time () . "), ('4', '菜单管理', '/system/menu', '1', '3', " . time () . ")	;";

// $sql5 = "CREATE TABLE IF NOT EXISTS`user_right` (
//   `id` int(11) NOT NULL AUTO_INCREMENT,
//   `user_role` int(11) DEFAULT NULL COMMENT '用户角色',
//   `item_id` int(11) DEFAULT NULL COMMENT '菜单ID',
//   `created_time` int(11) DEFAULT NULL COMMENT '创建时间',
//   PRIMARY KEY (`id`)
// ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='后台用户权限';";

// $sql6 = "INSERT INTO `user_right` (`id`, `user_role`, `item_id`, `created_time`)
// VALUES ('1', '1', '1', " . time () . "), ('2', '1', '2', " . time () . "), ('3', '1', '3', " . time () . "), ('4', '1', '4', " . time () . ");";

$transaction = $connection->beginTransaction ();
try {
	$connection->createCommand ( $sql1 )->execute ();
	$connection->createCommand ( $sql2 )->execute ();
	// .... other SQL executions
	echo '数据库 已配置好！';
	$transaction->commit ();
} catch ( Exception $e ) // an exception is raised if a query fails
{
	echo '数据库已配置好或者发生错误！';
	$transaction->rollback ();
}