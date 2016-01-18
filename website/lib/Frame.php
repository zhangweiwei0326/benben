<?php
class Frame {
	public static function DRLogout($msg = 'test') {
		var_dump ( $msg );
	}
	
	// 保存图片
	public static function saveImage($postName,$t='') {
		if (empty ( $_FILES [$postName] ['name'] ))
			return '';
		if (($_FILES[$postName]["type"] == "image/gif")
				|| ($_FILES[$postName]["type"] == "image/jpeg")
				|| ($_FILES[$postName]["type"] == "image/jpg")
				|| ($_FILES[$postName]["type"] == "image/png")
				|| ($_FILES[$postName]["type"] == "image/bmp")
				|| ($_FILES[$postName]["type"] == "image/pjpeg")
				|| ($_FILES[$postName]["type"] == "image/x-png")
		){
			$up = CUploadedFile::getInstanceByName ( $postName );
			return Frame::createFile ( $t, $up, "images", "create" );
		}else{
			return '';
		}
	}
	// 保存banner图片
	public static function saveThumb($postName,$t='') {
		if (empty ( $_FILES [$postName] ['name'] ))
			return '';
		if (($_FILES[$postName]["type"] == "image/gif")
			|| ($_FILES[$postName]["type"] == "image/jpeg")
			|| ($_FILES[$postName]["type"] == "image/jpg")
			|| ($_FILES[$postName]["type"] == "image/png")
			|| ($_FILES[$postName]["type"] == "image/bmp")
			|| ($_FILES[$postName]["type"] == "image/pjpeg")
			|| ($_FILES[$postName]["type"] == "image/x-png")
		){
			$up = CUploadedFile::getInstanceByName ( $postName );
			return Frame::createThumb ( $t, $up, "images", "create" );
		}else{
			return '';
		}
	}
	// 保存音频
	public static function saveAudio($postName) {
		if (empty ( $_FILES [$postName] ['name'] ))
			return '';
		$up = CUploadedFile::getInstanceByName ( $postName );
		return Frame::createFile1 ( $up, "audio", "create" );
	}
	// 保存Excel
	public static function saveExcel($postName) {
		if (empty ( $_FILES [$postName] ['name'] ))
			return '';
		$up = CUploadedFile::getInstanceByName ( $postName );
		return Frame::createFile1 ( $up, "excel", "create" );
	}
	// 保存文件
	public static function createFile($t='', $upload, $type, $act, $imgurl = '') {
		if (! empty ( $imgurl ) && $act === 'update') {
			// 更新文件前删除旧文件
			$deleteFile = Yii::app ()->basePath . '/../' . $imgurl;
			if (is_file ( $deleteFile ))
				unlink ( $deleteFile );
		}
		$dirPath = '/uploads/' . $type . '/' . date ( 'Ym', time () );
		$uploadDir = dirname ( __FILE__ ) . '/..' . $dirPath;
		self::recursionMkDir ( $uploadDir );
		$imgname = time () . '' . rand () . '.' . ($upload->extensionName ? $upload->extensionName : 'jpg');
		// 图片展示路径
		$imageurl = $dirPath . '/' . $imgname;
		// 存储使用绝对路径
		$uploadPath = $uploadDir . '/' . $imgname;
		if ($upload->saveAs ( $uploadPath )) {
			if($t == 1){
				Yii::import('ImageController');
				$images = new ImageController($imageurl);
				$images->ckeImg(150, 150);
				$thumb = $images->out();
			}
						
			//return $imageurl.'=='.$thumb;
			return $imageurl;
		} else {
			return null;
		}
	}

	// 保存缩略图
	public static function createThumb($t='', $upload, $type, $act, $imgurl = '') {
		if (! empty ( $imgurl ) && $act === 'update') {
			// 更新文件前删除旧文件
			$deleteFile = Yii::app ()->basePath . '/../' . $imgurl;
			if (is_file ( $deleteFile ))
				unlink ( $deleteFile );
		}
		$dirPath = '/uploads/' . $type . '/' . date ( 'Ym', time () );
		$uploadDir = dirname ( __FILE__ ) . '/..' . $dirPath;
		self::recursionMkDir ( $uploadDir );
		$imgname = time () . '' . rand () . '.' . ($upload->extensionName ? $upload->extensionName : 'jpg');
		// 图片展示路径
		$imageurl = $dirPath . '/' . $imgname;
		// 存储使用绝对路径
		$uploadPath = $uploadDir . '/' . $imgname;
		if ($upload->saveAs ( $uploadPath )) {
			if($t == 1){
				Yii::import('ImageController');
				$images = new ImageController($imageurl);
				$images->thumb(320, 200);
				$thumb = $images->out();
			}

			//return $imageurl.'=='.$thumb;
			return $imageurl;
		} else {
			return null;
		}
	}
	
