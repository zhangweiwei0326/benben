<?php

// uncomment the following to define a path alias
// Yii::setPathOfAlias('local','path/to/local-folder');

// This is the main Web application configuration. Any writable
// CWebApplication properties can be configured here.
$params = dirname(__FILE__).'/params.php';
require_once ($params);
return array(
	'basePath'=>dirname(__FILE__).DIRECTORY_SEPARATOR.'..',
	'name'=>'My Web Application',

	// preloading 'log' component
	'preload'=>array('log'),

	// autoloading model and component classes
	'import'=>array(
		'application.models.*',
		'application.components.*',
	),

	'modules'=>array(
		// uncomment the following to enable the Gii tool
		'gii'=>array(
			'class'=>'system.gii.GiiModule',
			'password'=>'111111',
			// If removed, Gii defaults to localhost only. Edit carefully to taste.
			'ipFilters'=>array('127.0.0.1','::1'),
		),
	),

	// application components
	'components'=>array(
		'user'=>array(
			// enable cookie-based authentication
			'allowAutoLogin'=>true,
		),
		// uncomment the following to enable URLs in path-format
		
		'urlManager'=>array(
			'urlFormat'=>'path',
			'rules'=>array(
				'<controller:\w+>/<id:\d+>'=>'<controller>/view',
				'<controller:\w+>/<action:\w+>/<id:\d+>'=>'<controller>/<action>',
				'<controller:\w+>/<action:\w+>'=>'<controller>/<action>',
			),
		),
		//文件缓存通讯录
		'filecache'=>array(
			'class'=>'system.caching.CFileCache',
			//我们使用CFileCache实现缓存,缓存文件存放在runtime文件夹中
			'directoryLevel'=>'2',   //缓存文件的目录深度
			),
		/*
		'db'=>array(
			'connectionString' => 'sqlite:'.dirname(__FILE__).'/../data/testdrive.db',
		),
		*/
		// uncomment the following to use a MySQL database
		
		'db'=>array(
			'connectionString' => 'mysql:host='.$dbhost.';dbname='.$dbname.'',
			'emulatePrepare' => true,
			'username' => $username,
			'password' => $password,
			'charset' => $charset,
		),
		
		'errorHandler'=>array(
			// use 'site/error' action to display errors
			'errorAction'=>'site/error',
		),
		'log'=>array(
			'class'=>'CLogRouter',
			'routes'=>array(
				array(
					'class'=>'CFileLogRoute',
					'levels'=>'error, warning',
				),
				// uncomment the following to show log messages on web pages
				/*
				array(
					'class'=>'CWebLogRoute',
				),
				*/
			),
		),
	),

	// application-level parameters that can be accessed
	// using Yii::app()->params['paramName']
	'params'=>array(
			'alipay_config'=>array(
					'partner' =>'2088121516774084',   //这里是你在成功申请支付宝接口后获取到的PID；
					'key'=>'n57ju25hhfuz2ytrtk39u05nmy9xo3cv',//这里是你在成功申请支付宝接口后获取到的Key
					'sign_type'=>strtoupper('MD5'),
					'input_charset'=> strtolower('utf-8'),
					'cacert'=> getcwd().'/cacert.pem',
					'transport'=> 'http',),
			'alipay'=>array(
				//这里是卖家的支付宝账号，也就是你申请接口时注册的支付宝账号
					'seller_email'=>'18958171188@189.cn',
				//这里是异步通知页面url，提交到项目的Pay控制器的notifyurl方法；
					'back_notify_url'=>URL."/index.php/alipay/backnotify",
				//这里是页面跳转通知url，提交到项目的Pay控制器的returnurl方法；
					'return_url'=>'http://www.xxx.com/Pay/returnurl',
				//支付成功跳转到的页面，我这里跳转到项目的User控制器，myorder方法，并传参payed（已支付列表）
					'successpage'=>'User/myorder?ordtype=payed',
				//支付失败跳转到的页面，我这里跳转到项目的User控制器，myorder方法，并传参unpay（未支付列表）
					'errorpage'=>'User/myorder?ordtype=unpay')
	),
);