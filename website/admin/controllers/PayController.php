<?php
/**
 * Created by PhpStorm.
 * User: Odeen
 * Date: 2016/1/19
 * Time: 19:51
 */
class PayController extends BaseController
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout='//layouts/admin';

    /**
     * @var int the define the index for the menu
     */

    public $menuIndex = 81;

    /**
     * Lists all models.
     */
    public function actionIndex(){
        $this->insert_log(81);
        $model = Pay::model();
        $cri = new CDbCriteria();
        $cri->select="t.*";
//        $cri->join.="left join store_order_goods b on t.order_id=b.order_id ";
        $cri->order = "t.time desc";
//        $cri->addCondition("t.extension_code =3");

        $result['order_status'] = -1;
        $result['service_name'] = -1;
        if(isset($_GET) && !empty($_GET)) {
            $result = array();
            $pay_id = $_GET['pay_id'];
            $type = $_GET['type'];
            $status = $_GET['status'];
            $nick_name = $_GET['nick_name'];
            $phone = $_GET['phone'];
            $account = $_GET['account'];
            $created_time1 = $_GET['created_time1'];
            $created_time2 = $_GET['created_time2'];

            if ($pay_id) {
                $cri->addSearchCondition('t.id', $pay_id, true, 'AND');
                $result['pay_id'] = $pay_id;
            }
            if ($type > -1) {
                $cri->addSearchCondition('t.type', $type, true, 'AND');
                $result['type'] = $type;
            } else {
                $result['type'] = -1;
            }
            if ($status > -1) {
                $cri->addCondition("t.status ={$status}");
                $result['status'] = $status;
            } else {
                $result['status'] = -1;
            }
            if ($nick_name) {
                $cri->join .= "left join member m on t.member_id=m.id ";
                $cri->addCondition("m.nick_name like '%$nick_name%'");
                $result['nick_name'] = $nick_name;
            }
            if ($phone) {
                $cri->join .= "left join member m on t.member_id=m.id ";
                $cri->addCondition("m.phone like '%$phone%'");
                $result['phone'] = $phone;
            }
            if($account){
                $cri->addCondition("t.account like '%$account%'");
                $result['account'] = $account;
            }
            if ($created_time1) {
                $created_time1_tmp=strtotime($created_time1);
                $cri->addCondition("t.time >= '".$created_time1_tmp."' ");
                $result['created_time1']=$created_time1;
            }
            if ($created_time2) {
                $created_time2_tmp=strtotime($created_time2);
                $cri->addCondition("t.time < '".$created_time2_tmp."' ");
                $result['created_time2']=$created_time2;
            }
        }

        $pages = new CPagination();
        $pages->itemCount = $model->count($cri);
        $pages->pageSize = 12;
        $pages->applyLimit($cri);
        $items = $model->findAll($cri);
        $this->render('index', array('items' => $items, 'pages' => $pages, 'result' => $result));
    }

    /**
     * Deletes a particular model.
     * If deletion is successful, the browser will be redirected to the 'admin' page.
     * @param integer $id the ID of the model to be deleted
     */
    public function actionDelete($id){
        $payInfo=Pay::model()->find("id={$id}");
        if(!$payInfo){
            $result['status']=0;
            $result['msg']="该记录不存在！";
        }else{
            $connection=Yii::app()->db;
            $transaction=$connection->beginTransaction();
            try{
                $right=Pay::model()->updateAll(array("status"=>2,"reason"=>"信息不正确，已取消"),"id={$id}");
                if($right) {
                    $minfo = Member::model()->find("id={$payInfo->member_id}");
                    $minfo->fee = $minfo['fee'] + $payInfo['fee'];
                    $minfo->save();
                }
                $transaction->commit();
                $result['status']="1";
            }catch (Exception $e){
                $transaction->rollBack();
                $result['status']=0;
                $result['msg']="网络错误！";
            }
        }
        echo json_encode($result);
        die();
    }

    /**
     * Description of PayController
     * 处理支付宝异步回调
     * @author Administrator
     * 支付宝支付
     */
    public function actionCashOut(){
        $payinfo=Pay::model()->findAll("status=0 and type=1 limit 1000");
        $total=0.00;
        $detail=array();
        foreach ($payinfo as $k => $v) {
            $detail[]=($v['id'])."^".$v['account']."^".$v['pay_name']."^".$v['fee']."^工资";
            $total+=$v['fee'];
            $changestatus['status']=3;//转账中
            Pay::model()->updateAll(array("status"=>1),"id={$v['id']}");
        }
        $this->redirect('index');
    }
