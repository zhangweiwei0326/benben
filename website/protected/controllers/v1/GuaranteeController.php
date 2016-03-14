<?php
class GuaranteeController extends PublicController
{
    public $layout = false;
    /**
     * 保证金规则页面
     */
    public function actionGuaranteeIntroduce(){
        header("Content-type: text/html; charset=utf-8");
        $this->render("introduce");
    }

    /**
     * 查询保证金缴纳额度
     * industry表
     */
    public function actionSearchGuarantee(){
        $this->check_key();
        $user=$this->check_user();
        $trainInfo=NumberTrain::model()->find("member_id={$user['id']} and status=0 and is_close=0");
        if(!$trainInfo){
            $result['ret_num'] = 2116;
            $result['ret_msg'] = '您可能未开通号码直通车或者已经关闭号码直通车';
            echo json_encode( $result );
            die();
        }
        $ginfo=Industry::model()->find("id={$trainInfo['industry']}");
        if(!$ginfo) {
            $result['ret_num'] = 2121;
            $result['ret_msg'] = '暂不支持该行业！';
            echo json_encode($result);
            die();
        }
        $pmInfo=PromotionManage::model()->find("member_id={$user['id']}");
        if(!$pmInfo){
            $result['ret_num'] = 3121;
            $result['ret_msg'] = '您暂未开通商家功能！';
            echo json_encode($result);
            die();
        }
        $result['ret_num'] = 0;
        $result['ret_msg'] = '操作成功！';
        $result['guarantee'] = $ginfo['guarantee'];
        $result['industry'] = $ginfo['id'];
        $result['rest'] = $pmInfo['guarantee']?$pmInfo['guarantee']:"0";
        echo json_encode($result);
    }

    /**
     * 缴纳保证金，不足充值后再缴纳
     * member表中的总保证金累加，promotion_manage表中的保证金累加，vip_type改变
     */
    public function actionGiveGuarantee(){
        $this->check_key();
        $user=$this->check_user();
        $industry = Frame::getIntFromRequest('industry');
        $guarantee = Frame::getStringFromRequest('guarantee');
        if(empty($industry)||empty($guarantee)){
            $result['ret_num'] = 2016;
            $result['ret_msg'] = '缺少参数';
            echo json_encode( $result );
            die();
        }
        $ginfo=Industry::model()->find("id={$industry}");
        if($ginfo['guarantee']>$guarantee){
            $result['ret_num'] = 2111;
            $result['ret_msg'] = '保证金小于要求！';
            echo json_encode( $result );
            die();
        }
        $connection=Yii::app()->db;
        $transaction=$connection->beginTransaction();
        try {
            $pm = PromotionManage::model()->find("member_id={$user['id']}");
            if(!$pm){
                $result['ret_num'] = 3121;
                $result['ret_msg'] = '您暂未开通商家功能！';
                echo json_encode($result);
//                throw new Exception("Value must be 1 or below");
                die();
            }
            $pm->vip_type = 2;
            $pm->guarantee = $pm->guarantee+$guarantee;

            $minfo=Member::model()->find("id={$user['id']}");
            if($minfo->fee<$guarantee){
                $result['ret_num'] = 3331;
                $result['ret_msg'] = '余额不足请充值！';
                echo json_encode($result);
//                throw new Exception("Value must be 1 or below");
                die();
            }
            $minfo->guarantee=$minfo->guarantee+$guarantee;
            $minfo->fee= $minfo->fee-$guarantee;

            $pm->save();
            $minfo->update();
            $transaction->commit(); //提交事务会真正的执行数据库操作
            $result['ret_num'] = 0;
            $result['ret_msg'] = '操作成功！';
            echo json_encode($result);
        }catch (Exception $e){
            $transaction->rollback(); //如果操作失败, 数据回滚
            $result['ret_num'] = 2000;
            $result['ret_msg'] = '操作失败！';
            echo json_encode($result);
        }
    }

    /**
     * 解冻保证金
     */
    public function actionReleaseGuarantee(){
        $this->check_key();
        $user=$this->check_user();
        $pm=PromotionManage::model()->find("member_id={$user['id']}");
        if(!$pm){
            $result['ret_num'] = 3121;
            $result['ret_msg'] = '您暂未开通商家功能！';
            echo json_encode($result);
            die();
        }
        //所有订单都结束(15天为限)
        $sql="select count(1) as num from store_order_info as a left join store_order_goods as b
        on a.order_id=b.order_id where b.store_id=".$pm['store_id']." and a.pay_time>".(time()-15*86400);
        $connection=Yii::app()->db;
        $command=$connection->createCommand($sql);
        $res=$command->queryAll();
        if($res[0]['num']){
            $result['ret_num'] = 3287;
            $result['ret_msg'] = '您有订单还未结束！';
            echo json_encode($result);
            die();
        }

        //会员到期判断
        $pinfo=PromotionManageAttach::model()->find("member_id={$user['id']} and is_member_ico=1 and overdue_date>".time());
        if($pinfo){
            if($pinfo->overdue_date>=time()){
                $result['ret_num'] = 3337;
                $result['ret_msg'] = '您的会员有效期还未到期！';
                echo json_encode($result);
                die();
            }
        }

        $connection=Yii::app()->db;
        $transaction=$connection->beginTransaction();
        try{
            $guarantee=$pm['guarantee'];
            $pmInfo=PromotionManage::model()->find("member_id={$user['id']}");

            $minfo=Member::model()->find("id={$user['id']}");
            $minfo->guarantee=$minfo['guarantee']-$guarantee;
            $minfo->fee=$minfo['fee']+$guarantee;
            $minfo->save();

            $pmInfo->vip_type= $pmInfo->store_type;
            $pmInfo->guarantee=0;
            $pmInfo->save();
            $transaction->commit(); //提交事务会真正的执行数据库操作

            $result['ret_num'] = 0;
            $result['ret_msg'] = '操作成功！';
            echo json_encode($result);
            die();
        }catch (Exception $e){
            $transaction->rollback(); //如果操作失败, 数据回滚
            $result['ret_num'] = 1000;
            $result['ret_msg'] = '操作失败！';
            echo json_encode($result);
            die();
        }
    }
}