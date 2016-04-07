<?php

class OrderController extends PublicController
{
    public $layout = false;

    /*
     * 新增订单
     * 涉及store_order_info,store_order_goods
     */
    public function actionAddorder()
    {
        $this->check_key();
        $user = $this->check_user();
        $promotion_id = Frame::getIntFromRequest('promotion_id');
        $consignee = Frame::getStringFromRequest('consignee');
        $province = Frame::getIntFromRequest('province');
        $city = Frame::getIntFromRequest('city');
        $area = Frame::getIntFromRequest('district');
        $street = Frame::getIntFromRequest('street');
        $address = Frame::getStringFromRequest('address');
//        $zipcode = Frame::getStringFromRequest('zipcode');
        $mobile = Frame::getStringFromRequest('mobile');
        $pay_id = Frame::getIntFromRequest('pay_id');
        $pay_name = Frame::getStringFromRequest('pay_name');
        $goods_amount = Frame::getStringFromRequest('goods_amount');//商品总金额
        $shipping_fee = Frame::getStringFromRequest('shipping_fee');//邮费
        $goods_number = Frame::getIntFromRequest('goods_number');
        $extension_code = Frame::getIntFromRequest('extension_code');//活动类型，0促销，1团购，2.我要买,4.会员充值
        if (empty($promotion_id) || empty($pay_id)) {
            $result ['ret_num'] = 2015;
            $result ['ret_msg'] = '参数不全';
            echo json_encode($result);
            die();
        }
        $order_amount = floatval($goods_amount) + floatval($shipping_fee);//订单应付金额
        //保存订单信息
        $og = new StoreOrderInfo();
        //生成订单号
        $num = StoreOrderInfo::model()->count("order_id!=0");
        $og->order_sn = (intval(date("Y")) - 2015) . ($num + 1) . date("i", time()) . substr($user['id'], -4) . substr($promotion_id, 0, 1);

        $og->member_id = $user['id'];
        $og->order_status = 0;
        $og->shipping_status = 0;
        $og->pay_status = 0;
        $og->consignee = $consignee;
        $og->province = $province;
        $og->city = $city;
        $og->area = $area;
        $og->street = $street;
        $og->address = $address;
        $og->mobile = $mobile;
        $og->pay_id = $pay_id;
        $og->pay_name = $pay_name;
        $og->extension_code = $extension_code;
        $og->goods_amount = $goods_amount;
        $og->shipping_fee = $shipping_fee;
        $og->order_amount = $order_amount;
        $og->add_time = time();
        $og->confirm_time = time();
        if ($pay_id == 2 || $pay_id == 3) {
            include('lib/phpqrcode/phpqrcode.php');
            $saveurl = "uploads/images/orderqc/" . time() . base64_encode($user['id']) . ".png";
            QRcode::png($og->order_sn, $saveurl);//访问地址(订单号)，图片保存地址
            $og->qrcode = "/" . $saveurl;
        }
        if ($og->save()) {
            //保存商品信息
            $gg = new StoreOrderGoods();
            $gg->order_id = $og->order_id;
            $gg->promotion_id = $promotion_id;
            if($extension_code==2){
                $qinfo=Quote::model()->find("id={$promotion_id}");
                $buyInfo=Buy::model()->find("id={$qinfo['item_id']}");
                $gg->goods_name = $buyInfo['title'];
                $gg->origion_price = $qinfo['price'];
                $gg->promotion_price = $qinfo['price'];
                $gg->store_id = $qinfo['store_id'];
            }else {
                $pinfo = Promotion::model()->find("id={$promotion_id}");
                $pminfo=PromotionManage::model()->find("id={$pinfo['pm_id']}");
                $gg->goods_name = $pinfo['name'];
                $gg->goods_sn = $pinfo['goods_sn'];
                $gg->origion_price = $pinfo['origion_price'];
                $gg->promotion_price = $pinfo['promotion_price'];
                $gg->store_id = $pminfo['store_id'];
            }
            $gg->goods_number = $goods_number;
            $gg->save();
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            $result ['order_id'] = $og->order_id;
            $result ['order_sn'] = $og->order_sn;
            echo json_encode($result);
        } else {
            $result ['ret_num'] = 1020;
            $result ['ret_msg'] = '您有重复订单！';
            echo json_encode($result);
        }
    }

    /*
     * 订单详情（个人）
     * 商家需要根据订单号进行验证，验证订单是否属于该商家的
     */
    public function actionOrderdetail()
    {
        $this->check_key();
        $user = $this->check_user();
        $order_id = Frame::getIntFromRequest('order_id');
        $order_sn = Frame::getStringFromRequest('order_sn');
        $extension_code = Frame::getStringFromRequest('extension_code');//接收商品类型
        if (empty($order_id)) {
            $result ['ret_num'] = 2015;
            $result ['ret_msg'] = '参数不全';
            echo json_encode($result);
            die();
        }

        //如果订单号存在，订单id不存在，则查询订单id
        if (!$order_id && $order_sn) {
            $stinfo = StoreOrderInfo::model()->find("order_sn='{$order_sn}'");
            $order_id = $stinfo['order_id'];
        }

        $connection = Yii::app()->db;
        $sql = "select a.order_id,a.order_sn,a.order_status,a.shipping_status,a.shipping_sn,a.pay_status,a.consignee,a.extension_code,
        a.qrcode,a.province,a.city,a.area,a.street,a.address,a.mobile,a.pay_id,a.pay_name,a.goods_amount,a.shipping_fee,
        a.order_amount,a.add_time,a.confirm_time,a.pay_time, b.promotion_id,b.goods_name,b.goods_number,b.promotion_price
         from store_order_info as a left join store_order_goods as b on a.order_id=b.order_id
        where a.order_id=" . $order_id . " and a.member_id=" . $user['id'];
        $command = $connection->createCommand($sql);
        $info = $command->queryAll();
        foreach ($info as $k => $v) {
            //商品信息
            $info[$k]['qrcode'] = $v['qrcode'] ? URL . $v['qrcode'] : "";

            if ($extension_code != 2) {
                $pinfo = Promotion::model()->find("id={$v['promotion_id']}");
                $info[$k]['is_close'] = $pinfo['is_close'] ? $pinfo['is_close'] : 0;
                $info[$k]['is_out'] = $pinfo['valid_right'] > time() ? 0 : 1;
                $info[$k]['promotion_pic'] = $pinfo['poster_st'] ? URL . $pinfo['poster_st'] : "";

                $pminfo = PromotionManage::model()->find("id={$pinfo['pm_id']}");
                $info[$k]['train_id'] = $pminfo['store_id'];

                //商家地址/信息
                $ninfo = NumberTrain::model()->find("id={$pminfo['store_id']}");
                $info[$k]['lat'] = $ninfo['lat'];
                $info[$k]['lng'] = $ninfo['lng'];
                $info[$k]['store_pic'] = $ninfo['poster'] ? URL . $this->getThumb($ninfo['poster']) : "";
                $info[$k]['short_name'] = $ninfo['short_name'] ? $ninfo['short_name'] : "";


                //商家环信名用于聊天
                $minfo = Member::model()->find("id={$pminfo['member_id']}");
                $info[$k]['huanxin_username'] = $minfo['huanxin_username'];
            } else {
                $qinfo = Quote::model()->find("id={$v['promotion_id']}");
                $info[$k]['train_id'] = $qinfo['store_id'];
                $qa = QuoteAttachment::model()->find("quote_id={$qinfo['id']}");
                $info[$k]['promotion_pic'] = $qa['poster'] ? URL . $qa['poster'] : "";

                //商家地址/信息
                $ninfo = NumberTrain::model()->find("id={$qinfo['store_id']}");
                $info[$k]['lat'] = $ninfo['lat'];
                $info[$k]['lng'] = $ninfo['lng'];
                $info[$k]['store_pic'] = $ninfo['poster'] ? URL . $this->getThumb($ninfo['poster']) : "";
                $info[$k]['short_name'] = $ninfo['short_name'] ? $ninfo['short_name'] : "";

                //商家环信名用于聊天
                $minfo = Member::model()->find("id={$qinfo['member_id']}");
                $info[$k]['huanxin_username'] = $minfo['huanxin_username'];
            }

            //退款
            $backinfo = BackOrder::model()->find("apply_id={$user['id']} and order_id= {$v['order_id']}");
            $info[$k]['back_deal_time'] = $backinfo['deal_time'] ? $backinfo['deal_time'] : 0;
            $info[$k]['back_apply_time'] = $backinfo['apply_time'] ? $backinfo['apply_time'] : 0;
            $info[$k]['back_status'] = $backinfo['status'] ? $backinfo['status'] : 0;
        }

        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result ['info'] = $info ? $info : array();
        echo json_encode($result);
    }

