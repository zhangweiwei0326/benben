<?php

// change the following paths if necessary
$yii=dirname(__FILE__).'/../framework/yii.php';
$config=dirname(__FILE__).'/protected/config/main.php';
$frame = dirname ( __FILE__ ) . '/lib/Frame.php';
$easemob = dirname ( __FILE__ ) . '/lib/Easemob.class.php';
// remove the following lines when in production mode
defined('YII_DEBUG') or define('YII_DEBUG',true);
// specify how many levels of call stack should be shown in each log message
defined('YII_TRACE_LEVEL') or define('YII_TRACE_LEVEL',3);

require_once($yii);
require_once($frame);
require_once($easemob);
Yii::createWebApplication($config)->run();
