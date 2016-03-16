<?php
// 配置文件
function getRole() {
	$role = array (
		1 => '管理员',
		2 => '编辑'
	);
	return $role;
}
function getMenu() {
	$menu = array (
		1 => array (
			'name' => '用户管理',
			'uri' => '/member' ,
		),
		2 => array (
			'name' => '百姓网管理',
			'uri' => '/bxapply'
		),
		3 => array (
			'name' => '通讯录管理',
			'uri' => '/enterprise/'
		),
		4 => array (
			'name' => '发现管理',
			'uri' => '/creation/'
		),
		5 => array (
			'name' => '好友联盟',
			'uri' => '/friendLeague'
		),
		6 => array (
			'name' => '消息管理',
			'uri' => '/notice'
		),
		7 => array (
				'name' => '奔犇商场',
				'uri' => '/storePriceAdmin'
		),
		8 => array (
				'name' => '支付管理',
				'uri' => '/pay'
		),
		9 => array (
			'name' => '其它管理',
			'uri' => '/protocol'
		),
		10 => array (
			'name' => '系统管理',
			'uri' => '/user/'
		) ,
		11 => array (
			'name' => '商家管理',
			'uri' => '/shop'
		),
		12 => array (
			'name' => '抽奖管理',
			'uri' => '/lottery'
		)
	);
	return $menu;
}
function getSubMenu() {
	$subMenu = array (
		1 => array (
			1 => array (
				'name' => '用户编辑',
				'uri' => '/member/index',
			),
			2 => array (
				'name' => '用户统计',
				'uri' => '/statistic/member',
			)
		),
		2 => array (
			1 => array (
				'name' => '百姓网管理',
				'uri' => '/bxapply/index' ,
			),
			2 => array (
				'name' => '导出申请数据',
				'uri' => '/bxapply/putexcel' ,
			),
			3 => array (
				'name' => '批量录入数据',
				'uri' => '/bxapply/inputexcel' ,
			),
			4 => array (
				'name' => '录入数据记录',
				'uri' => '/bxapply/log' ,
			),
			5 => array (
				'name' => '百姓网统计',
				'uri' => '/statistic/bx',
			)
		) ,
		3 => array (
			1 => array (
				'name' => '政企通讯录',
				'uri' => '/enterprise'
			),
			2 => array (
				'name' => '群组',
				'uri' => '/groups'
			),
			3 => array (
				'name' => '号码直通车',
				'uri' => '/numberTrain'
			)
		) ,
		4 => array (
			1 => array (
				'name' => '微创作',
				'uri' => '/creation'
			),
			2 => array (
				'name' => '我要买',
				'uri' => '/buy'
			),
			3 => array (
				'name' => '朋友圈',
				'uri' => '/friend'
			),
			4 => array (
				'name' => '开心一刻',
				'uri' => '/happy'
			),
			5 => array (
				'name' => '统计',
				'uri' => '/findstatistic/'
			)
		) ,
		5=>array(
			1=>array(
				'name' => '好友联盟',
				'uri' => '/friendLeague/'
			)
		),
		6 => array(
			1 => array(
				'name' => '系统通知',
				'uri' => '/notice/index'
			),
			2 => array(
				'name' => '定向通知',
				'uri' => '/notice/pushindex'
			),
			3=> array(
				'name' => '小喇叭管理',
				'uri' => '/notice/BroadcastingLog'
			)
		),
		7 => array(
				1 => array(
						'name' => '服务管理',
						'uri' => '/storePriceAdmin'
				),
				2 => array(
						'name' => '充值记录',
						'uri' => '/storeChargeAdmin'
				)
		),
		8 => array(
				1 => array(
						'name' => '支付管理',
						'uri' => '/pay'
				)
		),
		9 => array(
			1 => array(
				'name' => '协议&说明',
				'uri' => '/protocol'
			),
			2 => array(
				'name' => '行业字典',
				'uri' => '/industry'
			),
			3 => array(
				'name' => '版本管理',
				'uri' => '/version'
			),
			4 => array(
				'name' => '投诉建议',
				'uri' => '/complain'
			),
			5 => array(
				'name' => '开机页面',
				'uri' => '/splash'
			),
		),
		10 => array (
			1 => array (
				'name' => '用户管理',
				'uri' => '/user'
			),
			2 => array (
				'name' => '角色管理',
				'uri' => '/role'
			),
			3 => array (
				'name' => '个人密码修改',
				'uri' => '/password' ,
				'role' => 1
			),
			4 => array (
				'name' => '系统日志',
				'uri' => '/loginLog'
			),

		) ,
		11 => array(
			1 => array(
				'name'=> '拍卖管理',
				'uri' => '/topAuction'
			),
			2 => array(
				'name' =>'退款管理',
				'uri' =>'/backOrder'
			),
			3 => array(
				'name' =>'认证管理',
				'uri' =>'/storeAuth'
			),
		),
		12 => array(
			1 => array(
				'name'=> '奖品设置',
				'uri' => '/prizeSetting'
			),
			2 => array(
				'name' =>'中奖列表',
				'uri' =>'/winningList'
			)
		)

	);
	return $subMenu;
}