    /*
     * 取消订单
     * 只有order_info中的shipping_status=0，pay_status=0时，即未发货,未付款的时候才能进行取消订单处理
     * 否则需要退款处理
     */
    public function actionDelorder()
    {
        $this->check_key();
        $user = $this->check_user();
        $order_id = Frame::getIntFromRequest('order_id');
        if (empty($order_id)) {
            $result ['ret_num'] = 2015;
            $result ['ret_msg'] = '参数不全';
            echo json_encode($result);
            die();
        }
        $soinfo = StoreOrderInfo::model()->count("order_id={$order_id} and pay_status=2");//已付款不能取消
        if ($soinfo) {
            $result ['ret_num'] = 1234;
            $result ['ret_msg'] = '已付款，需要退款处理！';
            echo json_encode($result);
            die();
        }
        $soinfo2 = StoreOrderInfo::model()->count("order_id={$order_id} and shipping_status!=0");//已发货不能取消
        if ($soinfo2) {
            $result ['ret_num'] = 1334;
            $result ['ret_msg'] = '已发货，需要退款处理！';
            echo json_encode($result);
            die();
        }
        $soinfo3 = StoreOrderInfo::model()->count("order_id={$order_id} and (order_status=2 or order_status=4 or order_status=5 or order_status=6)");//已处理不能取消
        if ($soinfo3) {
            $result ['ret_num'] = 1344;
            $result ['ret_msg'] = '订单已处理，勿重复操作！';
            echo json_encode($result);
            die();
        }
        StoreOrderInfo::model()->updateAll(array("order_status" => 2), "order_id={$order_id} and member_id={$user['id']}");
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        echo json_encode($result);
    }

