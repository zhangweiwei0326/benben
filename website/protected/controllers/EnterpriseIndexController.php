<?php
class EnterpriseIndexController extends EnterpriseBaseController {
	public function actionIndex() {
		$enterprise = Enterprise::model()->findByPk($this->enterprise_id);
		$role = EnterpriseRole::model()->find("enterprise_id='" . $this->enterprise_id . "'");
		//系统消息
		$model1 = News::model();
		$cri1 = new CDbCriteria();
		$cri1->condition = "(identity1='" . $this->enterprise_id . "')and(status='0')and(type='4')";
		$cri1->order = "id desc";
		$news = $model1->find($cri1);
		//申请者信息
		$applyer = Member::model()->findByPk($news->sender);
		//公告
		if ($this->apply_status == 1) {
			$notice = EnterpriseNotice::model()->findAllBySql('select s.name as sname, t.content,t.update_time,t.created_time,t.member_id from
			                         enterprise_notice t left join enterprise_member s on t.member_id = s.member_id and t.enterprise_id = s.contact_id
			         where t.enterprise_id = ' . $this->enterprise_id . ' order by t.id desc, t.sort desc');

		}

		if (Yii::app()->request->isAjaxRequest) {
			$notice_content = Frame::getStringFromArray($_POST, 'notice_content');
			$notice_content = htmlspecialchars($notice_content);
			$new_content = Frame::getStringFromArray($_POST, 'new_content');
			$new_content = htmlspecialchars($new_content);
// 			var_dump($new_content);die();
			if ($notice_content) {
				$notice1 = EnterpriseNotice::model()->findBySql('select * from enterprise_notice order by id desc,sort desc');
				$notice1->content = $notice_content;
				$notice1->update_time = time();
				$notice1->enterprise_id = $this->enterprise_id;
				if ($notice1->save()) {
					echo 1;
				} else {
					echo 0;

				}
				die();
			}
			if ($new_content) {
				$notice2 = new EnterpriseNotice;
				$notice2->enterprise_id = $this->enterprise_id;
				if ($this->administrator_id) {
					$notice2->member_id = $this->administrator_id;
				} else {
					$notice2->apply_id = $this->apply_id;
				}
				$notice2->content = $new_content;
				$notice2->created_time = time();
				$notice2->update_time = time();
				if ($notice2->save()) {
					echo 1;
				} else {
					echo 0;
				}
				die();

			}

		}

		// p($enterprise);p($role);die;
		$this->render("Personal", array(
			'enterprise' => $enterprise,
			'role' => $role,
			'news' => $news, 'applyer' => $applyer,
			'administrator' => $this->administrator,
			'notice' => $notice,
		));
	}

	/**
	 * 历史公告
	 * */

	public function actionHistoryNotice() {
		$cri = new CDbCriteria();
		$model=EnterpriseNotice::model();
		if ($this->apply_status == 1) {
// 			$notice = EnterpriseNotice::model()->findAllBySql('select s.name as sname, t.content,t.update_time,t.created_time,t.member_id,b.name as bname from
// 			                         enterprise_notice t
// 					left join enterprise_member s on t.member_id = s.member_id and t.enterprise_id = s.contact_id
// 					left join apply_register b on t.apply_id=b.id
// 			         where t.enterprise_id = ' . $this->enterprise_id . ' order by t.id desc, t.sort desc');
			
			$pages = new CPagination();
			$cri->select='s.name as sname, t.content,t.update_time,t.created_time,t.member_id,b.name as bname';
			$cri->join='left join enterprise_member s on t.member_id = s.member_id and t.enterprise_id = s.contact_id
								left join apply_register b on t.apply_id=b.id
					';
			$cri->addCondition('t.enterprise_id= ' .$this->enterprise_id, 'AND');
			$cri->addCondition('t.created_time <  (select max(created_time) from benben_test.enterprise_notice where enterprise_id = '.$this->enterprise_id.')' , 'AND');
			$cri->order='t.id desc,t.sort desc';			
// 			var_dump();die();
			$pages->itemCount =$model-> count($cri);
			$pages->pageSize = 18;
			$pages->applyLimit($cri);
			$notice=$model->findAll($cri);
		}
		$this->render("historyNotice", array(
			'notice' => $notice,
			'pages' => $pages,
		));

	}

	/**
	 * 成员管理
	 */
	public function actionMember() {
// 		p($this->apply_type);die;
		$session = new CHttpSession();
		$session->open();
		Yii::app()->session->add('backUrl',Yii::app()->request->url);

		$this->verify(); // 验证政企通讯录是否审核通过
		if (Yii::app()->request->isPostRequest) {
			$msg = $this->actionImport(); //p($msg);die;
		}
		$enterprise = Enterprise::model()->findByPk($this->enterprise_id); // 政企信息
		$enterprise_role = EnterpriseRole::model()->find("enterprise_id='" . $this->enterprise_id . "'"); // 政企权限

		//获得最终子分组，统计各分组下的人数
		$model1 = EnterpriseMember::model();
		$cri1 = new CDbCriteria();
		$cri1->join = "left join enterprise_member_manage a on a.member_id=t.id";
		$cri1->condition = "(t.contact_id='" . $this->enterprise_id . "')";
		$enterprise_sum = $model1->count($cri1);
		// 		p($enterprise_sum);die;
		$final_group = $this->memberGroup('last');
		$final_group_id = '';
		$ungroup_num = 0;
		if (!empty($final_group)) {
			$group_num = array(); //最终分组的人数
			foreach ($final_group as $k => $v) {
				$cri1->condition = "(t.contact_id='" . $this->enterprise_id . "')and(a.group_id='" . $v->id . "')";
				$group_num[$v->id] = $model1->count($cri1);
				$ungroup_num += $group_num[$v->id];
				if (empty($k)) {
					$final_group_id = $v->id;
				} else {
					$final_group_id .= ',' . $v->id;
				}
			}
			$ungroup_num = $enterprise_sum - $ungroup_num;
		}else{
			$ungroup_num = $enterprise_sum;
		}
// 		p($final_group_id);die;

		// 政企成员
		$model = EnterpriseMemberNew::model();
		$cri = new CDbCriteria();
		$cri->join = "left join enterprise_member_manage a on a.member_id=t.id left join member b on b.id=t.member_id";
		$cri->select = "t.*,a.access_level,b.benben_id";
		$cri->condition = "(t.contact_id='" . $this->enterprise_id . "')";
		foreach ($_GET as $k => $v) {
			if (!empty($v)) {
				$v = addslashes($v);
				switch ($k) {
				case 'member_id':$cri->condition .= "and(b.benben_id like '%" . $v . "%')";
					break;
				case 'name':$cri->condition .= "and(t.name like '%" . $v . "%')";
					break;
				case 'remark_name':$cri->condition .= "and(t.remark_name like '%" . $v . "%')";
					break;
				case 'phone':
					if ($this->enterprise_type == 1) {
						$cri->condition .= "and( (t.phone like '%" . $v . "%')or(t.short_phone like '%" . $v . "%') )";
					}

					if ($this->enterprise_type == 2) {
						$cri->condition .= "and(t.short_phone like '%" . $v . "%')";
					}

					break;
				case 'access_level':$cri->condition .= "and(a.access_level='" . $v . "')";
					break;
				case 'group_id':$group_id = explode("|", $v); //$group_id=ksort($group_id);
					$cri->condition .= "and(a.group_id='" . $group_id[count($group_id) - 1] . "')";
					break;
				case 'ungroup':if (!empty($final_group_id)) {
						$cri->condition .= "and(a.group_id not in (" . $final_group_id . "))";
					}

					break;
				}
			}
		}
		$condition = base64_encode($cri->condition);
		$pages = new CPagination();
		$pages->itemCount = $model->count($cri);
		$pages->pageSize = 6;
		$pages->applyLimit($cri);
		$items = $model->findAll($cri);
		include "Enterservice.php";
		$enterservice = new Enterservice();
		$enterservice->set_member_id(Yii::app()->user->getState("Enterprise_memberInfo"));
		$enterservice->set_info();
		$enterservice->set_names();
		$enterservice->set_duration();
		$enterservice->set_view();
		//var_dump($enterservice);exit;
		// 		p($items);die;

		$this->render("Personal-2", array(
			'enterprise' => $enterprise,
			'enterprise_role' => $enterprise_role,
			'items' => $items,
			'pages' => $pages,
			'levels' => $levels, 'level1' => $level1,
			'condition' => $condition,
			'msg' => $msg,
			'enterprise_sum' => $enterprise_sum, 'ungroup_num' => $ungroup_num, 'group_num' => $group_num,
			'group_id' => $group_id, 'final_group_id' => $final_group_id,
			'enterservice' => $enterservice,
		));
	}
	public function actionGetInfo() {
		$names_money = Frame::getStringFromRequest("names_money");
		$duration_aa = Frame::getIntFromRequest("duration_aa");
		$money = Frame::getIntFromRequest("money");
		// $member_id = Yii::app()->user->getState("memberInfo")->id;
		if ((!$names_money) || (!$duration_aa)) {
			echo 1;
			die;
		}
		// $member_info = Yii::app()->user->getState("memberInfo");
		// $minfo = Member::model()->find("id={$member_info->id}");
		// $coin = $minfo['coin'];
		// $fee_tmp = $minfo['fee'];

		// $row = MemberRefundApply::model()->findAll("is_delete=0 and member_id={$member_id} and handle=0");
		// if ($row) {
		// 	foreach ($row as $value) {
		// 		$not_handle_fee += $value->fee;
		// 	}
		// }
		// $fee = $fee_tmp - $not_handle_fee; //可用的余额=金额-未处理的金额
		// $sevice_pay = new service();
		// $sevice_pay->set_member_id($member_info->id);
		// $sevice_pay->set_vip_info($service_type);
		// $re = $sevice_pay->pay_price($service_name, $service_duration);
		include "Enterservice.php";
		$enterservice = new Enterservice();
		$enterservice->set_member_id(Yii::app()->user->getState("Enterprise_memberInfo"));
		$enterservice->set_info();
		$enterservice->set_names();
		$enterservice->set_duration();
		$re = $enterservice->pay_price($names_money, $duration_aa);
		//var_dump($enterservice);e
		$data = array('price' => $re['price'], 'vip_price' => $re['vip_price']);
		echo json_encode($data);
	}

	public function actionPay() {
		include "Enterservice.php";
		// $use_fee = Frame::getStringFromRequest("use_fee");
		// $use_coin = Frame::getIntFromRequest("use_coin");
		// $service_type = Frame::getIntFromRequest("service_type");
		$names_money = Frame::getStringFromRequest("names_money");
		$duration_aa = Frame::getIntFromRequest("duration_aa");
		if($this->administrator_id)
		{
			echo "<meta http-equiv='Content-Type'' content='text/html; charset=utf-8'>";
			echo "<script>alert('不是超级管理员，不能购买！');history.go(-1);</script>";

		}
		if ((!$names_money) || (!$duration_aa) || ($duration_aa < 0)) {
			echo "<meta http-equiv='Content-Type'' content='text/html; charset=utf-8'>";
			echo "<script>alert('商品名称或有效期为空，不能购买！');history.go(-1);</script>";
			// $this->redirect('/index.php/serviceserviceDetail?type=' . $service_type . '&store=1');
		}
		// $member_info = Yii::app()->user->getState("memberInfo");
		// $validate = Member::model()->find("id={$member_info->id}");
		// if ($use_coin > 0) {
		//     if ($validate['coin'] < $use_coin || $use_coin < 0) {
		//         $this->redirect('/index.php/service/serviceDetail?type=' . $service_type . '&store=2');
		//         die;
		//     }
		// }
		// $row = MemberRefundApply::model()->findAll("is_delete=0 and member_id={$member_info->id} and handle=0");
		// if ($row) {
		//     foreach ($row as $value) {
		//         $not_handle_fee += $value->fee;

		//     }
		// }
		// $fee = $validate['fee'] - $not_handle_fee;
		// if ($use_fee > 0) {
		//     if ($fee < $use_fee || $fee < 0) {
		//         $this->redirect('/index.php/service/serviceDetail?type=' . $service_type . '&store=2');
		//         die;
		//     }
		// }
		$enterservice = new Enterservice();
		$enterservice->set_member_id(Yii::app()->user->getState("Enterprise_memberInfo"));
		$enterservice->set_info();
		$enterservice->set_names();
		$enterservice->set_duration();
		$re = $enterservice->pay_price($names_money, $duration_aa);
		$re['type'] = 15; //政企通讯录没有type所以默认给type=100
		if ($re['price'] <= 0) {
			echo "<meta http-equiv='Content-Type'' content='text/html; charset=utf-8'>";
			echo "<script>alert('应付金额小于当前拥有服务的折算金额，不能购买该服务，请购买其他服务套餐！!');history.go(-1);</script>";
			die;
		}	
		// if (($use_coin == $re['price']) || ($use_fee == $re['price']) || ($use_coin + $use_fee == $re['price'])) {
		//     $data = $this->createOrder($re['price'], $re['promotion_id'], $re['name'], $re['gname'], $re['count'], $re['type'], $use_coin, $use_fee);
		//     $orderinfo = StoreOrderInfo::model()->find("order_sn={$data['order_sn']}");
		//     $orderinfo->pay_status = 2;
		//     $orderinfo->money_paid = $orderinfo->order_amount;
		//     $orderinfo->shipping_status = 1;
		//     $orderinfo->order_status = 6;
		//     $orderinfo->pay_time = time();
		//     $orderinfo->shipping_time = time();
		//     $orderinfo->update();
		//     $this->WriteLogAndUpdate($orderinfo);
		//     $this->redirect('/index.php');
		// } else {
		$data = $this->createOrder($re['price'], $re['promotion_id'], $re['name'], $re['gname'], $re['count'], $re['type']);
		$this->AlipayApi($data);
		// }
	}
	/**
	 * #支付宝Api
	 */
	public function AlipayApi($data) {
		header("Content-type:text/html;charset=utf-8");
		require_once Yii::getPathOfAlias('webroot') . "/lib/alipay/alipay.config.php";
		require_once Yii::getPathOfAlias('webroot') . "/lib/alipay/lib/alipay_submit.class.php";

		/**************************请求参数**************************/
		//支付类型
		$payment_type = "1";
		//必填，不能修改
		//服务器异步通知页面路径
		$notify_url = Yii::app()->request->hostInfo . '/index.php/Alipay/AlipayWebNotify';
		//需http://格式的完整路径，不能加?id=123这类自定义参数

		//页面跳转同步通知页面路径
		$return_url = Yii::app()->request->hostInfo . '/index.php/Alipay/AlipayWebReturn';
		//需http://格式的完整路径，不能加?id=123这类自定义参数，不能写成http://localhost/

		//商户订单号
		$out_trade_no = $data['order_sn'];
		//商户网站订单系统中唯一订单号，必填

		//订单名称
		$subject = $data['goods_name'];
		//必填

		//付款金额
		$total_fee = $data['money'];
		//必填

		//订单描述

		$body = $data['describe'];
		//商品展示地址
		$show_url = $data['show_url'];
		//需以http://开头的完整路径，例如：http://www.商户网址.com/myorder.html

		//防钓鱼时间戳
		$anti_phishing_key = "";
		//若要使用请调用类文件submit中的query_timestamp函数

		//客户端的IP地址
		$exter_invoke_ip = "";
		//非局域网的外网IP地址，如：221.0.0.1

		/************************************************************/

		//构造要请求的参数数组，无需改动
		$parameter = array(
			"service" => "create_direct_pay_by_user",
			"partner" => trim($alipay_config['partner']),
			"seller_email" => trim($alipay_config['seller_email']),
			"payment_type" => $payment_type,
			"notify_url" => $notify_url,
			"return_url" => $return_url,
			"out_trade_no" => $out_trade_no,
			"subject" => $subject,
			"total_fee" => $total_fee,
			"body" => $body,
			"show_url" => $show_url,
			"anti_phishing_key" => $anti_phishing_key,
			"exter_invoke_ip" => $exter_invoke_ip,
			"_input_charset" => trim(strtolower($alipay_config['input_charset'])),
		);

		//建立请求
		$alipaySubmit = new AlipaySubmit($alipay_config);
		$html_text = $alipaySubmit->buildRequestForm($parameter, "get", "确认");
		echo $html_text;
	}

	/**
	 * #创建订单
	 * @param int $post_money 订单金额
	 * @param int $post_promotion_id 商品id
	 * @param int $post_name 商品名称
	 * @param int $attr_name 商品属性
	 * @param int $post_num 商品数量
	 * @param int $type 类型
	 * @param int $use_coin 使用犇币
	 * @param string $use_fee 使用余额
	 * @return array
	 */
	public function createOrder($post_money, $post_promotion_id, $post_name, $attr_name, $post_num, $type, $use_coin = 0, $use_fee = 0) //创建订单方法
	{
		$money = $post_money;
		$promotion_id = $post_promotion_id;
		$member_id = $this->apply_id;
		$num = StoreOrderInfo::model()->count("order_id!=0");
		$ModelA = new StoreOrderInfo();
		$ModelA->order_sn = (intval(date("Y")) - 2015) . ($num + 1) . date("i", time()) . substr($member_id, -4) . substr($promotion_id, 0, 1);
		$ModelA->order_status = 1;
		$ModelA->member_id = $member_id;
		$ModelA->shipping_status = 0;
		$ModelA->pay_status = 0;
		$ModelA->pay_id = 2;
		$ModelA->pay_name = '在线付';
		$ModelA->goods_amount = $money;
		$ModelA->shipping_fee = 0;
		$ModelA->pay_fee = 0;
		$ModelA->money_paid = 0;
		$ModelA->coin = $use_coin;
		$ModelA->fee = $use_fee;
		if (($use_coin > 0) || ($use_fee > 0)) {
			$ModelA->order_amount = round($money - $use_coin - $use_fee, 2);

		} else {
			$ModelA->order_amount = $money;
		}
		$ModelA->add_time = time();
		$ModelA->extension_code = 3;
		$ModelA->extension_id = 0;
		if ($ModelA->save()) {
			$ModelB = new StoreOrderGoods();
			$ModelB->order_id = $ModelA->order_id;
			$ModelB->promotion_id = $promotion_id;
			$ModelB->goods_name = $post_name;
			$ModelB->attr_name = $attr_name;
			$ModelB->goods_sn = '';
			$ModelB->goods_number = $post_num;
			$ModelB->origion_price = $money;
			$ModelB->promotion_price = $money;
			$ModelB->is_real = 0;
			$ModelB->extension_code = $type;
			if ($ModelB->save()) {
				$data['order_sn'] = $ModelA->order_sn;
				$data['goods_name'] = $ModelB->goods_name;
				$data['money'] = round($money - $use_coin - $use_fee, 2);
				$data['describe'] = "";
				$data['show_url'] = Yii::app()->request->hostInfo . '/index.php/Pay/index';
				return $data;
			}

		}
	}

	/**
	 * 通过父id找到所有的子id
	 */
	public function allSub($id) {
		$row = MemberGroup::model()->findAll("(enterprise_id='" . $this->enterprise_id . "')and(parent_id='" . $id . "')");
		foreach ($row as $val) {
			$data .= $this->allSub($val['id']);
			$data .= $val['id'] . ',';
		}
		return $data;
	}
	/**
	 * 编辑分组
	 */
	public function actionEditGroup() {
		if (Yii::app()->request->isAjaxRequest) {
			$data = $_POST['data'];
			$parent_level = explode("|", Frame::getStringFromRequest("parent_level"));
			$parent_id = intval($parent_level[0]);
			$level = intval($parent_level[1]);
			if (!empty($data)) {
				foreach ($data as $k => $v) {
					$v = explode("|", $v);
					if (!empty($v[0])) {
						$member_group = MemberGroup::model()->findByPk($v[0]);
						if ($member_group->parent_id == $parent_id && $member_group->level == $level && $member_group->enterprise_id == $this->enterprise_id) {
							if ($v[1] == 'edit') {
								$member_group->attributes = array('name' => htmlspecialchars($v[2]), 'sort' => intval($v[3]));
								$member_group->save();
							}
							if ($v[1] == 'delete') {
								MemberGroup::model()->deleteByPk($v[0]);
								$all_sub = $this->allSub($v[0]) . '0';
								MemberGroup::model()->deleteAll("(enterprise_id='" . $this->enterprise_id . "')and(id in(" . $all_sub . "))");
							}
						}
					} else {
						if ($v[1] == 'add') {
							if ($level <= $this->enterprise_group_level) {
								if (!empty($parent_id)) {
									$member_group = MemberGroup::model()->findByPk($parent_id);
									if ($member_group->id == $parent_id && $member_group->level < $this->enterprise_group_level && $member_group->enterprise_id == $this->enterprise_id) {
										$model = new MemberGroup();
										$model->attributes = array('enterprise_id' => $this->enterprise_id, 'name' => htmlspecialchars($v[2]),
											'level' => $level, 'sort' => intval($v[3]), 'parent_id' => $parent_id,
										);
										$model->save();
									}
								} else {
									$model = new MemberGroup();
									$model->attributes = array('enterprise_id' => $this->enterprise_id, 'name' => htmlspecialchars($v[2]),
										'level' => $level, 'sort' => intval($v[3]), 'parent_id' => $parent_id,
									);
									$model->save();
								}
							}
						}
					}
				}
			}
			echo json_encode(array('msg' => 'success'));die();
		}
		$lastUrl = Yii::app()->request->urlReferrer;
		$parent_id = Frame::getIntFromRequest("id");
		$cri = new CDbCriteria();
		$cri->condition = "(enterprise_id='" . $this->enterprise_id . "')and(parent_id='" . $parent_id . "')";
		$cri->order = "sort desc";
		$groups = MemberGroup::model()->findAll($cri);
		if (!empty($parent_id)) {
			$higher_group = MemberGroup::model()->findByPk($parent_id);
			$level = $higher_group->level + 1;
		} else {
			$level = 1;
		}
		if (!empty($groups)) {
			//当发现为空时
			$level = $groups[0]->level;
		}
		switch ($level) {
		case 1:$level_name = '一级';
			break;
		case 2:$level_name = '二级';
			break;
		case 3:$level_name = '三级';
			break;
		case 4:$level_name = '四级';
			break;
		}
// 		p($higher_group);die;
		$this->render("group_list", array(
			'lastUrl' => $lastUrl, 'groups' => $groups, 'level' => $level,
			'level_name' => $level_name, 'parent_id' => $parent_id,
			'groups' => $groups, 'higher_group' => $higher_group,
		));
	}
	/**
	 * 修改分组
	 */
	public function actionManageGroup() {
		$data = $_POST['data'];
		if (!empty($data)) {
			foreach ($data as $k => $v) {
				$tmp = explode("|", $v);
				if ($tmp[2] == 'edit') {
					$model = MemberGroup::model()->findByPk(intval($tmp[1]));
					$model->name = htmlspecialchars($tmp[0]);
					$model->save();
					unset($data[$k]);
				}
				if ($tmp[2] == 'delete') {
					MemberGroup::model()->deleteByPk(intval($tmp[1]));
					unset($data[$k]);
				}
			}
			if (!empty($data)) {
				$name = array();
				$id = array();
				$type = array();
				$pid = array();
				foreach ($data as $k => $v) {
					$tmp = explode("|", $v);
					$name[] = $tmp[0];
					$id[] = intval($tmp[1]);
					$type[] = $tmp[2];
					$pid[] = $tmp[3];
				}
				if (!empty($type)) {
					$parent_id = array();
					foreach ($type as $k => $v) {
						if ($v == 'add') {
							if ($pid[$k] == '0') {
								$model = new MemberGroup();
								$model->attributes = array(
									'enterprise_id' => $this->enterprise_id,
									'name' => $name[$k], 'parent_id' => intval($pid[$k]), 'created_time' => time(),
								);
								if ($model->save()) {
									$parent_id[$model->attributes['id']] = $pid[$k];
									unset($name[$k]);unset($id[$k]);unset($type[$k]);unset($pid[$k]);
								}
							}
						}
					}
					if (!empty($type)) {
						foreach ($type as $k => $v) {
							if (in_array($id[$k], $parent_id)) {
								//

							}
						}
					}
				}

			}
		}
// 		var_dump($data);
	}
	/**
	 * 成员编辑
	 */
	public function actionMemberEdit() {
		$this->verify(); // 验证政企通讯录是否审核通过
		$id = Frame::getIntFromRequest("id");
		if (Yii::app()->request->isAjaxRequest) {
			// 添加或修改成员信息
			$name = htmlspecialchars(trim(Frame::getStringFromRequest("name")));
			$remark_name = htmlspecialchars(trim(Frame::getStringFromRequest("remark_name")));
			$phone = Frame::getIntFromRequest("phone");
			$short_phone = Frame::getIntFromRequest("short_phone");
			$group_id = Frame::getIntFromRequest("group_id");
			$access_level = Frame::getIntFromRequest("access_level");
			$is_manage = Frame::getIntFromRequest("is_manage");
			$broadcast_per_month = Frame::getIntFromRequest("broadcast_per_month");
			$broadcast_available_month = Frame::getIntFromRequest("broadcast_available_month");
			if (!empty($_POST['manage_role'][0])) {
				array_unique($_POST['manage_role']);
				$manage_role = implode("|", $_POST['manage_role']);
			}
			if (empty($id)) {
				// 添加新成员
				if ($this->enterprise_type == 1) {
					//企业政企
					$member_id = Member::model()->find("phone='" . $phone . "'")->id;
					// 判断新成员是否已添加过
					if(!empty($member_id))
					$rs = EnterpriseMember::model()->find("(member_id='" . $member_id . "') and (contact_id='" . $this->enterprise_id . "')");
					if (!empty($rs)) {
						// 该成员已经添加过了
						echo json_encode(array(
							'msg' => 'failed',
							'status' => 'existed',
						));
						die();
					}
				} else {
					//虚拟网政企
					$member_id = Member::model()->find("cornet='" . $short_phone . "'")->id;
					// 判断新成员是否已添加过
					if(!empty($member_id))
					$rs = EnterpriseMember::model()->find("(member_id='" . $member_id . "') and (contact_id='" . $this->enterprise_id . "')");
					if (!empty($rs)) {
						// 该成员已经添加过了
						echo json_encode(array(
							'msg' => 'failed',
							'status' => 'existed',
						));
						die();
					}
				}

				$enterprise = Enterprise::model()->findByPk($this->enterprise_id); //通讯录信息
				$enterprise_role = EnterpriseRole::model()->find("enterprise_id='" . $this->enterprise_id . "'"); // 政企权限
				if ($enterprise->number >= $enterprise_role->member_limit) {
					//通讯录成员已达到上限
					echo json_encode(array('msg' => 'failed', 'status' => 'upperLimit'));die();
				}

				$enterpriseMember = new EnterpriseMember();
				$enterpriseMember->attributes = array(
					'member_id' => $member_id,
					'contact_id' => $this->enterprise_id,
					'name' => $name,
					'remark_name' => $remark_name,
					'phone' => $phone,
					'short_phone' => $short_phone,
					'created_time' => time(),
				);
				if ($enterpriseMember->save()) {
					$member_id = $enterpriseMember->attributes['id'];
					$enterpriseMemberManage = new EnterpriseMemberManage();
					$enterpriseMemberManage->attributes = array(
						'member_id' => $member_id,
						'group_id' => $group_id,
						'access_level' => $access_level,
						'is_manage' => $is_manage,
						'broadcast_per_month' => $broadcast_per_month,
						'broadcast_available_month' => $broadcast_available_month,
						'manage_role' => $manage_role,
						'created_time' => time(),
					);
					if ($enterpriseMemberManage->save()) {
						$enterprise->number += 1;
						$enterprise->save();
						echo json_encode(array(
							'msg' => 'success',
							'lastUrl' => $lastUrl,
						));
						die();
					} else {
						echo json_encode(array(
							'msg' => 'failed',
						));
						die();
					}
				} else {
					echo json_encode(array(
						'msg' => 'failed',
					));
					die();
				}
			} else {
				// 修改成员信息
				$enterpriseMember = EnterpriseMember::model()->findByPk($id);
				$enterpriseMember->attributes = array(
					'name' => $name,
					'remark_name' => $remark_name,
					'phone' => $phone,
					'short_phone' => $short_phone,
				);
				if ($enterpriseMember->save()) {
					$manage_id = EnterpriseMemberManage::model()->find("(member_id='" . $id . "')")->id;
					$enterpriseMemberManage = EnterpriseMemberManage::model()->findByPk($manage_id);
					$enterpriseMemberManage->attributes = array(
						'group_id' => $group_id,
						'access_level' => $access_level,
						'is_manage' => $is_manage,
						'broadcast_per_month' => $broadcast_per_month,
						'broadcast_available_month' => $broadcast_available_month,
						'manage_role' => $manage_role,
					);
					if ($enterpriseMemberManage->save()) {
						echo json_encode(array(
							'msg' => 'success',
							'lastUrl' => $lastUrl,
						));
						die();
					} else {
						echo json_encode(array(
							'msg' => 'failed',
						));
						die();
					}
				}
				die();
			}
			die();
		}
		$lastUrl = Yii::app()->request->urlReferrer;
		if (empty($id)) { // 成员id不存在,表示是添加成员
			// $this->redirect ( array (
			// "/enterpriseIndex/member"
			// ) );
			// die ();
		} else {
			$enterpriseMember = EnterpriseMember::model()->findByPk($id);
			if (empty($enterpriseMember)) {
				// 不存在该成员
				$this->redirect(array(
					"/enterpriseIndex/member",
				));
				die();
			}
			if ($this->enterprise_id != $enterpriseMember->contact_id) {
				// 该成员不属于登录的政企通讯录
				$this->redirect(array(
					"/enterpriseIndex/member",
				));
				die();
			}
			$model = EnterpriseMemberNew::model();
			$cri = new CDbCriteria();
			$cri->join = "left join enterprise_member_manage a on a.member_id=t.id left join member b on b.id=t.member_id";
			$cri->select = "t.*,a.access_level,a.broadcast_per_month,a.broadcast_available_month,b.benben_id,a.is_manage";
			$cri->condition = "(t.id='" . $id . "')";
			$enterpriseMember = $model->find($cri);
		}
		// 成员分组
		$group = $this->memberGroup();
		$group_last = array();
		$group_last = $this->memberGroup('last');
		foreach ($group_last as $k => $v) {
			$name = $this->parents($v->id);
			$name = explode(">", $name);
			krsort($name);
			$group_last[$k]->name = implode("-", $name);
		}
		// 成员管理角色
		$enterpriseMemberManage = EnterpriseMemberManage::model()->find(" (member_id='" . $id . "') ");
		$rs = $enterpriseMemberManage->manage_role;
		if (!empty($rs)) {
			$rs = explode("|", $rs);
			foreach ($rs as $k => $v) {
				$name = $this->parents($v);
				$name = explode(">", $name);
				krsort($name);
				$manage_role[$v] = implode(">", $name);
			}
		}
		$this->render("Personal-3", array(
			'enterpriseMember' => $enterpriseMember,
			'lastUrl' => $lastUrl,
			'group' => $group,
			'group_last' => $group_last,
			'enterpriseMemberManage' => $enterpriseMemberManage,
			'manage_role' => $manage_role,
		));
	}
	/**
	 * 通过子id获取所有父分组
	 */
	private function parents($id) {
		$memberGroup = MemberGroup::model();
		$rs = $memberGroup->findByPk($id);
		$name = $rs->name;
		$parent_id = $rs->parent_id;
		if ($parent_id > 0) {
			return $name . '>' . $this->parents($parent_id);
		} else {
			return $name;
		}
	}
	/**
	 * 数据统计
	 */
	public function actionDataAnalysis() {
		$this->render("Personal_5");
	}
	/**
	 * 管理员必读
	 */
	public function actionMustRead() {
		$content = Protocol::model()->findByPk(7)->content;
		$this->render("read", array('content' => $content));
	}
	/**
	 * 删除企业通讯录成员
	 */
	public function actionMemberDelete() {
		$id = Frame::getIntFromRequest("id");
		$member = EnterpriseMember::model()->findByPk($id);
		if (empty($member)) {
			// 成员不存在
			echo json_encode(array(
				'status' => 'failed',
				'msg' => '出错啦！',
			));
			die();
		} else {
			if ($member->contact_id != $this->enterprise_id) {
				// 成员不属于该企业通讯录
				echo json_encode(array(
					'status' => 'failed',
					'msg' => '出错啦！',
				));
				die();
			} else {
				if (EnterpriseMember::model()->deleteByPk($id)) {
					$enterprise = Enterprise::model()->findByPk($this->enterprise_id); //通讯录人数减一
					if ($enterprise->number >= 1) {
						$enterprise->number -= 1;
						$enterprise->save();
					}
					EnterpriseBroadcast::model()->deleteAll("(enterprise_id='".$this->enterprise_id."')and(member_id='".$member->member_id."')");
					$manage = EnterpriseMemberManage::model()->find("(member_id='" . $id . "')");
					if (!empty($manage)) {
						if (EnterpriseMemberManage::model()->deleteByPk($manage->id)) {
							echo json_encode(array(
								'status' => 'success',
								'msg' => '删除成功！',
							));
							die();
						} else {
							echo json_encode(array(
								'status' => 'failed',
								'msg' => '出错啦！',
							));
							die();
						}
					} else {
						echo json_encode(array(
							'status' => 'success',
							'msg' => '删除成功！',
						));
						die();
					}
				} else {
					echo json_encode(array(
						'status' => 'failed',
						'msg' => '出错啦！',
					));
					die();
				}
			}
		}
	}
	/**
	 * 获取成员分组
	 */
	private function memberGroup($level = 'first') {
		if ($level == 'first') {
			// 第一级别的分组
			$group = MemberGroup::model()->findAll("(enterprise_id='" . $this->enterprise_id . "') and (level='1')");
		}
		if ($level == 'last') {
			// 无子分组的分组
			$sql = "SELECT * FROM benben_test.member_group where enterprise_id = '" . $this->enterprise_id . "'
							and id != ALL (SELECT parent_id FROM benben_test.member_group where enterprise_id = '" . $this->enterprise_id . "'
							and parent_id = SOME (SELECT id FROM benben_test.member_group where enterprise_id = '" . $this->enterprise_id . "')
							);";
			$group = MemberGroup::model()->findAllBySql($sql);
		}
		return $group;
	}
	/**
	 * 通过1级分组id得到其所有直接子分组
	 */
	public function actionGroupSon() {
		$group_id = Frame::getIntFromRequest("group_id"); // $group_id=1;
		$data = Frame::getIntFromRequest("sort") + 1;
		if (empty($group_id)) {
			// 无效id
			echo json_encode(array(
				'msg' => 'success',
				'html' => $html,
			));
			die();
		} else {
			$rs = MemberGroup::model()->findAll("(enterprise_id='" . $this->enterprise_id . "') and (parent_id='" . $group_id . "')");
			if (empty($rs)) {
				// 没有子分组
				echo json_encode(array(
					'msg' => 'success',
					'html' => $html,
				));
				die();
			} else {
				// $r='<select class="chang-coe-1" ><option>人事部</option></select>';
				$html = '<select class="chang-coe-1 power" data="' . $data . '"><option value="0">---请选择---</option>';
				foreach ($rs as $k => $v) {
					$html .= '<option value="' . $v->id . '">' . $v->name . '</option>';
				}
				$html .= '</select>';
				echo json_encode(array(
					'msg' => 'success',
					'html' => $html,
				));
				die();
			}
		}
	}

	/**
	 * 导入成员
	 */
	public function actionImport() {

		if (Yii::app()->request->isPostRequest) {
			//关闭yii自动加载，避免和phpexcel冲突
			Yii::$enableIncludePath = false;
			define('__ROOT__', dirname(dirname(dirname(__FILE__))) . '/');
			require_once __ROOT__ . 'lib/phpexcel/Classes/PHPExcel.php';
			include_once __ROOT__ . "lib/uploadEnterpriseMember.class.php";
			$tname = $_FILES["file"]["tmp_name"];
			$fname = $_FILES["file"]["name"];
			$file_type = explode(".", $fname);
			$file_type = $file_type[count($file_type) - 1];
			$extensions = array('xlsx', 'xlsm', 'xltx', 'xltm', 'xlsb', 'xlam', 'xls'); //允许上传的文件
			if (!in_array($file_type, $extensions)) {
				$msg = "layer.msg('请上传excel文件',{btn:['知道了'],time:10000,icon:2,})";
				return $msg;
			} else {
				$new_path = __ROOT__ . 'uploads/excel/' . date('Y-m', time());
				if (!is_dir($new_path)) {
					mkdir($new_path, 0777, true);
					chmod($new_path, 0777);
				}
				$destination = $new_path . '/' . uniqid() . '.' . $file_type;
				move_uploaded_file($tname, $destination);

				$uploadmember = new uploadEnterpriseMember($this->enterprise_id, $this->enterprise_type);
				$all_rows = $uploadmember->countAllRows($destination, $file_location);
				$enterprise = Enterprise::model()->findByPk($this->enterprise_id); //通讯录信息
				$enterprise_role = EnterpriseRole::model()->find("enterprise_id='" . $this->enterprise_id . "'"); // 政企权限
				if ($enterprise->number + $all_rows > $enterprise_role->member_limit) {
					//通讯录成员已达到上限
					$msg = "layer.msg('excel文件的成员总数超过了政企人数的上限！',{btn:['知道了'],time:10000,icon:2,})";
					return $msg;
				}
				$count = $uploadmember->phpexcel($destination, $file_location);
				if ($count) {
					$enterprise = Enterprise::model()->findByPk($this->enterprise_id);
					$enterprise->number += $count;
					$enterprise->save();
					$this->redirect(array('/enterpriseIndex/member'));die();
				}
			}
		}
	}
	/**
	 * Excel导出
	 */
	public function actionExport() {
		//获得符合条件的节目资源
		$condition = base64_decode($_GET['condition']);
		// 政企成员
		$model = EnterpriseMemberNew::model();
		$cri = new CDbCriteria();
		$cri->join = "left join enterprise_member_manage a on a.member_id=t.id left join member b on b.id=t.member_id";
		$cri->select = "t.*,b.benben_id,a.access_level,a.broadcast_per_month,a.broadcast_available_month";
// 		$cri->condition = "(t.contact_id='" . $this->enterprise_id . "')";
		$cri->condition = "(1=1)and" . $condition;
		$data = $model->findAll($cri);
		//将节目中的字段内容改变成用户能看懂的
		$arr = array();
		if (!empty($data)) {
			foreach ($data as $k => $v) {
				$tmp = $v->attributes;
				$arr[] = $tmp;
				$arr[$k]['order'] = $k + 1; //人为添加排序号，无实际意义
				$arr[$k]['benben_id'] = $v->benben_id;
				$arr[$k]['access_level'] = $v->access_level;
				$arr[$k]['broadcast_per_month'] = $v->broadcast_per_month;
				$arr[$k]['broadcast_available_month'] = $v->broadcast_available_month;
			}
		}
// 		p($arr);die;
		//关闭yii自动加载，避免和phpexcel冲突
		Yii::$enableIncludePath = false;
		define('__ROOT__', dirname(dirname(dirname(__FILE__))) . '/');
		require_once __ROOT__ . 'lib/phpexcel/Classes/PHPExcel.php';
		//Excel数据导出
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Kalman")
			->setTitle("资源报表")
			->setSubject("资源报表")
			->setDescription("资源报表");
		$objPHPExcel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
		$objPHPExcel->getActiveSheet()->getStyle('A')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('B')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getColumnDimension('C')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getStyle('C')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('D')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getStyle('E')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getColumnDimension('F')->setWidth(15);
		$objPHPExcel->getActiveSheet()->getStyle('F')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getColumnDimension('G')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getStyle('G')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getColumnDimension('H')->setWidth(20);
		$objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setVertical(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
		$objPHPExcel->getActiveSheet()->getStyle('H')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);

		$objPHPExcel->setActiveSheetIndex(0)
			->setCellValue('A1', '序号')->setCellValue('B1', '姓名')->setCellValue('C1', '备注')->setCellValue('D1', '手机号码')->setCellValue('E1', '其他号码')
			->setCellValue('F1', '查阅等级')->setCellValue('G1', '每月可发送大喇叭')->setCellValue('H1', '本月剩余大喇叭');
// 		->mergeCells('B1:B2')->setCellValue('B1', '节目名')
		$offset = 2;
		if (!empty($arr)) {
			foreach ($arr as $i => $v) {
				$objPHPExcel->getActiveSheet(0)->setCellValue('A' . ($i + $offset), $arr[$i]['order']);
				$objPHPExcel->getActiveSheet(0)->setCellValue('B' . ($i + $offset), $arr[$i]['name']);
				$objPHPExcel->getActiveSheet(0)->setCellValue('C' . ($i + $offset), $arr[$i]['remark_name']);
				if ($this->enterprise_type == 1) {
					$objPHPExcel->getActiveSheet(0)->setCellValue('D' . ($i + $offset), $arr[$i]['phone']);
				}

				$objPHPExcel->getActiveSheet(0)->setCellValue('E' . ($i + $offset), $arr[$i]['short_phone']);
				$objPHPExcel->getActiveSheet(0)->setCellValue('F' . ($i + $offset), $arr[$i]['access_level']);
				$objPHPExcel->getActiveSheet(0)->setCellValue('G' . ($i + $offset), $arr[$i]['broadcast_per_month']);
				$objPHPExcel->getActiveSheet(0)->setCellValue('H' . ($i + $offset), $arr[$i]['broadcast_available_month']);
			}
		}
		$objPHPExcel->getActiveSheet()->setTitle('资源导出表');

		// Excel打开后显示的工作表
		$objPHPExcel->setActiveSheetIndex(0);

		//通浏览器输出Excel报表
		header('Content-Type: application/vnd.ms-excel');
		header('Content-Disposition: attachment;filename="资源导出表' . date("Y/m/d") . '.xlsx"');
		header('Cache-Control: max-age=0');

		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		$objWriter->save('php://output');
		Yii::app()->end();
	}

	/**
	 * 政企公告
	 */

	public function actionUpdateNotice() {
// 		$model = EnterpriseNotice::model();
		// 		//当前政企
		// 		$enterId = $this->enterprise_id;
		// 		$item = $model->find("enterprise_id = " . $enterId . " order by id desc, sort desc");
		echo 2;
	}

	/**
	 * 获取所有分组信息
	 */
	public function allGroups() {
		$member_group = MemberGroupNew::model();
		$cri1 = new CDbCriteria();
		$cri1->order = "t.sort desc,t.id asc";
		$cri1->condition = "t.enterprise_id='" . $this->enterprise_id . "'";
		// 		$cri1->with = 'children';
		$levels = $member_group->findAll($cri1);

		$level1 = array();
		$level2 = array();
		$level3 = array();
		$level4 = array();
		if (!empty($levels)) {
			foreach ($levels as $k => $v) {
				if ($v->parent_id == 0) {
					$level1[] = $v;unset($levels[$k]);
				}
			}
		}
		if (!empty($levels)) {
			if (!empty($level1)) {
				foreach ($level1 as $k => $v) {
					foreach ($levels as $kk => $vv) {
						if ($v->id == $vv->parent_id) {
							$level2[$v->id][] = $vv;unset($levels[$kk]);
						}
					}
				}
			}
		}
		if (!empty($levels)) {
			if (!empty($level2)) {
				foreach ($level2 as $k1 => $v1) {
					if (!empty($v1)) {
						foreach ($v1 as $k2 => $v2) {
							foreach ($levels as $kk => $vv) {
								if ($v2->id == $vv->parent_id) {
									$level3[$k1][$v2->id][] = $vv;unset($levels[$kk]);
								}
							}
						}
					}
				}
			}
		}
		if (!empty($levels)) {
			if (!empty($level3)) {
				foreach ($level3 as $k1 => $v1) {
					if (!empty($v1)) {
						foreach ($v1 as $k2 => $v2) {
							if (!empty($v2)) {
								foreach ($v2 as $k3 => $v3) {
									foreach ($levels as $kk => $vv) {
										if ($v3->id == $vv->parent_id) {
											$level4[$k1][$k2][$v3->id][] = $vv;unset($levels[$kk]);
										}
									}
								}
							}
						}
					}
				}
			}
		}
	}

}