	// 保存文件
	public static function createFile1($upload, $type, $act, $imgurl = '') {
		if (! empty ( $imgurl ) && $act === 'update') {
			// 更新文件前删除旧文件
			$deleteFile = Yii::app ()->basePath . '/../' . $imgurl;
			if (is_file ( $deleteFile ))
				unlink ( $deleteFile );
		}
		$dirPath = '/uploads/' . $type . '/' . date ( 'Y-m', time () );
		$uploadDir = dirname ( __FILE__ ) . '/..' . $dirPath;
		self::recursionMkDir ( $uploadDir );
		$imgname = time () . '-' . rand () . '.' . $upload->extensionName;
		// 图片展示路径
		$imageurl = $dirPath . '/' . $imgname;
		// 存储使用绝对路径
		$uploadPath = $uploadDir . '/' . $imgname;
		if ($upload->saveAs ( $uploadPath )) {
			return $imageurl;
		} else {
			return null;
		}
	}
	
	
	
	
	private static function recursionMkDir($dir) {
		if (! is_dir ( $dir )) {
			self::recursionMkDir ( dirname ( $dir ) );
			mkdir ( $dir, 0777 );
		}
	}
	
	// 生成
	public static function createUUID() {
		if (function_exists ( 'com_create_guid' )) {
			return com_create_guid ();
		} else {
			mt_srand ( ( double ) microtime () * 10000 ); // optional for php 4.2.0 and up.
			$charid = strtoupper ( md5 ( uniqid ( rand (), true ) ) );
			$hyphen = chr ( 45 ); // "-"
			$uuid = chr ( 123 ) . 			// "{"
			substr ( $charid, 0, 8 ) . $hyphen . substr ( $charid, 8, 4 ) . $hyphen . substr ( $charid, 12, 4 ) . $hyphen . substr ( $charid, 16, 4 ) . $hyphen . substr ( $charid, 20, 12 ) . chr ( 125 ); // "}"
			return $uuid;
		}
	}
	public static function truncate_utf8_string($string, $length, $etc = '...') {
		$result = '';
		$string = html_entity_decode ( trim ( strip_tags ( $string ) ), ENT_QUOTES, 'UTF-8' );
		$strlen = strlen ( $string );
		for($i = 0; (($i < $strlen) && ($length > 0)); $i ++) {
			if ($number = strpos ( str_pad ( decbin ( ord ( substr ( $string, $i, 1 ) ) ), 8, '0', STR_PAD_LEFT ), '0' )) {
				if ($length < 1.0) {
					break;
				}
				$result .= substr ( $string, $i, $number );
				$length -= 1.0;
				$i += $number - 1;
			} else {
				$result .= substr ( $string, $i, 1 );
				$length -= 0.5;
			}
		}
		$result = htmlspecialchars ( $result, ENT_QUOTES, 'UTF-8' );
		if ($i < $strlen) {
			$result .= $etc;
		}
		return $result;
	}
	public static function getStringFromRequest($key, $defaultValue = '') {
		$tmp = Yii::app ()->request->getParam ( $key, $defaultValue );
		if(!get_magic_quotes_gpc()){
 			$tmp = addslashes($tmp);
		}
		return $tmp;
//  		return addslashes ( Yii::app ()->request->getParam ( $key, $defaultValue ) );
	}
	public static function getIntFromRequest($key, $defaultValue = 0) {
		return intval ( Yii::app ()->request->getParam ( $key, $defaultValue ) );
	}
	public static function getStringFromObject($obj, $key, $defalutValue = '') {
		if (empty ( $obj ) || empty ( $key ) || empty ( $obj->$key ))
			return $defalutValue;
		return $obj->$key;
	}
	public static function getArrayFromObject($obj, $key, $defalutValue = array()) {
		if (empty ( $obj ) || empty ( $key ) || empty ( $obj->$key ))
			return $defalutValue;
		return $obj->$key;
	}
	public static function getStringFromArray($array, $key, $defalutValue = '') {
		if (empty ( $array ) || empty ( $key ) || empty ( $array [$key] ))
			return $defalutValue;
		return $array [$key];
	}
	public static function getArrayFromArray($array, $key, $defalutValue = array()) {
		if (empty ( $array ) || empty ( $key ) || empty ( $array [$key] ))
			return $defalutValue;
		return $array [$key];
	}
	
