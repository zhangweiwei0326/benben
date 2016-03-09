<?php
/**
定时脚本，
*/
Yii::$enableIncludePath = false;
define('__ROOT__',dirname(dirname(__FILE__)));
class CrontabscriptController extends Controller
{
	//检查百姓网用户
	public function actionCheckBx(){
		$connection = Yii::app()->db;
		$time = time() - 3600*24;
		$sql = "select status, apply_id from (select * from bxapply_record where status>2 and created_time > ".$time." order by id desc) a  group by apply_id";
		//$sql = "select phone, status from (select * from bxapply_record where  apply_id = 3 order by id desc) a left join bxapply as b on a.apply_id = b.id group by apply_id";
		$command = $connection->createCommand($sql);
		$result1 = $command->queryAll();
		$apply_id = array();
		if ($result1) {
			foreach ($result1 as $key => $value) {
				$apply_id[] = $value['apply_id'];
			}
		}
		if (count($apply_id) > 0) {
			$sql = "select phone, status, short_phone from bxapply where id in (".implode(",", $apply_id).")";
			$command = $connection->createCommand($sql);
			$result2 = $command->queryAll();
			if ($result2) {
				foreach($result2 as $each){
					if ($each['status'] == 3) {
						$sql = "update group_contact_phone set is_baixing = ".$each['short_phone']." where phone = ".$each['phone'];
						$command = $connection->createCommand($sql);
						$resultc = $command->execute();
					}else if ($each['status'] == 4) {
						$sql = "update group_contact_phone set is_baixing = 0 where phone = ".$each['phone'];
						$command = $connection->createCommand($sql);
						$resultc = $command->execute();
					}
					
				}
			}
		}

	}
	
	//自动
	public function actionAutotop()
	{
		$model= NumberTrain::model()->findAll("istop > 0");
		if($model)
		{
			foreach ($model as $va){
				$toplog = NumberTrainTop::model()->find("train_id = {$va->id} order by created_time desc");
	
				if($toplog->istop > 0){
					$toptime = $toplog->created_time + $toplog->number*24*60*60;
					if($toptime < time()){
						$va->istop = 0;
						$log = new NumberTrainTop();
						$log->train_id = $va->id;
						$log->user_id = 1;
						$log->created_time = time();
						$log->istop = 0;
						$log->number = 0;
						if($log->save()){
							$va->update();
						}
					}
				}
			}
		}
	}
	
