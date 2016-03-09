<?php

class SplashController extends PublicController
{
    public $layout = false;

    /**
     * 开机页面
     */

    public function actionGetSplash()
    {
        $this->check_key();

        $cri = new CDbCriteria();
        $cri->order = "created_time desc";
        $cri->limit = "1";
        $splash = Splash::model()->find($cri);
        $result = array();
        if (!$splash) {
            $result ['ret_num'] = 3000;
            $result ['ret_msg'] = '获取开机页面失败';
        } else {
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = 'OK';
            $result['splash'] = Yii::app()->request->getHostInfo() . $splash->image;
        }
        echo json_encode($result);
    }

    /*
     * 打开应用时提醒
     * 发现红点
      */
    public function actionRemind()
    {
        $this->check_key();
        $friend_time = Frame::getIntFromRequest('friend_time');
        $friendrush = Frame::getIntFromRequest('friendrush');
        $creation_time = Frame::getIntFromRequest('creation_time');
        $creationrush = Frame::getIntFromRequest('creationrush');
        $user = $this->check_user();
        $connection = Yii::app()->db;

        if (empty($friend_time) || empty($friendrush) || empty($creation_time) || empty($creationrush)) {
            $result['ret_num'] = 100;
            $result['ret_msg'] = '缺少参数';
            echo json_encode($result);
            die();
        }

        //应用内部提醒数字计数
        /*
         * 朋友圈好友内容更新相关查询
         * 朋友圈内容回复或被评论查询
         */
        //查出自己好友内容更新数
        $sqla = "select a.is_benben,b.name from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id
		where b.member_id = {$user->id} and a.is_benben>0";
        $command = $connection->createCommand($sqla);
        $resultxx = $command->queryAll();
        $benbenid = array();
        foreach ($resultxx as $v1) {
            $rename[$v1['is_benben']] = $v1['name'];
            $benbenid[] = $v1['is_benben'];
        }
        $benbenid = implode(",", $benbenid);
        if ($benbenid) {
            $sql = "select count(1) as num from friend a inner join member b on a.member_id = b.id where a.is_delete = 0 and b.benben_id in ({$benbenid}) and a.status = 0 and a.created_time > {$friend_time} order by a.created_time desc";
            $command = $connection->createCommand($sql);
            $resultu = $command->queryAll();
        }
        $create_num = $resultu[0]['num'];

        //查出被@的内容数
        $sqlfc = "select count(1) as num from friend_comment as a left JOIN friend as b on a.circle_id=b.id where b.is_delete=0 and a.replier={$user['id']} and b.member_id!={$user['id']} and a.created_time > {$friendrush}";
        $command = $connection->createCommand($sqlfc);
        $resultfc = $command->queryAll();
        $replier_num = $resultfc[0]['num'];

        //查出我发表的被回复的内容数
        $sqlr = "select count(1) as num from friend_comment as a left JOIN friend as b on a.circle_id=b.id where b.is_delete=0 and b.member_id={$user['id']} and a.member_id!={$user['id']} and a.created_time > {$friendrush}";
        $command = $connection->createCommand($sqlr);
        $resultr = $command->queryAll();
        $pub_num = $resultr[0]['num'];

        //被点赞数
        $sqldd = "select count(1) as num from friend_like as a left join friend as b on a.circle_id=b.id where b.member_id={$user['id']} and a.created_time> {$friend_time} and b.is_delete=0";
        $command = $connection->createCommand($sqldd);
        $resultdd = $command->queryAll();
        $dd_num = $resultdd[0]['num'];

        /*
         * 微创作我关注的内容更新相关查询
         * 微创作内容回复或被评论查询
         */
        //查出关注的人的更新数
        $sqlw = "select count(1) as num from creation as a left join creation_attention as b on a.member_id = b.creation_auth_id where b.member_id={$user['id']} and a.created_time>{$creation_time} and a.is_delete=0";
        $command = $connection->createCommand($sqlw);
        $resultw = $command->queryAll();
        $watch_num = $resultw[0]['num'];

        //查出被@的内容数
        $sqlcc = "select count(1) as num from creation_comment as a left JOIN creation as b on a.creation_id=b.id where b.is_delete=0 and a.replier={$user['id']} and b.member_id!={$user['id']} and a.created_time > {$creationrush}";
        $command = $connection->createCommand($sqlcc);
        $resultcc = $command->queryAll();
        $creation_re_num = $resultcc[0]['num'];

        //查出我发表的被回复的内容数
        $sqlp = "select count(1) as num from creation_comment as a left join creation as b on a.creation_id=b.id where b.member_id={$user['id']} and a.member_id!={$user['id']} and a.created_time>{$creationrush} and b.is_delete=0";
        $command = $connection->createCommand($sqlp);
        $resultp = $command->queryAll();
        $p_num = $resultp[0]['num'];

        //被点赞数
        $sqll = "select count(1) as num from creation_like as a left join creation as b on a.creation_id=b.id where b.member_id={$user['id']} and a.created_time>{$creation_time} and b.is_delete=0";
        $command = $connection->createCommand($sqll);
        $resultl = $command->queryAll();
        $l_num = $resultl[0]['num'];

        $result ['ret_num'] = 0;
        $result ['ret_msg'] = 'OK';
        $result['friend'] = array(
            "friend_num" => $create_num ? $create_num : 0,
            "hit_me" => $replier_num ? $replier_num : 0,
            "publish" => $pub_num ? $pub_num : 0,
            "applaud" => $dd_num ? $dd_num : 0
        );
        $result['creation'] = array(
            "watcher_num" => $watch_num ? $watch_num : 0,
            "hit_me" => $creation_re_num ? $creation_re_num : 0,
            "publish" => $p_num ? $p_num : 0,
            "applaud" => $l_num ? $l_num : 0
        );
        echo json_encode($result);
    }

    /*
     * 积分折算犇币
     * 积分10换0.1犇币
     */
    public function actionExchange()
    {
        $this->check_key();
        $user = $this->check_user();
        $iconInfo = IconExchangeLog::model()->find("member_id=" . $user['id'] . " order by created_time desc");
        //首次兑换为直接创建，不是则对比以前记录
        $transaction = Yii::app()->db->beginTransaction(); //创建事务
        try {

            $icon = new IconExchangeLog();
            if($iconInfo) {
                $no = floor(($user->integral-$iconInfo->consume_integral) / 10);
                $icon->consume_integral =$iconInfo->consume_integral + $no * 10;
            }else{
                $no = floor(($user->integral) / 10);
                $icon->consume_integral =$no * 10;
            }
            $icon->member_id = $user->id;
            $icon->created_time = time();
            if($no) {
                Member::model()->updateAll(array("coin"=>$no * 0.1+$user->coin),"id=".$user['id']);
                $icon->save();
            }

            $transaction->commit(); //提交事务会真正的执行数据库操作
        } catch (Exception $e) {
            $transaction->rollback(); //如果操作失败, 数据回滚
        }
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = 'OK';
        echo json_encode($result);
    }

}