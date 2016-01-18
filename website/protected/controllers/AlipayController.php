<?php

class AlipayController extends PublicController {
	public $layout = false;

	/*
		             * alipay移动支付异步通知
	*/
    public function actionnotifyurl()
    {
        /* *
         * 功能：支付宝服务器异步通知页面
         * 版本：3.3
         * 日期：2012-07-23
         * 说明：
         * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
         * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。


         *************************页面功能说明*************************
         * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
         * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
         * 该页面调试工具请使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyNotify
         * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
         */
        include_once('lib/alipay/Corefunction.php');
        include_once('lib/alipay/Md5function.php');
        include_once('lib/alipay/Rsafunction.php');
        include_once('lib/alipay/Notify.php');
        include_once('lib/alipay/Submit.php');
        $alipay_config = Yii::app()->params['alipay_config'];

        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();

        if ($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

            //支付成功详细信息

            $success_details = $_POST['trade_status'];
            if ($success_details == "TRADE_SUCCESS") {
                $order_sn = $_POST['out_trade_no'];
                $orderinfo = StoreOrderInfo::model()->find("order_sn='{$order_sn}'");
                $ordergoodsinfo = StoreOrderGoods::model()->find("order_id={$orderinfo['order_id']}");
                $orderinfo->pay_status = 2;
                if ($orderinfo['extension_code'] == 4) {
                    //会员充值,改成已发货
                    $orderinfo->shipping_status = 1;
                    $orderinfo->order_status = 6;
                    $orderinfo->shipping_time = time();
                } elseif ($orderinfo['extension_code'] == 5) {
                    //拍卖支付，将拍卖的支付改为成功promotion_id=auction_id,改成已发货
                    $auction_info = TopAuction::model()->find("auction_id=" . $ordergoodsinfo['promotion_id']);
                    if ($auction_info) {
                        $auction_info->is_paid = 1;
                        $auction_info->update();
                    }
                    $orderinfo->shipping_status = 1;
                    $orderinfo->order_status = 6;
                    $orderinfo->shipping_time = time();
                }
                $orderinfo->pay_time = strtotime($_POST['gmt_payment']);
                if ($orderinfo->update()) {
                    $payinfo = PayLog::model()->find("order_id={$orderinfo['order_id']}");
                    if (!$payinfo) {
                        $paylog = new PayLog();
                        $paylog->order_id = $orderinfo['order_id'];
                        $paylog->trade_no = $_POST['trade_no'];
                        $paylog->trade_status = $_POST['trade_status'];
                        $paylog->order_amount = $_POST['total_fee'];
                        $paylog->seller_email = $_POST['seller_email'];
                        $paylog->buyer_email = $_POST['buyer_email'];
                        if ($orderinfo['extension_code'] != 4) {
                            $paylog->order_type = 0;
                        } else {
                            $paylog->order_type = 1;
                        }
                        $paylog->is_paid = 1;
                        $paylog->save();
                    }
                }
                //更新商家账户余额
                if ($orderinfo['extension_code'] == 4) {
                    //会员充值直接增加余额
                    $minfo = Member::model()->find("id={$orderinfo['member_id']}");
                    $minfo->fee = $minfo['fee'] + $_POST['total_fee'];
                    $minfo->update();
                } else {
                    //商品交易查询用户后，增加给商户
                    if ($ordergoodsinfo) {
                        $pinfo = Promotion::model()->find("id={$ordergoodsinfo['promotion_id']}");
                    }
                    if ($pinfo) {
                        $pminfo = PromotionManage::model()->find("id={$pinfo['pm_id']}");
                    }
                    if ($pminfo['member_id']) {
                        $minfo = Member::model()->find("id={$pminfo['member_id']}");
                        $minfo->fee = $minfo['fee'] + $_POST['total_fee'];
                        $minfo->update();
                    }
                }
            } else if ($success_details == "WAIT_BUYER_PAY") {
                $order_sn = $_POST['out_trade_no'];
                //由于异步通知，可能晚于已付通知到达需要判断
                $tplinfo = StoreOrderInfo::model()->find("order_sn='{$order_sn}'");
                if ($tplinfo['pay_status'] != 2) {
                    StoreOrderInfo::model()->updateAll(array("pay_status" => 1), "order_sn='{$order_sn}'");
                }
            }

            //判断是否在商户网站中已经做过了这次通知返回的处理
            //如果没有做过处理，那么执行商户的业务程序
            //如果有做过处理，那么不执行商户的业务程序

            echo "success";        //请不要修改或删除

            //调试用，写文本函数记录程序运行情况是否正常
            //            logResult(json_decode($_POST)."\r\n");

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } else {
            //验证失败
            echo "fail";
            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            logResult("fail" . date("Y/m/d H:i:s", time()) . "\r\n");
        }
    }

