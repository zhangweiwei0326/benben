<?php
define('APPKEY_IPHONE','iphone');
define('APPKEY_ANDROID','android');
// define('URL', 'http://benben.xun-ao.com');
define('URL', 'http://112.124.101.177:81');
define('ROOT','/website/benben_test/website');
define('MAXUSER', 600);
define("MAX_CHIEF",3);
define("MAX_WORK",100);
define("MAX_HERO",300);
define("MAX_COMPANY",5000);

//通讯录版本缓存标记
define("ADDRESS_VERSION",'addrsversion_for_test:');
//百姓版本缓存标记
define("BX_VERSION",'bxapply_for_test:');
//通讯录内容缓存标记
define("CONTACTS",'contacts_for_test:');

//环信
define("CLIENT_ID","YXA6hYUeUMCoEeSLzs9YqkHScQ");
define("CLIENT_SECRET","YXA6fC_v-if7CLg62Ti-kt9zqsOzdDo");
define("ORG_NAME","benben2015");
define("APP_NAME","benben");


//短信发送
//模板ID
// define('TEMPID',6869);//注册
// define('TEMPIDP',6970);//找回密码
// define('TEMPIDC',6869);//更换号码
define('TEMPID',35174);//注册
define('TEMPIDP',38605);//找回密码
define('TEMPIDC',35174);//更换号码

//主帐号,对应开官网发者主账号下的 ACCOUNT SID
define('ACCOUNT_SID', '8a48b551495b42ea014974de0c7b119b');

//主帐号令牌,对应官网开发者主账号下的 AUTH TOKEN
define('AUTH_TOKEN', '2bcd35809bb043d29e79372f654d38af');

//应用Id，在官网应用列表中点击应用，对应应用详情中的APP ID
//在开发调试的时候，可以使用官网自动为您分配的测试Demo的APP ID
define('APP_ID', 'aaf98f8949754cfb014979a7f14a025f');

//请求地址
//沙盒环境（用于应用开发调试）：sandboxapp.cloopen.com
//生产环境（用户应用上线使用）：app.cloopen.com
define('SERVER_IP', 'app.cloopen.com');

//请求端口，生产环境和沙盒环境一致
define('SERVER_PORT', '8883');

//REST版本号，在官网文档REST介绍中获得。
define('SOFT_VERSION', '2013-12-26');

//数据库变量
//数据库变量
// $dbhost = '42.121.59.240';
$dbhost = 'rds6wfhisehv94ixcz0s8.mysql.rds.aliyuncs.com';
$dbname = 'benben_test';
// $username = 'cfl';
$username = 'benben_test';
// $password = '123456';
$password = 'Benben2015';
$charset = 'utf8';

//积分和等级
function getlevel(){
	$level_all = array(
			array(1,150,"游民"),
			array(2,310,"佃户"),
			array(3,480,"贫农"),
			array(4,660,"中农"),
			array(5,850,"富农"),
			array(6,1050,"地主"),
			array(7,1260,"县令"),
			array(8,1480,"知府"),
			array(9,1710,"巡抚"),
			array(10,1950,"总督"),
			array(11,2200,"丞相"),
			array(12,2460,"皇帝"),
			array(13,2730,"牛"),
			array(14,6750,"牛牛"),
			array(15,12210,"犇")	
	);
	return $level_all;
}

//保留号码
function getphone(){
	$reserve_phone = array(95598,96310,95504,95119,95588,95533,95599,95566,95580,95555,95559,
			95558,95528,95561,95595,95511,95577,95568,95527,95516,95508,95518,95519,95511,95500,
			95589,95569,95556,95509,95596,95581,95505,95522,95567,95558,95510,95502,95585,95539,
			95530,95583,95524,95080,95557,95572,96102,96678,96300,10000,
			10010,10011,10060,11185,12310,12315,12319,12333,12345,12348,12358,12365,12366,12369,
			12395,95119,95500,95511,95518,95519,95522,95533,95555,95559,95566,95577,95588,95595,
			95598,95599,96300,12300,12348,12358,12365,12366,12369,12395,12591,16300,17900,17908,
			17901,17909,17990,17991,17999,17931,17950,17951,17970,17971,17987,17910,17911,17920,
			17921,95033,95105,95116,95118,95120,95160,95168,95169,95500,95501,95511,95512,95516,
			95518,95519,95522,95533,95500,95555,95558,95559,95561,95566,95567,95568,95577,95588,
			95595,95598,95599,95766,95777,95803,95805,95806,95808,95809,95810,95811,95812,95813,
			95815,95816,95828,95829,95838,95839,95858,95859,95877,95880,95888,95900,95901,95911,
			95936,95937,95938,95939,95950,95951,95958,95963,95968,95993,96000,96001,96002,96003,
			96005,96008,96010,96011,96014,96015,96096,96100,96101,96105,96110,96111,96112,96113,
			96118,96119,96120,96121,96126,96128,96129,96157,96158,96166,96168,96169,96177,96178,
			96179,96181,96188,96190,96191,96196,96197,96199
	);
	return $reserve_phone;
}