    /*
     * 获取订单列表
     * 会有退款处理情况
     * 涉及store_order_info表
     * shipping_status，0已下单，1待收货，2待评价(order_status=5)，4已退款(order_status=4)
     */
    public function actionOrderlist()
    {
        $this->check_key();
        $user = $this->check_user();
        $p = Frame::getIntFromRequest('page');
        $status = Frame::getStringFromRequest('shipping_status');//store_order_info中的shipping_status，如果不传则代表全部
        if (!$p) {
            $p = 1;
        }
        $connection = Yii::app()->db;
        $maxnum = 10;//最大单页显示数据条数
        if ($status != '0' && $status != '1' && $status != '2' && $status != '4') {
            //status未传,全部数据
            $where = " where a.member_id=" . $user['id'];
        } else if ($status == 2) {
            //待评价
            $where = " where a.member_id=" . $user['id'] . " and a.shipping_status=2 and a.order_status=5";
        } else if ($status == 4) {
            //申请退款
            $where = " where a.member_id=" . $user['id'] . " and a.order_status=4";
        } else {
            //具体状态数据,非取消的订单和退货的
            $where = " where a.member_id=" . $user['id'] . " and a.shipping_status=" . $status . " and a.order_status!=2 and a.order_status!=4";
        }
        //除去电脑版的订单和手机版商城订单
        $where .= " and a.extension_code!=3 and a.extension_code!=4";
        $sql_num = "select count(1) as num from store_order_info as a " . $where;
        $command = $connection->createCommand($sql_num);
        $num_tpl = $command->queryAll();
        $num = $num_tpl[0]['num'];//总数据条数
        $allpage = ceil($num / $maxnum);//总页数

        //全部订单（除去电脑版的订单和手机版商城订单）
        $sql_num00 = "select count(1) as num from store_order_info as a where a.member_id={$user['id']} and a.extension_code!=3 and a.extension_code!=4";
        $command = $connection->createCommand($sql_num00);
        $num00_tpl = $command->queryAll();

        //获取已下单未发货的数量
        $sql_num0 = "select count(1) as num from store_order_info as a where a.shipping_status=0 and a.member_id={$user['id']} and a.order_status!=2 and a.order_status!=4 and a.extension_code!=3 and a.extension_code!=4";
        $command = $connection->createCommand($sql_num0);
        $num0_tpl = $command->queryAll();

        //获取已发货的=待收货的数量
        $sql_num1 = "select count(1) as num from store_order_info as a where a.shipping_status=1 and a.member_id={$user['id']} and a.order_status!=2 and a.order_status!=4 and a.extension_code!=3 and a.extension_code!=4";
        $command = $connection->createCommand($sql_num1);
        $num1_tpl = $command->queryAll();

        //获取已收货待评价的数量
        $sql_num2 = "select count(1) as num from store_order_info as a where a.shipping_status=2 and a.order_status=5 and a.member_id={$user['id']} and a.order_status!=2 and a.order_status!=4 and a.extension_code!=3 and a.extension_code!=4";
        $command = $connection->createCommand($sql_num2);
        $num2_tpl = $command->queryAll();

        //获取已退货的数量
        $sql_num3 = "select count(1) as num from store_order_info as a where a.order_status=4 and a.member_id={$user['id']} and a.order_status!=2 and a.extension_code!=3 and a.extension_code!=4";
        $command = $connection->createCommand($sql_num3);
        $num3_tpl = $command->queryAll();

        //取出每页数据
        if ($p <= $allpage) {
            $sql = "select a.order_id,a.order_sn,a.shipping_status,a.shipping_sn,a.pay_id,a.pay_name,a.goods_amount,a.shipping_fee,a.order_amount,a.pay_status,
             a.extension_code,a.order_status,b.store_id as train_id, b.promotion_id,b.goods_name,b.goods_number,b.promotion_price,b.extension_code as pc_code
            from store_order_info as a left join store_order_goods as b on a.order_id = b.order_id" . $where . " order by a.order_id Desc limit " . ($p - 1) * $maxnum . " ," . $maxnum;
            $command = $connection->createCommand($sql);
            $result0 = $command->queryAll();
            foreach ($result0 as $k => $v) {
                $promotionid_arr[] = $v['promotion_id'];
                $order_arr[] = $v['order_id'];
            }
            //获取订单的商品图片信息
            $poster_tpl = Promotion::model()->findAll("id in (" . implode(",", $promotionid_arr) . ")");
            foreach ($poster_tpl as $k => $v) {
                $pic_tpl[$v['id']] = $v['poster_st'];
                $is_close[$v['id']] = $v['is_close'] ? $v['is_close'] : 0;
                $is_out[$v['id']] = $v['valid_right'] > time() ? 0 : 1;
            }

            //获取退款状态
            $backinfo = BackOrder::model()->findAll("apply_id={$user['id']} and order_id in (" . implode(",", $order_arr) . ")");
            foreach ($backinfo as $kb => $vb) {
                $back_tpl[$vb['order_id']] = $vb;
            }
            $shopinfo = $this->getShopinfo(implode(",", $promotionid_arr));
            foreach ($result0 as $kk => $vv) {
                $result0[$kk]['store_pic'] = $shopinfo[$vv['promotion_id']]['poster'] ? URL . $this->getThumb($shopinfo[$vv['promotion_id']]['poster']) : "";

                //我要买+商城图额外展现
                if ($vv['extension_code'] == 3 || $vv['extension_code'] == 4 || $vv['extension_code'] == 5) {
                    //商城图额外展现
                    $result0[$kk]['short_name'] = "奔犇商城";
                    $result0[$kk]['store_pic'] = "";
                } elseif ($vv['extension_code'] == 2) {
                    //我要买额外展现
                    $quoteInfo = Quote::model()->find("id={$vv['promotion_id']}");
                    if ($quoteInfo) {
                        $trainInfo = NumberTrain::model()->find("id={$quoteInfo['store_id']}");
                    }
                    $result0[$kk]['short_name'] = $trainInfo ? $trainInfo['short_name'] : "";
                    $result0[$kk]['store_pic'] = $trainInfo ? URL . $trainInfo['poster'] : "";
                } else {
                    $result0[$kk]['short_name'] = $shopinfo[$vv['promotion_id']]['short_name'];
                    $result0[$kk]['store_pic'] = $shopinfo[$vv['promotion_id']]['poster'] ? URL . $this->getThumb($shopinfo[$vv['promotion_id']]['poster']) : "";
                    $result0[$kk]['train_id'] = $shopinfo[$vv['promotion_id']]['id'];
                }

                //非系统商店
                if ($vv['extension_code'] == 0 || $vv['extension_code'] == 1) {
                    $result0[$kk]['promotion_pic'] = $pic_tpl[$vv['promotion_id']] ? URL . $this->getThumb($pic_tpl[$vv['promotion_id']]) : "";
                }
                //系统商店
                if (!$result0[$kk]['promotion_pic']) {
                    if ($vv['extension_code'] == 4) {
                        $result0[$kk]['promotion_pic'] = URL . "/uploads/images/benbenStore/recharge.png";
                    } elseif ($vv['extension_code'] == 5) {
                        $result0[$kk]['promotion_pic'] = URL . "/uploads/images/benbenStore/auction.png";
                    } elseif ($vv['extension_code'] == 2) {
                        $att = QuoteAttachment::model()->find("quote_id={$vv['promotion_id']}");
                        $result0[$kk]['promotion_pic'] = $att ? URL . $att['poster'] : "";
                    } elseif ($vv['extension_code'] == 3) {
                        //0：促销，1：团购，4.充值，11：会员号，10：我要开分店，14：好友联盟，13：大喇叭，12：小喇叭，15：政企
                        switch ($vv['pc_code']) {
                            case "0":
                                $result0[$kk]['promotion_pic'] = URL . "/uploads/images/benbenStore/promotion.png";
                                break;
                            case 1:
                                $result0[$kk]['promotion_pic'] = URL . "/uploads/images/benbenStore/groupbuy.png";
                                break;
                            case 4:
                                $result0[$kk]['promotion_pic'] = URL . "/uploads/images/benbenStore/recharge.png";
                                break;
                            case 11:
                                $result0[$kk]['promotion_pic'] = URL . "/uploads/images/benbenStore/vip.png";
                                break;
                            case 10:
                                $result0[$kk]['promotion_pic'] = URL . "/uploads/images/benbenStore/dispatch.png";
                                break;
                            case 14:
                                $result0[$kk]['promotion_pic'] = URL . "/uploads/images/benbenStore/league.png";
                                break;
                            case 13:
                                $result0[$kk]['promotion_pic'] = URL . "/uploads/images/benbenStore/bigshut.png";
                                break;
                            case 12:
                                $result0[$kk]['promotion_pic'] = URL . "/uploads/images/benbenStore/smallshut.png";
                                break;
                            case 15:
                                $result0[$kk]['promotion_pic'] = URL . "/uploads/images/benbenStore/peopleup.png";
                                break;
                        }
                    }
                }
                $result0[$kk]['back_status'] = $back_tpl[$vv['order_id']]['status'] ? $back_tpl[$vv['order_id']]['status'] : 0;
                $result0[$kk]['back_apply_time'] = $back_tpl[$vv['order_id']]['apply_time'] ? $back_tpl[$vv['order_id']]['apply_time'] : 0;
                $result0[$kk]['back_deal_time'] = $back_tpl[$vv['order_id']]['deal_time'] ? $back_tpl[$vv['order_id']]['deal_time'] : 0;
                $result0[$kk]['is_close'] = $is_close[$vv['promotion_id']] ? $is_close[$vv['promotion_id']] : 0;
                $result0[$kk]['is_out'] = $is_out[$vv['promotion_id']] ? $is_out[$vv['promotion_id']] : 0;
            }
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            $result['orderinfo'] = $result0;
            $result['page'] = $allpage;
            $result['all'] = $num00_tpl[0]['num'];
            $result['confirm'] = $num0_tpl[0]['num'];
            $result['wanna_get'] = $num1_tpl[0]['num'];
            $result['ready_comment'] = $num2_tpl[0]['num'];
            $result['back'] = $num3_tpl[0]['num'];
            echo json_encode($result);
        } else {
            $result ['ret_num'] = 8844;
            $result ['ret_msg'] = '已无更多内容！';
            echo json_encode($result);
            die();
        }
    }

    /*
     * 确认收货
     * 涉及store_order_info
     */
    public function actionSureget()
    {
        $this->check_key();
        $user = $this->check_user();
        $order_id = Frame::getIntFromRequest('order_id');
        if (empty($order_id)) {
            $result ['ret_num'] = 2015;
            $result ['ret_msg'] = '缺少参数！';
            echo json_encode($result);
            die();
        }
        $sinfo = StoreOrderInfo::model()->find("order_id={$order_id} and member_id={$user['id']}");
        if (!$sinfo) {
            $result ['ret_num'] = 8019;
            $result ['ret_msg'] = '该订单不存在！';
            echo json_encode($result);
            die();
        }
        if ($sinfo->order_status == 5 && $sinfo->shipping_status == 2) {
            $result ['ret_num'] = 1001;
            $result ['ret_msg'] = '请勿重复操作';
            echo json_encode($result);
            die();
        }
        $sinfo->order_status = 5;//切为待评价
        $sinfo->shipping_status = 2;//切为已收货
        if ($sinfo->update()) {
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            echo json_encode($result);
        } else {
            $result ['ret_num'] = 1001;
            $result ['ret_msg'] = '请勿重复操作';
            echo json_encode($result);
        }
    }

