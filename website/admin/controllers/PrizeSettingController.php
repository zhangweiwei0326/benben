<?php

class PrizeSettingController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin';
	
	/**
	 * @var int the define the index for the menu
	 */
	 
	 public $menuIndex = 121;
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	
	
	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$this->insert_log(121);
		//获取查询参数
		$result = array();
		$statues = intval($_GET['status_num']);
		$model = PrizeSetting::model();
		$cri = new CDbCriteria();
		//参数判断
		if($statues != -1){
            $cri->addCondition('t.statues = ' . $statues, 'AND');
            $result['status'] = $statues;
        }

		$cri->select = "t.*";
        $cri->order = "id";
        $pages = new CPagination();
        $pages->itemCount = $model->count($cri);
        $pages->pageSize = 20;
        $pages->applyLimit($cri);
        $items = $model->findAll($cri);
        // var_dump($items);
        // exit;
		$this->render('index',array('items'=>$items,'pages'=> $pages,'result'=>$result,));

	}


	//下线
	public function actionDown(){
		 $id = Frame::getIntFromRequest('id');

		//访问数据库操作
        $ret = PrizeSetting::model()->updateAll (array (
                                   'statues' => 0,
                                ), "id={$id}");
        if($ret >=0 ){
            $result['status']=1;
            $result['id']=$id;
            echo json_encode($result);
        }else {
            $result['status']=0;
            $result['id']=$id;
            echo json_encode($result);;
        }

	}

	//上线 中奖次数大于0，且有且只有一个状态是1
	public function actionUp(){
		 $id = Frame::getIntFromRequest('id');
		//访问数据库操作
		 //所有状态都变为下线
		$rest=PrizeSetting::model()->updateAll (array (
                                   'statues' => 0,
                                ), "statues=1");
		//该id对应的是否还有中奖次数
		$resu=PrizeSetting::model()->findByPk($id);
		if($resu->frequency==0){
			$result['status']=2;
            echo json_encode($result);
		}else{
			$ret = PrizeSetting::model()->updateAll (array (
                                   'statues' => 1,
                                ), "id={$id}");
	        if($ret >=0 ){
	            $result['status']=1;
	            $result['id']=$id;
	            echo json_encode($result);
	        }else {
	            $result['status']=0;
	            $result['id']=$id;
	            echo json_encode($result);;
	        }
		}
        

	}
	
	// 删除
	public function actionDelete(){
		 $id = Frame::getIntFromRequest('id');

		//访问数据库操作
        $ret = PrizeSetting::model()->updateAll (array (
                                   'statues' => 2,
                                ), "id={$id}");
        if($ret >=0 ){
            $result['status']=1;

            echo json_encode($result);
        }else {
            $result['status']=0;

            echo json_encode($result);;
        }

	}
	// 添加奖品
	public function actionAdd(){
		$this->render('add');
	}
	//保存
	public function actionSave(){
		//获取参数
		// $name = Frame::getStringFromRequest('name');
		// $parent_id = Frame::getIntFromRequest('parent_id');

		$prize_name = Frame::getStringFromRequest('prize_name');
		$prize = Frame::getStringFromRequest('prize');
		$frequency = Frame::getIntFromRequest('frequency');



		//访问数据库操作
		$prizeSetting= new PrizeSetting();
		
		$prizeSetting->prize_name=$prize_name;
		$prizeSetting->prize=$prize;
		$prizeSetting->frequency=$frequency;
		$prizeSetting->last_time =0;
		$prizeSetting->statues=0;	



		$ret=$prizeSetting->save();

        if($ret >0 ){
            $result['status']=1;
            $result['message']=$prize;
            echo json_encode($result);
        }else {
        	$result['status']=0;
        	$result['message']=$frequency;
            echo json_encode($result);;
        }
	}
	//上传奖品图片
	public function actionUpload()
	{
		/**
		 * upload.php
		 *
		 * Copyright 2013, Moxiecode Systems AB
		 * Released under GPL License.
		 *
		 * License: http://www.plupload.com/license
		 * Contributing: http://www.plupload.com/contributing
		 */

		#!! 注意
		#!! 此文件只是个示例，不要用于真正的产品之中。
		#!! 不保证代码安全性。

		#!! IMPORTANT:
		#!! this file is just an example, it doesn't incorporate any security checks and
		#!! is not recommended to be used in production environment as it is. Be sure to
		#!! revise it and customize to your needs.


		// Make sure file is not cached (as it happens for example on iOS devices)
		header("Expires: Mon, 26 Jul 1997 05:00:00 GMT");
		header("Last-Modified: " . gmdate("D, d M Y H:i:s") . " GMT");
		header("Cache-Control: no-store, no-cache, must-revalidate");
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache");


		// Support CORS
		// header("Access-Control-Allow-Origin: *");
		// other CORS headers if any...
		if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
		    exit; // finish preflight CORS requests here
		}


		if ( !empty($_REQUEST[ 'debug' ]) ) {
		    $random = rand(0, intval($_REQUEST[ 'debug' ]) );
		    if ( $random === 0 ) {
		        header("HTTP/1.0 500 Internal Server Error");
		        exit;
		    }
		}

		// header("HTTP/1.0 500 Internal Server Error");
		// exit;


		// 5 minutes execution time
		@set_time_limit(5 * 60);

		// Uncomment this one to fake upload time
		// usleep(5000);

		// Settings
		// $targetDir = ini_get("upload_tmp_dir") . DIRECTORY_SEPARATOR . "plupload";
		$targetDir = 'upload_tmp';
		$uploadDir = 'upload';

		$cleanupTargetDir = true; // Remove old files
		$maxFileAge = 5 * 3600; // Temp file age in seconds


		// Create target dir
		if (!file_exists($targetDir)) {
		    @mkdir($targetDir);
		}

		// Create target dir
		if (!file_exists($uploadDir)) {
		    @mkdir($uploadDir);
		}

		// Get a file name
		if (isset($_REQUEST["name"])) {
		    $fileName = $_REQUEST["name"];
		} elseif (!empty($_FILES)) {
		    $fileName = $_FILES["file"]["name"];
		} else {
		    $fileName = uniqid("file_");
		}

		$filePath = $targetDir . DIRECTORY_SEPARATOR . $fileName;
		$uploadPath = $uploadDir . DIRECTORY_SEPARATOR . $fileName;

		// Chunking might be enabled
		$chunk = isset($_REQUEST["chunk"]) ? intval($_REQUEST["chunk"]) : 0;
		$chunks = isset($_REQUEST["chunks"]) ? intval($_REQUEST["chunks"]) : 1;


		// Remove old temp files
		if ($cleanupTargetDir) {
		    if (!is_dir($targetDir) || !$dir = opendir($targetDir)) {
		        die('{"jsonrpc" : "2.0", "error" : {"code": 100, "message": "Failed to open temp directory."}, "id" : "id"}');
		    }

		    while (($file = readdir($dir)) !== false) {
		        $tmpfilePath = $targetDir . DIRECTORY_SEPARATOR . $file;

		        // If temp file is current file proceed to the next
		        if ($tmpfilePath == "{$filePath}_{$chunk}.part" || $tmpfilePath == "{$filePath}_{$chunk}.parttmp") {
		            continue;
		        }

		        // Remove temp file if it is older than the max age and is not the current file
		        if (preg_match('/\.(part|parttmp)$/', $file) && (@filemtime($tmpfilePath) < time() - $maxFileAge)) {
		            @unlink($tmpfilePath);
		        }
		    }
		    closedir($dir);
		}


		// Open temp file
		if (!$out = @fopen("{$filePath}_{$chunk}.parttmp", "wb")) {
		    die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		}

		if (!empty($_FILES)) {
		    if ($_FILES["file"]["error"] || !is_uploaded_file($_FILES["file"]["tmp_name"])) {
		        die('{"jsonrpc" : "2.0", "error" : {"code": 103, "message": "Failed to move uploaded file."}, "id" : "id"}');
		    }

		    // Read binary input stream and append it to temp file
		    if (!$in = @fopen($_FILES["file"]["tmp_name"], "rb")) {
		        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
		    }
		} else {
		    if (!$in = @fopen("php://input", "rb")) {
		        die('{"jsonrpc" : "2.0", "error" : {"code": 101, "message": "Failed to open input stream."}, "id" : "id"}');
		    }
		}

		while ($buff = fread($in, 4096)) {
		    fwrite($out, $buff);
		}

		@fclose($out);
		@fclose($in);

		rename("{$filePath}_{$chunk}.parttmp", "{$filePath}_{$chunk}.part");

		$index = 0;
		$done = true;
		for( $index = 0; $index < $chunks; $index++ ) {
		    if ( !file_exists("{$filePath}_{$index}.part") ) {
		        $done = false;
		        break;
		    }
		}
		if ( $done ) {
		    if (!$out = @fopen($uploadPath, "wb")) {
		        die('{"jsonrpc" : "2.0", "error" : {"code": 102, "message": "Failed to open output stream."}, "id" : "id"}');
		    }

		    if ( flock($out, LOCK_EX) ) {
		        for( $index = 0; $index < $chunks; $index++ ) {
		            if (!$in = @fopen("{$filePath}_{$index}.part", "rb")) {
		                break;
		            }

		            while ($buff = fread($in, 4096)) {
		                fwrite($out, $buff);
		            }

		            @fclose($in);
		            @unlink("{$filePath}_{$index}.part");
		        }

		        flock($out, LOCK_UN);
		    }
		    @fclose($out);
		}

		// Return Success JSON-RPC response
				    $result['status']=1;
		            $result['path']=$uploadPath;
		            echo json_encode($result);;

			}


}
