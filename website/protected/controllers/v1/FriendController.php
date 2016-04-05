<?php

class FriendController extends PublicController
{
    public $layout = false;

    /**
     * 我的朋友圈
     */
    public function actionMylist()
    {
        $this->check_key();
        $last_time = Frame::getIntFromRequest('last_time');
        $user = $this->check_user();
        $connection = Yii::app()->db;

        //查出自己通讯录里的犇犇用户
        $sqla = "select a.is_benben,b.name from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id
		where b.member_id = {$user->id} and a.is_benben>0";
        $command = $connection->createCommand($sqla);
        $resul = $command->queryAll();
        $rename = array();
        foreach ($resul as $v1) {
            $rename[$v1['is_benben']] = $v1['name'];
        }

        // $sql = "select b.id MemberId,b.nick_name Name,b.name rname,b.benben_id,b.poster Poster,a.id Id,a.description Description,a.views Views,a.created_time CreatedTime from friend a inner join member b on a.member_id = b.id where a.status = 0 and b.id = {$user->id} order by a.created_time desc limit 10";
        $sql = "select b.id MemberId,b.nick_name Name,b.name rname,b.benben_id,b.poster Poster,a.id Id,a.description Description,a.views Views,a.created_time CreatedTime,a.status from friend a
		inner join member b on a.member_id = b.id where a.is_delete = 0 and b.id = {$user->id} order by a.created_time desc limit 10";
        if ($last_time) {
            // $sql = "select b.id MemberId,b.nick_name Name,b.name rname,b.benben_id,b.poster Poster,a.id Id,a.description Description,a.views Views,a.created_time CreatedTime from friend a inner join member b on a.member_id = b.id where a.status = 0 and a.created_time < {$last_time} and b.id = {$user->id} order by a.created_time desc limit 10";
            $sql = "select b.id MemberId,b.nick_name Name,b.name rname,b.benben_id,b.poster Poster,a.id Id,a.description Description,a.views Views,a.created_time CreatedTime,a.status from friend a
			inner join member b on a.member_id = b.id where a.is_delete = 0 and a.created_time < {$last_time} and b.id = {$user->id} order by a.created_time desc limit 10";
        }

        $command = $connection->createCommand($sql);
        $result0 = $command->queryAll();

        $creationid = "";
        foreach ($result0 as $val) {
            $creationid .= $val['Id'] . ',';
        }
        $creationid = trim($creationid);
        $creationid = trim($creationid, ',');
        if ($creationid) {
            $sql1 = "select circle_id,attachment from friend_attachment where circle_id in ({$creationid})";
            $command = $connection->createCommand($sql1);
            $result1 = $command->queryAll();
            //查询评论
            $sql2 = "select a.circle_id,a.member_id,a.review,a.created_time,b.nick_name,b.name rname,b.benben_id from friend_comment a
			inner join member b on a.member_id = b.id where a.circle_id in ({$creationid}) order by a.created_time asc";
            $command = $connection->createCommand($sql2);
            $result2 = $command->queryAll();
            $comment = array();
            foreach ($result2 as $key => $va) {
                //if(count($comment[$va['circle_id']])>2) continue;
                $comment[$va['circle_id']][] = array(
                    "circle_id" => $va['circle_id'],
                    "member_id" => $va['member_id'],
                    "nick_name" => $rename[$va['benben_id']] ? $rename[$va['benben_id']] : $va['nick_name'],
                    "review" => $va['review'],
                    "created_time" => $va['created_time']
                );
            }
            //var_dump($comment);exit();
            //查询是否点赞
            $laud_status = array();
            $sql = "select a.circle_id, b.nick_name as name, b.id,b.benben_id from friend_like as a left join member as b on a.member_id = b.id where a.circle_id in ({$creationid})";
            $command = $connection->createCommand($sql);
            $laud = $command->queryAll();
            foreach ($laud as $valu) {
                $laud_status[$valu['circle_id']][] = $valu['id'];
                $laud_status_name[$valu['circle_id']][] = $rename[$valu['benben_id']] ? $rename[$valu['benben_id']] : $valu['name'];
            }
        }
        //添加图片信息
        $thumb = "";
        foreach ($result0 as $key => $value) {
// 			if($value['rname']){
// 				$result0[$key]['Name'] = $value['rname'];
// 			}
            if ($rename[$value['benben_id']]) {
                $result0[$key]['Name'] = $rename[$value['benben_id']];
            }
            $currentLaud = 0;
            if (isset($laud_status[$value['Id']]) && in_array($user->id, $laud_status[$value['Id']])) {
                $currentLaud = 1;
            }
            $laudString = '';
            if (isset($laud_status_name[$value['Id']])) {
                if (count($laud_status_name[$value['Id']]) < 5) {
                    $laudString = implode("、", $laud_status_name[$value['Id']]);
                } else {
                    $laudString = $laud_status_name[$value['Id']][0] . "、" . $laud_status_name[$value['Id']][1] . "、" . $laud_status_name[$value['Id']][2] . "、" . $laud_status_name[$value['Id']][3];
                }
                $laudString .= '等人点赞';
            }

            $result0[$key]['Laud'] = $currentLaud;
            $result0[$key]['laud_list'] = $laudString;
            $result0[$key]['Poster'] = $value['Poster'] ? URL . $value['Poster'] : "";
            $result0[$key]['Images'] = array();
            $img = "";
            $thumb_img = "";
            $targetImage = null;
            foreach ($result1 as $v) {
                if ($value['Id'] == $v['circle_id']) {
                    $thumb = explode("/", $v['attachment']);
                    $thumb[4] = 'small' . $thumb[4];
                    $img .= URL . $v['attachment'] . ",";
                    $thumb_img .= URL . implode("/", $thumb) . ",";
                    if (!$targetImage) $targetImage = Yii::getPathOfAlias('webroot') . implode("/", $thumb);
                }
            }
            if (file_exists($targetImage)) {
                if (substr($targetImage, -3) != "amr") {
                    $info = getimagesize($targetImage);
                    $result0[$key]['Width'] = $info[0];
                    $result0[$key]['Height'] = $info[1];
                }
            }
            $img = trim($img);
            $img = trim($img, ',');
            $thumb_img = trim($thumb_img);
            $thumb_img = trim($thumb_img, ',');
            if ($img) {
                $img = explode(',', $img);
            }
            if ($thumb_img) {
                $thumb_img = explode(',', $thumb_img);
            }
            $result0[$key]['Images'] = $img ? $img : array();
            $result0[$key]['Thumb'] = $thumb_img ? $thumb_img : array();
// 			if(count($result0[$key]['Thumb']) == 1){
// 				if($thumb_img[0]){
// 					if(file_exists($thumb_img[0])){
// 							if(substr($thumb_img[0], -3) != "amr"){
// 								$info = getimagesize($thumb_img[0]);
// 								$result0[$key]['Width'] = $info[0];
// 								$result0[$key]['Height'] = $info[1];
// 							}
// 						}
// 				}
// 			}
            $result0[$key]['Comment'] = $comment[$value['Id']] ? $comment[$value['Id']] : array();
        }

        if ($result0) {
            $sql0 = "update friend set views=views+1 where id in ({$creationid})";
            $command = $connection->createCommand($sql0);
            $re = $command->execute();
        }
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result ['number_info'] = $result0;
        echo json_encode($result);
    }

