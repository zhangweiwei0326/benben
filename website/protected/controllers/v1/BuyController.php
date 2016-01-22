<?php
class BuyController extends PublicController
{
	public $layout = false;
	/**
	 * 我的我要买及搜索
	 */
	public function actionMylist(){
		$this->check_key();
		$last_time = Frame::getIntFromRequest('last_time');
		$page = Frame::getIntFromRequest('page');
		$keyword = Frame::getStringFromRequest('keyword');
		$type = Frame::getIntFromRequest('type');
		$user = $this->check_user();
		//$pinfo = $this->pcinfo();
		$nowtime = time();
		$connection = Yii::app()->db;

		//设置状态
		$sql0 = "update buy set is_close = 1 where deadline <= {$nowtime}";
		$command = $connection->createCommand($sql0);
		$res0 = $command->execute();

		//type=2表示我报价的数据
		if ($type == 2) {
			$itemTime = array();
			$sql = "select item_id, created_time FROM quote where member_id = ".$user->id;
			// if ($last_time) {
			// 	$sql .= " and created_time < {$last_time} ";
			// }
			$start = $page*10;
			$sql .= " group by item_id order by id desc limit {$start},10";
			$command = $connection->createCommand($sql);
			$itemQuery = $command->queryAll();
			$itemsID = array();
			if ($itemQuery) {
				foreach($itemQuery as $each){
					$itemsID[] = $each['item_id'];
					$itemTime[$each['item_id']] = $each['created_time'];
				}
			}
			if (count($itemsID)) {
				// $sql = "select b.id MemberId,b.nick_name Name,b.poster Poster,a.province,a.city,a.area,a.id Id,a.title Title,a.amount Amount,a.description Description,a.deadline Deadline,a.quoted_number QuotedNumber,a.created_time CreatedTime,a.is_close from buy a inner join member b on a.member_id = b.id where a.status = 0 and a.id in (".implode(",", $itemsID).") order by a.is_close asc,instr(',".implode(",", $itemsID).",',concat(',',a.id,','))";
				$sql = "select b.id MemberId,b.nick_name Name,b.poster Poster,a.province,a.city,a.area,a.id Id,a.title Title,a.amount Amount,a.description Description,a.deadline Deadline,a.quoted_number QuotedNumber,a.created_time CreatedTime,a.is_close,a.status from buy a inner join member b on a.member_id = b.id where a.id in (".implode(",", $itemsID).") order by a.is_close asc,instr(',".implode(",", $itemsID).",',concat(',',a.id,','))";
			}else{
				$sql = '';
			}
			
		}else{
			// $sql = "select b.id MemberId,b.nick_name Name,b.poster Poster,a.province,a.city,a.area,a.id Id,a.title Title,a.amount Amount,a.description Description,a.deadline Deadline,a.quoted_number QuotedNumber,a.created_time CreatedTime,a.is_close from buy a inner join member b on a.member_id = b.id where a.status = 0 and b.id = {$user->id} ";
			$sql = "select b.id MemberId,b.nick_name Name,b.poster Poster,a.province,a.city,a.area,a.id Id,a.title Title,a.amount Amount,a.description Description,a.deadline Deadline,a.quoted_number QuotedNumber,a.created_time CreatedTime,a.is_close,a.status from buy a inner join member b on a.member_id = b.id where b.id = {$user->id} ";
			if ($keyword) {
				$sql .= " and  a.title like '%{$keyword}%' ";
				$sql .= " and  Deadline > {$nowtime} ";
			}
			// if ($last_time) {
			// 	$sql .= " and a.created_time < {$last_time} ";
			// }
			$start = $page*10;
			// $sql .= "order by a.created_time desc limit {$start},10";
			$sql .= " order by a.is_close asc,a.created_time desc limit {$start},10";
		}
		
		if ($sql) {
			$command = $connection->createCommand($sql);
			$result0 = $command->queryAll();
		}
		
		if($result0){
			$buyid = "";
			foreach ($result0 as $key =>$value){
				$buyid .= $value['Id'].",";
				$result0[$key]['Poster'] = $value['Poster'] ? URL.$value['Poster']:"";
				if ($type == 2) {
					$result0[$key]['CreatedTime']  = $itemTime[$result0[$key]['Id']];
				}
			}
			$buyid = trim($buyid);
			$buyid =trim($buyid,',');
			//查询报价
			if($buyid){
				$sql2 = "select a.item_id,a.member_id,a.price,a.description,a.created_time,b.name,b.short_name from quote a inner join number_train b on a.store_id=b.id where a.item_id in ({$buyid}) order by a.created_time desc";
				$command = $connection->createCommand($sql2);
				$result2 = $command->queryAll();
				$quotearr = array();
				foreach($result2 as $key => $va){
					if(count($quotearr[$va['item_id']])>2) continue;
					$quotearr[$va['item_id']][] = array(
							"item_id"=>$va['item_id'],
							"name"=>$va['short_name'] ? $va['short_name'] : $va['name'],
							"member_id"=>$va['member_id'],
							"price"=>$va['price'],
							"description"=>$va['description'],
							"created_time"=>$va['created_time']
					);
				}
				//var_dump($comment);exit();
			}
			$pinfo = $this->ProCity($result0);
			foreach ($result0 as $key =>$value){
				$result0[$key]['Quote'] = $quotearr[$value['Id']] ? $quotearr[$value['Id']] : array();
				$result0[$key]['pro_city'] = $pinfo[$value['city']]." ".$pinfo[$value['area']];
			}
		}	
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
		$result ['number_info'] = $result0;
		echo json_encode( $result );
	}
	
