<?php
/**
 * Created by PhpStorm.
 * User: Odeen
 * Date: 2016/3/16
 * Time: 22:05
 */
class AlipayController extends Controller{
    /*
     * 支付验证，阿里返回该笔支付成功与否
     */
    public function actionCashnotify(){
        include_once('lib/alipay/Corefunction.php');
        include_once('lib/alipay/Md5function.php');
        include_once('lib/alipay/Rsafunction.php');
        include_once('lib/alipay/Notify.php');
        include_once('lib/alipay/Submit.php');
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
        $alipay_config = Yii::app()->params['alipay_config'];
        //计算得出通知验证结果
        $alipayNotify = new \AlipayNotify($alipay_config);
        $verify_result = $alipayNotify->verifyNotify();
        if($verify_result) {//验证成功
            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
            //请在这里加上商户的业务逻辑程序代
            $data1=array();
            $data2=array();
            $money=array();

            //——请根据您的业务逻辑来编写程序（以下代码仅作参考）——

            //获取支付宝的通知返回参数，可参考技术文档中服务器异步通知参数列表

            //批量付款数据中转账成功的详细信息

            $success_details = $_POST['success_details'];
            if($success_details){
                $tpl_success=explode("|", $success_details);
                foreach ($tpl_success as $k=>$v){
                    unset($data1);
                    if($v){
                        $eachinfo=explode("^", $v);
                        //写入付款情况, status=3 =>已提交支付宝处理
                        $data1['status']=1;
                        $data1['pay_time']=$eachinfo[7];
                        Pay::model()->updateAll($data1,"id={$eachinfo[0]} and status=3");
//                        M('pay')->where("id={$eachinfo[0]} and status=3")->data($data1)->save();
                    }
                }
            }

            //批量付款数据中转账失败的详细信息
            $fail_details = $_POST['fail_details'];
            if($fail_details){
                $tpl_fail=explode("|", $fail_details);
                foreach ($tpl_fail as $k=>$v){
                    unset($data2);
                    if($v){
                        $eachinfo1=explode("^", $v);
                        $userinfo=Pay::model()->find("id={$eachinfo1[0]} and status=3");
//                        $userinfo=M('pay')->where("id={$eachinfo1[0]} and status=3")->find();
                        //写入付款情况, status=3 =>已提交支付宝处理
                        $data2['status']=2;
                        $data2['pay_time']=$eachinfo1[7];
                        $data2['reason']=$eachinfo1[5];
                        Pay::model()->updateAll($data2,"id={$eachinfo1[0]} and status=3");
//                        M('pay')->where("id={$eachinfo1[0]} and status=3")->data($data2)->save();

                        //失败退回款项, status=3 =>已提交支付宝处理
                        if($userinfo){
                            $minfo=Member::model()->find("member_id={$userinfo['member_id']}");
//                            $minfo=M('pay_info')->where("member_id={$userinfo['member_id']} and id={$userinfo['payinfo_id']}")->find();
                            $money['fee']=$minfo['fee']+$eachinfo1[3];
                            Member::model()->updateAll($money,"member_id={$userinfo['member_id']}");
//                            M('pay_info')->where("member_id={$userinfo['member_id']} and id={$userinfo['payinfo_id']}")->data($money)->save();
                        }
                    }
                }
            }

            //判断是否在商户网站中已经做过了这次通知返回的处理
            //如果没有做过处理，那么执行商户的业务程序
            //如果有做过处理，那么不执行商户的业务程序

            echo "success";		//请不要修改或删除

            //调试用，写文本函数记录程序运行情况是否正常
            logResult($success_details."\r\n".$fail_details."\r\n");

            //——请根据您的业务逻辑来编写程序（以上代码仅作参考）——

            /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        }
        else {
            //验证失败
            echo "fail";
            //调试用，写文本函数记录程序运行情况是否正常
            //logResult("这里写入想要调试的代码变量值，或其他运行的结果记录");
            logResult("fail".date("Y/m/d H:i:s",time())."\r\n");
        }
    }
}