//    public function actionCashOut(){
//        include_once('lib/alipay/Corefunction.php');
//        include_once('lib/alipay/Md5function.php');
//        include_once('lib/alipay/Rsafunction.php');
//        include_once('lib/alipay/Notify.php');
//        include_once('lib/alipay/Submit.php');
//
//        header("Content-type:text/html;charset=utf-8");
//        $alipay_config = Yii::app()->params['alipay_config'];
//
//        /**************************请求参数**************************/
//
//        //服务器异步通知页面路径
//        $notify_url = Yii::app()->params['alipay']['cash_notify_url'];
//        //需http://格式的完整路径，不允许加?id=123这类自定义参数
//        //付款账号
//        $email = Yii::app()->params['alipay']['seller_email'];
//        //必填
//
//        //付款账户名"杭州"
//        $account_name = Yii::app()->params['alipay']['account_name'];
//        //必填，个人支付宝账号是真实姓名公司支付宝账号是公司名称
//
//        //付款当天日期
//        $pay_date = date("Ymd",  time());
//        //必填，格式：年[4位]月[2位]日[2位]，如：20100801
//
//        //批次号
//        $batch_no = $pay_date.time();
//        //必填，格式：当天日期[8位]+序列号[3至16位]，如：201008010000001
//        //type=1支付宝
//        $payinfo=Pay::model()->findAll("status=0 and type=1 limit 1000");
//        $total=0.00;
//        $detail=array();
//        foreach ($payinfo as $k => $v) {
//            $detail[]=($v['id'])."^".$v['account']."^".$v['pay_name']."^".$v['fee']."^工资";
//            $total+=$v['fee'];
//            $changestatus['status']=3;//转账中
//            Pay::model()->updateAll(array("status"=>3),"id={$v['id']}");
//        }
//
//        $num=count($detail);
//        $more=  implode("|", $detail);
//
//        //付款总金额
//        $batch_fee = $total;
//        //必填，即参数detail_data的值中所有金额的总和
//
//        //付款笔数
//        $batch_num = $num;
//        //必填，即参数detail_data的值中，“|”字符出现的数量加1，最大支持1000笔（即“|”字符出现的数量999个）
//
//        //付款详细数据
//        $detail_data = $more;
//        //必填，格式：流水号1^收款方帐号1^真实姓名^付款金额1^备注说明1|流水号2^收款方帐号2^真实姓名^付款金额2^备注说明2....
//
//
//        /************************************************************/
//
//        //构造要请求的参数数组，无需改动
//        $parameter = array(
//            "service" => "batch_trans_notify",
//            "partner" => trim($alipay_config['partner']),
//            "notify_url"	=> $notify_url,
//            "email"	=> $email,
//            "account_name"	=> $account_name,
//            "pay_date"	=> $pay_date,
//            "batch_no"	=> $batch_no,
//            "batch_fee"	=> $batch_fee,
//            "batch_num"	=> $batch_num,
//            "detail_data"	=> $detail_data,
//            "_input_charset"	=> trim(strtolower($alipay_config['input_charset']))
//        );
//
//        //建立请求
//        $alipaySubmit = new \AlipaySubmit($alipay_config);
//        $html_text = $alipaySubmit->buildRequestForm($parameter,"post", "处理中，请勿点击！");
//        echo $html_text;
//    }

    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionBackOrder(){
        $this->insert_log(121);
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
        $html_text = $alipaySubmit->buildRequestForm($parameter,"post", "确认");
        echo $html_text;
    }
}