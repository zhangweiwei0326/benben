<?php
//include("DB_Mysql.class.php");

class Easemob {
	private $client_id;
	private $client_secret;
	private $org_name;
	private $app_name;
	private $url;

	private $access_token;
	private $expires_time;
	private $application;
	
	/**
	 * 初始化参数
	 *
	 * @param array $options   
	 * @param $options['client_id']    	
	 * @param $options['client_secret'] 
	 * @param $options['org_name']    	
	 * @param $options['app_name']   		
	 */
	public function __construct($options) {
		$this->client_id = isset ( $options ['client_id'] ) ? $options ['client_id'] : 'YXA63MvPoLTxEeSUEYcIozaQBA';
		$this->client_secret = isset ( $options ['client_secret'] ) ? $options ['client_secret'] : 'YXA6MIAjk-D0ozvbOt6ou0m2vtbsSo4';
		$this->org_name = isset ( $options ['org_name'] ) ? $options ['org_name'] : 'congzhijingjie';
		$this->app_name = isset ( $options ['app_name'] ) ? $options ['app_name'] : 'chatdemo';
		if (! empty ( $this->org_name ) && ! empty ( $this->app_name )) {
			$this->url = 'https://a1.easemob.com/' . $this->org_name . '/' . $this->app_name . '/';
		}
	}
	/**
	 * 开放注册模式
	 *
	 * @param username 用户名        	
	 * @param password 密码    
	 * @param nickname 昵称，是可选的，这个nickname用于IOS推送
	 */
	public function openRegister($username, $password, $nickname='') {
		$options['username'] = $username;
		$options['password'] = $password;
		$options['nickname'] = $nickname;
		$url = $this->url . "users";
		$result = $this->postCurl ( $url, $options, $head = array() );
		return $result;
	}
	