	/**
	 * 我的我要买搜索
	 */
	public function actionMysearch(){
		$this->check_key();
		$last_time = Frame::getIntFromRequest('last_time');
		$keyword = Frame::getStringFromRequest('keyword');
		$user = $this->check_user();
		//$pinfo = $this->pcinfo();
		$nowtime = time();
		$result0 = array();
		$connection = Yii::app()->db;
		if($keyword){
			$sql = "select b.id MemberId,b.name Name,b.poster Poster,a.province,a.city,a.area,a.id Id,a.title Title,a.amount Amount,a.description Description,a.deadline Deadline,a.quoted_number QuotedNumber,a.created_time CreatedTime from buy a inner join member b on a.member_id = b.id where a.title like '%{$keyword}%' and a.status = 0 and b.id = {$user->id} order by a.created_time desc,a.deadline desc limit 10";
			if($last_time){
				$sql = "select b.id MemberId,b.name Name,b.poster Poster,a.province,a.city,a.area,a.id Id,a.title Title,a.amount Amount,a.description Description,a.deadline Deadline,a.quoted_number QuotedNumber,a.created_time CreatedTime from buy a inner join member b on a.member_id = b.id where a.title like '%{$keyword}%' and a.status = 0 and a.created_time < {$last_time} and b.id = {$user->id} order by a.created_time desc,a.deadline desc limit 10";
			}
		
		$command = $connection->createCommand($sql);
		$result0 = $command->queryAll();
		if($result0){
			$buyid = "";
			foreach ($result0 as $key =>$value){
				$buyid .= $value['Id'].",";
				$result0[$key]['Poster'] = $value['Poster'] ? URL.$value['Poster']:"";
			}
			$buyid = trim($buyid);
			$buyid =trim($buyid,',');
			//查询报价
			if($buyid){
				$sql2 = "select a.item_id,a.member_id,a.price,a.description,a.created_time,b.name,b.short_name from quote a inner join number_train b on a.store_id=b.id where a.item_id in ({$buyid}) order by a.created_time desc";
				$command = $connection->createCommand($sql2);
				$result2 = $command->queryAll();
				$quotearr = array();
				foreach($result2 as $key => $va){
					if(count($quotearr[$va['item_id']])>2) continue;
					$quotearr[$va['item_id']][] = array(
							"item_id"=>$va['item_id'],
							"name"=>$va['short_name'] ? $va['short_name'] : $va['name'],
							"member_id"=>$va['member_id'],
							"price"=>$va['price'],
							"accept"=>$va['accept'],
							"description"=>$va['description'],
							"created_time"=>$va['created_time']
					);
				}
				//var_dump($comment);exit();
			}
			$pinfo = $this->ProCity($result0);
			foreach ($result0 as $key =>$value){
				$result0[$key]['Quote'] = $quotearr[$value['Id']] ? $quotearr[$value['Id']] : array();
				$result0[$key]['pro_city'] = $pinfo[$value['province']]." ".$pinfo[$value['city']]." ".$pinfo[$value['area']];
			}
		}
	}
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
		$result ['number_info'] = $result0;
		echo json_encode( $result );
	}
	
