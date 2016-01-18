<?php
/**
 * Created by PhpStorm.
 * User: Odeen
 * Date: 2015/12/14
 * Time: 10:10
 */
class PayController extends PublicController
{
    public $layout = false;

    /*
     * 支付方式查询
     * 涉及Payment
     */
    public function actionPaymethods(){
        $this->check_key();
        $user = $this->check_user();
        $paymethods=Payment::model()->findAll("enabled=1");
        foreach($paymethods as $k=>$v){
            $pay[]=array(
                "pay_id"=>  $v['pay_id'],
                "pay_name"=> $v['pay_name'],
            );
        }
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result ['pay_methods'] = $pay;//id=1需要填写快递费
        echo json_encode($result);
    }
}