    public function actionBacknotify() {
        /* *
                            * 功能：支付宝服务器异步通知页面
                            * 版本：3.3
                            * 日期：2012-07-23
                            * 说明：
                            * 以下代码只是为了方便商户测试而提供的样例代码，商户可以根据自己网站的需要，按照技术文档编写,并非一定要使用该代码。
                            * 该代码仅供学习和研究支付宝接口使用，只是提供一个参考。

                            *************************页面功能说明*************************
                            * 创建该页面文件时，请留心该页面文件中无任何HTML代码及空格。
                            * 该页面不能在本机电脑测试，请到服务器上做测试。请确保外部可以访问该页面。
                            * 该页面调试工具请使用写文本函数logResult，该函数已被默认关闭，见alipay_notify_class.php中的函数verifyNotify
                            * 如果没有收到该页面返回的 success 信息，支付宝会在24小时内按一定的时间策略重发通知
        */
        include_once 'lib/alipay/Corefunction.php';
        include_once 'lib/alipay/Md5function.php';
        include_once 'lib/alipay/Rsafunction.php';
        include_once 'lib/alipay/Notify.php';
        include_once 'lib/alipay/Submit.php';
        $alipay_config = Yii::app()->params['alipay_config'];

        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();

        if ($verify_result) {
            //验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

            //支付成功详细信息

            logResult("\r\n" . json_encode($_POST) . "\r\n");
            if ($_POST['success_num']) {
            }

            //判断是否在商户网站中已经做过了这次通知返回的处理
            //如果没有做过处理，那么执行商户的业务程序
            //如果有做过处理，那么不执行商户的业务程序

            echo "success"; //请不要修改或删除

            //调试用，写文本函数记录程序运行情况是否正常
            //            logResult(json_decode($_POST)."\r\n");

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        } else {
            //验证失败
            echo "fail";
            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            logResult("fail" . date("Y/m/d H:i:s", time()) . "\r\n");
        }
    }


//    public function actionget(){
	//        $public_key = file_get_contents(getcwd().'/rsa_public_key.pem');
	//        $private = file_get_contents(getcwd().'/rsa_private_key.pem');
	//        $str='{"discount":"0.00","payment_type":"1","subject":"\u54c7\u5566","trade_no":"2015122321001004230050844487","buyer_email":"jia_yang@yeah.net","gmt_create":"2015-12-23 17:59:15","notify_type":"trade_status_sync","quantity":"1","out_trade_no":"20151223175858396720250426042129","seller_id":"2088121516774084","notify_time":"2015-12-23 18:03:40","body":"\u54c7\u5566","trade_status":"TRADE_SUCCESS","is_total_fee_adjust":"N","total_fee":"0.01","gmt_payment":"2015-12-23 17:59:16","seller_email":"18958171188@189.cn","price":"0.01","buyer_id":"2088002452741230","notify_id":"d73b6d7f325949241ea4b329ba78498hrw","use_coupon":"N","sign_type":"RSA","sign":"ch7S48Q+aaC9dTvtE4NJlYeSiGecmvSyKxcKLEy0t+TCTNMboUaI8YkPf\/VmeSN1QSBS65tLraxwGgrLXztvUwMBBN+wbrF2Gq6hsIuG7naswr0sM2vdz3vdhHnCdO+f3C9UeBkTnO7rfkgHAvELYyjMnpY4yIcpR8N0NbWUAuU="}';
	//        $tpl=json_decode($str,true);
	//        //除去待签名参数数组中的空值和签名参数
	//        $para_filter =$this->paraFilter($tpl);
	//        //对待签名参数数组排序
	//        $para_sort = $this->argSort($para_filter);
	//
	//        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
	//        $prestr =$this->createLinkstring($para_sort);
	////        var_dump($prestr);
	////        var_dump($tpl['sign']);
	//
	//        $isSgin = $this->rsaVerify($prestr, $tpl['sign']);
	//        var_dump($isSgin);
	////        var_dump($public_key) ;
	////        var_dump($private) ;
	//    }
	//
	//    function paraFilter($para) {
	//        $para_filter = array();
	//        while (list ($key, $val) = each ($para)) {
	//            if($key == "sign" || $key == "sign_type" || $val == "")continue;
	//            else	$para_filter[$key] = $para[$key];
	//        }
	//        return $para_filter;
	//    }
	//    /**
	//     * 对数组排序
	//     * @param $para 排序前的数组
	//     * return 排序后的数组
	//     */
	//    function argSort($para) {
	//        ksort($para);
	//        reset($para);
	//        return $para;
	//    }
	//
	//    function createLinkstring($para) {
	//        $arg  = "";
	//        while (list ($key, $val) = each ($para)) {
	//            $arg.=$key."=".$val."&";
	//        }
	//        //去掉最后一个&字符
	//        $arg = substr($arg,0,count($arg)-2);
	//
	//        //如果存在转义字符，那么去掉转义
	//        if(get_magic_quotes_gpc()){$arg = stripslashes($arg);}
	//
	//        return $arg;
	//    }
	//
	//    function rsaVerify($prestr, $sign)
	//    {
	//        $sign = base64_decode($sign);
	//        $public_key = file_get_contents(getcwd().'/rsa_public_key.pem');
	//        $pkeyid = openssl_get_publickey($public_key);
	//        if ($pkeyid) {
	//            $verify = openssl_verify($prestr, $sign, $pkeyid);
	//            openssl_free_key($pkeyid);
	//        }
	//        if ($verify == 1) {
	//            return true;
	//        } else {
	//            return false;
	//        }
	//    }

/**
 * 网站支付宝异步通知页面
 */
	public function actionAlipayWebNotify() {
		include "service.php";
		require_once Yii::getPathOfAlias('webroot') . "/lib/alipay/alipay.config.php";
		require_once Yii::getPathOfAlias('webroot') . "/lib/alipay/lib/alipay_notify.class.php";

//计算得出通知验证结果
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyNotify();

		$data['out_trade_no'] = $_POST['out_trade_no'];
		$data['trade_status'] = $_POST['trade_status'];
		$log_url = Yii::getPathOfAlias('webroot') . "/lib/alipay/logs/alipay_code_" . date('Y-m') . '.log';
		$log_data = date('Y-m-d H:i:s') . var_export($data, true) . "\r\n";
		file_put_contents($log_url, $log_data, FILE_APPEND);

		if ($verify_result) {
			$order_sn = $_POST['out_trade_no'];
			if ($_POST['trade_status'] == 'TRADE_FINISHED') {
			} else if ($_POST['trade_status'] == 'TRADE_SUCCESS') {
				$this->alipayCommonFunc(0);
				//判断该笔订单是否在商户网站中已经做过处理
				//如果没有做过处理，根据订单号（out_trade_no）在商户网站的订单系统中查到该笔订单的详细，并执行商户的业务程序
				//请务必判断请求时的total_fee、seller_id与通知时获取的total_fee、seller_id为一致的
				//如果有做过处理，不执行商户的业务程序

				//注意：
				//付款完成后，支付宝系统发送该交易状态通知

				//调试用，写文本函数记录程序运行情况是否正常
				//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
			}

			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

			echo "success"; //请不要修改或删除

			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		} else {
			//验证失败
			echo "fail";

			//调试用，写文本函数记录程序运行情况是否正常
			//logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
		}

	}
/**
 * 网站支付宝同步通知页面
 */
	public function actionAlipayWebReturn() {
		include "service.php";
		require_once Yii::getPathOfAlias('webroot') . "/lib/alipay/alipay.config.php";
		require_once Yii::getPathOfAlias('webroot') . "/lib/alipay/lib/alipay_notify.class.php";
//计算得出通知验证结果
		$alipayNotify = new AlipayNotify($alipay_config);
		$verify_result = $alipayNotify->verifyReturn();
		$data['out_trade_no'] = $_GET['out_trade_no'];
		$data['trade_status'] = $_GET['trade_status'];
		$log_url = Yii::getPathOfAlias('webroot') . "/lib/alipay/logs/alipay_code_" . date('Y-m') . '.log';
		$log_data = date('Y-m-d H:i:s') . var_export($data, true) . "\r\n";
		file_put_contents($log_url, $log_data, FILE_APPEND);
		if ($verify_result) {
			if ($_GET['trade_status'] == 'TRADE_FINISHED' || $_GET['trade_status'] == 'TRADE_SUCCESS') {
				$this->alipayCommonFunc(1);

			} else {
				echo "trade_status=" . $_GET['trade_status'];
			}

			$this->redirect('/index.php');
			//——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

			/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
		} else {
			//验证失败
			//如要调试，请看alipay_notify.php页面的verifyReturn函数
			echo "验证失败";
		}

	}