    /**
     * 删除自己的朋友圈
     **/
    public function actionDeleteItem()
    {
        $this->check_key();
        $user = $this->check_user();
        $id = Frame::getIntFromRequest('id');
        $model = Friend::model()->findByPk($id);
        if (empty($model)) {
            $result ['ret_num'] = 100;
            $result ['ret_msg'] = '帖子不存在';
            echo json_encode($result);
            die();
        }
        if ($model->member_id != $user->id) {
            $result ['ret_num'] = 101;
            $result ['ret_msg'] = '没有删除权限';
            echo json_encode($result);
            die();
        }
        $model->is_delete = 1;
        if ($model->update()) {
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '删除成功';
            echo json_encode($result);
            die();
        } else {
            $result ['ret_num'] = 102;
            $result ['ret_msg'] = '删除失败';
            echo json_encode($result);
            die();
        }
    }

    /**
     * 朋友圈列表
     */
    public function actionList()
    {
        $this->check_key();
        $last_time = Frame::getIntFromRequest('last_time');
        $user = $this->check_user();
        $connection = Yii::app()->db;

        //查出自己通讯录里的犇犇用户
        $sqla = "select a.is_benben,b.name from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id
		where b.member_id = {$user->id} and a.is_benben>0";
        $command = $connection->createCommand($sqla);
        $resul = $command->queryAll();
        $rename = array();
        $benbenid = array();
        $benbenid[] = $user->benben_id;
        foreach ($resul as $v1) {
            $rename[$v1['is_benben']] = $v1['name'];
            $benbenid[] = $v1['is_benben'];
        }
        $benbenid = implode(",", $benbenid);
        if (!$benbenid) {
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            $result ['number_info'] = array();
            echo json_encode($result);
            die();
        }
        $sql = "select b.id MemberId,b.benben_id,b.nick_name Name,b.name rname,b.poster Poster,a.id Id,a.description Description,a.views Views,a.type Type,a.created_time CreatedTime from friend a inner join member b on a.member_id = b.id where a.is_delete = 0 and b.benben_id in ({$benbenid}) and a.status = 0 order by a.created_time desc limit 10";
        if ($last_time) {
            $sql = "select b.id MemberId,b.benben_id,b.nick_name Name,b.name rname,b.poster Poster,a.id Id,a.description Description,a.views Views,a.created_time CreatedTime from friend a inner join member b on a.member_id = b.id where a.is_delete = 0 and b.benben_id in ({$benbenid}) and a.status = 0 and a.created_time < {$last_time} order by a.created_time desc limit 10";
        }

        $command = $connection->createCommand($sql);
        $result0 = $command->queryAll();

        $creationid = "";
        foreach ($result0 as $val) {
            $creationid .= $val['Id'] . ',';
            $friendid_tpl[$val['Id']] = $val['MemberId'];
        }
        $creationid = trim($creationid);
        $creationid = trim($creationid, ',');
        if (!$creationid) {
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            $result ['number_info'] = array();
            echo json_encode($result);
            die();
        }
        if ($creationid) {
            //查询图片
            $sql1 = "select circle_id,attachment from friend_attachment where circle_id in ({$creationid})";
            $command = $connection->createCommand($sql1);
            $result1 = $command->queryAll();
            //查询评论
            $benben_arr = $this->getfriend($user['id']);
            //获取好友的奔犇号和id之间的对应关系
            $memberinfo=array();
            if($benben_arr){
                $members=Member::model()->findAll("benben_id in (".implode(",",$benben_arr).")");
                if($members){
                    foreach($members as $member){
                        if($member['benben_id']) {
                            $memberinfo[$member['benben_id']] = $member['id'];
                        }
                    }
                }
            }
            $benben = $this->getfriend($user['id'], 2);
            foreach ($benben_arr as $kb => $vb) {
                $memberid[] = $memberinfo[$vb];
                $membername[$memberinfo[$vb]] = $benben[$vb];
            }

            if (!in_array($user['id'], $memberid)) {
                $memberid[] = $user['id'];
            }

            $sql2 = "select a.circle_id,a.member_id,a.review,a.created_time,a.replier,b.nick_name,b.name rname,b.benben_id from friend_comment a
			left join member b on a.member_id = b.id where a.circle_id in ({$creationid}) and a.replier in (" . implode(",", $memberid) . ") order by a.created_time asc";
            $command = $connection->createCommand($sql2);
            $result2 = $command->queryAll();

            $comment = array();
            foreach ($result2 as $key => $va) {
                //if(count($comment[$va['circle_id']])>2) continue;
                $comment[$va['circle_id']][] = array(
                    "circle_id" => $va['circle_6id'],
                    "member_id" => $va['member_id'],
                    "nick_name" => $rename[$va['benben_id']] ? $rename[$va['benben_id']] : $va['nick_name'],
                    "review" => $va['replier'] == $friendid_tpl[$va['circle_id']] ? $va['review'] :
                        ($va['replier'] == $user['id'] ? $va['review'] . "@" . $user['nick_name'] : $va['review'] . "@" . $membername[$va['replier']]),
                    "created_time" => $va['created_time']
                );
            }
            //var_dump($comment);exit();
            //查询是否点赞
            //查询是否点赞
            $laud_status = array();
            $sql = "select a.circle_id, b.nick_name as name, b.id,b.benben_id from friend_like as a left join member as b on a.member_id = b.id where a.circle_id in ({$creationid})";
            $command = $connection->createCommand($sql);
            $laud = $command->queryAll();
            foreach ($laud as $valu) {
                $laud_status[$valu['circle_id']][] = $valu['id'];
                $laud_status_name[$valu['circle_id']][] = $rename[$valu['benben_id']] ? $rename[$valu['benben_id']] : $valu['name'];
            }
        }
        //添加图片信息
        $thumb = "";
        foreach ($result0 as $key => $value) {
            if ($rename[$value['benben_id']]) {
                $result0[$key]['Name'] = $rename[$value['benben_id']];
            }

            $currentLaud = 0;
            if (isset($laud_status[$value['Id']]) && in_array($user->id, $laud_status[$value['Id']])) {
                $currentLaud = 1;
            }
            $laudString = '';
            if (isset($laud_status_name[$value['Id']])) {
                if (count($laud_status_name[$value['Id']]) < 5) {
                    $laudString = implode("、", $laud_status_name[$value['Id']]);
                } else {
                    $laudString = $laud_status_name[$value['Id']][0] . "、" . $laud_status_name[$value['Id']][1] . "、" . $laud_status_name[$value['Id']][2] . "、" . $laud_status_name[$value['Id']][3];
                }
                $laudString .= '等人点赞';
            }

            $result0[$key]['Laud'] = $currentLaud;
            $result0[$key]['laud_list'] = $laudString;
            $result0[$key]['Poster'] = $value['Poster'] ? URL . $value['Poster'] : "";
            $result0[$key]['Images'] = array();
            $img = "";
            $thumb_img = "";
            $targetImage = null;
            foreach ($result1 as $v) {
                if ($value['Id'] == $v['circle_id']) {
                    $thumb = explode("/", $v['attachment']);
                    $thumb[4] = 'small' . $thumb[4];
                    $img .= URL . $v['attachment'] . ",";
                    $thumb_img .= URL . implode("/", $thumb) . ",";
                    if (!$targetImage) $targetImage = Yii::getPathOfAlias('webroot') . implode("/", $thumb);
                }
            }

            if (file_exists($targetImage)) {
                if (substr($targetImage, -3) != "amr") {
                    $info = getimagesize($targetImage);
                    $result0[$key]['Width'] = $info[0];
                    $result0[$key]['Height'] = $info[1];
                }
            }

            $img = trim($img);
            $img = trim($img, ',');
            $thumb_img = trim($thumb_img);
            $thumb_img = trim($thumb_img, ',');
            if ($img) {
                $img = explode(',', $img);
            }
            if ($thumb_img) {
                $thumb_img = explode(',', $thumb_img);
            }
            $result0[$key]['Images'] = $img ? $img : array();
            $result0[$key]['Thumb'] = $thumb_img ? $thumb_img : array();
            $result0[$key]['Comment'] = $comment[$value['Id']] ? $comment[$value['Id']] : array();
        }

        if ($result0) {
            $sql0 = "update friend set views=views+1 where id in ({$creationid})";
            $command = $connection->createCommand($sql0);
            $re = $command->execute();
        }

        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result ['number_info'] = $result0;
        echo json_encode($result);
    }