    /*
     * 延长收货时间，一般为货到7天，延长时间为3天
     */
    public function actionExtendtime()
    {
        $this->check_key();
        $user = $this->check_user();
        $order_id = Frame::getIntFromRequest('order_id');
        if (empty($order_id)) {
            $result ['ret_num'] = 2015;
            $result ['ret_msg'] = '缺少参数！';
            echo json_encode($result);
            die();
        }
        $sinfo = StoreOrderInfo::model()->find("order_id={$order_id} and member_id={$user['id']}");
        if (!$sinfo) {
            $result ['ret_num'] = 8019;
            $result ['ret_msg'] = '该订单不存在！';
            echo json_encode($result);
            die();
        }
        $sinfo->extend_shipping_time = $sinfo->extend_shipping_time + 3;
        if ($sinfo->update()) {
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            echo json_encode($result);
        } else {
            $result ['ret_num'] = 1001;
            $result ['ret_msg'] = '请勿重复操作';
            echo json_encode($result);
        }
    }

    /*
     * 申请退款
     * 涉及back_order
     */
    public function actionBackorder()
    {
        $this->check_key();
        $user = $this->check_user();
        $order_id = Frame::getIntFromRequest("order_id");
        $train_id = Frame::getIntFromRequest("train_id");
        if (empty($order_id) || empty($train_id)) {
            $result ['ret_num'] = 2015;
            $result ['ret_msg'] = '参数不全！';
            echo json_encode($result);
            die();
        }
        $bcount = BackOrder::model()->count("order_id={$order_id} and train_id={$train_id}");
        if ($bcount) {
            $result ['ret_num'] = 211;
            $result ['ret_msg'] = '已提交退款！请勿重复提交';
            echo json_encode($result);
            die();
        }

        //到店交易，费用不在平台上发生的
        $oinfo = StoreOrderInfo::model()->find("order_id={$order_id}");
        if ($oinfo['pay_id'] == 3) {
            $result ['ret_num'] = 311;
            $result ['ret_msg'] = '该订单为到店交易,需要与商家直接沟通退款！';
            echo json_encode($result);
            die();
        }

        //支付时间超过1个月(30天)，不予以退款
        if ($oinfo['pay_time'] <= (time() - 30 * 86400)) {
            $result ['ret_num'] = 411;
            $result ['ret_msg'] = '该订单已超过退款时限（30天）！';
            echo json_encode($result);
            die();
        }

        //账户充值的订单不予退款
        if ($oinfo['extension_code'] == 4) {
            $result ['ret_num'] = 511;
            $result ['ret_msg'] = '现金充值不予退款申请，可联系客服处理！';
            echo json_encode($result);
            die();
        }
        $back = new BackOrder();
        $back->order_id = $order_id;
        $back->status = 1;//1.退款申请中，2.已退款，3.拒绝退款，4.退款中,5.商家同意退款
        $back->apply_id = $user['id'];
        $back->train_id = $train_id;
        $back->apply_time = time();
        if ($back->save()) {
            StoreOrderInfo::model()->updateAll(array("order_status" => 4), "order_id={$order_id} and member_id={$user['id']}");
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            $result ['back_id'] = $back->back_id;
            echo json_encode($result);
        } else {
            $result ['ret_num'] = 3020;
            $result ['ret_msg'] = '保存失败，请重新尝试！';
            echo json_encode($result);
        }
    }

    /*
     * 个人商品评论
     * 涉及store_comment,number_train
     * 需要区分该评论是待评论还是已评论的追加回复
     */
    public function actionOrdercomment()
    {
        $this->check_key();
        $user = $this->check_user();
        $promotion_id = Frame::getIntFromRequest('promotion_id');
        $order_id = Frame::getIntFromRequest('order_id');
        $comment_type = Frame::getIntFromRequest('comment_type');//0评论的是促销,1评论的是团购
        $content = Frame::getStringFromRequest('content');
        $comment_rank = Frame::getIntFromRequest('comment_rank');//3好评，2中评，1差评
        $connection = Yii::app()->db;
        if (empty($order_id) || empty($comment_type) || empty($content) || empty($comment_rank)) {
            $result ['ret_num'] = 2015;
            $result ['ret_msg'] = '参数不全！';
            echo json_encode($result);
            die();
        }
        //判断订单是否处于待评论或已评论状态
        $stinfo = StoreOrderInfo::model()->find("order_id={$order_id} and member_id={$user['id']}");
        if ($stinfo['order_status'] == 5) {
            //待评论
            $parent_id = 0;
        } else if ($stinfo['order_status'] == 6) {
            //已评论，需追加
            $fatherinfo = StoreComment::model()->find("order_id={$order_id}");
            $parent_id = $fatherinfo['comment_id'];
        } else {
            $result ['ret_num'] = 1120;
            $result ['ret_msg'] = '您还未收货，不能评论！';
            echo json_encode($result);
            die();
        }

        $comment = new StoreComment();
        $comment->comment_type = $comment_type;
        $comment->promotion_id = $promotion_id;
        //该评论人的环信账号用于聊天
        $comment->huanxin_username = $user['huanxin_username'];

        $comment->user_name = $user['nick_name'];
        $comment->content = $content;
        $comment->comment_rank = $comment_rank;
        $comment->add_time = time();
        $comment->parent_id = $parent_id;
        $comment->member_id = $user['id'];
        $comment->order_id = $order_id;
        $comment->is_seller = 0;
        if ($comment->save()) {
            if ($stinfo['order_status'] == 5) {
                //待评论转已评论
                $stinfo->order_status = 6;
                $stinfo->update();

                $nowmonth = strtotime(date("Y-m", time()) . "-1 0:0:0");
                $month = date("m", time());
                if ($month == 12) {
                    $month = 1;
                } else {
                    $month++;
                }
                $nextmonth = strtotime(date("Y", time()) . "-" . $month . "-1 0:0:0");
                //好评+1，中评0，差评-1分，number_train
                //判断同产品/每月/相同买家/卖家之间分数-5<=x<=10
                $sql = "SELECT sum(comment_rank)-2*count(1) as new_red from store_comment
                WHERE comment_rank>0 and add_time>=" . $nowmonth . " and add_time<" . $nextmonth . " and member_id=" . $user['id'] . " and promotion_id=" . $promotion_id;
                $command = $connection->createCommand($sql);
                $result0 = $command->queryAll();
                if ($result0['new_red'] <= 10 && $result0['new_red'] >= -5) {
                    $shopinfo = $this->getShopinfo($promotion_id);
                    $train = NumberTrain::model()->find("id={$shopinfo[$promotion_id]['id']}");
                    $train->score = $train['score'] + $comment_rank - 2;
                    $train->update();
                }
                //相同买家/卖家/同产品/1月内，交易成功增加已售数量
                $num = StoreComment::model()->count("add_time>=" . $nowmonth . " and add_time<" . $nextmonth . " and member_id=" . $user['id'] . " and promotion_id=" . $promotion_id);
                if ($num <= 15) {
                    $pro = Promotion::model()->find("id={$promotion_id}");
                    $pro->sellcount = $pro['sellcount'] + 1;
                    $pro->update();
                }
            }
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            $result ['comment_id'] = $comment->comment_id;
            echo json_encode($result);
        } else {
            $result ['ret_num'] = 3020;
            $result ['ret_msg'] = '保存失败，请重新尝试！';
            echo json_encode($result);
        }
    }