	//自动解禁
	public function actionAutoopen(){
		$member = Member::model()->findAll("status <> 0 or creation_disable <> 0 or buy_disable <> 0 or enterprise_disable <> 0 or group_disable <> 0 or store_disable <> 0 or league_disable <> 0");
		$timeArray = array(1=>7*24*60*60,2=>14*24*60*60,3=>30*24*60*60,4=>90*24*60*60,5=>'无限期');
		foreach ($member as $va){
			if($va->status && ($va->status != 5)){
				$status_log = MemberDisable::model()->find("member_id = {$va->id} and status = {$va->status} order by created_time desc");
				$opentime = $status_log->created_time + $timeArray[$va->status];
				if($opentime <= time()){
					$va->status = 0;
					if($va->update()){
						$status_log = new MemberDisable();
						$status_log->member_id = $va->id;
						$status_log->status = 0;
						$status_log->user_id = 1;
						$status_log->reason = "禁用期限到期";
						$status_log->created_time = time();
						$status_log->save();
					}
				}
			}
			
			if($va->creation_disable && ($va->creation_disable != 5)){
				$status_log = ServiceDisable::model()->find("member_id = {$va->id} and status = {$va->creation_disable} and type= 1 order by created_time desc");
				$opentime = $status_log->created_time + $timeArray[$va->creation_disable];
				if($opentime <= time()){
					$va->creation_disable = 0;
					if($va->update()){
						$status_log = new ServiceDisable();
						$status_log->member_id = $va->id;
						$status_log->status = 0;
						$status_log->user_id = 1;
						$status_log->type = 1;
						$status_log->reason = "禁用期限到期";
						$status_log->created_time = time();
						$status_log->save();
					}
				}
			}
			
			if($va->buy_disable && ($va->buy_disable != 5)){
				$status_log = ServiceDisable::model()->find("member_id = {$va->id} and status = {$va->buy_disable} and type= 2 order by created_time desc");
				$opentime = $status_log->created_time + $timeArray[$va->buy_disable];
				if($opentime <= time()){
					$va->buy_disable = 0;
					if($va->update()){
						$status_log = new ServiceDisable();
						$status_log->member_id = $va->id;
						$status_log->status = 0;
						$status_log->user_id = 1;
						$status_log->type = 2;
						$status_log->reason = "禁用期限到期";
						$status_log->created_time = time();
						$status_log->save();
					}
				}
			}
			
			if($va->enterprise_disable && ($va->enterprise_disable != 5)){
				$status_log = ServiceDisable::model()->find("member_id = {$va->id} and status = {$va->enterprise_disable} and type= 3 order by created_time desc");
				$opentime = $status_log->created_time + $timeArray[$va->enterprise_disable];
				if($opentime <= time()){
					$va->enterprise_disable = 0;
					if($va->update()){
						$status_log = new ServiceDisable();
						$status_log->member_id = $va->id;
						$status_log->status = 0;
						$status_log->user_id = 1;
						$status_log->type = 3;
						$status_log->reason = "禁用期限到期";
						$status_log->created_time = time();
						$status_log->save();
					}
				}
			}
			
			if($va->group_disable && ($va->group_disable != 5)){
				$status_log = ServiceDisable::model()->find("member_id = {$va->id} and status = {$va->group_disable} and type= 4 order by created_time desc");
				$opentime = $status_log->created_time + $timeArray[$va->group_disable];
				if($opentime <= time()){
					$va->group_disable = 0;
					if($va->update()){
						$status_log = new ServiceDisable();
						$status_log->member_id = $va->id;
						$status_log->status = 0;
						$status_log->user_id = 1;
						$status_log->type = 4;
						$status_log->reason = "禁用期限到期";
						$status_log->created_time = time();
						$status_log->save();
					}
				}
			}
			
			if($va->store_disable && ($va->store_disable != 5)){
				$status_log = ServiceDisable::model()->find("member_id = {$va->id} and status = {$va->store_disable} and type= 5 order by created_time desc");
				$opentime = $status_log->created_time + $timeArray[$va->store_disable];
				if($opentime <= time()){
					$va->store_disable = 0;
					if($va->update()){
						$status_log = new ServiceDisable();
						$status_log->member_id = $va->id;
						$status_log->status = 0;
						$status_log->user_id = 1;
						$status_log->type = 5;
						$status_log->reason = "禁用期限到期";
						$status_log->created_time = time();
						$status_log->save();
					}
				}
			}
			
			if($va->league_disable && ($va->league_disable != 5)){
				$status_log = ServiceDisable::model()->find("member_id = {$va->id} and status = {$va->league_disable} and type= 6 order by created_time desc");
				$opentime = $status_log->created_time + $timeArray[$va->league_disable];
				if($opentime <= time()){
					$va->league_disable = 0;
					if($va->update()){
						$status_log = new ServiceDisable();
						$status_log->member_id = $va->id;
						$status_log->status = 0;
						$status_log->user_id = 1;
						$status_log->type = 6;
						$status_log->reason = "禁用期限到期";
						$status_log->created_time = time();
						$status_log->save();
					}
				}
			}
			
		}
	}
	/**
	更新设置东阳百姓网的常用联系人
	*/
	public function actionAddcommon(){
		$beginTime = time();
		$dongyang = 136;
		$connection = Yii::app()->db;
		// $time = time() - 3600*2;
		$time = 0;
		//查找最近2小时内新增加的成员
		$sql = "select id, phone, short_phone from enterprise_member where contact_id = ".$dongyang." and created_time >= ".$time;
		$command = $connection->createCommand($sql);
		$result = $command->queryAll();
		$newMemberPhone = array();
		$newMemberRelation = array();
		if ($result) {
			foreach($result as $e){
				if ($e['phone']) {
					$newMemberPhone[] = $e['phone'];
					$newMemberRelation[$e['phone']] = $e['id'];
				}
				if ($e['short_phone']) {
					$newMemberPhone[] = $e['short_phone'];
					$newMemberRelation[$e['short_phone']] = $e['id'];
				}
				
			}
		}

		//查找政企中的犇犇用户
		$sql = 'select a.id, a.phone from member a left join enterprise_member b on a.phone = b.phone where b.contact_id='.$dongyang.' and a.id_enable = 1';
		$command = $connection->createCommand($sql);
		$memberQuery = $command->queryAll();
		if ($memberQuery) {
			$memberInfo = array();
			foreach($memberQuery as $e){
				$memberInfo[] = $e['id'];
			}

			//查找是否已经设置常用联系人，如果没有设置常用联系人，则不更新
			$sql = "select member_id from enterprise_display_member_log where enterprise_id = ".$dongyang." and member_id in (".implode(',', $memberInfo).")";
			$command = $connection->createCommand($sql);
			$rightQuery = $command->queryAll();
			$rightMember = array();
			if ($rightQuery) {
				foreach($rightQuery as $e){
					$rightMember[] = $e['member_id'];
				}
				//查找用户设置的常用联系人
				$sql = "select user_id, member_id from enterprise_display_member where enterprise_id = ".$dongyang." and user_id in (".implode(",", $rightMember).")";
				$command = $connection->createCommand($sql);
				$commonQuery = $command->queryAll();
				$commonInfo = array();
				if ($commonQuery) {
					foreach($commonQuery as $e){
						$commonInfo[$e['user_id']][] = $e['member_id'];
					}
				}

				//如果用户常用联系人超过50个，则取消
				foreach ($rightMember as $key => $value) {
					$countMember = $commonInfo[$value];
					if ($countMember && $countMember>=50) {
						unset($rightMember[$key]);
					}
				}
				if (count($rightMember) == 0) {
					die();
				}

				//查找有权限更新用户的通讯录
				$sql = "select b.phone, a.member_id from group_contact_info a left join group_contact_phone b on a.id = b.contact_info_id where a.member_id in (".implode(",", $rightMember).")";
				$command = $connection->createCommand($sql);
				$contactQuery = $command->queryAll();
				$contactInfo = array();
				$insertArray = array();
				if ($contactQuery){
					foreach($contactQuery as $e) {
						if (in_array($e['phone'], $newMemberPhone)) {
							if (isset($commonInfo[$e['member_id']])) {
								$commonIds = $commonInfo[$e['member_id']];
								if (in_array($newMemberRelation[$e['phone']], $commonIds)) {
									continue;
								}
							}
							$commonInfo[$e['member_id']][] = $newMemberRelation[$e['phone']];
							$insertArray[] = '('.$e['member_id'].", ".$newMemberRelation[$e['phone']].", ".$dongyang.", 0)";
						}
					}
					if (count($insertArray) > 0) {
						$sql = "insert into enterprise_display_member(user_id, member_id, enterprise_id, group_id) values ".implode(",", $insertArray);
						$command = $connection->createCommand($sql);
						$excute = $command->execute();
					}
				}

			}
		}
		echo time()-$beginTime;


	}