    /*
     * 被评论列表
     * 涉及FriendComment，frend,member
     */
    public function actionFriendcommentlist()
    {
        $this->check_key();
        $user = $this->check_user();
        $friend_time = Frame::getIntFromRequest('friend_time');
        $listnum = Frame::getIntFromRequest('listnum');
        $connection = Yii::app()->db;

        if (empty($friend_time) || empty($listnum)) {
            $result['ret_num'] = 100;
            $result['ret_msg'] = '缺少参数';
            echo json_encode($result);
            die();
        }

        $sqlr = "select a.id,a.circle_id as friend_id,a.member_id,a.review,a.created_time,a.replier,b.description,c.nick_name,c.poster,c.huanxin_username from friend_comment as a
		left JOIN friend as b on a.circle_id=b.id left join member as c on c.id=a.member_id
		where b.is_delete=0 and (b.member_id={$user['id']} or a.replier={$user['id']}) and a.created_time < {$friend_time} and a.member_id!={$user->id} order by a.created_time desc limit 0,{$listnum}";
        $command = $connection->createCommand($sqlr);
        $resultr = $command->queryAll();
        $reply_num = $resultr ? count($resultr) : 0;
        $nickname = $this->getContactIdName($user['id']);
        foreach ($resultr as $k => $v) {
            $resultr[$k]['poster'] = $v['poster'] ? URL . $v['poster'] : "";
            $resultr[$k]['nick_name'] = $nickname[$v['member_id']] ? $nickname[$v['member_id']] : $v['nick_name'];
        }
        $result['comment'] = $resultr;
        $result['num'] = $reply_num;
        $result['ret_num'] = 0;
        $result['ret_msg'] = '获取成功';
        echo json_encode($result);
    }