	/**
	 * 我要买列表及搜索
	 */
	public function actionList(){
		$this->check_key();
		$last_time = Frame::getIntFromRequest('last_time');
		$page = Frame::getIntFromRequest('page');
		$page = max(0, $page);
		$keyword = Frame::getStringFromRequest('keyword');
		$province = Frame::getIntFromRequest('province');
		$industry = Frame::getIntFromRequest('industry');
		$city = Frame::getIntFromRequest('city');
		$area = Frame::getIntFromRequest('area');
		$user = $this->check_user();
		$nowtime = time();
		$connection = Yii::app()->db;
		//设置状态
		$sql0 = "update buy set is_close = 1 where deadline <= {$nowtime}";
		$command = $connection->createCommand($sql0);
		$res0 = $command->execute();
		
		$sql = "select b.id MemberId,b.name Name,b.nick_name, b.poster Poster,a.province,a.city,a.area,a.id Id,a.title Title,
		a.amount Amount,a.description Description,a.deadline Deadline,a.quoted_number QuotedNumber,a.created_time CreatedTime,a.is_close
		from buy a inner join member b on a.member_id = b.id where a.status = 0 and a.is_close=0 ";
		//$sql .= " and  Deadline > {$nowtime} ";		
		if ($keyword) {
			$sql .= "and a.title like '%{$keyword}%'";
			//$sql .= " and  Deadline > {$nowtime} ";
		}
		if ($province) {
			$sql .= "and a.province = {$province} ";
		}
		if ($city) {
			$sql .= "and a.city = {$city} ";
		}
		if ($area) {
			$sql .= "and a.area = {$area} ";
		}
		 if ($industry) {
		 	$sql .= "and a.industry = {$industry}";
		 }
		$start = $page*10;
		$sql .= " order by a.is_close asc,a.created_time desc limit {$start},10";
		$command = $connection->createCommand($sql);
		$result0 = $command->queryAll();
		if($result0){
			$buyid = "";
			foreach ($result0 as $key =>$value){
	            $buyid .= $value['Id'].",";
				$result0[$key]['Poster'] = $value['Poster'] ? URL.$value['Poster']:"";
				$name = $result0[$key]['Name']?$result0[$key]['Name']:$result0[$key]['nick_name'];
				$result0[$key]['Name'] = $name;
			}		
			$buyid = trim($buyid);
			$buyid =trim($buyid,',');
			//统计我报价的次数
			if ($buyid) {
				$qsql = "select count(*) c, item_id from quote where member_id = ".$user->id." and item_id in (".$buyid.") group by item_id";
				$command = $connection->createCommand($qsql);
				$quoteQuery = $command->queryAll();
				$quoteInfo = array();
				if ($quoteQuery) {
					foreach ($quoteQuery as $each) {
						$quoteInfo[$each['item_id']] = $each['c'];
					}
				}
			}
			
			//查询报价
			if($buyid){			
				$sql2 = "select a.accept, a.item_id,a.member_id,a.price,a.description,a.created_time,b.name,b.short_name from quote a inner join number_train b on a.store_id=b.id where a.item_id in ({$buyid}) order by a.created_time desc";
				$command = $connection->createCommand($sql2);
				$result2 = $command->queryAll();
				$quotearr = array();
				foreach($result2 as $key => $va){
					
				   if(count($quotearr[$va['item_id']])>2) continue;
	               $quotearr[$va['item_id']][] = array(
						                                       "item_id"=>$va['item_id'],
	               		                                       "name"=>$va['short_name'] ? $va['short_name'] : $va['name'],
						                                       "member_id"=>$va['member_id'],
						                                       "price"=>$va['price'],
						                                       "accept"=>$va['accept'],
	               		                                       "description"=>$va['description'],
						                                       "created_time"=>$va['created_time']
						               	                     );
				}
				//var_dump($comment);exit();
			}
			$pinfo = $this->ProCity($result0);
			foreach ($result0 as $key =>$value){ 
				$result0[$key]['Quote'] = $quotearr[$value['Id']] ? $quotearr[$value['Id']] : array();
				$result0[$key]['pro_city'] = $pinfo[$value['city']]." ".$pinfo[$value['area']];
				$quoteNumber = 0;
				if (isset($quoteInfo[$result0[$key]['Id']])) {
					$quoteNumber = $quoteInfo[$result0[$key]['Id']];
				}
				$result0[$key]['haveQuote'] = $quoteNumber;
			}

			//查询我的发布图片
			if($buyid) {
				$buypic=BuyAttachment::model()->findAll("buy_id in ({$buyid})");
				if($buypic) {
					foreach ($buypic as $kb => $vb) {
						$posterArr[$vb['buy_id']][] = array(
								"pic_id" => $vb['id'],
								"poster" => $vb['poster'] ? URL . $vb['poster'] : ""
						);
					}
				}
				foreach ($result0 as $key =>$value) {
					$result0[$key]['poster'] = $posterArr[$value['Id']]?$posterArr[$value['Id']]:array();
				}
			}
		}					
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
		$result ['number_info'] = $result0;
		echo json_encode( $result );
	}
	