	/**
	 * 授权注册模式 || 批量注册
	 *
	 * @param $options['username'] 用户名        	
	 * @param $options['password'] 密码
	 *        	批量注册传二维数组
	 */
	public function accreditRegister($options) {
		$url = $this->url . "users";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, $options, $header );
		return $result;
	}
	
	/**
	 * 获取指定用户详情
	 *
	 * @param $username 用户名        	
	 */
	public function userDetails($username) {
		$url = $this->url . "users/" . $username;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = 'GET' );
		return $result;
	}
	
	/**
	 * 重置用户密码
	 *
	 * @param $options['username'] 用户名        	
	 * @param $options['password'] 密码        	
	 * @param $options['newpassword'] 新密码        	
	 */
	public function editPassword($options) {
		$url = $this->url . "users/" . $options ['username'] . "/password";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, $options, $header, $type = 'PUT');
		return $result;
	}
	/**
	 * 删除用户
	 *
	 * @param $username 用户名        	
	 */
	public function deleteUser($username) {
		$url = $this->url . "users/" . $username;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = 'DELETE' );
	}
	
	/**
	 * 批量删除用户
	 * 描述：删除某个app下指定数量的环信账号。上述url可一次删除300个用户,数值可以修改 建议这个数值在100-500之间，不要过大
	 *
	 * @param $limit="300" 默认为300条        	
	 * @param $ql 删除条件
	 *        	如ql=order+by+created+desc 按照创建时间来排序(降序)
	 */
	public function batchDeleteUser($limit = "300", $ql = '') {
		$url = $this->url . "users?limit=" . $limit;
		if (! empty ( $ql )) {
			$url = $this->url . "users?ql=" . $ql . "&limit=" . $limit;
		}
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = 'DELETE' );
	}
	
	/**
	 * 给一个用户添加一个好友
	 *
	 * @param
	 *        	$owner_username
	 * @param
	 *        	$friend_username
	 */
	public function addFriend($owner_username, $friend_username) {
		$url = $this->url . "users/" . $owner_username . "/contacts/users/" . $friend_username;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header );
	}
	/**
	 * 删除好友
	 *
	 * @param
	 *        	$owner_username
	 * @param
	 *        	$friend_username
	 */
	public function deleteFriend($owner_username, $friend_username) {
		$url = $this->url . "users/" . $owner_username . "/contacts/users/" . $friend_username;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "DELETE" );
	}
	/**
	 * 查看用户的好友
	 *
	 * @param
	 *        	$owner_username
	 */
	public function showFriend($owner_username) {
		$url = $this->url . "users/" . $owner_username . "/contacts/users/";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET" );
	}
	// +----------------------------------------------------------------------
	// | 聊天相关的方法
	// +----------------------------------------------------------------------
	/**
	 * 查看用户是否在线
	 *
	 * @param
	 *        	$username
	 */
	public function isOnline($username) {
		$url = $this->url . "users/" . $username . "/status";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET" );
		return $result;
	}
	/**
	 * 发送消息
	 *
	 * @param string $from_user
	 *        	发送方用户名
	 * @param array $username
	 *        	array('1','2')
	 * @param string $target_type
	 *        	默认为：users 描述：给一个或者多个用户(users)或者群组发送消息(chatgroups)
	 * @param string $content        	
	 * @param array $ext
	 *        	自定义参数
	 */
	function yy_hxSend($from_user = "admin", $username, $content, $target_type = "users", $ext) {
		$option ['target_type'] = $target_type;
		$option ['target'] = $username;
		$params ['type'] = "txt";
		$params ['msg'] = $content;
		$option ['msg'] = $params;
		$option ['from'] = $from_user;
		$option ['ext'] = $ext;
		$url = $this->url . "messages";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, $option, $header );
		return $result;
	}
	/*
	 * 发送透传消息
	 */
	public function tc_hxSend($from_user = "admin", $username, $content, $target_type = "users", $ext){
		$option ['target_type'] = $target_type;
		$option ['target'] = $username;
		$params ['type'] = "cmd";
		$params ['action'] = $content;
		$option ['msg'] = $params;
		$option ['from'] = $from_user;
		$option ['ext'] = $ext;
		$url = $this->url . "messages";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, $option, $header );
		return $result;
	}
	/*
	 * 上传图片/音频
	 * $img图片/音频地址
	 */
	public function upload($img){
		$option['file']= file_get_contents($img);
		$url= $this->url . "chatfiles";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$header [] = 'restrict-access:true';
		$result=$this->postCurl ( $url, $option, $header,'POST',0 );

		$data=json_decode($result,true);
		$re['uuid']=$data['entities'][0]['uuid'];
		$re['secret']=$data['entities'][0]['share-secret'];
		return $re;
	}
	/*
	 * 发送图片消息
	 * $size array（ width，height）
	 */
	public function img_hxSend($from_user = "admin", $username, $url, $secret,$size, $target_type = "users", $ext){
		$option ['target_type'] = $target_type;
		$option ['target'] = $username;
		$params ['type'] = "img";
		$params ['url'] = $url;
		$params ['filename'] = time().".jpg";
		$params ['secret'] = $secret;
		$params ['size'] = $size;
		$option ['msg'] = $params;
		$option ['from'] = $from_user;
		$option ['ext'] = $ext;
		$url = $this->url . "messages";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, $option, $header,'POST',1,$is_back );
		return $result;
	}
	/*
	 * 发送语音
	 */
	public function aud_hxSend($from_user = "admin", $username, $url, $secret,$length,$target_type = "users", $ext){
		$option ['target_type'] = $target_type;
		$option ['target'] = $username;
		$params ['type'] = "audio";
		$params ['url'] = $url;
		$params ['filename'] = time().".amr";
		$params ['secret'] = $secret;
		$params ['length'] = $length;
		$option ['msg'] = $params;
		$option ['from'] = $from_user;
		$option ['ext'] = $ext;
		$url = $this->url . "messages";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, $option, $header );
		return $result;
	}
	/**
	 * 获取app中所有的群组
	 */
	public function chatGroups() {
		$url = $this->url . "chatgroups";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET" );
		return $result;
	}
	/**
	 * 创建群组
	 *
	 * @param $option['groupname'] //群组名称,
	 *        	此属性为必须的
	 * @param $option['desc'] //群组描述,
	 *        	此属性为必须的
	 * @param $option['public'] //是否是公开群,
	 *        	此属性为必须的 true or false
	 * @param $option['approval'] //加入公开群是否需要批准,
	 *        	没有这个属性的话默认是true, 此属性为可选的
	 * @param $option['owner'] //群组的管理员,
	 *        	此属性为必须的
	 * @param $option['members'] //群组成员,此属性为可选的        	
	 */
	public function createGroups($option) {
		$url = $this->url . "chatgroups";
		$access_token = $this->getToken();
		$header[] = 'Authorization: Bearer '.$access_token;
		//$header[] = 'Authorization: Bearer YWMtvuxexsiFEeSfroXS3Jae_QAAAU1ByDJ8NDGAs0F_Fl2t_bgRyR0tS55_XJk';
		$result = $this->postCurl ( $url, $option, $header );
		return $result;
	}
	/**
	 * 获取群组详情
	 *
	 * @param
	 *        	$group_id
	 */
	public function chatGroupsDetails($group_id) {
		$url = $this->url . "chatgroups/" . $group_id;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET" );
		return $result;
	}
	/**
	 * 删除群组
	 *
	 * @param
	 *        	$group_id
	 */
	public function deleteGroups($group_id) {
		$url = $this->url . "chatgroups/" . $group_id;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "DELETE" );
		return $result;
	}
	/**
	 * 获取群组成员
	 *
	 * @param
	 *        	$group_id
	 */
	public function groupsUser($group_id) {
		$url = $this->url . "chatgroups/" . $group_id . "/users";
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET" );
		return $result;
	}
	/**
	 * 群组添加成员
	 *
	 * @param
	 *        	$group_id
	 * @param
	 *        	$username
	 */
	public function addGroupsUser($group_id, $username) {
		$url = $this->url . "chatgroups/" . $group_id . "/users/" . $username;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "POST" );
		return $result;
	}
	/**
	 * 群组添加成员(批量)
	 *
	 * @param
	 *        	$group_id
	 * @param
	 *        	$username
	 */
	public function addGroupsUserA($group_id, $username) {
		$url = $this->url . "chatgroups/" . $group_id . "/users/";
		$options['usernames'] = $username;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, $options, $header, $type = "POST" );
		return $result;
	}
	/**
	 * 群组删除成员
	 *
	 * @param
	 *        	$group_id
	 * @param
	 *        	$username
	 */
	public function delGroupsUser($group_id, $username) {
		$url = $this->url . "chatgroups/" . $group_id . "/users/" . $username;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "DELETE" );
		return $result;
	}
	/**
	 * 群组信息修改
	 *
	 * @param
	 *        	$group_id
	 * @param $option['groupname'] //群组名称,
	 *        	此属性为必须的
	 * @param $option['desc'] //群组描述,
	 *        	此属性为必须的
	 * @param $option['maxusers'] //群组成员最大数(包括群主), 值为数值类型,
	 *        	此属性为必须的
	 */
	public function editGroupsInfo($group_id, $option) {
		$url = $this->url . "chatgroups/" . $group_id;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, $option, $header, $type = "PUT" );
		return $result;
	}
	/**
 	 * 群组转让
 	 * @param $group_id
 	 * @param $username
 	 */
	public function transferGroups($group_id, $username) {
		$url = $this->url . "chatgroups/" . $group_id;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$option['newowner'] = $username;
		$result = $this->postCurl ($url, $option, $header, $type = "PUT" );
		return $result;
	}
	/**
	 * 聊天消息记录
	 *
	 * @param $ql 查询条件如：$ql
	 *        	= "select+*+where+from='" . $uid . "'+or+to='". $uid ."'+order+by+timestamp+desc&limit=" . $limit . $cursor;
	 *        	默认为order by timestamp desc
	 * @param $cursor 分页参数
	 *        	默认为空
	 * @param $limit 条数
	 *        	默认20
	 */
	public function chatRecord($ql = '', $cursor = '', $limit = 20) {
		$ql = ! empty ( $ql ) ? "ql=" . $ql : "order+by+timestamp+desc";
		$cursor = ! empty ( $cursor ) ? "&cursor=" . $cursor : '';
		$url = $this->url . "chatmessages?" . $ql . "&limit=" . $limit . $cursor;
		$access_token = $this->getToken ();
		$header [] = 'Authorization: Bearer ' . $access_token;
		$result = $this->postCurl ( $url, '', $header, $type = "GET " );
		return $result;
	}
	/**
	 * 获取Token
	 */
	public function getToken() {
		$connection = Yii::app()->db;
		$nowtime = time();
		//token还在有效期内
		if ($this->expires_time < $nowtime && $this->access_token) {	
			return $this->access_token;
		}
		//读取数据库存储token
		$sql = "select * from huanxin_apptoken limit 1";
		$command = $connection->createCommand($sql);
		$app = $command->queryAll();
		
		if ($app && $app[0]['expires_time'] > $nowtime) {
			//token没有过期
			$this->access_token = $app[0]['access_token'];
			$this->expires_time = $app[0]['expires_time'];
			$this->application = $app[0]['application'];
		}else {
			//token已经过期，重新请求token
			$option ['grant_type'] = "client_credentials";
			$option ['client_id'] = $this->client_id;
			$option ['client_secret'] = $this->client_secret;
			$url = $this->url . "token";
			$head[] = "Content-Type”:”application/json";
			$result = $this->postCurl ( $url, $option, $head );
			//$result = '{"access_token":"YWMtycq5jrWlEeSPIn3rOHrOQwAAAUzGFXrxfO5YyjX9iWC3ZIQTtI-B1IR1TmE","expires_in":5184000,"application":"dccbcfa0-b4f1-11e4-9411-8708a3369004"}';
			$result = json_decode($result);
			$result->expires_time = $result->expires_in + time ();

			$this->access_token = $token = $result->access_token;
			$this->expires_time = $expires_time = $result->expires_time;
			$this->application = $application = $result->application;

			$id = intval($app[0]['id']);
			if ($id > 0) {
				$sql = "UPDATE huanxin_apptoken SET access_token ='$token', expires_time =$expires_time, application ='$application' WHERE id =$id";
				$command = $connection->createCommand($sql);
		        $ap = $command->execute();
			}else {
				$sql = "INSERT INTO huanxin_apptoken (access_token, expires_time, application) VALUES ('$token', $expires_time, '$application')";
				$command = $connection->createCommand($sql);
		        $ap = $command->execute();
			}
		}
		return $this->access_token;
	}
	
	/**
	 * CURL Post
	 * $stype=1默认常规编码，$type=0为multipart/form-data编码
	 */
	private function postCurl($url, $option, $header = 0, $type = 'POST',$stype=1) {
		$curl = curl_init (); // 启动一个CURL会话
		curl_setopt ( $curl, CURLOPT_URL, $url ); // 要访问的地址
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYPEER, FALSE ); // 对认证证书来源的检查
		curl_setopt ( $curl, CURLOPT_SSL_VERIFYHOST, FALSE ); // 从证书中检查SSL加密算法是否存在
		curl_setopt ( $curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)' ); // 模拟用户使用的浏览器
		if (! empty ( $option )) {
			if($stype==1) {
				$options = json_encode($option);
				curl_setopt($curl, CURLOPT_POSTFIELDS, $options); // Post提交的数据包
			}else{
				curl_setopt($curl, CURLOPT_POSTFIELDS, $option); // Post提交的数据包
			}
		}
		curl_setopt ( $curl, CURLOPT_TIMEOUT, 30 ); // 设置超时限制防止死循环
		curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header ); // 设置HTTP头
		curl_setopt ( $curl, CURLOPT_RETURNTRANSFER, 1 ); // 获取的信息以文件流的形式返回
		curl_setopt ( $curl, CURLOPT_CUSTOMREQUEST, $type );
		$result = curl_exec ( $curl ); // 执行操作
		//$res = object_array ( json_decode ( $result ) );
		//$res ['status'] = curl_getinfo ( $curl, CURLINFO_HTTP_CODE );
		//pre ( $res );
		curl_close ( $curl ); // 关闭CURL会话
		return $result;
	}
}