	/*
	 * 团购/促销过期提醒还剩10天，3天，1天到期自动发送环信提醒
	 */
	public function actionExpireRemind(){
		$connection = Yii::app()->db;
		$now=time();
		$sql1="select c.huanxin_username,a.vip_time from promotion_manage as a left join store_auth as b on a.member_id=b.member_id
		left join member as c on a.member_id=c.id where b.status=2 and a.vip_time>".$now." and a.vip_time<=".($now+864000);
		$command=$connection->createCommand($sql1);
		$result1=$command->queryAll();
		$now_time=date("Y-m-d",$now);
		foreach($result1 as $k=>$v){
			$vip=date("Y-m-d",$v['vip_time']);
			$left=strtotime($vip." 0:0:0")-strtotime($now_time." 0:0:0");
			if($left==864000){
				$this->sendHXMessage([$v['huanxin_username']],"您的号码直通车套餐还剩余10天就到期了");
			}elseif($left==259200){
				$this->sendHXMessage([$v['huanxin_username']],"您的号码直通车套餐还剩余3天就到期了");
			}elseif($left==86400){
				$this->sendHXMessage([$v['huanxin_username']],"您的号码直通车套餐在1天内就到期了");
			}
		}
	}

	/*
	 * 订单自动确认，如果没有延长收货，发货后7天自动确认收货，延长则往上加时间
	 * 读取store_order_info中的extend_shipping_time获取总到期时间
	 */
	public function actionAutoConfirmOrder(){
		$connection = Yii::app()->db;
		$now=strtotime(date("Y-m-d",time())." 0:0:0");
		$yestoday=$now-86400;
		$sql="update store_order_info set order_status=5 ,shipping_status=2 ,confirm_time=add_time where pay_status=2 and
		shipping_time+extend_shipping_time*86400>".$yestoday." and shipping_time+extend_shipping_time*86400<=".$now;
		$command=$connection->createCommand($sql);
		$re=$command->execute();
		echo $re;
	}
	/*
	 * 收货后7天不评论，自动给予商家好评
	 */
	public function actionAutoComment(){
		$connection = Yii::app()->db;
		$now=strtotime(date("Y-m-d",time())." 0:0:0");
		$yestoday=$now-86400;
		$sql="select a.order_id,b.promotion_id,a.extension_code,a.member_id from store_order_info as a left join store_order_goods as b on a.order_id=b.order_id
		where a.order_status=5 and a.pay_status=2 and a.extension_code!=4 and a.extension_code!=5 and
		a.shipping_time + a.extend_shipping_time * 86400+7*86400>".$yestoday." and a.shipping_time + a.extend_shipping_time * 86400+7*86400<=".$now;
		$command=$connection->createCommand($sql);
		$all=$command->queryAll();
		foreach($all as $k=>$v){
			StoreOrderInfo::model()->updateAll(array("order_status"=>6),"order_id={$v['order_id']}");
			$minfo=Member::model()->find("id={$v['member_id']}");
			$insert_arr[]="(".$v['extension_code'].",".$v['promotion_id'].",'".$minfo['huanxin_username']."','".$minfo['nick_name']."','好评',3,".time().",0,".$v['member_id'].",".$v['order_id']."0)";

			$nowmonth=strtotime(date("Y-m",time())."-1 0:0:0");
			$month=date("m",time());
			if($month==12){
				$month=1;
			}else{
				$month++;
			}
			$nextmonth=strtotime(date("Y",time())."-".$month."-1 0:0:0");
			//好评+1，中评0，差评-1分，number_train
			//判断同产品/每月/相同买家/卖家之间分数-5<=x<=10
			$sql2="SELECT sum(comment_rank)-2*count(1) as new_red from store_comment
                WHERE comment_rank>0 and add_time>=".$nowmonth." and add_time<".$nextmonth." and member_id=".$v['member_id']." and promotion_id=".$v['promotion_id'];
			$command=$connection->createCommand($sql2);
			$result0=$command->queryAll();
			if($result0['new_red']<=10&&$result0['new_red']>=-5){
				$shopinfo=$this->getShopinfo($v['promotion_id']);
				$train=NumberTrain::model()->find("id={$shopinfo[$v['promotion_id']]['id']}");
				$train->score=$train['score']+1;
				$train->update();
			}
			//相同买家/卖家/同产品/1月内，交易成功增加已售数量
			$num=StoreComment::model()->count("add_time>=".$nowmonth." and add_time<".$nextmonth." and member_id=".$v['member_id']." and promotion_id=".$v['promotion_id']);
			if($num<=15){
				$pro=Promotion::model()->find("id={$v['promotion_id']}");
				$pro->sellcount=$pro['sellcount']+1;
				$pro->update();
			}
		}

		if($insert_arr) {
			$sql1 = "insert into store_comment(comment_type,promotion_id,huanxin_username,user_name,content,comment_rank,add_time,parent_id,member_id,order_id,is_seller)
		values " . implode(",", $insert_arr);
			$command = $connection->createCommand($sql1);
			$re = $command->execute();
		}
	}
	/*
	 *系统推送消息
	 * 传入$username=array ; $content=string; $arr额外信息(t1为是否显示在聊天栏中0no/1yes；t2为是否进入通知界面0no/1yes；t3为处理进度0wait/1ok/2no；t4为消息类型:1好友联盟，2群组消息，3好友请求,4.我要买,5.群组转让)
	 * 返回array,其中data有用
 	 * */
	public function sendHXMessage($username, $content, $arr = array(), $from_user = "admin")
	{
		include_once(dirname(__ROOT__)."/lib/Easemob.class.php");
		$target_type = "users";
		$ext = array("em_apns_ext" => array("em_push_title" => "{$content}"));
		$ext = array_merge($ext, $arr);
		$options = array(
				"client_id" => CLIENT_ID,
				"client_secret" => CLIENT_SECRET,
				"org_name" => ORG_NAME,
				"app_name" => APP_NAME
		);
		$huanxin = new Easemob($options);
		$huanxin->yy_hxSend($from_user, $username, $content, $target_type, $ext);
//		$re = json_decode($re, true);
//		return $re;
	}