	/**
	 * 我要买列表搜索
	 */
	public function actionSearch(){
		$this->check_key();
		$last_time = Frame::getIntFromRequest('last_time');
		$keyword = Frame::getStringFromRequest('keyword');
		$user = $this->check_user();		
		//$pinfo = $this->pcinfo();		
		$nowtime = time();
		$result0 = array();
		$connection = Yii::app()->db;
		if($keyword){
			$sql = "select b.id MemberId,b.name Name,b.poster Poster,a.province,a.city,a.area,a.id Id,a.title Title,a.amount Amount,a.description Description,a.deadline Deadline,a.quoted_number QuotedNumber,a.created_time CreatedTime from buy a inner join member b on a.member_id = b.id where a.title like '%{$keyword}%' and a.status = 0 order by a.created_time desc,a.deadline desc limit 10";
			if($last_time){
				$sql = "select b.id MemberId,b.name Name,b.poster Poster,a.province,a.city,a.area,a.id Id,a.title Title,a.amount Amount,a.description Description,a.deadline Deadline,a.quoted_number QuotedNumber,a.created_time CreatedTime from buy a inner join member b on a.member_id = b.id where a.title like '%{$keyword}%' and a.status = 0 and a.created_time < {$last_time} order by a.created_time desc,a.deadline desc limit 10";
			}
		
		$command = $connection->createCommand($sql);
		$result0 = $command->queryAll();
		if($result0){
			$buyid = "";
			foreach ($result0 as $key =>$value){
				$buyid .= $value['Id'].",";
				$result0[$key]['Poster'] = $value['Poster'] ? URL.$value['Poster']:"";
			}
			$buyid = trim($buyid);
			$buyid =trim($buyid,',');
			//查询报价
			if($buyid){
				$sql2 = "select a.item_id,a.member_id,a.price,a.description,a.created_time,b.name,b.short_name from quote a inner join number_train b on a.store_id=b.id where a.item_id in ({$buyid}) order by a.created_time desc";
				$command = $connection->createCommand($sql2);
				$result2 = $command->queryAll();
				$quotearr = array();
				foreach($result2 as $key => $va){
					if(count($quotearr[$va['item_id']])>2) continue;
					$quotearr[$va['item_id']][] = array(
							"item_id"=>$va['item_id'],
							"name"=>$va['short_name'] ? $va['short_name'] : $va['name'],
							"member_id"=>$va['member_id'],
							"price"=>$va['price'],
							"accept"=>$va['accept'],
							"description"=>$va['description'],
							"created_time"=>$va['created_time']
					);
				}
				//var_dump($comment);exit();
			}
			$pinfo = $this->ProCity($result0);
			foreach ($result0 as $key =>$value){
				$result0[$key]['Quote'] = $quotearr[$value['Id']] ? $quotearr[$value['Id']] : array();
				$result0[$key]['pro_city'] = $pinfo[$value['province']]." ".$pinfo[$value['city']]." ".$pinfo[$value['area']];
			}
		}
	}
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
		$result ['number_info'] = $result0;
		echo json_encode( $result );
	}
	