    /*
     * 快递公司查询（废弃）
     * 涉及store_shipping表
     */
    public function actionFindshipping()
    {
        $this->check_key();
        $user = $this->check_user();
        $shippinginfo = StoreShipping::model()->findAll("is_del=0");
        foreach ($shippinginfo as $k => $v) {
            $info[$k]['shipping_id'] = $v['shipping_id'];
            $info[$k]['shipping_name'] = $v['shipping_name'];
            $info[$k]['shipping_code'] = $v['shipping_code'];
        }
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result ['info'] = $info;
        echo json_encode($result);
    }

    /*
     * 根据订单号查询快递公司（废弃）
     * 涉及store_shipping表
     */
    public function actionNotoCo()
    {
        $this->check_key();
        $user = $this->check_user();
        $shipping_sn = Frame::getStringFromRequest('shipping_sn');
        if (empty($shipping_sn)) {
            $result ['ret_num'] = 2015;
            $result ['ret_msg'] = '参数不全';
            echo json_encode($result);
            die();
        }
        $url = "http://m.kuaidi100.com/autonumber/auto?num=" . $shipping_sn;
        $info_str = file_get_contents("$url");
        $info = json_decode($info_str, true);
        $shippinginfo = StoreShipping::model()->find("is_del=0 and shipping_code='{$info[0]['comCode']}'");
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result ['shipping_name'] = $shippinginfo['shipping_name'];
        $result ['shipping_id'] = $shippinginfo['shipping_id'];
        $result ['shipping_code'] = $shippinginfo['shipping_code'];
        echo json_encode($result);
    }

    /*
     * 回复评论
     * 涉及store_comment
     */
    public function actionReply()
    {
        $this->check_key();
        $user = $this->check_user();
        $order_id = Frame::getIntFromRequest('order_id');
        $parent_id = Frame::getIntFromRequest('comment_id');
        $comment_type = Frame::getIntFromRequest('comment_type');//0评论的是促销,1评论的是团购
        $promotion_id = Frame::getIntFromRequest('promotion_id');
        $content = Frame::getStringFromRequest('content');
        if (empty($order_id) || empty($parent_id) || empty($comment_type) || empty($content)) {
            $result ['ret_num'] = 2015;
            $result ['ret_msg'] = '参数不全';
            echo json_encode($result);
            die();
        }
        $comment = new StoreComment();
        $comment->comment_type = $comment_type;
        $comment->promotion_id = $promotion_id;
        $comment->huanxin_username = $user['huanxin_username'];
        $comment->user_name = $user['nick_name'];
        $comment->content = $content;
        $comment->add_time = time();
        $comment->parent_id = $parent_id;
        $comment->member_id = $user['id'];
        $comment->order_id = $order_id;
        $oinfo = StoreOrderInfo::model()->find("order_id={$order_id}");
        if ($oinfo['member_id'] != $user['id']) {
            $comment->is_seller = 1;
        } else {
            $comment->is_seller = 0;
        }
        if ($comment->save()) {
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            $result ['comment_id'] = $comment->comment_id;
            echo json_encode($result);
        } else {
            $result ['ret_num'] = 3020;
            $result ['ret_msg'] = '保存失败，请重新尝试！';
            echo json_encode($result);
        }
    }

    /*
     * 查询某订单的所有评论
     * 涉及store_order_info
     */
    public function actionOrderCommentList()
    {
        $this->check_key();
        $user = $this->check_user();
        $order_id = Frame::getIntFromRequest('order_id');
        $oinfo = StoreComment::model()->findAll("order_id={$order_id}");
        $tpl = array();
        $info = array();
        //取各个子评论
        foreach ($oinfo as $k => $v) {
            if ($v['parent_id'] != 0) {
                $tpl[$v['parent_id']][] = array(
                    'comment_id' => $v['comment_id'],
                    'comment_type' => $v['comment_type'],
                    'promotion_id' => $v['promotion_id'],
                    'huanxin_username' => $v['huanxin_username'],
                    'content' => $v['content'],
                    'comment_rank' => $v['comment_rank'],
                    'add_time' => $v['add_time'],
                    'is_seller' => $v['is_seller']//0买家,1卖家
                );
            }
        }

        //取主评论
        foreach ($oinfo as $ko => $vo) {
            if ($vo['parent_id'] == 0) {
                $info[] = array(
                    'comment_id' => $vo['comment_id'],
                    'comment_type' => $vo['comment_type'],
                    'promotion_id' => $vo['promotion_id'],
                    'huanxin_username' => $vo['huanxin_username'],
                    'content' => $vo['content'],
                    'comment_rank' => $vo['comment_rank'],
                    'add_time' => $vo['add_time'],
                    'is_seller' => $vo['is_seller'],//0买家,1卖家
                    'reply' => $tpl[$vo['comment_id']] ? $tpl[$vo['comment_id']] : array(),
                );
            }
        }
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result ['info'] = $info ? $info : array();
        echo json_encode($result);
    }

    //================================================商家订单相关处理=============================================
    /*
     * 订单详情(商家)
     * 需要待验证该订单是否属于此商家
     */
    public function actionCheckorder()
    {
        $this->check_key();
        $user = $this->check_user();
        $order_sn = Frame::getStringFromRequest('order_sn');
        if (empty($order_sn)) {
            $result ['ret_num'] = 2015;
            $result ['ret_msg'] = '参数不全';
            echo json_encode($result);
            die();
        }
        $pmid = PromotionManage::model()->find("member_id={$user['id']}");
        if (!$pmid) {
            $result ['ret_num'] = 2225;
            $result ['ret_msg'] = '您暂未开通商城';
            echo json_encode($result);
            die();
        }

        $connection = Yii::app()->db;
        $sql = "select a.order_id,a.order_sn,a.order_status,a.shipping_status,a.pay_status,a.consignee,
        a.mobile,a.pay_id,a.pay_name,a.goods_amount,a.shipping_fee,a.order_amount,a.add_time,a.confirm_time,
        a.pay_time, b.promotion_id,b.goods_name,b.goods_number,b.promotion_price
         from store_order_info as a left join store_order_goods as b on a.order_id=b.order_id
        where a.order_sn='" . $order_sn . "'";
        $command = $connection->createCommand($sql);
        $info = $command->queryAll();
        if (!$info[0]['promotion_id']) {
            $result ['ret_num'] = 2212;
            $result ['ret_msg'] = '不存在此订单';
            echo json_encode($result);
            die();
        }
        $pinfo = Promotion::model()->find("pm_id={$pmid['id']} and id={$info[0]['promotion_id']}");
        //加入首图
        $info[0]['promotion_pic'] = $pinfo['poster_st'] ? URL . $this->getThumb($pinfo['poster_st']) : "";

        if (!$pinfo) {
            $result ['ret_num'] = 2222;
            $result ['ret_msg'] = '不存在此订单';
            echo json_encode($result);
            die();
        }

        //判断是否消费过订单
        if ($info[0]['shipping_status'] == 2 || $info[0]['shipping_status'] == 4 || ($info[0]['order_status'] != 0 && $info[0]['order_status'] != 1)) {
            $info[0]['is_consume'] = 1;
        } else {
            $info[0]['is_consume'] = 0;
        }
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result ['info'] = $info ? $info : array();
        echo json_encode($result);
    }

