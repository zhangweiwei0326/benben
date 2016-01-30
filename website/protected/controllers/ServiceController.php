<?php
include "service.php";
class ServiceController extends PublicController
{
    // public $layout = false;

    public function actionIndex()
    {
//        service::PayRecode(142246762);exit;
        if (!Yii::app()->user->getState("memberInfo")) {
            $this->redirect('/index.php/site/login');
        }
        $member_info=Yii::app()->user->getState("memberInfo");
//        $member_id=76;
//        $store = PromotionManage::model()->findByAttributes(array("member_id"=>$member_info->id,"is_close"=>0));
        $store = 0;
        $url = "/index.php/service/serviceDetail?type=";
        $this->render("index",array("store"=>$store?"1":"0","url"=>$url,"member_info"=>$member_info));
    }

    public function actionServiceDetail()
    {
        if (!Yii::app()->user->getState("memberInfo")) {
            $this->redirect('/index.php/site/login');
        }
        $member_id=Yii::app()->user->getState("memberInfo")->id;
        $coin=Member::model()->find("id={$member_id}")->coin;
        $type = Frame::getIntFromRequest("type");
        $renewals = Frame::getIntFromRequest("renewals");
        $member_info=Yii::app()->user->getState("memberInfo");
        $service_detail = new service();
        $service_detail->set_member_id($member_info->id);
        $service_detail->set_renewals($renewals);
        $service_detail->set_vip_info($type);
        if($type != $service_detail->type){
            $this->redirect('/index.php/service?store=2');
            exit;
        }
        $service_detail->set_type();
        if((!in_array($type,array(12,14))) && ($service_detail->is_vip == 0)){
            $this->redirect('/index.php/service?store=1');
            exit;
        }
        $service_detail->set_names();
        $service_detail->set_duration();
        $service_detail->set_content();
        if(in_array($service_detail->info->type, array(12, 13))) {
            $duration = "永久有效";
            $duration1 = 0;
        }else if(in_array($service_detail->info->type, array(0, 1,10))){
            if(!$service_detail->vip_pro->vip_time || $service_detail->vip_pro->vip_time<time()){
                $service_detail->vip_pro->vip_time = time();
            }
            if(($service_detail->vip_pro->store_type == 0) && ($service_detail->info->type == 1)){
                $service_detail->vip_pro->vip_time = time();
            }
            $duration = date("Y-m-d", $service_detail->vip_pro->vip_time);
            $duration1 = $service_detail->vip_pro->vip_time;
        }else if(in_array($service_detail->info->type, array(11,14))){
            if($service_detail->vip_info->overdue_date){
                if(($renewals == 1)&&($service_detail->vip_info->overdue_date>=time())){
                    $duration = date("Y-m-d", $service_detail->vip_info->overdue_date);
                    $duration1 = $service_detail->vip_info->overdue_date;
                }else{
                    $duration = date("Y-m-d", time());//date("Y-m-d", $service_detail->vip_info->overdue_date);
                    $duration1 = time();//$service_detail->vip_info->overdue_date;
                }

            }else{
                $duration = date("Y-m-d", time());
                $duration1 = time();
            }

        }
        $this->render("detail",array("detail"=>$service_detail,"coin"=>$coin,
            "duration"=>$duration,"duration1"=>$duration1,
            "store_num"=>($service_detail->type==11)?$service_detail->vip_info->store_num:0));
    }


}