	/**
	 * 我要买发布
	 */
	public function actionCreate(){
		$this->check_key();
		$title = Frame::getStringFromRequest('title');
		$province = Frame::getIntFromRequest('province');
		$city = Frame::getIntFromRequest('city');
		$area = Frame::getIntFromRequest('area');
		$street = Frame::getIntFromRequest('street');
		$amount = Frame::getStringFromRequest('amount');		
		$deadline = Frame::getIntFromRequest('deadline');
		$description = Frame::getStringFromRequest('description');
		$industry = Frame::getIntFromRequest('industry');
		$pic[] = Frame::saveImage('pic1', 1);
		$pic[] = Frame::saveImage('pic2', 1);
		$pic[] = Frame::saveImage('pic3', 1);
		$pic[] = Frame::saveImage('pic4', 1);
		$pic[] = Frame::saveImage('pic5', 1);
		$pic[] = Frame::saveImage('pic6', 1);
		$user = $this->check_user();

		$connection = Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$creation_info = new Buy();
			$creation_info->member_id = $user->id;
			$creation_info->title = $title;
			$creation_info->province = $province;
			$creation_info->city = $city;
			$creation_info->area = $area;
			$creation_info->street = $street;
			$creation_info->amount = $amount;
			$creation_info->deadline = $deadline;
			$creation_info->description = $description;
			$creation_info->description = $industry;
			$creation_info->created_time = time();
			$creation_info->save();

			//储存图片
			foreach($pic as $v){
				if($v){
					$picinfo=new BuyAttachment();
					$picinfo->poster=$v;
					$picinfo->buy_id=$creation_info['id'];
					$picinfo->save();
				}
			}
			$transaction->commit(); //提交事务会真正的执行数据库操作
		} catch (Exception $e) {
			$transaction->rollback(); //如果操作失败, 数据回滚
			$result['ret_num'] = 200;
			$result['ret_msg'] = '我要买发布失败';
			echo json_encode( $result );
			die();
		}