    /*
     * 确认消费，用于到店消费的
     */
    public function actionSureconsume()
    {
        $this->check_key();
        $user = $this->check_user();
        $order_id = Frame::getStringFromRequest('order_id');
        $now = time();
        if (empty($order_id)) {
            $result ['ret_num'] = 2015;
            $result ['ret_msg'] = '参数不全';
            echo json_encode($result);
            die();
        }
        $soinfo = StoreOrderInfo::model()->find("order_id={$order_id}");
        if (!$soinfo) {
            $result ['ret_num'] = 110;
            $result ['ret_msg'] = '该笔订单不存在！';
            echo json_encode($result);
            die();
        }
        if ($soinfo->pay_id == 1) {
            $result ['ret_num'] = 105;
            $result ['ret_msg'] = '您选择的不是到店消费，不能使用';
            echo json_encode($result);
            die();
        }
        if ($soinfo->pay_id == 2 && $soinfo->pay_status != 2) {
            if ($soinfo->pay_status == 1) {
                $result ['ret_num'] = 1225;
                $result ['ret_msg'] = '订单正在支付中，请稍后。。。';
                echo json_encode($result);
                die();
            } else {
                $result ['ret_num'] = 1125;
                $result ['ret_msg'] = '您尚未支付该笔订单';
                echo json_encode($result);
                die();
            }
        }
        if ($soinfo['shipping_status'] == 2 || $soinfo['shipping_status'] == 4 || ($soinfo['order_status'] != 0 && $soinfo['order_status'] != 1)) {
            $result ['ret_num'] = 1115;
            $result ['ret_msg'] = '已经消费过，请勿重复使用';
            echo json_encode($result);
            die();
        }
        $soinfo->shipping_status = 2;
        $soinfo->order_status = 5;
        $soinfo->pay_time = $now;
        if ($soinfo->pay_id == 3) {
            $soinfo->pay_status = 2;
        }
        if ($soinfo->update()) {
            $log = new OrderOfflineLog();
            $log->user_id = $soinfo->member_id;
            $log->shopper_id = $user['id'];
            $log->order_id = $soinfo->order_id;
            $log->order_sn = $soinfo->order_sn;
            $goods = StoreOrderGoods::model()->find("order_id=" . $soinfo->order_id);
            $log->name = $goods->goods_name;
            $log->consume_time = $now;
            $log->save();
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            echo json_encode($result);
        } else {
            $result ['ret_num'] = 2115;
            $result ['ret_msg'] = '网络延迟，请稍后重试';
            echo json_encode($result);
            die();
        }
    }

    /**
     * 商家确认消费记录
     */
    public function actionConsumeRecords()
    {
        $this->check_key();
        $user = $this->check_user();
        $connection = Yii::app()->db;
        $sql = "select * from order_offline_log where shopper_id=" . $user['id'];
        $command = $connection->createCommand($sql);
        $res = $command->queryAll();
        $result['list'] = $res ? $res : array();
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        echo json_encode($result);
    }

    /*
     * 商家订单列表
     * status=0待发货，1已发货，2待评价，4.退款
     */
    public function actionStorderlist()
    {
        $this->check_key();
        $user = $this->check_user();
        $p = Frame::getIntFromRequest('page');
        $status = Frame::getStringFromRequest('status');//主要看store_order_info中的shipping_status，如果不传则代表全部
        if (!$p) {
            $p = 1;
        }
        //取出所有商家的产品（包括已下架）
        $traininfo = NumberTrain::model()->find("member_id={$user['id']}");
//        $pminfo = PromotionManage::model()->find("member_id={$user['id']}");
//        $pinfo = Promotion::model()->findAll("pm_id={$pminfo['id']}");
//        $tpl_pid = array();
//        foreach ($pinfo as $k => $v) {
//            $tpl_pid[] = $v['id'];
//        }
//        if (!$tpl_pid) {
//            $result ['ret_num'] = 6554;
//            $result ['ret_msg'] = '亲！您还没有销售出任何商品';
//            echo json_encode($result);
//            die();
//        }
        $connection = Yii::app()->db;
        $maxnum = 10;//最大单页显示数据条数
        if ($status != '0' && $status != '1' && $status != '2' && $status != '4') {
            //status未传,全部数据
            $where = " where b.store_id = ".$traininfo['id'];
        } else if ($status == 2) {
            //商家待评价,用户已收货，且用户已评价
            $where = " where b.store_id =".$traininfo['id'] ." and a.shipping_status=2 and a.order_status=6 and a.store_comment_status=0";
        } else if ($status == 4) {
            //申请退款
            $where = " where b.store_id =".$traininfo['id'] ." and a.order_status=4";
        } else {
            //具体状态数据,非取消的订单和退货的
            $where = " where b.store_id =".$traininfo['id'] ." and a.shipping_status=" . $status . " and a.order_status!=2 and a.order_status!=4";
        }
        //商家订单不包括促销、团购以外的订单
        $where .= " and (a.extension_code=0 or a.extension_code=1 or a.extension_code=2)";

        $sql_num = "select count(1) as num from store_order_info where order_id in (select a.order_id from store_order_goods as b
        left join store_order_info as a on a.order_id=b.order_id " . $where . " GROUP BY b.order_id)";
        $command = $connection->createCommand($sql_num);
        $num_tpl = $command->queryAll();
        $num = $num_tpl[0]['num'];//总数据条数
        $allpage = ceil($num / $maxnum);//总页数