	public function alipayCommonFunc($type = 0) {
		//0异步 //1同步
		//支付宝公共方法
		if ($type == 0) {
			$order_sn = $_POST['out_trade_no'];
			$gmt_payment = $_POST['gmt_payment'];
			$trade_no = $_POST['trade_no'];
			$trade_status = $_POST['trade_status'];
			$total_fee = $_POST['total_fee'];
			$seller_email = $_POST['seller_email'];
			$buyer_email = $_POST['buyer_email'];
			$total_fee = $_POST['total_fee'];
		} else {
			$order_sn = $_GET['out_trade_no'];
			$gmt_payment = $_GET['notify_time'];
			$trade_no = $_GET['trade_no'];
			$trade_status = $_GET['trade_status'];
			$total_fee = $_GET['total_fee'];
			$seller_email = $_GET['seller_email'];
			$buyer_email = $_GET['buyer_email'];
			$total_fee = $_GET['total_fee'];

		}
		$orderinfo = StoreOrderInfo::model()->find("order_sn='{$order_sn}'");
		if ($orderinfo->pay_status != 2) {
			$orderinfo->pay_status = 2;
			$orderinfo->money_paid = $orderinfo->order_amount;
			//会员由网页端充值,改成已发货
			$orderinfo->shipping_status = 1;
			$orderinfo->order_status = 6;

			$orderinfo->pay_time = strtotime($gmt_payment);
			$orderinfo->shipping_time = strtotime($gmt_payment);

			if ($orderinfo->update()) {
				$payinfo = PayLog::model()->find("order_id={$orderinfo['order_id']}");
				if (!$payinfo) {
					$paylog = new PayLog();
					$paylog->order_id = $orderinfo['order_id'];
					$paylog->trade_no = $trade_no;
					$paylog->trade_status = $trade_status;
					$paylog->order_amount = $total_fee;
					$paylog->seller_email = $seller_email;
					$paylog->buyer_email = $buyer_email;
					$paylog->order_type = 3;
					$paylog->is_paid = 1;
					$paylog->save();
				}
				if ($orderinfo['coin'] > 0) {
					$coinpayinfo = CoinPayLog::model()->find("order_id={$orderinfo['order_id']}");
					if (!$coinpayinfo) {
						$coinpaylog = new CoinPayLog();
						$coinpaylog->order_id = $orderinfo['order_id'];
						$coinpaylog->pay_time = $orderinfo['pay_time'];
						$coinpaylog->total_money = $orderinfo['goods_amount'];
						$coinpaylog->money_paid = $total_fee;
						$coinpaylog->use_coin = $orderinfo['coin'];
						$coinpaylog->save();
					}
				}

			}
			//更新商家账户余额(extension_code==4)
			$orderGoods = StoreOrderGoods::model()->find("order_id={$orderinfo['order_id']}");
			$minfo = Member::model()->find("id={$orderinfo['member_id']}");

			if ($orderGoods->extension_code == 4) {
				//会员充值直接增加余额
				$minfo->fee = $minfo['fee'] + $total_fee;
				$minfo->update();
			} else {
				$minfo->coin = $minfo->coin - $orderinfo['coin'];
				$minfo->update();
				service::PayRecode($order_sn);
			}
		}

	}
}