    /**
     * 朋友圈发布
     */
    public function actionCreate()
    {


        $this->check_key();
        $img1 = Frame::saveImage('img1', 1);
        $img2 = Frame::saveImage('img2', 1);
        $img3 = Frame::saveImage('img3', 1);
        $img4 = Frame::saveImage('img4', 1);
        $img5 = Frame::saveImage('img5', 1);
        $img6 = Frame::saveImage('img6', 1);
        $type = Frame::getIntFromRequest('type');
        $description = Frame::getStringFromRequest('description');
        $user = $this->check_user();
        $connection = Yii::app()->db;

        $friend_info = new Friend();
        $friend_info->member_id = $user->id;
        $friend_info->type = $type;
        $friend_info->description = $description;
        $friend_info->created_time = time();
        $friend_info->views = 0;
        $friend_info->goods = 0;
        if ($friend_info->save()) {
            $id = $friend_info->id;
            $v = "";
            if ($img1) {
                $v .= "({$id},'{$img1}'),";
            }
            if ($img2) {
                $v .= "({$id},'{$img2}'),";
            }
            if ($img3) {
                $v .= "({$id},'{$img3}'),";
            }
            if ($img4) {
                $v .= "({$id},'{$img4}'),";
            }
            if ($img5) {
                $v .= "({$id},'{$img5}'),";
            }
            if ($img6) {
                $v .= "({$id},'{$img6}'),";
            }
            $v = trim($v);
            $v = trim($v, ',');
            if ($v) {
                $sql = "insert into friend_attachment (circle_id,attachment) values {$v}";
                $command = $connection->createCommand($sql);
                $result1 = $command->execute();
            }

            $result['ret_num'] = 0;
            $result['ret_msg'] = '操作成功';
        } else {
            $result['ret_num'] = 126;
            $result['ret_msg'] = '朋友圈发布失败';
        }
        echo json_encode($result);
    }

