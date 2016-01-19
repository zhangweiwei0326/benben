<?php
Yii::$enableIncludePath = false;
define('__ROOT__',dirname(dirname(__FILE__)));
require_once (__ROOT__.'/PHPExcel/PHPExcel.php');
date_default_timezone_set('PRC');//时区设置
class AuctionController extends BaseController
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout='//layouts/admin';

    /**
     * @var int the define the index for the menu
     */

    public $menuIndex = 91;
    /**
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionIndex()
    {
        $this->insert_log(91);
        $model = Auction::model();
        $result = array();
        $cri = new CDbCriteria();
        $province = $this->getProvince();
        //获取参数
        $industry = intval($_GET['industry']);
        $post_province = intval($_GET['province']);
        $post_city = intval($_GET['city']);
        $post_area = intval($_GET['area']);
        $status = intval($_GET['status_num']);
        $ten_day = intval($_GET['tenDay']);
        //行业
        if($industry > 0){
                $result['industry'] = $industry;
            }
        $info =  Industry::model ()->findAll ('parent_id = 0');
        $industryInfo = array();
        foreach ($info as $key => $value) {
                $industryInfo[$value['id']] = $value['name'];
        }
        //参数判断
        if($industry > 0){
            $cri->addSearchCondition('t.industry', $industry, true, 'AND');
            $result['industry'] = $industry;
        }
        if ($post_province > 0) {
            $res = $this->getCity($post_province);
        }
        if ($post_city > 0) {
            $res2 = $this->getArea($post_city);
        }
        if ($post_province && ($post_province != -1)) {
            $cri->addCondition('t.province = ' . $post_province, 'AND');
            $result['province'] = $post_province;
        }
        if ($post_city && ($post_city != -1)) {
            $cri->addCondition('t.city = ' . $post_city, 'AND');
            $result['city'] = $post_city;
        }
        if ($post_area && ($post_area != -1)) {
            $cri->addCondition('t.area = ' . $post_area, 'AND');
            $result['area'] = $post_area;
        }
        $now =time();
        if(isset($_GET['status_num']) && ($status != -1)){
            if($status == 0){//成交
               
                $cri->addCondition('t.is_close = 1','AND');
                $cri->addCondition('t.is_paid = 1','AND');
                $cri->addCondition('t.end_time < ' .$now.'','AND');
                $result['status'] = 0;
            }
            if($status == 1){//流拍
                $cri->addCondition('t.is_close = 1','AND');
                $cri->addCondition('t.is_paid = 0','AND');
                $cri->addCondition('t.end_time < ' .$now.'','AND');
                $result['status'] = 1;
            }
            if($status == 2){//进行中
                $cri->addCondition('t.is_close = 0','AND');
                $cri->addCondition('t.is_paid = 0','AND');
                $cri->addCondition('t.start_time < ' .$now.'','AND');
                $cri->addCondition('t.end_time > ' .$now.'','AND');
                $cri->addCondition('t.is_paid =0 ','AND');
                $result['status'] = 2;
            }
            if($status == 3){//等待中
                $cri->addCondition('t.is_close = 0','AND');
                $cri->addCondition('t.is_paid = 0','AND');
                $cri->addCondition('t.start_time > ' .$now.'','AND');
                $result['status'] = 3;
            }
            if($status == 4){//未开始
                $cri->addCondition('t.is_close = 1','AND');
                $cri->addCondition('t.is_paid = 0','AND');
                $cri->addCondition('t.start_time > ' .$now.'','AND');
                $result['status'] = 4;
            }

        }
        if($ten_day > 0){
            $now =time();
            $time_ten=$now+$ten_day*24*10*60*60;
            $cri->addCondition('t.end_time <= ' . $time_ten, 'AND');
        }
        //数据查询
        $cri->select = "t.*,auction_log.price as price";
        $cri->join = "left join auction_log on auction_log.auction_id = t.auction_id";
        $cri->order = "auction_id";
        $pages = new CPagination();
        $pages->itemCount = $model->count($cri);
        $pages->pageSize = 10;
        $pages->applyLimit($cri);
        $items = $model->findAll($cri);

        // var_dump($items);
        $areaInfo = array();
        if ($items) {
            //地区
            $areaItem = array();
            foreach ($items as $key => $value) {
                $areaItem[] = $value ['province'];
                $areaItem[] = $value ['city'];
                $areaItem[] = $value ['area'];
            }
           
            $area = new Area();
            $sql = "select bid, area_name from area where bid in (" . implode(",", $areaItem) . ")";
            $areaResult = $area->findAllBySql($sql);
            foreach ($areaResult as $key => $value) {
                $areaInfo[$value['bid']] = $value['area_name'];
            }
        }
        $this->render('index', array('items'=>$items,'result' => $result,'res' => $res, 'res2' => $res2,'province' => $province,'areaInfo' => $areaInfo,'industryInfo' => $industryInfo,'pages'=> $pages,'status'=>$status));
    }

    public function actionCloseAuction(){
        $auction_id = Frame::getIntFromRequest('auction_id');
        
        $edit_url = Yii::app()->createUrl('auction/edit',array('auction_id'=>$auction_id));
        
        //访问数据库操作
        $ret = Auction::model()->updateAll (array (
                                        'end_time' =>strtotime(time())-1,
                                        'is_close' => 1
                                ), "auction_id=" . $auction_id);
        if($ret){
            $result['status']=1;
            $result['url']= $edit_url;
            echo json_encode($result);
        }else {
            $result['status']=0;
            $result['url']= $edit_url;
            echo json_encode($result);;

        }
    }


    public function actionOpenAuction(){
        $auction_id = Frame::getIntFromRequest('auction_id');

        $edit_url = Yii::app()->createUrl('auction/edit',array('auction_id'=>$auction_id));
        
        //访问数据库操作
        $ret = Auction::model()->updateAll (array (
                                        'is_close' => 0
                                ), "auction_id=" . $auction_id);
        if($ret){
            $result['status']=1;
            $result['url']= $edit_url;
            echo json_encode($result);
        }else {
            $result['status']=0;
            $result['url']= $edit_url;
            $result['id']=$auction_id;
            echo json_encode($result);;

        }
        
    }

    public function actionEdit()
    {
        $this->insert_log(91);
        $model = Auction::model();
        $result = array();
        $cri = new CDbCriteria();
        $province = $this->getProvince();
        //获取参数
        $auction_id = intval($_GET['auction_id']);
        $cri->addCondition('t.auction_id = ' . $auction_id, 'AND');
        $cri->select = "t.*";
        $item = $model -> findAll($cri);
        //渲染页面
        if($item){
            //地区
            $areaItem = array();
            foreach ($item as $key => $value) {
                $areaItem[] = $value ['province'];
                $areaItem[] = $value ['city'];
                $areaItem[] = $value ['area'];
                $item[$key]['start_time'] = date("Y/m/d H:i:s",$value ['start_time']);
                $item[$key]['end_time'] = date("Y/m/d H:i:s",$value ['end_time']);
                $item[$key]['top_start_period'] = date("Y/m/d H:i:s",$value ['top_start_period']);
                $item[$key]['top_end_period'] = date("Y/m/d H:i:s",$value ['top_end_period']);
            }
           
            $area = new Area();
            $sql = "select bid, area_name from area where bid in (" . implode(",", $areaItem) . ")";
            $areaResult = $area->findAllBySql($sql);
            foreach ($areaResult as $key => $value) {
                $areaInfo[$value['bid']] = $value['area_name'];
            }
            //行业
            if($industry > 0){
                $result['industry'] = $industry;
            }
            $info =  Industry::model ()->findAll ('parent_id = 0');
            $industryInfo = array();
            foreach ($info as $key => $value) {
                $industryInfo[$value['id']] = $value['name'];
            }
        }
        $this->render('edit', array('auction_id'=>$auction_id,'item'=>$item, 'province' => $province,'res' => $res, 'res2' => $res2,
               'areaInfo' => $areaInfo,'industryInfo' => $industryInfo,'status'=>$status));
    }

    public function actionSaveAuction(){

        $auction_id = Frame::getIntFromRequest('auction_id');

        $start_time = Frame::getIntFromRequest('start_time');
        $end_time = Frame::getIntFromRequest('end_time');
        $start_price = Frame::getIntFromRequest('start_price');
        $add_step = Frame::getIntFromRequest('add_step');
        $guarantee = Frame::getIntFromRequest('guarantee');
        $place = Frame::getIntFromRequest('place');
        $top_start_period = Frame::getIntFromRequest('top_start_period');
        $top_end_period = Frame::getIntFromRequest('top_end_period');
        $is_close = Frame::getIntFromRequest('is_close');
        $is_paid = Frame::getIntFromRequest('is_paid');

        //访问数据库操作
        $ret = Auction::model()->updateAll (array (
                                        'start_time' => $start_time,
                                        'end_time' => $end_time,
                                        'start_price' => $start_price,
                                        'add_step' => $add_step,
                                        'guarantee' => $guarantee,
                                        'place' => $place,
                                        'top_start_period' =>$top_start_period,
                                        'top_end_period' => $top_end_period,
                                        'is_close' => $is_close,
                                        'is_paid' => $is_paid,
                                ), "auction_id={$auction_id}");
        if($ret >=0 ){
            $result['status']=1;
            // $result['start_time']=$start_time;
            // $result['end_time']=$end_time;
            // $result['top_start_period']=$top_start_period;
            // $result['top_end_period']=$top_end_period;

            $result['url']= $edit_url;
            echo json_encode($result);
        }else {
            $result['status']=0;
            $result['url']= $edit_url;
            echo json_encode($result);;
        }
    }
}
