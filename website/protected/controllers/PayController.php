<?php

class PayController extends Controller
{
    /**
     * #首页展示
     */
    public function actionIndex()
    {
        if (!Yii::app()->user->getState("memberInfo")) {
            $this->redirect('/index.php/site/login');
        }
        $member_id = Yii::app()->user->getState("memberInfo")->id;
        $memberInfo = Member::model()->find("id={$member_id}");
        $fee = $memberInfo['fee'];
        $this->render('index', array('fee' => $fee));
    }

    /**
     * #犇奔账户充值
     */
    public function actionCharge()
    {
        if (!Yii::app()->user->getState("memberInfo")) {
            $this->redirect('/index.php/site/login');
        } else {
            $money = isset($_POST['money']) ? round(($_POST['money']), 2) : 0;
            $data = $this->createOrder($money, 1, '奔犇-账户充值', '账户充值', 1, 4);
            $this->AlipayApi($data);

        }

    }

    /**
     * #账户余额转出
     */
    public function actionRefund()
    {
        if (!Yii::app()->user->getState("memberInfo")) {
            $this->redirect('/index.php/site/login');
        }
        $member_id = Yii::app()->user->getState("memberInfo")->id;
        $sql = "select  b.buyer_email from store_order_info a join pay_log b on a.order_id=b.order_id where a.member_id={$member_id} limit 1 ";
        $data = Yii::app()->db->createCommand($sql)->queryAll();
        $buyer_email = $data[0]['buyer_email'];
        $memberInfo = Member::model()->find("id={$member_id}");
        $fee = $memberInfo['fee'];
        if (isset($_POST['money'])) {
            $money = is_numeric($_POST['money']) ? $_POST['money'] : 0;
            if ($money <= 0) {
                echo "<meta http-equiv='Content-Type'' content='text/html; charset=utf-8'>";
                echo "<script>alert('金额有误，请重新输入!');window.location.href='/index.php/pay/refund';</script>";
                die;
            }
            $row = MemberRefundApply::model()->findAll("is_delete=0 and member_id={$member_id} and handle=0");
            if (!$row) {
                if ($money > $fee) {
                    echo "<meta http-equiv='Content-Type'' content='text/html; charset=utf-8'>";
                    echo "<script>alert('转出金额不能大于实际金额!');window.location.href='/index.php/pay/refund';</script>";
                    die;
                }
                $Model = new MemberRefundApply();
                $Model->member_id = $member_id;
                $Model->fee = $money;
                $Model->is_delete = 0;
                $Model->refund_type = 1;
                $Model->handle = 0;
                $Model->add_date = time();
                $Model->update_date = time();
                $Model->save();
                if ($Model->save()) {
                    echo "<script>alert('您已成功申请犇奔账户余额转出，请耐心等待...');location.href='/index.php'</script>";
                } else {
                    echo "<script>alert('未知错误，请联系管理员...');location.href='/index.php'</script>";
                }

            } else {
                $not_handle_fee = 0;
                foreach ($row as $value) {
                    $not_handle_fee += $value->fee;
                }
                $current_fee = $fee - $not_handle_fee; //金额-未处理的金额
                if ($money > $current_fee) {
                    echo "<meta http-equiv='Content-Type'' content='text/html; charset=utf-8'>";
                    echo "<script>alert('您有部分金额正在申请转出，转出金额不能大于实际金额!');window.location.href='/index.php/pay/refund';</script>";
                    die;
                }
                $ModelB = new MemberRefundApply();
                $ModelB->member_id = $member_id;
                $ModelB->fee = $money;
                $ModelB->is_delete = 0;
                $ModelB->refund_type = 1;
                $ModelB->handle = 0;
                $ModelB->add_date = time();
                $ModelB->update_date = time();
                if ($ModelB->save()) {
                    echo "<script>alert('您已成功申请犇奔账户余额转出，请耐心等待...');location.href='/index.php'</script>";
                } else {
                    echo "<script>alert('未知错误，请联系管理员...');location.href='/index.php'</script>";

                }

            }
        }

        $this->render('refund', array('fee' => $fee, 'buyer_email' => $buyer_email));
    }

    /**
     * #Ajax查询用户余额和折算金额
     */
    public function actionGetInfo()
    {
        include "service.php";
        // $benben_coin = Frame::getIntFromRequest("benben_coin");
        $service_type = Frame::getIntFromRequest("service_type");
        $service_name = Frame::getStringFromRequest("service_name");
        $service_duration = Frame::getIntFromRequest("service_duration");
        $member_id = Yii::app()->user->getState("memberInfo")->id;
        if ((!$service_name) || (!$service_duration)) {
            echo 1;
            die;
        }
        $member_info = Yii::app()->user->getState("memberInfo");
        $minfo = Member::model()->find("id={$member_info->id}");
        $coin = $minfo['coin'];
        $fee_tmp = $minfo['fee'];

        $row = MemberRefundApply::model()->findAll("is_delete=0 and member_id={$member_id} and handle=0");
        if ($row) {
            foreach ($row as $value) {
                $not_handle_fee += $value->fee;
            }
        }
        $fee = $fee_tmp - $not_handle_fee; //可用的余额=金额-未处理的金额
        $sevice_pay = new service();
        $sevice_pay->set_member_id($member_info->id);
        $sevice_pay->set_vip_info($service_type);
        $re = $sevice_pay->pay_price($service_name, $service_duration);
        if ($re['price'] <= 0) {
            echo 2;
            die;
        }
        $data = array('price' => $re['price'], 'vip_price' => $re['vip_price'], 'coin' => $coin, 'fee' => $fee);
        echo json_encode($data);
    }