        //全部订单
        $sql_num00 = "select count(1) as num from store_order_info where order_id in (select a.order_id from store_order_goods as b
        left join store_order_info as a on a.order_id=b.order_id where b.store_id = ".$traininfo['id']." GROUP BY b.order_id)";
        $command = $connection->createCommand($sql_num00);
        $num00_tpl = $command->queryAll();

        //获取已下单未发货的数量
        $sql_num0 = "SELECT COUNT(1) as num from store_order_info where order_id in (select a.order_id from store_order_goods as b
        left join store_order_info as a on a.order_id=b.order_id where b.store_id = ".$traininfo['id']." and a.shipping_status=0 and a.order_status!=2 and a.order_status!=4 GROUP BY b.order_id)";
        $command = $connection->createCommand($sql_num0);
        $num0_tpl = $command->queryAll();

        //获取已发货的=待收货的数量
        $sql_num1 = "SELECT COUNT(1) as num from store_order_info where order_id in (select a.order_id from store_order_goods as b
        left join store_order_info as a on a.order_id=b.order_id where b.store_id = ".$traininfo['id']." and a.shipping_status=1 and a.order_status!=2 and a.order_status!=4 GROUP BY b.order_id)";
        $command = $connection->createCommand($sql_num1);
        $num1_tpl = $command->queryAll();

        //获取用户已收货已评价，商家待评价的数量
        $sql_num2 = "SELECT COUNT(1) as num from store_order_info where order_id in (select a.order_id from store_order_goods as b
        left join store_order_info as a on a.order_id=b.order_id where b.store_id = ".$traininfo['id']." and a.shipping_status=2 and a.order_status=6 and a.store_comment_status=0 GROUP BY b.order_id)";
        $command = $connection->createCommand($sql_num2);
        $num2_tpl = $command->queryAll();

        //获取已退货的数量
        $sql_num3 = "SELECT COUNT(1) as num from store_order_info where order_id in (select a.order_id from store_order_goods as b
        left join store_order_info as a on a.order_id=b.order_id where b.store_id = ".$traininfo['id']." and a.order_status!=2 and a.order_status=4 GROUP BY b.order_id)";
        $command = $connection->createCommand($sql_num3);
        $num3_tpl = $command->queryAll();

        //取出每页数据
        if ($p <= $allpage) {
            $sql = "select a.order_id,a.order_sn,a.shipping_status,a.shipping_sn,a.pay_id,a.pay_name,a.goods_amount,a.shipping_fee,a.order_amount,a.pay_status,
             a.order_status,a.member_id,a.extension_code,b.promotion_id,b.goods_name,b.goods_number,b.promotion_price
            from store_order_info as a left join store_order_goods as b on a.order_id = b.order_id" . $where . " order by a.order_id Desc limit " . ($p - 1) * $maxnum . " ," . $maxnum;
            $command = $connection->createCommand($sql);
            $result0 = $command->queryAll();
            foreach ($result0 as $k => $v) {
                if($v['extension_code']!=2) {
                    $promotionid_arr[] = $v['promotion_id'];
                }else if($v['extension_code']==2){
                    $quote_arr[]=$v['promotion_id'];
                }
                $order_arr[] = $v['order_id'];
                $member_arr[] = $v['member_id'];
            }
            //获取订单的商品图片信息
            if($promotionid_arr) {
                $poster_tpl = Promotion::model()->findAll("id in (" . implode(",", $promotionid_arr) . ")");
                foreach ($poster_tpl as $k => $v) {
                    $poster_tpl[$v['id']] = $v['poster_st'];
                    $is_close[$v['id']] = $v['is_close'] ? $v['is_close'] : 0;
                    $is_out[$v['id']] = $v['valid_right'] > time() ? 0 : 1;
                }
            }
            //获取我要买详情
            if($quote_arr) {
                $qa = QuoteAttachment::model()->findAll("quote_id in (" . implode(",", $quote_arr) . ") group by quote_id");
                foreach ($qa as $k => $v) {
                    $poster_quote_tpl[$v['quote_id']] = $v['poster'];
                }
            }
            //获取退款状态
            $backinfo = BackOrder::model()->findAll("train_id={$traininfo['id']} and order_id in (" . implode(",", $order_arr) . ")");
            foreach ($backinfo as $kb => $vb) {
                $back_tpl[$vb['order_id']] = $vb;
            }

            //获取个人信息
            $minfo = Member::model()->findAll("id in (" . implode(",", $member_arr) . ")");
            foreach ($minfo as $km => $vm) {
                $userpic[$vm['id']] = $vm['poster'];
                $userconnect[$vm['id']] = $vm['huanxin_username'];
                $usernick[$vm['id']] = $vm['nick_name'];
            }
            foreach ($result0 as $kk => $vv) {
                $result0[$kk]['user_poster'] = $userpic[$vv['member_id']] ? URL . $userpic[$vv['member_id']] : "";
                $result0[$kk]['nick_name'] = $usernick[$vv['member_id']] ? $usernick[$vv['member_id']] : "";
                $result0[$kk]['huanxin_username'] = $userconnect[$vv['member_id']] ? $userconnect[$vv['member_id']] : "";
                $result0[$kk]['promotion_pic'] = $vv['extension_code']==2?
                    ($poster_quote_tpl[$vv['promotion_id']] ? URL . $this->getThumb($poster_quote_tpl[$vv['promotion_id']]) : ""):
                    ($poster_tpl[$vv['promotion_id']] ? URL . $this->getThumb($poster_tpl[$vv['promotion_id']]) : "");
                $result0[$kk]['back_status'] = $back_tpl[$vv['order_id']]['status'] ? $back_tpl[$vv['order_id']]['status'] : 0;
                $result0[$kk]['back_apply_time'] = $back_tpl[$vv['order_id']]['apply_time'] ? $back_tpl[$vv['order_id']]['apply_time'] : 0;
                $result0[$kk]['back_deal_time'] = $back_tpl[$vv['order_id']]['deal_time'] ? $back_tpl[$vv['order_id']]['deal_time'] : 0;
                $result0[$kk]['is_close'] = $is_close[$vv['promotion_id']] ? $is_close[$vv['promotion_id']] : 0;
                $result0[$kk]['is_out'] = $is_out[$vv['promotion_id']] ? $is_out[$vv['promotion_id']] : 0;
            }
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            $result['orderinfo'] = $result0;
            $result['page'] = $allpage;
            $result['all'] = $num00_tpl[0]['num'];
            $result['confirm'] = $num0_tpl[0]['num'];
            $result['wanna_get'] = $num1_tpl[0]['num'];
            $result['ready_comment'] = $num2_tpl[0]['num'];
            $result['back'] = $num3_tpl[0]['num'];
            echo json_encode($result);
        } else {
            $result ['ret_num'] = 8844;
            $result ['ret_msg'] = '已无更多内容！';
            echo json_encode($result);
            die();
        }
    }

    /*
     * 商家查看订单详情
     */
    public function actionStorderdetail()
    {
        $this->check_key();
        $user = $this->check_user();
        $order_id = Frame::getIntFromRequest('order_id');
        $order_sn = Frame::getStringFromRequest('order_sn');
        $extension_code = Frame::getStringFromRequest('extension_code');//商品类型
        if (empty($order_id)) {
            $result ['ret_num'] = 2015;
            $result ['ret_msg'] = '参数不全';
            echo json_encode($result);
            die();
        }

        //如果订单号存在，订单id不存在，则查询订单id
        if (!$order_id && $order_sn) {
            $stinfo = StoreOrderInfo::model()->find("order_sn='{$order_sn}'");
            $order_id = $stinfo['order_id'];
        }

        $connection = Yii::app()->db;
        $sql = "select a.order_id,a.order_sn,a.order_status,a.shipping_status,a.shipping_sn,a.pay_status,a.consignee,a.extension_code,
        a.qrcode,a.province,a.city,a.area,a.street,a.address,a.mobile,a.pay_id,a.pay_name,a.goods_amount,a.shipping_fee,
        a.order_amount,a.add_time,a.confirm_time,a.pay_time,a.member_id, b.promotion_id,b.goods_name,b.goods_number,b.promotion_price
         from store_order_info as a left join store_order_goods as b on a.order_id=b.order_id
        where a.order_id=" . $order_id;
        $command = $connection->createCommand($sql);
        $info = $command->queryAll();
        foreach ($info as $k => $v) {
            //如果是在线支付，到店消费隐藏订单号
            if(($v['pay_id']==2||$v['pay_id']==3)&&$v['shipping_status']==0){
                $info[$k]['order_sn']='';
            }
            //商品信息
            $info[$k]['qrcode'] = $v['qrcode'] ? URL . $v['qrcode'] : "";
            if ($extension_code != 2) {
                $pinfo = Promotion::model()->find("id={$v['promotion_id']}");
                $info[$k]['is_close'] = $pinfo['is_close'] ? $pinfo['is_close'] : 0;
                $info[$k]['is_out'] = $pinfo['valid_right'] > time() ? 0 : 1;
                $info[$k]['promotion_pic'] = $pinfo['poster_st'] ? URL . $pinfo['poster_st'] : "";
            } else {
                $qinfo = Quote::model()->find("id={$v['promotion_id']}");
                $info[$k]['train_id'] = $qinfo['store_id'];
                $qa = QuoteAttachment::model()->find("quote_id={$qinfo['id']}");
                $info[$k]['promotion_pic'] = $qa['poster'] ? URL . $qa['poster'] : "";
            }

            //买家收货地址
            $dis = array("province" => $v['province'], "city" => $v['city'], "area" => $v['area'], "street" => $v['street']);
            if (!empty($v['province']) || !empty($v['city']) || !empty($v['area']) || !empty($v['street'])) {
                $district = $this->ProCity(array(0 => $dis));
                $info[$k]['address'] = $district[$v['province']] . $district[$v['city']] . $district[$v['area']] . $district[$v['street']] . $v['address'];
            } else {
                $info[$k]['address'] = "";
            }

            //买家环信名用于聊天
            $minfo = Member::model()->find("id={$v['member_id']}");
            $info[$k]['huanxin_username'] = $minfo['huanxin_username'];
            $info[$k]['user_poster'] = $minfo['poster'] ? URL . $minfo['poster'] : "";
            $info[$k]['nick_name'] = $minfo['nick_name'] ? $minfo['nick_name'] : "";
        }

        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result ['info'] = $info ? $info : array();
        echo json_encode($result);
    }

    /*
     * 手动发货
     * 涉及store_order_info
     */
    public function actionManualsend()
    {
        $this->check_key();
        $user = $this->check_user();
        $order_id = Frame::getIntFromRequest("order_id");
        $shipping_name = Frame::getStringFromRequest("sname");//物流公司名
        $shipping_sn = Frame::getStringFromRequest("sn");//物流运单号
        if (empty($order_id) || empty($shipping_name) || empty($shipping_sn)) {
            $result ['ret_num'] = 2015;
            $result ['ret_msg'] = '参数不全！';
            echo json_encode($result);
            die();
        }
        $oinfo = StoreOrderInfo::model()->find("order_id={$order_id}");
        if (!$oinfo) {
            $result ['ret_num'] = 1115;
            $result ['ret_msg'] = '订单不存在！';
            echo json_encode($result);
            die();
        }
        $oinfo->shipping_name = $shipping_name;
        $oinfo->shipping_sn = $shipping_sn;
        $oinfo->shipping_status = 1;
        $oinfo->shipping_time = time();
        if ($oinfo->update()) {
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
        } else {
            $result ['ret_num'] = 190;
            $result ['ret_msg'] = '网络延迟，请重新尝试';
        }
        echo json_encode($result);
    }

    /*
     * 商家同意退款处理
     * 需要进行支付宝退款处理操作
     */
    public function actionAgreeback()
    {
        $this->check_key();
        $user = $this->check_user();
        $order_id = Frame::getIntFromRequest("order_id");
        if (empty($order_id)) {
            $result ['ret_num'] = 2015;
            $result ['ret_msg'] = '参数不全！';
            echo json_encode($result);
            die();
        }
        $backinfo = BackOrder::model()->find("order_id={$order_id}");
        if (!$backinfo) {
            $result ['ret_num'] = 4590;
            $result ['ret_msg'] = '该退款操作不存在！';
            echo json_encode($result);
            die();
        }
        $sginfo = StoreOrderGoods::model()->find("order_id={$order_id}");
        $shopinfo = $this->getShopinfo($sginfo['promotion_id']);
        if ($shopinfo[$sginfo['promotion_id']]['member_id'] != $user['id']) {
            $result ['ret_num'] = 2590;
            $result ['ret_msg'] = '您不是店主无权退款！';
            echo json_encode($result);
            die();
        }
        $backinfo->status = 5;//商家同意退款
        if ($backinfo->update()) {
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            echo json_encode($result);
        } else {
            $result ['ret_num'] = 3020;
            $result ['ret_msg'] = '保存失败，请重新尝试！';
            echo json_encode($result);
        }
    }

    /*
     * 商家拒绝退款
     * 涉及back_order表
     */
    public function actionRefuse()
    {
        $this->check_key();
        $user = $this->check_user();
        $order_id = Frame::getIntFromRequest("order_id");
        if (empty($order_id)) {
            $result ['ret_num'] = 2015;
            $result ['ret_msg'] = '缺少参数！';
            echo json_encode($result);
            die();
        }
        $backinfo = BackOrder::model()->find("order_id={$order_id}");
        if (!$backinfo) {
            $result ['ret_num'] = 1015;
            $result ['ret_msg'] = '该笔退款不存在！';
            echo json_encode($result);
            die();
        }
        $backinfo->status = 3;
        if ($backinfo->update()) {
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功！';
            echo json_encode($result);
        } else {
            $result ['ret_num'] = 3020;
            $result ['ret_msg'] = '保存失败，请重新尝试！';
            echo json_encode($result);
        }
    }

    /*
     * 商家对个人评论
     * 涉及store_comment
     */
    public function actionStordercomment()
    {
        $this->check_key();
        $user = $this->check_user();
        $promotion_id = Frame::getIntFromRequest('promotion_id');
        $order_id = Frame::getIntFromRequest('order_id');
        $comment_type = Frame::getIntFromRequest('comment_type');//0评论的是促销,1评论的是团购
        $content = Frame::getStringFromRequest('content');
        $comment_rank = Frame::getIntFromRequest('comment_rank');//3好，2中，1差
        if (empty($order_id) || empty($comment_type) || empty($content)) {
            $result ['ret_num'] = 2015;
            $result ['ret_msg'] = '参数不全！';
            echo json_encode($result);
            die();
        }
        //判断订单是否处于待评论或已评论状态
        $shopinfo = $this->getShopinfo($promotion_id);
        if ($shopinfo[$promotion_id]['member_id'] != $user['id']) {
            $result ['ret_num'] = 135;
            $result ['ret_msg'] = '您不是商户，不能评论！';
            echo json_encode($result);
            die();
        }
        $stinfo = StoreOrderInfo::model()->find("order_id={$order_id}");
        if ($stinfo['store_comment_status'] == 1) {
            $result ['ret_num'] = 1120;
            $result ['ret_msg'] = '您已评价过！';
            echo json_encode($result);
            die();
        }
        $comment = new StoreComment();
        $comment->comment_type = $comment_type;
        $comment->promotion_id = $promotion_id;
        //该评论人的环信账号用于聊天
        $comment->huanxin_username = $user['huanxin_username'];

        $comment->user_name = $user['nick_name'];
        $comment->content = $content;
        $comment->add_time = time();
        $comment->comment_rank = $comment_rank;
        $comment->parent_id = 0;
        $comment->member_id = $user['id'];
        $comment->order_id = $order_id;
        $comment->is_seller = 1;
        if ($comment->save()) {
            if ($stinfo['store_comment_status'] == 0) {
                //商家一次都未评论，评论后需要切换状态为已评论
                $stinfo->store_comment_status = 1;
                $stinfo->update();
            }
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            $result ['comment_id'] = $comment->comment_id;
            echo json_encode($result);
        } else {
            $result ['ret_num'] = 3020;
            $result ['ret_msg'] = '保存失败，请重新尝试！';
            echo json_encode($result);
        }
    }

}