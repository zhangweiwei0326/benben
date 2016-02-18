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
    public function actionDrawLottery(){

    $this->render('lotterydraw',array());
}


}