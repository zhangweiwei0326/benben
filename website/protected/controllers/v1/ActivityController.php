<?php

class ActivityController extends PublicController
{
    public $layout = false;

    /*
     * 活动现场相册
     * 涉及activity、activity_attachment表
     */
    public function actionActivityalbum()
    {
        $this->check_key();
        $user = $this->check_user();
        $id=Frame::getIntFromRequest("id");//号码直通车id
        $ninfo=NumberTrain::model()->find("id={$id}");
        if (!$this->storevip($ninfo['member_id'])) {
            $result['ret_num'] = 1110;
            $result['ret_msg'] = "已过商城试用期";
            echo json_encode($result);
            die();
        }

        $connection=Yii::app()->db;
        $maxalbun=6;//最多6个相册
        $maxpic=20;//最多20张图片
        $resulta=array();

        $sql="select * from activity where member_id={$ninfo['member_id']}";
        $command = $connection->createCommand($sql);
        $resulta = $command->queryAll();

        if($resulta) {
            foreach ($resulta as $k => $v) {
                $resulta[$k]['poster_cover']=$v['poster_cover'] ? URL.$v['poster_cover'] :"";
                $resulta[$k]['small_poster_cover']=$v['poster_cover'] ? URL.$this->getThumb($v['poster_cover']) :"";
            }
        }

        $result['ret_num'] = 0;
        $result['ret_msg'] = "操作成功";
        $result['maxalbun']=$maxalbun;
        $result['maxpic']=$maxpic;
        $result['album_info'] = $resulta;
        echo json_encode($result);

    }

    /*
     * 增加相册/首次最多传6张照片
     * 涉及activity、activity_attachment表
     */
    public function actionAddalbum(){
        $this->check_key();
        $user = $this->check_user();
        if (!$this->storevip($user['id'])) {
            $result['ret_num'] = 1110;
            $result['ret_msg'] = "您已过商城试用期";
            echo json_encode($result);
            die();
        }

        $title = Frame::getStringFromRequest('title');
        $poster_cover = Frame::saveImage('poster_cover',1);
        $pic[] = Frame::saveImage('pic1', 1);
        $pic[] = Frame::saveImage('pic2', 1);
        $pic[] = Frame::saveImage('pic3', 1);
        $pic[] = Frame::saveImage('pic4', 1);
        $pic[] = Frame::saveImage('pic5', 1);
        $pic[] = Frame::saveImage('pic6', 1);

        if(empty($title)||empty($poster_cover)){
            $result['ret_num'] = 2015;
            $result['ret_msg'] = "缺少参数";
            echo json_encode($result);
        }
        $album=new Activity();
        $album->member_id=$user['id'];
        $album->title=$title;
        $album->poster_cover=$poster_cover;
        $album->time=time();
        $album->is_close=0;

        //照片数量
        $num=0;
        foreach($pic as $vv){
            if($vv){
                $num++;
            }
        }
        $album->poster_num=$num;
        if($album->save()){
            if($pic) {
                foreach ($pic as $v) {
                    if ($v) {
                        $attachement = new ActivityAttachment();
                        $attachement->activity_id = $album->id;
                        $attachement->poster = $v;
                        $attachement->save();
                    }
                }
            }
            $result['ret_num'] = 0;
            $result['ret_msg'] = "操作成功";
            echo json_encode($result);
        }
    }

    /*
     * 删除相册
     * 涉及activity、activity_attachment表
     */
    public function actionDelalbum(){
        $this->check_key();
        $user = $this->check_user();
        if (!$this->storevip($user['id'])) {
            $result['ret_num'] = 1110;
            $result['ret_msg'] = "您已过商城试用期";
            echo json_encode($result);
            die();
        }

        $activity_id = Frame::getIntFromRequest('activity_id');
        if(empty($activity_id)){
            $result['ret_num'] = 2015;
            $result['ret_msg'] = "缺少参数";
            echo json_encode($result);
            die();
        }

        $ainfo=Activity::model()->find("id={$activity_id}");
        if($ainfo){
            $ainfo->delete();
            $attinfo=ActivityAttachment::model()->findAll("activity_id={$activity_id}");
            if($attinfo){
                foreach($attinfo as $k){
                    unlink(ROOT.$k['poster']);
                    unlink(ROOT.$this->getThumb($k['poster']));
                }
                ActivityAttachment::model()->deleteAll("activity_id={$activity_id}");
            }
        }
        $result['ret_num'] = 0;
        $result['ret_msg'] = "操作成功";
        echo json_encode($result);
    }

    /*
     * 编辑相册
     * 修改内容
     * 包括修改封面和修改相册名称
     */
    public function actionEditalbum(){
        $this->check_key();
        $user = $this->check_user();
        if (!$this->storevip($user['id'])) {
            $result['ret_num'] = 1110;
            $result['ret_msg'] = "您已过商城试用期";
            echo json_encode($result);
            die();
        }
        $activity_id = Frame::getIntFromRequest('activity_id');
        if(empty($activity_id)){
            $result['ret_num'] = 2015;
            $result['ret_msg'] = "缺少参数";
            echo json_encode($result);
            die();
        }

        $title = Frame::getStringFromRequest('title');
        $poster_cover = Frame::saveImage('poster_cover',1);

        $ainfo=Activity::model()->find("id={$activity_id}");
        if(!$ainfo){
            $result['ret_num'] = 1115;
            $result['ret_msg'] = "该相册不存在";
            echo json_encode($result);
            die();
        }

        if($title){
            $ainfo->title=$title;
            $ainfo->time=time();
            $ainfo->update();
        }

        if($poster_cover){
            unlink(ROOT.$ainfo->poster_cover);
            unlink(ROOT.$this->getThumb($ainfo->poster_cover));
            $ainfo->poster_cover=$poster_cover;
            $ainfo->update();
        }

        $result['ret_num'] = 0;
        $result['ret_msg'] = "操作成功";
        echo json_encode($result);
    }