    /**
     * 朋友圈评论
     */
    public function actionComment()
    {
        $this->check_key();
        $friendid = Frame::getIntFromRequest('friendid');
        $replier = Frame::getIntFromRequest('replier');
        $content = Frame::getStringFromRequest('content');
        if (empty($friendid)) {
            $result['ret_num'] = 129;
            $result['ret_msg'] = '朋友圈ID为空';
            echo json_encode($result);
            die();
        }
        if (!empty($replier)) {
            $replierinfo = Member::model()->find("id={$replier}");
            if (!$replierinfo) {
                $result['ret_num'] = 111;
                $result['ret_msg'] = '您@的用户不存在';
                echo json_encode($result);
                die();
            }
        }
        if (empty($content)) {
            $result['ret_num'] = 130;
            $result['ret_msg'] = '评论内容为空';
            echo json_encode($result);
            die();
        }
        $user = $this->check_user();
        $friend = Friend::model()->findByPk($friendid);
        if (!$friend) {
            $result['ret_num'] = 131;
            $result['ret_msg'] = '朋友圈ID不存在';
            echo json_encode($result);
            die();
        }

        $comment_info = new FriendComment();
        $comment_info->member_id = $user->id;
        $comment_info->circle_id = $friendid;
        $comment_info->review = $content;
        $comment_info->created_time = time();
        $comment_info->replier = $replier ? $replier : $friend['member_id'];
        if ($comment_info->save()) {
            //发送给评论者
            if ($replier != $friend['member_id']) {
                $nickname = $this->getContactIdName($replier);
                $tpl_1 = array(
                    "id" => $comment_info['id'],
                    "friend_id" => $friendid,
                    "member_id" => $user->id,
                    "review" => $content,
                    "created_time" => $comment_info->created_time,
                    "replier" => $replier,
                    "description" => $friend['description'],
                    "nick_name" => $nickname[$user['id']] ? $nickname[$user['id']] : $user['nick_name'],
                    "poster" => $user['poster'] ? URL . $user['poster'] : "",
                    "huanxin_username" => $user['huanxin_username']
                );
                $tinfo = Member::model()->find("id={$replier}");
                $this->sendTCMessage('admin', array(0 => $tinfo['huanxin_username']), "action1", $tpl_1);
            }
            //发送给作者
            if ($user->id != $friend['member_id']) {
                $nickname1 = $this->getContactIdName($friend['member_id']);
                $tpl_2 = array(
                    "id" => $comment_info['id'],
                    "friend_id" => $friendid,
                    "member_id" => $user->id,
                    "review" => $content,
                    "created_time" => $comment_info->created_time,
                    "replier" => $replier,
                    "description" => $friend['description'],
                    "nick_name" => $nickname1[$user['id']] ? $nickname1[$user['id']] : $user['nick_name'],
                    "poster" => $user['poster'] ? URL . $user['poster'] : "",
                    "huanxin_username" => $user['huanxin_username']
                );
                $tinfo1 = Member::model()->find("id={$friend['member_id']}");
                $this->sendTCMessage('admin', array(0 => $tinfo1['huanxin_username']), "action1", $tpl_2);
            }

            //查出自己通讯录里的自己的名字
            $connection = Yii::app()->db;
            $sqla = "select a.is_benben,b.name from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id
			where b.member_id = {$user->id} and a.is_benben={$user->benben_id}";
            $command = $connection->createCommand($sqla);
            $resul = $command->queryAll();
            $rename = array();
            foreach ($resul as $v1) {
                $rename = $v1['name'];
            }
            $result['display_name'] = empty($rename) ? $user->nick_name : $rename;
            $result['ret_num'] = 0;
            $result['ret_msg'] = '操作成功';
        } else {
            $result['ret_num'] = 127;
            $result['ret_msg'] = '朋友圈评论发布失败';
        }
        echo json_encode($result);
    }

