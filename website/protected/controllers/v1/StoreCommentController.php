<?php

class StoreCommentController extends PublicController
{
    public $layout = false;

    /*
     * 查询某商品所有评论
     * 涉及store_comment,member表
     */
    public function actionPromotionCommentList()
    {
        $this->check_key();
        $user = $this->check_user();
        $promotion_id = Frame::getIntFromRequest('promotion_id');
        if (empty($promotion_id)) {
            $result ['ret_num'] = 2015;
            $result ['ret_msg'] = '参数不全！';
            echo json_encode($result);
            die();
        }
        $connection = Yii::app()->db;
        $info = array();
        $tpl = array();
        //查询非商家的主回复
        $sql = "select a.*,b.nick_name,b.poster from store_comment as a left join member as b on a.member_id=b.id where a.promotion_id=" . $promotion_id . " and ((a.parent_id=0 and a.is_seller!=1) or
        (a.parent_id not in (select comment_id from store_comment where parent_id=0 and is_seller=1) and a.parent_id!=0)) order by a.comment_id Desc";
        $command = $connection->createCommand($sql);
        $result0 = $command->queryAll();
        if ($result0) {
            //取各个子评论
            foreach ($result0 as $k => $v) {
                if ($v['parent_id'] != 0) {
                    $tpl[$v['parent_id']][] = array(
                        'comment_id' => $v['comment_id'],
                        'comment_type' => $v['comment_type'],
                        'promotion_id' => $v['promotion_id'],
                        'huanxin_username' => $v['huanxin_username'],
                        'content' => $v['content'],
                        'comment_rank' => $v['comment_rank'],
                        'add_time' => $v['add_time'],
                        'nick_name' => $v['is_seller'] == 1 ? "掌柜" : $v['nick_name'],
                        'is_seller' => $v['is_seller']//0买家,1卖家
                    );
                }
            }

            //取主评论
            foreach ($result0 as $ko => $vo) {
                if ($vo['parent_id'] == 0) {
                    $info[] = array(
                        'comment_id' => $vo['comment_id'],
                        'comment_type' => $vo['comment_type'],
                        'promotion_id' => $vo['promotion_id'],
                        'huanxin_username' => $vo['huanxin_username'],
                        'content' => $vo['content'],
                        'comment_rank' => $vo['comment_rank'],
                        'add_time' => $vo['add_time'],
                        'poster' => $vo['poster'] ? URL . $vo['poster'] : "",
                        'nick_name' => $vo['nick_name'],
                        'is_seller' => $vo['is_seller'],//0买家,1卖家
                        'reply' => $tpl[$vo['comment_id']] ? $tpl[$vo['comment_id']] : array(),
                    );
                }
            }
        }
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result ['info'] = $info ? $info : array();
        echo json_encode($result);
    }

    /*
     * 商品评论列表页面输出
     */
    public function actionPromotionlist()
    {
        $this->check_key();
        $promotion_id = Frame::getIntFromRequest('promotion_id');
        if (empty($promotion_id)) {
            $result ['ret_num'] = 2015;
            $result ['ret_msg'] = '参数不全！';
            echo json_encode($result);
            die();
        }
        $connection = Yii::app()->db;
        $info = array();
        $tpl = array();
        //查询非商家的主回复
        $sql = "select a.*,b.nick_name,b.poster from store_comment as a left join member as b on a.member_id=b.id where a.promotion_id=" . $promotion_id . " and ((a.parent_id=0 and a.is_seller!=1) or
        (a.parent_id not in (select comment_id from store_comment where parent_id=0 and is_seller=1) and a.parent_id!=0)) order by a.comment_id Desc";
        $command = $connection->createCommand($sql);
        $result0 = $command->queryAll();
        if ($result0) {
            //取各个子评论
            foreach ($result0 as $k => $v) {
                if ($v['parent_id'] != 0) {
                    $tpl[$v['parent_id']][] = array(
                        'comment_id' => $v['comment_id'],
                        'comment_type' => $v['comment_type'],
                        'promotion_id' => $v['promotion_id'],
                        'huanxin_username' => $v['huanxin_username'],
                        'content' => $v['content'],
                        'comment_rank' => $v['comment_rank'],
                        'add_time' => $v['add_time'],
                        'nick_name' => $v['is_seller'] == 1 ? "掌柜" : $v['nick_name'],
                        'is_seller' => $v['is_seller']//0买家,1卖家
                    );
                }
            }

            //取主评论
            $good=0;
            foreach ($result0 as $ko => $vo) {
                if ($vo['parent_id'] == 0) {
                    $info[] = array(
                        'comment_id' => $vo['comment_id'],
                        'comment_type' => $vo['comment_type'],
                        'promotion_id' => $vo['promotion_id'],
                        'huanxin_username' => $vo['huanxin_username'],
                        'content' => $vo['content'],
                        'comment_rank' => $vo['comment_rank'],
                        'add_time' => $vo['add_time'],
                        'poster' => $vo['poster'] ? URL . $vo['poster'] : "",
                        'nick_name' => $vo['nick_name'],
                        'is_seller' => $vo['is_seller'],//0买家,1卖家
                        'reply' => $tpl[$vo['comment_id']] ? $tpl[$vo['comment_id']] : array(),
                    );
                    //取好评数
                    if($vo['comment_rank']==3){
                        $good++;
                    }
                }
            }
        }
        $result['ret_num'] = 0;
        $result['ret_msg'] = '操作成功';
        $result['info'] = $info ? $info : array();
        $result['num'] = count($info) ? count($info) : 0;
        $result['mean_rate'] = count($info) ? (number_format($good/count($info),4,".","")*100)."%" : "100%";
        $this->render("promotionlist", array("result" => $result));
    }

    /*
     * 查询某店铺所有评论
     * 涉及store_comment,member表
     */
    public function actionStoreCommentList()
    {
        $this->check_key();
        $user = $this->check_user();
        $store_id = Frame::getIntFromRequest('store_id');
        if (empty($store_id)) {
            $result ['ret_num'] = 2015;
            $result ['ret_msg'] = '参数不全！';
            echo json_encode($result);
            die();
        }
        $pminfo = PromotionManage::model()->find("store_id={$store_id}");

        //无论是否删除商品该评论都能查到,所有商品集合
        $pinfo = Promotion::model()->findAll("pm_id={$pminfo['id']}");
        foreach ($pinfo as $vp) {
            $promotion_id_arr[] = $vp['id'];
        }

        $connection = Yii::app()->db;
        $info = array();
        $tpl = array();
        //查询非商家的主回复
        if($promotion_id_arr) {
            $sql = "select a.*,b.nick_name,b.poster from store_comment as a left join member as b on a.member_id=b.id where a.promotion_id in (" . implode(",", $promotion_id_arr) . ") and ((a.parent_id=0 and a.is_seller!=1) or
        (a.parent_id not in (select comment_id from store_comment where parent_id=0 and is_seller=1) and a.parent_id!=0)) order by a.comment_id Desc";
            $command = $connection->createCommand($sql);
            $result0 = $command->queryAll();
        }

        if ($result0) {
            //取各个子评论
            foreach ($result0 as $k => $v) {
                if ($v['parent_id'] != 0) {
                    $tpl[$v['parent_id']][] = array(
                        'comment_id' => $v['comment_id'],
                        'comment_type' => $v['comment_type'],
                        'promotion_id' => $v['promotion_id'],
                        'huanxin_username' => $v['huanxin_username'],
                        'content' => $v['content'],
                        'comment_rank' => $v['comment_rank'],
                        'add_time' => $v['add_time'],
                        'nick_name' => $v['is_seller'] == 1 ? "掌柜" : $v['nick_name'],
                        'is_seller' => $v['is_seller']//0买家,1卖家
                    );
                }
            }

            //取主评论
            foreach ($result0 as $ko => $vo) {
                if ($vo['parent_id'] == 0) {
                    $info[] = array(
                        'comment_id' => $vo['comment_id'],
                        'comment_type' => $vo['comment_type'],
                        'promotion_id' => $vo['promotion_id'],
                        'huanxin_username' => $vo['huanxin_username'],
                        'content' => $vo['content'],
                        'comment_rank' => $vo['comment_rank'],
                        'add_time' => $vo['add_time'],
                        'poster' => $vo['poster'] ? URL . $vo['poster'] : "",
                        'nick_name' => $vo['nick_name'],
                        'is_seller' => $vo['is_seller'],//0买家,1卖家
                        'reply' => $tpl[$vo['comment_id']] ? $tpl[$vo['comment_id']] : array(),
                    );
                }
            }
        }
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result ['info'] = $info ? $info : array();
        echo json_encode($result);
    }
}

?>