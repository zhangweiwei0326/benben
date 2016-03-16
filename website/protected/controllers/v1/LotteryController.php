<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/30
 * Time: 13:48
 */
class LotteryController extends PublicController
{
    public $layout = false;

    /*
     * 抽奖
     */
    public function actionLottery(){
        $benben_id=Frame::getIntFromRequest('benben_id');
        if(empty($benben_id)){
            throw new CHttpException(404);
        }
        $this->render('lotterydraw',array("benben_id"=>$benben_id));
}

    /**
     *初始化抽奖
     *1.初始化金额，2.初始化特等奖，3.初始化活动细则
     **/
    public function actionInitLottery(){
        //初始化金额
        $benben_id = Frame::getIntFromRequest('benben_id');
        $coin='';
        $criteria = new CDbCriteria;
        $criteria->condition='benben_id='.$benben_id;
        // echo '<pre>';
        // var_dump($criteria);
        // exit;
        $data = Member::model() -> find($criteria);
        $coin=$data->coin;
        //初始化特等奖
        $resu=PrizeSetting::model()->find('statues=:status',array(':status'=>'1'));
        // echo '<pre>';
        // var_dump($resu);
        // exit;

        $result['coin']=$coin;
        $result['prize']=$resu->prize;
        echo json_encode($result);
    }
    /*
     * 抽奖
     */
    public function actionDrawLottery(){
        //特等奖为十万分之一
        $max_lottery=100000;
        $benben_id = Frame::getIntFromRequest('benben_id');
        $coin='';
        $criteria = new CDbCriteria;
        $criteria->condition='benben_id='.$benben_id;
        // echo '<pre>';
        // var_dump($criteria);
        // exit;
        $data = Member::model() -> find($criteria);
        $result['benben_id']=$benben_id;
        //抽奖开始

        $coin=$data->coin;

        if($coin<0.5){
            $result['status']=0;
            $result['message']="余额不足抽一次奖";
            echo json_encode($result);
        }
        else{
            $coin=$coin-0.5;
            //扣0.5犇币
            $ret = Member::model()->updateAll (array (
                'coin' =>$coin,
            ), "benben_id=" . $benben_id);
            if($ret){//扣犇币成功
                $temp = rand(0,$max_lottery);
                //奖9
                if($temp==$max_lottery){
                    $result['status']=9;
                    $result['send']=1;
                    $result['num']=9;
                    $result['img_url']="images/money.png";
                    $result['message']="特等实物大奖!";
                }
                //奖8
                if($temp>$max_lottery*0.98&&$temp<$max_lottery){
                    $result['status']=8;
                    $result['send']=0;
                    $result['num']=5;
                    $result['img_url']="images/money.png";
                    $result['message']="获得5犇币!";
                }
                //奖7
                if($temp>$max_lottery*0.96&&$temp<=$max_lottery*0.98){
                    $result['status']=7;
                    $result['send']=0;
                    $result['num']=2;
                    $result['img_url']="images/money.png";
                    $result['message']="获得2犇币!";
                }
                //奖6
                if($temp>$max_lottery*0.95&&$temp<=$max_lottery*0.96){
                    $result['status']=6;
                    $result['send']=0;
                    $result['num']=1;
                    $result['img_url']="images/money.png";
                    $result['message']="获得1犇币!";
                }
                //奖5
                if($temp>$max_lottery*0.9&&$temp<=$max_lottery*0.95){
                    $result['status']=5;
                    $result['send']=0;
                    $result['num']=0.5;
                    $result['img_url']="images/money.png";
                    $result['message']="获得0.5犇币!";
                }
                //奖4
                if($temp>$max_lottery*0.8&&$temp<=$max_lottery*0.9){
                    $result['status']=4;
                    $result['send']=0;
                    $result['num']=0.4;
                    $result['img_url']="images/money.png";
                    $result['message']="获得0.4犇币!";
                }
                //奖3
                if($temp>$max_lottery*0.6&&$temp<=$max_lottery*0.8){
                    $result['status']=3;
                    $result['send']=0;
                    $result['num']=0.3;
                    $result['img_url']="images/money.png";
                    $result['message']="获得0.3犇币!";
                }
                //奖2
                if($temp>$max_lottery*0.4&&$temp<=$max_lottery*0.6){
                    $result['status']=2;
                    $result['send']=0;
                    $result['num']=0.2;
                    $result['img_url']="images/money.png";
                    $result['message']="获得0.2犇币!";
                }
                //奖1
                if($temp>=0 && $temp<=$max_lottery*0.4){
                    $result['status']=1;
                    $result['send']=0;
                    $result['num']=0.1;
                    $result['img_url']="images/money.png";
                    $result['message']="获得0.1犇币!";
                }
            }
            if( $result['status']!=9){
                //加上中奖获得的犇币
                $coin=$coin+$result['num'];
                $ret = Member::model()->updateAll (array (
                    'coin' =>$coin,
                ), "benben_id=" . $benben_id);

            }
            else{
                //抽中特等奖 次数减一
                $ret = PrizeSetting::model()->updateAll (array (
                    'frequency' =>$frequency-1,
                ), "statues=" . 1);
            }
            //存入记录
            $lotteryLog = new LotteryLog();

            // $lotteryLog->benben_id=$result['benben_id'];
            $lotteryLog->benben_id=(int)$benben_id;
            $lotteryLog->lottery_time=Time();
            $lotteryLog->lottery_num=$result['status'];
            $lotteryLog->is_send=$result['send'];
            if($lotteryLog->save()>0){
                $result['coin']=$coin;
                echo json_encode($result);

            }else{
                $result['status']=-1;
                $result['message']="记录保存失败";
                echo json_encode($result);
            }
        }

    }


}