		$this->addIntegral($user->id, 6);
		$result['ret_num'] = 0;
		$result['ret_msg'] = '操作成功';
		echo json_encode( $result );
	}

	/**
	 * 我要买报价
	 */
	public function actionQuoted(){
		$this->check_key();
		$buyid = Frame::getIntFromRequest('buyid');
		$price = Frame::getStringFromRequest('price');
		$description = Frame::getStringFromRequest('description');
		$pic[] = Frame::saveImage('pic1', 1);
		$pic[] = Frame::saveImage('pic2', 1);
		$pic[] = Frame::saveImage('pic3', 1);
		$pic[] = Frame::saveImage('pic4', 1);
		$pic[] = Frame::saveImage('pic5', 1);
		$pic[] = Frame::saveImage('pic6', 1);
		if(empty($buyid)){
			$result['ret_num'] = 201;
			$result['ret_msg'] = '我要买ID为空';
			echo json_encode( $result );
			die();
		}
		if(empty($price)){
			$result['ret_num'] = 205;
			$result['ret_msg'] = '报价为空';
			echo json_encode( $result );
			die();
		}	
		$user = $this->check_user();
		$buy = Buy::model()->findByPk($buyid);		
		if(!$buy){
			$result['ret_num'] = 202;
			$result['ret_msg'] = '我要买ID不存在';
			echo json_encode( $result );
			die();
		}
		$nowtime = time();
		if ($buy->deadline <= $nowtime) {
			$result['ret_num'] = 203;
			$result['ret_msg'] = '交易已经关闭，不能报价';
			echo json_encode( $result );
			die();
		}
		//自己发布的不能报价
		if($buy->member_id == $user->id){
			$result['ret_num'] = 207;
			$result['ret_msg'] = '自己发布的自己不能报价';
			echo json_encode( $result );
			die();
		}
		
		//是否是直通车用户
		$nt = NumberTrain::model()->find("member_id = {$user->id}");
		if(empty($nt)){
			$result['ret_num'] = 204;
			$result['ret_msg'] = '未注册号码直通车';
			echo json_encode( $result );
			die();
		}
		if(($nt->status > 0)){
			$result['ret_num'] = 2260;
			$result['ret_msg'] = '号码直通车被屏蔽';
			echo json_encode( $result );
			die();
		}
		if(($nt->is_close == 1)){
			$result['ret_num'] = 2261;
			$result['ret_msg'] = '号码直通车被关闭';
			echo json_encode( $result );
			die();
		}
		if(($user->store_disable == 1)){
			$result['ret_num'] = 2262;
			$result['ret_msg'] = '号码直通车被禁用';
			echo json_encode( $result );
			die();
		}
		//是否已报价2次
		$sql = "select count(id) num from quote where item_id = {$buyid} and store_id = {$nt->id}";
		$connection = Yii::app()->db;
		$command = $connection->createCommand($sql);
		$re = $command->queryAll();
		if($re[0]['num'] >= 2){
			$result['ret_num'] = 208;
			$result['ret_msg'] = '已报价2次,不能再报价';
			echo json_encode( $result );
			die();
		}

		$connection=Yii::app()->db;
		$transaction=$connection->beginTransaction();
		try {
			$quote_info = new Quote();
			$quote_info->item_id = $buyid;
			$quote_info->store_id = $nt->id;
			$quote_info->price = $price;
			$quote_info->description = $description;
			$quote_info->member_id = $user->id;
			$quote_info->created_time = time();
			if ($quote_info->save()) {
				$buy->quoted_number = $buy->quoted_number + 1;
				$buy->update();
				$this->addIntegral($user->id, 18);

				//插入评论图片
				foreach($pic as $k=>$v){
					if($v) {
						$quoteAtt = new QuoteAttachment();
						$quoteAtt->quote_id=$quote_info['id'];
						$quoteAtt->poster=$v;
						$quoteAtt->save();
					}
				}

				$result['ret_num'] = 0;
				$result['ret_msg'] = '操作成功';

				//推送消息提示报价
				$content = "有商家给予您报价了，快去看看";
				$t = time();
				$newinfo = "(8,{$user->id},{$buy['member_id']},'{$content}',{$t},{$buyid},3,0)";
				$sqln = "insert into news (type,sender,member_id,content,created_time,identity1, identity2,display) values " . $newinfo;
				$command = $connection->createCommand($sqln);
				$resultn = $command->execute();

				//环信推送消息
				$buyuser = Member::model()->find("id={$buy['member_id']}");
				//$arr额外信息(t1为是否显示在聊天栏中0no/1yes；t2为是否进入通知界面0no/1yes；t3为处理进度0wait/1ok/2no,t4为4是我要买)
				$other_arr = array("t1" => 1, "t2" => 0, "t3" => 0, "t4" => 4, "buyid" => $buyid, "time" => $t);
				$this->sendHXMessage(array(0 => $buyuser['huanxin_username']), $content, $other_arr);
			}
			echo json_encode( $result );
			die();
		}catch(Exception $e){
			$transaction->rollBack();
			$result['ret_num'] = 206;
			$result['ret_msg'] = '报价失败';
			echo json_encode( $result );
			die();
		}
	}
	
	/**
	 * 我要买详情
	 */
	public function actionDetail(){
		$this->check_key();
		$buyid = Frame::getIntFromRequest('buyid');		
		if(empty($buyid)){
			$result['ret_num'] = 201;
			$result['ret_msg'] = '我要买ID为空';
			echo json_encode( $result );
			die();
		}
		
		$user = $this->check_user();
		//$pinfo = $this->pcinfo();
		$connection = Yii::app()->db;
		//查询发布人信息
		$sqla = "select a.id,a.title,a.amount,a.description,a.deadline,a.province,a.city,a.area,a.quoted_number,a.created_time,a.is_close,b.id member_id,b.name,b.nick_name,b.poster,b.address from buy a inner join member b on a.member_id = b.id where a.id = {$buyid}";
		$command = $connection->createCommand($sqla);
		$re = $command->queryAll();
		//查询发布人直通车信息
		$sqlac = "select name,short_name,poster from number_train  where member_id = {$re[0]['member_id']}";
		$command = $connection->createCommand($sqlac);
		$rec = $command->queryAll();
		
		if(!$re){
			$result['ret_num'] = 202;
			$result['ret_msg'] = '我要买ID不存在';
			echo json_encode( $result );
			die();
		}
		//查询报价信息
		$sql = "select a.id,a.store_id,a.member_id,a.price,a.accept,a.description,a.created_time,b.nick_name,c.poster,b.huanxin_username,c.name,c.short_name from quote a inner join number_train c on a.store_id = c.id inner join member b on c.member_id = b.id where a.item_id = {$buyid} order by a.created_time desc";
		$command = $connection->createCommand($sql);
		$result1 = $command->queryAll();
		foreach ($result1 as $key => $value){
			$result1[$key]['poster'] = $value['poster'] ? URL.$value['poster']:""; 
			//$result1[$key]['created_time'] = date("Y-m-d H:i:s",$value['created_time']);
		}
		//查询每张商品图片
		$pic=array();
		$picinfo=BuyAttachment::model()->findAll("buy_id={$buyid}");
		foreach($picinfo as $kp=>$vp){
			$pic[]=array(
					"pic_id"=>$vp['id'],
					"poster"=>$vp['poster']?URL.$vp['poster']:""
			);
		}
		$re[0]['poster'] = $re[0]['poster'] ? URL.$re[0]['poster']:"";
		$re[0]['sell_pic'] = $pic?$pic:array();
		$re[0]['short_name'] = $rec[0]['short_name'] ;
		$province = $this->getProCity($re[0]['province']);
		$city = $this->getProCity($re[0]['city']);
		$area = $this->getProCity($re[0]['area']);
		$re[0]['pro_city'] = $province[0]['area_name']." ".$city[0]['area_name']." ".$area[0]['area_name'];
		$re[0]['quoted_info'] = $result1;
		$re[0]['left_time'] = max(0, $re[0]['deadline']-time());
		if($re){
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';
			$result['buy_info'] = $re[0];
		}else{
			$result['ret_num'] = 203;
			$result['ret_msg'] = '查看详情失败';
		}
		echo json_encode( $result );
	}
	
	/**
	 * 我要买接受报价
	 */
	public function actionAccept(){
		$this->check_key();
		$buyid = Frame::getIntFromRequest('buyid');
		$quoteid = Frame::getIntFromRequest('quoteid');

		if(empty($buyid)){
			$result['ret_num'] = 201;
			$result['ret_msg'] = '我要买ID为空';
			echo json_encode( $result );
			die();
		}
		// if(empty($ntid)){
		// 	$result['ret_num'] = 209;
		// 	$result['ret_msg'] = '直通车ID为空';
		// 	echo json_encode( $result );
		// 	die();
		// }
		if(empty($quoteid)){
			$result['ret_num'] = 209;
			$result['ret_msg'] = '参数错误';
			echo json_encode( $result );
			die();
		}	

		$user = $this->check_user();
		$buy = Buy::model()->findByPk($buyid);
		if(empty($buy)){
			$result['ret_num'] = 2109;
			$result['ret_msg'] = '我要买不存在';
			echo json_encode( $result );
			die();
		}
		$re = Quote::model()->count("item_id = {$buyid} and accept=1");
		if ($re > 0) {
			$result['ret_num'] = 2110;
			$result['ret_msg'] = '已经接受了其它报价';
			echo json_encode( $result );
			die();
		}

		$re = Quote::model()->findByPk($quoteid);
		if($re){
			$re->accept = 1;
			$re->update();
			$buy->deadline = time();
			$buy->is_accept = 1;
			$buy->update();
			$this->addIntegral($user->id, 17, $re->member_id);
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';

			//推送消息提示报价
			$content="关于{$buy['title']},买家接受了您的报价，快去看看";
			$t=time();
			$newinfo= "(8,{$user->id},{$re['member_id']},'{$content}',{$t},{$buyid},2,0)";
			$sqln = "insert into news (type,sender,member_id,content,created_time,identity1, identity2,display) values ".$newinfo;
			$connection=Yii::app()->db;
			$command = $connection->createCommand($sqln);
			$resultn = $command->execute();
			$buyuser=Member::model()->find("id={$re['member_id']}");
			$other_arr=array("t1"=>1,"t2"=>0,"t3"=>1,"t4"=>4,"buyid"=>$buyid,"time"=>$t);
			$this->sendHXMessage(array(0=>$buyuser['huanxin_username']),$content,$other_arr);
		}else{
			$result['ret_num'] = 210;
			$result['ret_msg'] = '报价不存在';
		}
		echo json_encode( $result );
	}

	/*
	 * 我要买拒绝报价
	 */
	public function actionReject(){
		$this->check_key();
		$buyid = Frame::getIntFromRequest('buyid');
		$quoteid = Frame::getIntFromRequest('quoteid');

		if(empty($buyid)){
			$result['ret_num'] = 201;
			$result['ret_msg'] = '我要买ID为空';
			echo json_encode( $result );
			die();
		}
		// if(empty($ntid)){
		// 	$result['ret_num'] = 209;
		// 	$result['ret_msg'] = '直通车ID为空';
		// 	echo json_encode( $result );
		// 	die();
		// }
		if(empty($quoteid)){
			$result['ret_num'] = 209;
			$result['ret_msg'] = '参数错误';
			echo json_encode( $result );
			die();
		}

		$user = $this->check_user();
		$buy = Buy::model()->findByPk($buyid);
		if(empty($buy)){
			$result['ret_num'] = 2109;
			$result['ret_msg'] = '我要买不存在';
			echo json_encode( $result );
			die();
		}

		$re = Quote::model()->findByPk($quoteid);
		if($re){
			$re->accept = 2;
			$re->update();
			$buy->deadline = time();
			$buy->is_accept = 2;
			$buy->update();
			$this->addIntegral($user->id, 17, $re->member_id);
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';

			//推送消息提示报价
			$content="关于{$buy['title']},买家拒绝了您的报价，快去看看";
			$t=time();
			$newinfo= "(8,{$user->id},{$re['member_id']},'{$content}',{$t},{$buyid},3,0)";
			$sqln = "insert into news (type,sender,member_id,content,created_time,identity1, identity2,display) values ".$newinfo;
			$connection=Yii::app()->db;
			$command = $connection->createCommand($sqln);
			$resultn = $command->execute();
			$buyuser=Member::model()->find("id={$re['member_id']}");
			$other_arr=array("t1"=>1,"t2"=>0,"t3"=>2,"t4"=>4,"buyid"=>$buyid,"time"=>$t);
			$this->sendHXMessage(array(0=>$buyuser['huanxin_username']),$content,$other_arr);
		}else{
			$result['ret_num'] = 210;
			$result['ret_msg'] = '报价不存在';
		}
		echo json_encode( $result );
	}

	/**
	 * 我要买申诉
	 */
	public function actionAppeal(){
		$this->check_key();
		$buyid = Frame::getIntFromRequest('buyid');
		$reason = Frame::getStringFromRequest('reason');
		$user = $this->check_user();
		if(empty($buyid)){
			$result['ret_num'] = 201;
			$result['ret_msg'] = '我要买ID为空';
			echo json_encode( $result );
			die();
		}
		if(empty($reason)){
			$result['ret_num'] = 211;
			$result['ret_msg'] = '申诉原因为空';
			echo json_encode( $result );
			die();
		}
		$buy = Buy::model()->findByPk($buyid);
		if(!$buy){
			$result['ret_num'] = 214;
			$result['ret_msg'] = '我要买ID不存在';
			echo json_encode( $result );
			die();
		}		
		$re = BuyAppeal::model()->find("buy_id ={$buyid} and member_id = {$user->id}");
		if($re){
			$result['ret_num'] = 213;
			$result['ret_msg'] = '你已申诉，不能再申诉';
			echo json_encode( $result );
			die();
		}
		$appeal = new BuyAppeal();
		$appeal->buy_id = $buyid;
		$appeal->member_id = $user->id;
		$appeal->reason = $reason;
		$appeal->created_time = time();
		if($appeal->save()){
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';
		}else{
			$result['ret_num'] = 212;
			$result['ret_msg'] = '申诉失败';
		}
		echo json_encode( $result );
	}
	
	/**
	 * 我要买举报
	 */
	public function actionReport(){
		$this->check_key();
		$userid = Frame::getIntFromRequest('userid');
		$reason = Frame::getStringFromRequest('reason');
		$user = $this->check_user();
		if(empty($userid)){
			$result['ret_num'] = 5232;
			$result['ret_msg'] = 'ID为空';
			echo json_encode( $result );
			die();
		}
		if(empty($reason)){
			$result['ret_num'] = 5231;
			$result['ret_msg'] = '举报内容为空';
			echo json_encode( $result );
			die();
		}
		$buyreport = Member::model()->findByPk($userid);
		if(!$buyreport){
			$result['ret_num'] = 5233;
			$result['ret_msg'] = '用户不存在';
			echo json_encode( $result );
			die();
		}
// 		$re = Buy::model()->find("user_id ={$userid} and member_id = {$user->id}");
// 		if($re){
// 			$result['ret_num'] = 213;
// 			$result['ret_msg'] = '你已申诉，不能再申诉';
// 			echo json_encode( $result );
// 			die();
// 		}
		$appeal = new BuyReport();
		$appeal->user_id = $userid;
		$appeal->member_id = $user->id;
		$appeal->reason = $reason;
		$appeal->created_time = time();
		if($appeal->save()){
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';
		}else{
			$result['ret_num'] = 5234;
			$result['ret_msg'] = '举报失败';
		}
		echo json_encode( $result );
	}
	
	/**
	 * 我要买交易关闭
	 */
	public function actionClose(){
		$this->check_key();
		$buyid = Frame::getIntFromRequest('buyid');
		if(empty($buyid)){
				$result['ret_num'] = 201;
				$result['ret_msg'] = '我要买ID为空';
				echo json_encode( $result );
				die();
			}
		$user = $this->check_user();
		$buy = Buy::model()->findByPk($buyid);
		if(!$buy){
			$result['ret_num'] = 214;
			$result['ret_msg'] = '我要买ID不存在';
			echo json_encode( $result );
			die();
		}
		
		$buy->deadline = time();
		$buy->is_close = 1;		
		if($buy->update()){
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';
		}else{
			$result['ret_num'] = 215;
			$result['ret_msg'] = '关闭交易失败';
		}
		echo json_encode( $result );
	}
	
}