	/*
     * 根据商品号查询商店情况
     * 多个promotionid用逗号隔开
     */
	public function getShopinfo($promotionid){
		$pinfo=Promotion::model()->findAll("id in ({$promotionid})");
		foreach($pinfo as $k=>$v){
			$pmids[]=$v['pm_id'];
			$goods[$v['id']]=$v['pm_id'];
		}
		$ids=implode(",",$pmids);
		$connection=Yii::app()->db;
		$sql="select a.poster,a.id,a.short_name,a.member_id,b.id as pm_id from number_train as a left join promotion_manage as b on a.id=b.store_id where b.id in ({$ids})";
		$command=$connection->createCommand($sql);
		$result0=$command->queryAll();
		foreach($result0 as $kk=>$vv){
			$info[$vv['pm_id']]=$vv;
		}
		foreach($goods as $kg=>$vg){
			$goods[$kg]=$info[$vg];
		}
		return $goods;
	}

	/*
	 * 自动取消号码直通车转让
	 * 时限7天，未处理自动取消
	 */
	public function actionCancelStoreTransfer(){
		$tinfo=StoreTransfer::model()->findAll("status=0 and (created_time+7*86400)<=".time());
		if($tinfo){
			StoreTransfer::model()->deleteAll("status=0 and (created_time+7*86400)<=".time());
		}
	}

