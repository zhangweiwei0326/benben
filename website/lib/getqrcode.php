<?php
Yii::import("application.lib.phpqrcode.phpqrcode.php");
class getqrcode {
	public function get(){
		
		QRcode::png('http://www.cnblogs.com/txw1958/');
		
	}
}