    /*
     * 删除照片
     */
    public function actionDelphoto(){
        $this->check_key();
        $user = $this->check_user();
        if (!$this->storevip($user['id'])) {
            $result['ret_num'] = 1110;
            $result['ret_msg'] = "您已过商城试用期";
            echo json_encode($result);
            die();
        }
        $activity_id = Frame::getIntFromRequest('activity_id');
        $id =Frame::getStringFromRequest('picid');//以逗号隔开

        $attinfo=ActivityAttachment::model()->findAll("id in ({$id}) and activity_id={$activity_id}");
        foreach($attinfo as $k=>$v) {
            unlink(ROOT . $v['poster']);
            unlink(ROOT . $this->getThumb($v['poster']));
        }
        $result0=ActivityAttachment::model()->deleteAll("id in ({$id}) and activity_id={$activity_id}");
        if($result0){
            $num=ActivityAttachment::model()->count("activity_id={$activity_id}");
            Activity::model()->updateAll(array("poster_num"=>$num),"id={$activity_id} and member_id={$user['id']}");
        }

        $result['ret_num'] = 0;
        $result['ret_msg'] = "操作成功";
        echo json_encode($result);
    }

    /*
     * 添加照片
     */
    public function actionAddphoto(){
        $this->check_key();
        $user = $this->check_user();
        $connection=Yii::app()->db;
        if (!$this->storevip($user['id'])) {
            $result['ret_num'] = 1110;
            $result['ret_msg'] = "您已过商城试用期";
            echo json_encode($result);
            die();
        }

        $activity_id = Frame::getIntFromRequest('activity_id');
        if(empty($activity_id)){
            $result['ret_num'] = 2015;
            $result['ret_msg'] = "缺少参数";
            echo json_encode($result);
            die();
        }

        $pic[] = Frame::saveImage('pic1', 1);
        $pic[] = Frame::saveImage('pic2', 1);
        $pic[] = Frame::saveImage('pic3', 1);
        $pic[] = Frame::saveImage('pic4', 1);
        $pic[] = Frame::saveImage('pic5', 1);
        $pic[] = Frame::saveImage('pic6', 1);

        $maxpic=20;//最大照片数
        $out=0;
        $anum=ActivityAttachment::model()->count("activity_id={$activity_id}");
        if($anum>=$maxpic){
            $result['ret_num'] = 1110;
            $result['ret_msg'] = "您的照片已达最大限制";
            echo json_encode($result);
            die();
        }

        foreach($pic as $k){
            if($k) {
                if ($anum < $maxpic) {
                    $insert_arr[] = "(" . $activity_id . ",'" . $k . "')";
                    $anum++;
                }else{
                    $out=1;
                }
            }
        }

        $sql="insert into activity_attachment (activity_id,poster) values ".implode(",",$insert_arr);
        $command=$connection->createCommand($sql);
        $result0=$command->execute();

        if($result0) {
            Activity::model()->updateAll(array("poster_num" => $anum), "id={$activity_id} and member_id={$user['id']}");
        }

        if($out){
            $result['ret_num'] = 1111;
            $result['ret_msg'] = "您的照片已达最大限制,有部分未上传";
            echo json_encode($result);
            die();
        }else{
            $result['ret_num'] = 0;
            $result['ret_msg'] = "操作成功";
            echo json_encode($result);
        }
    }

    /*
     * 相册详情
     */
    public function actionAlbumdetail(){
        $this->check_key();
        $user = $this->check_user();
        $connection=Yii::app()->db;

        $activity_id = Frame::getIntFromRequest('activity_id');
        if(empty($activity_id)){
            $result['ret_num'] = 2015;
            $result['ret_msg'] = "缺少参数";
            echo json_encode($result);
            die();
        }

        $acinfo=Activity::model()->find("id={$activity_id} and is_close=0");

        if (!$this->storevip($acinfo['member_id'])) {
            $result['ret_num'] = 1110;
            $result['ret_msg'] = "已过商城试用期";
            echo json_encode($result);
            die();
        }

        $attinfo=ActivityAttachment::model()->findAll("activity_id={$activity_id}");
        $pic=array();
        if($attinfo) {
            foreach ($attinfo as $k => $v) {
                $pic[] = array(
                    "picid" => $v['id'],
                    "activity_id" => $v['activity_id'],
                    "poster" => $v['poster'] ? URL . $v['poster'] : "",
                    "small_poster" => $v['poster'] ? URL . $this->getThumb($v['poster']) : ""
                );
            }
        }

        $result['ret_num'] = 0;
        $result['ret_msg'] = "操作成功";
        $result['member_id'] = $acinfo['member_id'];
        $result['title'] = $acinfo['title'];
        $result['time'] = $acinfo['time'];
        $result['poster_num'] = $acinfo['poster_num'];
        $result['poster_cover'] = $acinfo['poster_cover'] ? URL.$acinfo['poster_cover'] : "";
        $result['small_poster_cover'] = $acinfo['poster_cover'] ? URL.$this->getThumb($acinfo['poster_cover']) : "";
        $result['is_close'] = $acinfo['is_close'];
        $result['pic'] = $pic;
        echo json_encode($result);
    }
}