	// 发邮件
	public static function sendMail($to, $topic, $message, &$error = '') {
		$validator = new CEmailValidator ();
		if (! $validator->validateValue ( $to )) {
			$error = '邮箱不合法';
			return false;
		}
		if (empty ( $topic ) || ! trim ( $topic )) {
			$error = '主题不能为空';
			return false;
		}
		if (empty ( $message ) || ! trim ( $message )) {
			$error = '邮件内容不能为空';
			return false;
		}
		$mailer = Yii::createComponent ( 'webroot.lib.mailer.EMailer' );
		$mailer->Host = 'smtp.163.com';
		$mailer->IsSMTP ();
		$mailer->SMTPAuth = true;
		$mailer->From = 'DataRenaissance@163.com'; // 设置发件地址
		                                           // $mailer->AddReplyTo('DataRenaissance@163.com');
		$mailer->AddAddress ( $to ); // 设置收件件地址
		$mailer->FromName = '数据复兴'; // 这里设置发件人姓名
		$mailer->Username = 'DataRenaissance'; // 这里输入发件地址的用户名
		$mailer->Password = 'drzaq12wsx'; // 这里输入发件地址的密码
		$mailer->SMTPDebug = false; // 设置SMTPDebug为true，就可以打开Debug功能，根据提示去修改配置
		$mailer->CharSet = 'UTF-8';
		$mailer->Subject = Yii::t ( 'DR', $topic ); // 设置邮件的主题
		$mailer->Body = $message;
		return $mailer->Send ();
	}
	
	//发短信
	public static function sendsns($to,$data,$tempId)
	{
		include('lib/CCPRestSmsSDK.php');

		// 初始化REST SDK
		//global $accountSid,$accountToken,$appId,$serverIP,$serverPort,$softVersion;
		$rest = new REST(SERVER_IP,SERVER_PORT,SOFT_VERSION);
		$rest->setAccount(ACCOUNT_SID,AUTH_TOKEN);
		$rest->setAppId(APP_ID);
		
		// 发送模板短信
        $datas = array($data,'30');
		$result = $rest->sendTemplateSMS($to,$datas,$tempId);
		return $result;
		
	}
		
	//判断key
	public static function appkey($key){
		if(($key != APPKEY_IPHONE) && ($key != APPKEY_ANDROID)){
			$result ['ret_num'] = 2006;
			$result ['ret_msg'] = 'key值不合法';
			echo json_encode ( $result );
			die ();
		}
	}
	//截取中英文字符
	public static function ch_en($str, $len, $charset="utf-8")
	{
		if( !is_numeric($len) or $len <= 0 )
		{
			return "";
		}
		$sLen = strlen($str);
		if( $len >= $sLen )
		{
			return $str;
		}
		if ( strtolower($charset) == "utf-8" )
		{
			$len_step = 3; //如果是utf-8编码，则中文字符长度为3
		}else{
			$len_step = 2; //如果是gb2312或big5编码，则中文字符长度为2
		}
	
		//执行截取操作
		$len_i = 0;
		//初始化计数当前已截取的字符串个数，此值为字符串的个数值（非字节数）
		$substr_len = 0; //初始化应该要截取的总字节数
	
		for( $i=0; $i < $sLen; $i++ )
		{
			if ( $len_i >= $len ) break; //总截取$len个字符串后，停止循环
			//判断，如果是中文字符串，则当前总字节数加上相应编码的中文字符长度
			if( ord(substr($str,$i,1)) > 0xa0 )
			{
				$i += $len_step - 1;
				$substr_len += $len_step;
			}else{ //否则，为英文字符，加1个字节
				$substr_len ++;
			}
			$len_i ++;
		}
		$result_str = substr($str,0,$substr_len );
		return $result_str;
	}
	
}