    /**
     * 朋友圈点赞
     */
    public function actionLaud()
    {
        $this->check_key();
        $friendid = Frame::getIntFromRequest('friendid');
        if (empty($friendid)) {
            $result['ret_num'] = 129;
            $result['ret_msg'] = '朋友圈ID为空';
            echo json_encode($result);
            die();
        }
        $user = $this->check_user();
        $friend = Friend::model()->findByPk($friendid);
        if (!$friend) {
            $result['ret_num'] = 131;
            $result['ret_msg'] = '朋友圈ID不存在';
            echo json_encode($result);
            die();
        }
        $laud_info = FriendLike::model()->find("member_id = {$user->id} and circle_id = {$friendid}");
        if ($laud_info) {

            $result['ret_num'] = 1281;
            $result['ret_msg'] = '已经点赞';
            echo json_encode($result);
            exit();
        }

        $laud_info = new FriendLike();
        $laud_info->member_id = $user->id;
        $laud_info->circle_id = $friendid;
        $laud_info->created_time = time();
        if ($laud_info->save()) {
            $connection = Yii::app()->db;
            //查出自己通讯录里的犇犇用户
            $sqla = "select a.is_benben,b.name from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id
			where b.member_id = {$user->id} and a.is_benben>0";
            $command = $connection->createCommand($sqla);
            $resul = $command->queryAll();
            $rename = array();
            foreach ($resul as $v1) {
                $rename[$v1['is_benben']] = $v1['name'];
            }

            $friend->goods = $friend->goods + 1;
            $friend->update();
            $sql = "select a.circle_id, b.nick_name as name, b.id,b.benben_id from friend_like as a left join member as b on a.member_id = b.id where a.circle_id={$friendid}";
            $command = $connection->createCommand($sql);
            $laud = $command->queryAll();
            $laud_status_name = array();
            foreach ($laud as $valu) {
                $memberid = $valu['benben_id'];
                $laud_status_name[] = empty($rename[$memberid]) ? $valu['name'] : $rename[$memberid];
            }
            $msg = '';
            if (count($laud_status_name) > 0) {
                if (count($laud_status_name) < 5) {
                    $msg = implode("、", $laud_status_name);
                } else {
                    $msg = $laud_status_name[0] . "、" . $laud_status_name[1] . "、" . $laud_status_name[2] . "、" . $laud_status_name[3];
                }
                $msg .= '等人点赞';
            }
            $result['ret_num'] = 0;
            $result['msg'] = $msg;
            $result['ret_msg'] = '操作成功';
        } else {
            $result['ret_num'] = 128;
            $result['ret_msg'] = '朋友圈点赞失败';
        }
        echo json_encode($result);
    }

