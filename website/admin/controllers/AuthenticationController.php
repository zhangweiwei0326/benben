<?php
Yii::$enableIncludePath = false;
define('__ROOT__',dirname(dirname(__FILE__)));
require_once (__ROOT__.'/PHPExcel/PHPExcel.php');
date_default_timezone_set('PRC');//时区设置
class AuthenticationController extends BaseController
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout='//layouts/admin';

    /**
     * @var int the define the index for the menu
     */

    public $menuIndex = 93;
    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionIndex()
    {
        $this->insert_log(93);
        $model = Authentication::model();
        $result = array();
        $cri = new CDbCriteria();

        //获取参数
        $status = intval($_GET['status']);
        $type = intval($_GET['type']);
        $created_time1 = $_GET['created_time1'];
        $created_time2 = $_GET['created_time2'];
        $created_time1 = strtotime($created_time1);
        $created_time2 = strtotime($created_time2);
        $store_no = $_GET['store_no'];
        //参数处理
        if(isset($_GET['status'])&&$status != -1){
            $cri->addCondition('t.status ='.$status,'AND');
            $result['status'] = $status;
        }
        if(isset($_GET['type'])&&$type != -1){
            $type=$type+1;
            $cri->addCondition('t.type ='.$type,'AND');
            $result['type'] = $type-1;
        }
        if($created_time1 && $created_time2){
            $cri->addBetweenCondition('time',$created_time1,$created_time2);
            $result['created_time1'] = date('Y-m-d H:i:s',$created_time1);
            $result['created_time2'] = date('Y-m-d H:i:s',$created_time2);
        }
        if($store_no){
            if(preg_match('/\d+/',$store_no,$arr)){
                $store_no=$arr[0];
            }
            $cri->addCondition('member.benben_id ='.$store_no,'AND');
            $result['store_no'] = $store_no;
        }

        //访问数据库
        //数据查询
        $cri->select = "t.*,member.benben_id as store_no";
        $cri->join = "left join member on member.id = t.member_id";
        $cri->order = "time desc";
        $pages = new CPagination();
        $pages->itemCount = $model->count($cri);
        $pages->pageSize = 10;
        $pages->applyLimit($cri);
        $items = $model->findAll($cri);

        $this->insert_log(93);
        $this->render('index',array('items'=>$items,'pages'=> $pages,'result' => $result,));
    }

    //通过认证
    public function actionAcceptAuthentication()
    {
        $id = Frame::getIntFromRequest('id');
        //$deal_time = Frame::getIntFromRequest('deal_time');
        //访问数据库操作
        $ret = Authentication::model()->updateAll (array (
            'status' =>2,
            //'deal_time' =>$deal_time,
        ), "id=" . $id);
        if($ret){
            $result['status']=1;
            //$result['deal_time']=$deal_time;
            echo json_encode($result);
        }else {
            $result['status']=0;
            echo json_encode($result);;

        }

    }

    //拒绝认证
    public function actionRefuseAuthentication()
    {
        $id = Frame::getIntFromRequest('id');
        //$deal_time = Frame::getIntFromRequest('deal_time');
        //访问数据库操作
        $ret = Authentication::model()->updateAll (array (
            'status' =>1,
            //'deal_time' =>$deal_time,
        ), "id=" . $id);
        if($ret){
            $result['status']=1;
            //$result['deal_time']=$deal_time;
            echo json_encode($result);
        }else {
            $result['status']=0;
            echo json_encode($result);;

        }

    }

    //详情页面
    public function actionDetail(){
        $this->insert_log(93);
        $model = Authentication::model();
        $result = array();
        $cri = new CDbCriteria();
        //获取参数
        $id = intval($_GET['id']);
        $cri->addCondition('t.id = ' . $id, 'AND');
        $cri->select = "t.*,member.benben_id as store_no";
        $cri->join = "left join member on member.id = t.member_id";
        $cri->order = "id";

        $item = $model->findAll($cri);
        $this->render('detail',array('item'=>$item));
    }
}