	/*
	 * 拍卖结束自动退回非中标者保证金
	 * 每小时执行
	 */
	public function actionBackAuctionGuarantee(){
		$info=TopAuction::model()->findAll("pid=0 and is_close=0 and end_time<=".time());
		if(!$info){
			echo "None!";
			die();
		}
		foreach($info as $k=>$v){
			//退还所有保证金
			$son=TopAuction::model()->findAll("pid={$v['auction_id']}");
			if(!$son){
				die();
			}
			foreach($son as $kk=>$vv){
				$allpart=AuctionLog::model()->findAll("auction_id={$vv['auction_id']}");
				foreach($allpart as $ka=>$va){
					$one=array();
					if($va['top']!=1){
						//非中标者退回保证金,即个人账户余额增加，总保证金减少
						$one=Member::model()->find("id={$va['member_id']}");
						$one->fee=$one['fee']+$va['guarantee'];
						$one->guarantee=$one['guarantee']-$va['guarantee'];
						$one->update();
					}else{
						/*
						 * 中标者，中标金额<=保证金，直接扣，产生已付款订单，归还多余保证金，并在top_auction中更新状态，is_paid=1,is_close=1
						 * 否则，产生剩余保证金订单，订单一经取消，不退回保证金，并在top_auction中更新状态，is_close=1
						 */
						if($va['price']<=$va['guarantee']){
							$one=Member::model()->find("id={$va['member_id']}");
							$one->fee=$one['fee']+($va['guarantee']-$va['price']);
							$one->guarantee=$one['guarantee']-$va['guarantee'];
							$one->update();

							//保存订单信息
							$og = new StoreOrderInfo();
							//生成订单号
							$num = StoreOrderInfo::model()->count("order_id!=0");
							$og->order_sn = (intval(date("Y")) - 2015) . ($num + 1) . date("i", time()) . substr($va['member_id'], -4) . substr(41, 0, 1);

							$og->member_id = $va['member_id'];
							$og->order_status = 6;
							$og->shipping_status = 2;
							$og->pay_status = 2;
							$og->pay_id = 1;
							$og->pay_name = "在线支付";
							$og->extension_code = 5;
							$og->goods_amount = $va['price'];
							$og->order_amount = $va['price'];
							$og->add_time = time();
							$og->confirm_time = time();
							$og->shipping_time = time();
							$og->pay_time = time();
							$og->extend_shipping_time = 3;
							if($og->save()){
								//保存商品信息
								$gg = new StoreOrderGoods();
								$gg->order_id = $og->order_id;
								$gg->promotion_id = $va['auction_id'];
								$gg->goods_name = "拍卖付款";
								$gg->goods_sn = "BB".time();
								$gg->goods_number = 1;
								$gg->origion_price = $va['price'];
								$gg->promotion_price = $va['price'];
								$gg->save();
								$topinfo=TopAuction::model()->find("auction_id={$va['auction_id']}");
								$topinfo->is_paid=1;
								$topinfo->is_close=1;
								$topinfo->owner_id=$va['member_id'];
								$topinfo->update();
							}
						}else{
							$one=Member::model()->find("id={$va['member_id']}");
							$one->guarantee=$one['guarantee']-$va['guarantee'];
							$one->update();

							//保存订单信息
							$og = new StoreOrderInfo();
							//生成订单号
							$num = StoreOrderInfo::model()->count("order_id!=0");
							$og->order_sn = (intval(date("Y")) - 2015) . ($num + 1) . date("i", time()) . substr($va['member_id'], -4) . substr(41, 0, 1);

							$og->member_id = $va['member_id'];
							$og->order_status = 1;
							$og->shipping_status = 0;
							$og->pay_status = 0;
							$og->pay_id = 1;
							$og->pay_name = "在线支付";
							$og->extension_code = 5;
							$og->goods_amount = $va['price'];
							$og->order_amount = $va['price']-$va['guarantee'];
							$og->money_paid = $va['guarantee'];
							$og->add_time = time();
							$og->confirm_time = time();
							$og->extend_shipping_time = 3;
							if($og->save()){
								//保存商品信息
								$gg = new StoreOrderGoods();
								$gg->order_id = $og->order_id;
								$gg->promotion_id = $va['auction_id'];
								$gg->goods_name = "拍卖付款";
								$gg->goods_sn = "BB".time();
								$gg->goods_number = 1;
								$gg->origion_price = $va['price'];
								$gg->promotion_price = $va['price'];
								$gg->save();
								$topinfo=TopAuction::model()->find("auction_id={$va['auction_id']}");
								$topinfo->is_paid=0;
								$topinfo->is_close=1;
								$topinfo->owner_id=$va['member_id'];
								$topinfo->update();
							}
						}
					}
				}
			}
			//关闭主拍卖场
			TopAuction::model()->updateAll(array("is_close"=>1),"auction_id={$v['auction_id']}");
		}
		echo "Complete!";
	}

	/*
	 * 拍卖3天不付款自动取消订单
	 * extension_code=5
	 */
	public function actionCancelAuction(){
		$connection = Yii::app()->db;
		$now=strtotime(date("Y-m-d",time())." 0:0:0");
		$yestoday=$now-86400;
		$sql="update store_order_info set order_status=2 ,shipping_status=0 where pay_status=0 and extension_code=5 and order_status=1 and
		confirm_time+extend_shipping_time*86400>".$yestoday." and confirm_time+extend_shipping_time*86400<=".$now;
		$command=$connection->createCommand($sql);
		$re=$command->execute();
	}
}