    /**
     * 朋友圈取消点赞
     */
    public function actionCancellaud()
    {
        $this->check_key();
        $friendid = Frame::getIntFromRequest('friendid');
        if (empty($friendid)) {
            $result['ret_num'] = 129;
            $result['ret_msg'] = '朋友圈ID为空';
            echo json_encode($result);
            die();
        }
        $user = $this->check_user();
        $friend = Friend::model()->findByPk($friendid);
        if (!$friend) {
            $result['ret_num'] = 131;
            $result['ret_msg'] = '朋友圈ID不存在';
            echo json_encode($result);
            die();
        }

        $laud_info = FriendLike::model()->find("member_id = {$user->id} and circle_id = {$friendid}");
        if ($laud_info && $laud_info->delete()) {
            $connection = Yii::app()->db;
            //查出自己通讯录里的犇犇用户
            $sqla = "select a.is_benben,b.name from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id
			where b.member_id = {$user->id} and a.is_benben>0";
            $command = $connection->createCommand($sqla);
            $resul = $command->queryAll();
            $rename = array();
            foreach ($resul as $v1) {
                $rename[$v1['is_benben']] = $v1['name'];
            }

            $friend->goods = $friend->goods - 1;
            $friend->update();
            $sql = "select a.circle_id, b.nick_name as name, b.id,b.benben_id from friend_like as a left join member as b on a.member_id = b.id where a.circle_id={$friendid}";
            $command = $connection->createCommand($sql);
            $laud = $command->queryAll();
            $laud_status_name = array();
            foreach ($laud as $valu) {
                $memberid = $valu['benben_id'];
                $laud_status_name[] = empty($rename[$memberid]) ? $valu['name'] : $rename[$memberid];
            }
            $msg = '';
            if (count($laud_status_name) > 0) {
                if (count($laud_status_name) < 5) {
                    $msg = implode("、", $laud_status_name);
                } else {
                    $msg = $laud_status_name[0] . "、" . $laud_status_name[1] . "、" . $laud_status_name[2] . "、" . $laud_status_name[3];
                }
                $msg .= '等人点赞';
            }
            $result['ret_num'] = 0;
            $result['msg'] = $msg;
            $result['ret_msg'] = '操作成功';
        } else {
            $result['ret_num'] = 1280;
            $result['ret_msg'] = '朋友圈取消点赞失败';
        }
        echo json_encode($result);
    }


}