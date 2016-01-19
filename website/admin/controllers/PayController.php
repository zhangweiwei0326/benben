<?php
/**
 * Created by PhpStorm.
 * User: Odeen
 * Date: 2016/1/19
 * Time: 19:51
 */
class PayController extends Controller
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout='//layouts/admin';

    /**
     * @var int the define the index for the menu
     */

    public $menuIndex = 0;
    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionBackOrder(){
        header("Content-type: text/html; charset=utf-8");
        include_once('lib/alipay/Corefunction.php');
        include_once('lib/alipay/Md5function.php');
        include_once('lib/alipay/Rsafunction.php');
        include_once('lib/alipay/Notify.php');
        include_once('lib/alipay/Submit.php');

        $order_id_arr = Frame::getStringFromRequest("order_id");//以逗号隔开
        if(empty($order_id_arr)){
            $result['status']=2016;
            $result['err']="缺少参数！";
            echo json_encode($result);
            die();
        }
        $deal_time=time();
        $backinfo = DrawBack::model()->findAll("order_id in ({$order_id_arr}) and status=5");
        if(empty($backinfo)){
            $result['status']=2111;
            $result['err']="该订单不存在！";
            echo json_encode($result);
            die();
        }
        if(count($backinfo)>1000){
            $result['status']=2211;
            $result['err']="1次最多处理1000笔交易！";
            echo json_encode($result);
            die();
        }
        $back_arr=array();
        foreach($backinfo as $kb=>$vb){
            $back_arr[]=$vb['order_id'];
        }
        //===========================================支付宝退款操作================================================
        $alipay_config=Yii::app()->params['alipay_config'];
        $alipay_info=Yii::app()->params['alipay'];
        /**************************请求参数**************************/

        //服务器异步通知页面路径
        $notify_url = $alipay_info['back_notify_url'];
        //需http://格式的完整路径，不允许加?id=123这类自定义参数

        //卖家支付宝帐户
        $seller_email = $alipay_info['seller_email'];
        //必填

        //退款当天日期
        $refund_date = date("Y-m-d H:i:s",$deal_time);
        //必填，格式：年[4位]-月[2位]-日[2位] 小时[2位 24小时制]:分[2位]:秒[2位]，如：2007-10-01 13:13:13

        //批次号
        $batch_no =date("YmdHi",$deal_time).$backinfo[0]['back_id'];
        //必填，格式：当天日期[8位]+序列号[3至24位]，如：201008010000001

        $loginfo=PayLog::model()->findAll("order_id in (".implode(",",$back_arr).") limit 1000");
        if(empty($loginfo)){
            $result['status']=2111;
            $result['err']="该订单不存在！";
            echo json_encode($result);
            die();
        }
        //退款笔数
        $batch_num =count($loginfo);
        //必填，参数detail_data的值中，“#”字符出现的数量加1，最大支持1000笔（即“#”字符出现的数量999个）

        //退款详细数据
        $detail_data="";
        $flag=0;
        foreach($loginfo as $k=>$v){
            $detail_data.= $loginfo['trade_no']."^".$loginfo['order_amount']."^退款";
            $flag++;
            if($flag<$batch_num){
                $detail_data.="#";
            }
        }

        $transaction = Yii::app()->db->beginTransaction(); //创建事务
        try{
            foreach($backinfo as $ki=>$vi){
                DrawBack::model()->updateAll(array("status"=>4,"deal_time"=>$deal_time),"order_id=".$vi['order_id']);
            }
            $transaction->commit(); //提交事务
        }catch(Exception $e){
            $transaction->rollback(); //回滚事务
            $result ['ret_num'] = 3020;
            $result ['ret_msg'] = '保存失败，请重新尝试！';
            echo json_encode($result);
            die();
        }

        //必填，具体格式请参见接口技术文档，支付流水单号^钱^原因#支付流水单号^钱^原因.....
        /************************************************************/

        //构造要请求的参数数组，无需改动
        $parameter = array(
            "service" => "refund_fastpay_by_platform_pwd",
            "partner" => trim($alipay_config['partner']),
            "notify_url"	=> $notify_url,
            "seller_email"	=> $seller_email,
            "refund_date"	=> $refund_date,
            "batch_no"	=> $batch_no,
            "batch_num"	=> $batch_num,
            "detail_data"	=> $detail_data,
            "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
        );
        //建立请求
        $alipaySubmit = new \AlipaySubmit($alipay_config);
        $html_text = $alipaySubmit->buildRequestForm($parameter,"get", "确认");
        echo $html_text;
    }
}