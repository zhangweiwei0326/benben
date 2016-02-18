<?php

class EnterpriseDataAnalysisController extends EnterpriseBaseController
{

    public function actionIndex()
    {
        $session = new CHttpSession();
        $session->open();
        Yii::app()->session->add('backUrl',Yii::app()->request->url);
        // 获得政企通讯录人数
        $enterpriseMemberCount = EnterpriseMember::model()->findAllByAttributes(array(
            'contact_id' => $this->enterprise_id
        ));
        // 获得政企通讯录人数上线
        $enterpriseMemberLimit = EnterpriseRole::model()->findByAttributes(array(
            'enterprise_id' => $this->enterprise_id
        ));
        
        //获得未激活人员信息
        $enterpriseMemberNo = EnterpriseMember::model();
        $criNo  = new CDbCriteria();
        $criNo->select  =  "t.*, b.access_level as access_level , c.name as cname";
        $criNo->join = "left join enterprise_member_manage b on t.id = b.member_id
                                    left join member_group c on b.group_id = c.id";
        $criNo->condition = "t.member_id = 0 and contact_id = ". $this->enterprise_id;
        
        $enterpriseMemberNoActive = $enterpriseMemberNo->findAll($criNo);
        
        // 获得当前月
        $sql = "SELECT d.name as manage_name, b.name as bname, b.remark_name as bremark_name,  d.nick_name as nick_name, c.broadcast_per_month as broadcast_per_month, c.broadcast_available_month as broadcast_available_month FROM benben_test.enterprise_broadcast a 
                    inner join benben_test.enterprise_member b
                    on b.member_id = a.member_id and b.contact_id =  $this->enterprise_id 
                    right join benben_test.enterprise_member_manage c 
                    on b.id = c.member_id  and  c.broadcast_per_month >= 0 and c.broadcast_available_month
                    right join benben_test.member d on a.member_id = d.id
                    where FROM_UNIXTIME(a.created_time, '%Y-%m' ) = date_format(now(),'%Y-%m') and a.enterprise_id = $this->enterprise_id 
                  group by a.member_id";
        
        //获得上一个月
        $sql_last = "SELECT d.name as manage_name,  d.nick_name as nick_name, c.broadcast_per_month as broadcast_per_month, c.broadcast_available_month as broadcast_available_month FROM benben_test.enterprise_broadcast a
                    left join benben_test.enterprise_member b
                    on b.member_id = a.member_id and b.contact_id =  $this->enterprise_id 
                    inner join benben_test.enterprise_member_manage c
                    on b.id = c.member_id
                    left join benben_test.member d on a.member_id = d.id
                    where FROM_UNIXTIME(a.created_time, '%Y-%m' ) = date_format(date_sub(date_sub(date_format(now(),'%y-%m-%d'),interval extract(  
                    day from now())-1 day),interval 1 month), '%Y-%m')  and a.enterprise_id = $this->enterprise_id 
                    group by a.member_id ";
        
        $sql_last_two = "SELECT d.name as manage_name,  d.nick_name as nick_name, c.broadcast_per_month as broadcast_per_month, c.broadcast_available_month as broadcast_available_month FROM benben_test.enterprise_broadcast a
                    left join benben_test.enterprise_member b
                    on b.member_id = a.member_id and b.contact_id =  $this->enterprise_id 
                    inner join benben_test.enterprise_member_manage c
                    on b.id = c.member_id
                    left join benben_test.member d on a.member_id = d.id
                    where FROM_UNIXTIME(a.created_time, '%Y-%m' ) = date_format(date_sub(date_sub(date_format(now(),'%y-%m-%d'),interval extract(
                    day from now())-1 day),interval 2 month), '%Y-%m')  and a.enterprise_id = $this->enterprise_id 
                    group by a.member_id ";
        
        $broadCast = EnterpriseBroadcast::model()->findAllBySql($sql);
        $broadCastLast = EnterpriseBroadcast::model()->findAllBySql($sql_last);
        $broadCastLastTwo = EnterpriseBroadcast::model()->findAllBySql($sql_last_two);
        include "Enterservice.php";
        $enterservice = new Enterservice();
        $enterservice->set_member_id(Yii::app ()->user->getState ( "Enterprise_memberInfo" ));
        $enterservice->set_info();
        $enterservice->set_names();
        $enterservice->set_duration();
        $enterservice->set_view();
        
        $this->render('index', array(
            'enterprise_member_count' => $enterpriseMemberCount,
            'enterprise_member_limit' => $enterpriseMemberLimit->member_limit,
            'broadCast' => $broadCast,
            'broadCastLast' => $broadCastLast,
            'broadCastLastTwo' => $broadCastLastTwo,
            'enterprise_no_active' => $enterpriseMemberNoActive,
            'enterservice'=>$enterservice
        ));
    }
}