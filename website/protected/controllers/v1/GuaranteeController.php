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
            $pm->vip_type = 2;
            $pm->guarantee = $guarantee;
            $pm->save();
            $minfo=Member::model()->find("id={$user['id']}");
            $minfo->guarantee=$minfo->guarantee+$guarantee;
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
}