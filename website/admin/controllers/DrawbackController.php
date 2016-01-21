<?php
Yii::$enableIncludePath = false;
define('__ROOT__',dirname(dirname(__FILE__)));
require_once (__ROOT__.'/PHPExcel/PHPExcel.php');
date_default_timezone_set('PRC');//时区设置
class DrawbackController extends BaseController
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout='//layouts/admin';

    /**
     * @var int the define the index for the menu
     */

    public $menuIndex = 92;
    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionIndex(){
        $this->insert_log(92);
        $model = DrawBack::model();
        $result = array();
        $cri = new CDbCriteria();

        //获取参数
        $status_num = intval($_GET['status']);
        $created_time1 = $_GET['created_time1'];
        $created_time2 = $_GET['created_time2'];
        $created_time1 = strtotime($created_time1);
        $created_time2 = strtotime($created_time2);
        //参数判断
        if(isset($_GET['status'])&&$status_num != -1){
            if($status_num >= 0){
                $status_num=$status_num+1;
                $cri->addCondition('t.status ='.$status_num,'AND');
                $result['status'] = $status_num-1;
            }
        }
        if($created_time1 && $created_time2){
            $cri->addBetweenCondition('apply_time',$created_time1,$created_time2);
            $result['created_time1'] = date('Y-m-d H:i:s',$created_time1);
            $result['created_time2'] = date('Y-m-d H:i:s',$created_time2);
        }

        //数据查询
        $cri->select = "t.*,member.name as name,member.phone as phone,number_train.short_name as shop_name,pay_log.buyer_email as account";
        $cri->join = "left join member on member.id = t.apply_id left join pay_log on pay_log.order_id = t.order_id left join number_train on number_train.id=t.train_id";
        $cri->order = "apply_time desc";
        $pages = new CPagination();
        $pages->itemCount = $model->count($cri);
        $pages->pageSize = 10;
        $pages->applyLimit($cri);
        $items = $model->findAll($cri);
        //渲染页面
        $this->render('index',array('items'=>$items,'pages'=> $pages,'result' => $result,));
    }


    public function actionRefuseDrawBack(){
        $back_id = Frame::getIntFromRequest('back_id');
        $deal_time = Frame::getIntFromRequest('deal_time');
        //访问数据库操作
        $ret = DrawBack::model()->updateAll (array (
            'status' =>3,
            'deal_time' =>$deal_time,
        ), "back_id=" . $back_id);
        if($ret){
            $result['status']=1;
            $result['deal_time']=$deal_time;
            echo json_encode($result);
        }else {
            $result['status']=0;
            echo json_encode($result);;

        }
    }
}