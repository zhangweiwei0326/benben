<?php
trait hornTrait{
    public function getHornNum($user){
        $connection=Yii::app()->db;
        $command = $connection->createCommand("select count(*) as c from broadcasting_log where member_id = ".$user->id." and created_time >= ".strtotime(date('Y-m-01', strtotime(date("Y-m-d")))));
        $authority = $command->queryAll();
        $authorityNumber = 30;
        $is_exist=PromotionManageAttach::model()->find("member_id={$user['id']}");
        if($is_exist['small_horn_num']){
            $all_small=$is_exist['small_horn_num'];
        }else{
            $all_small=0;
        }
        if ($authority) {
            $authorityNumber = 30 - $authority[0]['c'] + $all_small;
        }
        return $authorityNumber;
    }
}