    /**
     * #购买服务
     */
    public function actionServicedetail()
    {
        include "service.php";
        $use_fee = Frame::getStringFromRequest("use_fee");
        $use_coin = Frame::getIntFromRequest("use_coin");
        $service_type = Frame::getIntFromRequest("service_type");
        $service_name = Frame::getStringFromRequest("service_name");
        $service_duration = Frame::getIntFromRequest("service_duration");
        if ((!$service_name) || (!$service_duration) || ($service_duration < 0)) {
            $this->redirect('/index.php/serviceserviceDetail?type=' . $service_type . '&store=1');
        }
        $member_info = Yii::app()->user->getState("memberInfo");
        $validate = Member::model()->find("id={$member_info->id}");
        if ($use_coin > 0) {
            if ($validate['coin'] < $use_coin || $use_coin < 0) {
                $this->redirect('/index.php/service/serviceDetail?type=' . $service_type . '&store=2');
                die;
            }
        }
        $row = MemberRefundApply::model()->findAll("is_delete=0 and member_id={$member_info->id} and handle=0");
        if ($row) {
            foreach ($row as $value) {
                $not_handle_fee += $value->fee;

            }
        }
        $fee = $validate['fee'] - $not_handle_fee;
        if ($use_fee > 0) {
            if ($fee < $use_fee || $fee < 0) {
                $this->redirect('/index.php/service/serviceDetail?type=' . $service_type . '&store=2');
                die;
            }
        }
        $sevice_pay = new service();
        $sevice_pay->set_member_id($member_info->id);
        $sevice_pay->set_vip_info($service_type);
        $sevice_pay->set_type();
        $re = $sevice_pay->pay_price($service_name, $service_duration);
        if ($re['price'] <= 0) {
            $this->redirect('/index.php/service/serviceDetail?type=' . $service_type . '&store=2');
            die;
        }
        if (($use_coin == $re['price']) || ($use_fee == $re['price']) || ($use_coin + $use_fee == $re['price'])) {
            $data = $this->createOrder($re['price'], $re['promotion_id'], $re['name'], $re['gname'], $re['count'], $re['type'], $use_coin, $use_fee);
            $orderinfo = StoreOrderInfo::model()->find("order_sn={$data['order_sn']}");
            $orderinfo->pay_status = 2;
            $orderinfo->money_paid = $orderinfo->order_amount;
            $orderinfo->shipping_status = 1;
            $orderinfo->order_status = 6;
            $orderinfo->pay_time = time();
            $orderinfo->shipping_time = time();
            $orderinfo->update();
            $this->WriteLogAndUpdate($orderinfo);
            $this->redirect('/index.php');
        } else {
            $data = $this->createOrder($re['price'], $re['promotion_id'], $re['name'], $re['gname'], $re['count'], $re['type'], $use_coin, $use_fee);
            $this->AlipayApi($data);
        }
    }

    /**
     * #犇币和余额后续操作
     */
    public function WriteLogAndUpdate($orderinfo)
    {
        $CoinFeePayLogInfo = CoinFeePayLog::model()->find("order_id={$orderinfo['order_id']}");
        if (!$CoinFeePayLogInfo) {
            $coinfeepaylog = new CoinFeePayLog();
            $coinfeepaylog->order_id = $orderinfo['order_id'];
            $coinfeepaylog->pay_time = $orderinfo['pay_time'];
            $coinfeepaylog->total_money = $orderinfo['goods_amount'];
            $coinfeepaylog->money_paid = $orderinfo['money_paid'];
            $coinfeepaylog->use_coin = $orderinfo['coin'];
            $coinfeepaylog->use_fee = $orderinfo['fee'];
            $coinfeepaylog->save();
        }
        $minfo = Member::model()->find("id={$orderinfo['member_id']}");
        $minfo->coin = $minfo->coin - $orderinfo['coin'];
        $minfo->fee = $minfo->fee - $orderinfo['fee'];
        $minfo->update();
        service::PayRecode($orderinfo['order_sn']);
    }

    /**
     * #交易明细
     */
    public function actionTransactionDetail()
    {
        if (!Yii::app()->user->getState("memberInfo")) {
            $this->redirect('/index.php/site/login');
        } else {
            $member_id = Yii::app()->user->getState("memberInfo")->id;
            $model = StoreOrderInfo::model();
            $cri = new CDbCriteria();
            $cri->order = "add_time desc";
            $cri->addCondition("member_id={$member_id}");
            $pages = new CPagination();
            $pages->itemCount = $model->count($cri);
            $pages->pageSize = 12;
            $pages->applyLimit($cri);
            $nextPage = 2;
            if (isset($_GET['page']) && !empty($_GET['page'])) {
                if ($_GET['page'] >= $pages->pageCount) {
                    $nextPage = $pages->pageCount;
                    $beforePage = intval($_GET['page']) - 1;
                } else {
                    $nextPage = intval($_GET['page']) + 1;
                    $beforePage = intval($_GET['page']) - 1;
                }
            }
            $items = $model->findAll($cri);
        }

        $this->render('transactiondetail', array('items' => $items, 'pages' => $pages, 'nextPage' => $nextPage, 'beforePage' => $beforePage));

    }

    /**
     * #支付宝Api
     */
    public function AlipayApi($data)
    {
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
        $member_id = Yii::app()->user->getState("memberInfo")->id;
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

}
