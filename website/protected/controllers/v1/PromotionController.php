<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/30
 * Time: 13:48
 */
class PromotionController extends PublicController
{
    public $layout = false;

    /*
     * 促销管理
     * 涉及Promotion和promotion_manage
     */
    public function actionPromotionmanage()
    {
        $this->check_key();
        $user = $this->check_user();
        if (!$this->storevip($user['id'])) {
            $result['ret_num'] = 1110;
            $result['ret_msg'] = "您已过商城试用期";
            echo json_encode($result);
            die();
        }
        $pminfo = PromotionManage::model()->find("member_id={$user['id']} and store_type=0");
        if(empty($pminfo)){
            $result['ret_num'] = 1220;
            $result['ret_msg'] = "您暂未开通促销";
            echo json_encode($result);
            die();
        }
        $pinfo = Promotion::model()->findAll("pm_id={$pminfo['id']}");

        //读取促销模版的封面图和名称,同时处理有效期的促销
        $now = time();
        $off = 0;//下线模版数量
        $on = 0;//上线模版数量
        $out_tpl = array();
        foreach ($pinfo as $kp => $vp) {
            $pinfo[$kp]['poster_st'] = $vp['poster_st'] ? URL . $vp['poster_st'] : "";
            //下线模版为被手动下线，或者超有效期
            if ($vp['is_close'] == 0) {
                if ($vp['valid_left'] > $now) {
                    //促销未开始
//                    if ($vp['is_close'] != 1) {
//                        $pinfo[$kp]['is_close'] = 1;
//                    }
//                    $off++;
                    $info_tpl[$kp]['is_reach'] = 0;
                    $on++;
                } elseif ($vp['valid_right'] <= $now) {
                    //促销已结束
//                    if ($vp['is_close'] != 1) {
//                        $pinfo[$kp]['is_close'] = 1;
//                    }
//                    $off++;
                    $info_tpl[$kp]['is_down'] = 1;
                    $on++;
                } elseif ($vp['valid_left'] <= $now && $now < $vp['valid_right']) {
                    //促销正在进行中
                    if ($vp['is_close'] != 0) {
                        $pinfo[$kp]['is_close'] = 0;
                    }
                    $info_tpl[$kp]['is_reach'] = 1;
                    $info_tpl[$kp]['is_down'] = 0;
                    $on++;
                }
            } else {
                $off++;
            }

            $out_tpl[] = array(
                "is_close" => $pinfo[$kp]['is_close'],
                "valid_left" => $pinfo[$kp]['valid_left'],
                "valid_right" => $pinfo[$kp]['valid_right'],
                "name" => $pinfo[$kp]['name'],
                "poster_st" => $this->getThumb($pinfo[$kp]['poster_st']),
                "pm_id" => $pinfo[$kp]['pm_id'],
                "promotionid" => $pinfo[$kp]['id'],
                "vip_time"=>$pinfo[$kp]['vip_time'],
                "is_overtime"=>($vp['vip_time']>time()) ? 0 : 1,
                "is_down"=> $info_tpl[$kp]['is_down'],
                "is_reach"=> $info_tpl[$kp]['is_reach'],
                "is_del"=> $pinfo[$kp]['is_del'],
            );
        }
        $result['ret_num'] = 0;
        $result['ret_msg'] = "操作成功";
        $result['on'] = $on;
        $result['off'] = $off;
        $result['offline_restrict'] = $pminfo['offline_restrict'];
        $result['online_restrict'] = $pminfo['online_restrict'];
        $result['promotion_info'] = $out_tpl;
        echo json_encode($result);
    }

    /*
     * 获取促销详情
     * 涉及Promotion和promotion_manage
     */
    public function actionGetpromotion()
    {
        $this->check_key();
        $user = $this->check_user();
        $promotionid = Frame::getIntFromRequest('promotionid');
        if (!$this->storevip($user['id'])) {
            $result['ret_num'] = 1110;
            $result['ret_msg'] = "您已过商城试用期";
            echo json_encode($result);
            die();
        }

        $promotion = Promotion::model()->find("id={$promotionid} and is_del=0");

        if($promotion) {
            $result['ret_num'] = 0;
            $result['ret_msg'] = "操作成功";
            $result['poster_st'] = $promotion['poster_st'] ? URL . $promotion['poster_st'] : "";
            $result['small_poster_st'] = $promotion['poster_st'] ? URL . $this->getThumb($promotion['poster_st']) : "";
            $result['poster_nd'] = $promotion['poster_nd'] ? URL . $promotion['poster_nd'] : "";
            $result['small_poster_nd'] = $promotion['poster_nd'] ? URL . $this->getThumb($promotion['poster_nd']) : "";
            $result['poster_rd'] = $promotion['poster_rd'] ? URL . $promotion['poster_rd'] : "";
            $result['small_poster_rd'] = $promotion['poster_rd'] ? URL . $this->getThumb($promotion['poster_rd']) : "";
            $result['name'] = $promotion['name'];
            $result['origion_price'] = $promotion['origion_price'];
            $result['promotion_price'] = $promotion['promotion_price'];
            $result['valid_left'] = $promotion['valid_left'];
            $result['valid_right'] = $promotion['valid_right'];
            $result['description'] = $promotion['description'];
            $result['is_close'] = $promotion['is_close'];
        }else{
            $result['ret_num'] = 1000;
            $result['ret_msg'] = "该促销已被禁用";
        }

        echo json_encode($result);
    }

    /*
     * 促销详情页显示
     */
    public function actionpromotionDetail()
    {
        $this->check_key();
        $promotionid = Frame::getIntFromRequest('promotionid');
        $promotion = Promotion::model()->find("id={$promotionid}");
        if($promotion) {
            if(empty($promotion['poster_st'])&&empty($promotion['poster_nd'])&&empty($promotion['poster_rd'])){
                throw new CHttpException(404,'该促销已结束');
            }
            if (preg_match('/(benben)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
                $result['shownext'] = 1;
            } else {
                $result['shownext'] = 0;
            }

            if ($promotion['poster_st']) {
                $result['pic'][] = URL . $promotion['poster_st'];
                if ($this->getThumb($promotion['poster_st'])) {
                    $result['smallpic'][] = URL . $this->getThumb($promotion['poster_st']);
                }
            }
            if ($promotion['poster_nd']) {
                $result['pic'][] = URL . $promotion['poster_nd'];
                if ($this->getThumb($promotion['poster_nd'])) {
                    $result['smallpic'][] = URL . $this->getThumb($promotion['poster_nd']);
                }
            }
            if ($promotion['poster_rd']) {
                $result['pic'][] = URL . $promotion['poster_rd'];
                if ($this->getThumb($promotion['poster_rd'])) {
                    $result['smallpic'][] = URL . $this->getThumb($promotion['poster_rd']);
                }
            }
            $pminfo=PromotionManage::model()->find("id={$promotion['pm_id']}");
            if($pminfo){
                //获取商城拥有者的信息
                $traininfo=NumberTrain::model()->find("id={$pminfo['store_id']}");
                $ownerInfo=Member::model()->find("id={$pminfo['member_id']}");
            }

            $result['name'] = $promotion['name'];
            $result['origion_price'] = $promotion['origion_price'];
            $result['promotion_price'] = $promotion['promotion_price'];
            $result['valid_left'] = $promotion['valid_left'];
            $result['valid_right'] = $promotion['valid_right'];
            $result['description'] = nl2br($promotion['description']);
            $result['is_close'] = $promotion['is_close'];
            $result['huanxin_username'] = $ownerInfo ? $ownerInfo['huanxin_username'] : "";//商家消息
            $result['tel'] = $traininfo ? ( $traininfo['phone'] ? ($traininfo['telephone'] ? $traininfo['phone']."#".$traininfo['telephone']:$traininfo['phone']): ($traininfo['telephone'] ?$traininfo['telephone']:"")):"";//商店电话
            $result['train_id'] = $pminfo ? $pminfo['store_id'] : "";//商店号
            $result['type'] = 0;//0是促销

            $this->render("detail", array("result" => $result));
        }else{
            throw new CHttpException(404,'该促销已结束.');
        }
    }

    /*
     * 添加促销模版
     * 涉及Promotion和promotion_manage
     */
    public function  actionAddpromotion()
    {
        $this->check_key();
        $user = $this->check_user();
        if (!$this->storevip($user['id'])) {
            $result['ret_num'] = 1110;
            $result['ret_msg'] = "您已过商城试用期";
            echo json_encode($result);
            die();
        }
        $name = Frame::getStringFromRequest('name');
        $origion_price = Frame::getStringFromRequest('origion_price');
        $promotion_price = Frame::getStringFromRequest('promotion_price');
        $valid_left = Frame::getIntFromRequest('valid_left');
        $valid_right = Frame::getIntFromRequest('valid_right');
        $description = Frame::getStringFromRequest('description');
        $poster_st = Frame::saveThumb('poster_st', 1);
        $poster_nd = Frame::saveThumb('poster_nd', 1);
        $poster_rd = Frame::saveThumb('poster_rd', 1);

        if (empty($name) || empty($origion_price) || empty($promotion_price) || empty($valid_left) || empty($valid_right) || empty($description) || (empty($poster_st) && empty($poster_nd) && empty($poster_rd))) {
            $result['ret_num'] = 2015;
            $result['ret_msg'] = "缺少参数";
            echo json_encode($result);
            die();
        }

        //判断是否达到模版限制
        $pminfo = PromotionManage::model()->find("member_id={$user['id']}");
        $pinfo = Promotion::model()->findAll("pm_id={$pminfo['id']}");
        $now = time();
        $off = 0;//下线模版数量
        $on = 0;//上线模版数量
        foreach ($pinfo as $kp => $vp) {
            $pinfo[$kp]['poster_st'] = $vp['poster_st'] ? URL . $vp['poster_st'] : "";

            //下线模版为被手动下线，或者不再有效期内
            if ($vp['is_close'] == 0) {
                if ($vp['valid_left'] > $now) {
                    //促销未开始
//                    if ($vp['is_close'] != 1) {
//                        $pinfo[$kp]['is_close'] = 1;
//                    }
                    $on++;
                } elseif ($vp['valid_right'] <= $now) {
                    //促销已结束
//                    if ($vp['is_close'] != 1) {
//                        $pinfo[$kp]['is_close'] = 1;
//                    }
                    $on++;
                } elseif ($vp['valid_left'] <= $now && $now < $vp['valid_right']) {
                    //促销正在进行中
                    if ($vp['is_close'] != 0) {
                        $pinfo[$kp]['is_close'] = 0;
                    }
                    $on++;
                }
            } else {
                $off++;
            }
        }

        if ($off > $pminfo['offline_restrict']) {
            $result['ret_num'] = 1210;
            $result['ret_msg'] = "下架商品已超过仓库上限5个，请清理后再新增！";
            echo json_encode($result);
            die();
        }

        if ($on >= $pminfo['online_restrict']) {
            $result['ret_num'] = 1310;
            $result['ret_msg'] = "上架商品已超过上限，请下架或删除后再新增！";
            echo json_encode($result);
            die();
        }

        $promotion_add = new Promotion();
        $promotion_add->pm_id = $pminfo['id'];
        $promotion_add->name = $name;
        $promotion_add->origion_price = $origion_price;
        $promotion_add->promotion_price = $promotion_price;
        $promotion_add->valid_left = $valid_left;
        $promotion_add->valid_right = $valid_right;
        $promotion_add->description = $description;
        $promotion_add->poster_st = $poster_st;
        $promotion_add->poster_nd = $poster_nd;
        $promotion_add->poster_rd = $poster_rd;
        $promotion_add->goods_sn="BB".time();
        $promotion_add->is_close = 0;
        $promotion_add->vip_time = $pminfo['vip_time'];//有效期至2016年3月31日00:00:00
        $promotion_add->type = 0;
        $promotion_add->save();

        $result['ret_num'] = 0;
        $result['ret_msg'] = "操作成功";
        echo json_encode($result);
    }

    /*
     * 编辑促销模版
     * 涉及Promotion和promotion_manage
     */
    public function actionEditpromotion()
    {
        $this->check_key();
        $user = $this->check_user();
        if (!$this->storevip($user['id'])) {
            $result['ret_num'] = 1110;
            $result['ret_msg'] = "您已过商城试用期";
            echo json_encode($result);
            die();
        }

        $promotionid = Frame::getIntFromRequest('promotionid');
        $name = Frame::getStringFromRequest('name');
        $origion_price = Frame::getStringFromRequest('origion_price');
        $promotion_price = Frame::getStringFromRequest('promotion_price');
        $valid_left = Frame::getIntFromRequest('valid_left');
        $valid_right = Frame::getIntFromRequest('valid_right');
        $description = Frame::getStringFromRequest('description');
        $ids = Frame::getStringFromRequest('ids');//促销图片id，以逗号分割，st=1,nd=2,rd=3,用于删除
        $poster_st = Frame::saveThumb('poster_st', 1);
        $poster_nd = Frame::saveThumb('poster_nd', 1);
        $poster_rd = Frame::saveThumb('poster_rd', 1);

        if (empty($promotionid)) {
            $result['ret_num'] = 2015;
            $result['ret_msg'] = "缺少参数";
            echo json_encode($result);
            die();
        }

        $pinfo = Promotion::model()->find("id={$promotionid} and is_del=0");
        $type=array();//促销图片id数组
        if($pinfo) {
            $type=explode(",",$ids);
            if ($name) {
                $pinfo->name = $name;
            }
            if ($origion_price) {
                $pinfo->origion_price = $origion_price;
            }
            if ($promotion_price) {
                $pinfo->promotion_price = $promotion_price;
            }
            if ($valid_left) {
                $pinfo->valid_left = $valid_left;
            }
            if ($valid_right) {
                $pinfo->valid_right = $valid_right;
            }
            if ($description) {
                $pinfo->description = $description;
            }

            //有type才可以删除
            if($type) {
                if (in_array(1, $type)) {
                    if(file_exists(ROOT.$pinfo->poster_st)&&$pinfo->poster_st) {
                        unlink(ROOT . $pinfo->poster_st);
                        unlink(ROOT . $this->getThumb($pinfo->poster_st));
                    }
                    $pinfo->poster_st = "";
                }
                if (in_array(2, $type)) {
                    if(file_exists(ROOT.$pinfo->poster_nd)&&$pinfo->poster_nd) {
                        unlink(ROOT . $pinfo->poster_nd);
                        unlink(ROOT . $this->getThumb($pinfo->poster_nd));
                    }
                    $pinfo->poster_nd = "";
                }
                if (in_array(3, $type)) {
                    if(file_exists(ROOT.$pinfo->poster_rd)&&$pinfo->poster_rd) {
                        unlink(ROOT . $pinfo->poster_rd);
                        unlink(ROOT . $this->getThumb($pinfo->poster_rd));
                    }
                    $pinfo->poster_rd = "";
                }
            }

            if ($poster_st) {
                $pinfo->poster_st = $poster_st;
            }
            if ($poster_nd) {
                $pinfo->poster_nd = $poster_nd;
            }
            if ($poster_rd) {
                $pinfo->poster_rd = $poster_rd;
            }

            $pinfo->update();
            $result['ret_num'] = 0;
            $result['ret_msg'] = "操作成功";
            echo json_encode($result);
        }else{
            $result['ret_num'] = 1115;
            $result['ret_msg'] = "该促销项目已被禁用";
            echo json_encode($result);
            die();
        }
    }

    /*
     * 删除促销模版
     * 涉及Promotion和promotion_manage
     */
    public function actionDelpromotion(){
        $this->check_key();
        $user = $this->check_user();
        if (!$this->storevip($user['id'])) {
            $result['ret_num'] = 1110;
            $result['ret_msg'] = "您已过商城试用期";
            echo json_encode($result);
            die();
        }
        $promotionid = Frame::getIntFromRequest('promotionid');
        if(empty($promotionid)){
            $result['ret_num'] = 2015;
            $result['ret_msg'] = "缺少参数";
            echo json_encode($result);
            die();
        }
        $pinfo=Promotion::model()->find("id={$promotionid}");
        if($pinfo){
            //排空数据库图库，首张封面图不清空
            if(file_exists(ROOT.$pinfo['poster_nd'])&&$pinfo['poster_nd']){
                unlink(ROOT.$pinfo['poster_nd']);
                if(file_exists(ROOT.$this->getThumb($pinfo['poster_nd']))) {
                    unlink(ROOT . $this->getThumb($pinfo['poster_nd']));
                }
            }
            if(file_exists(ROOT.$pinfo['poster_rd'])&&$pinfo['poster_rd']){
                unlink(ROOT.$pinfo['poster_rd']);
                if(file_exists(ROOT.$this->getThumb($pinfo['poster_rd']))) {
                    unlink(ROOT . $this->getThumb($pinfo['poster_rd']));
                }
            }
            $pinfo->delete();
        }
        $result['ret_num'] = 0;
        $result['ret_msg'] = "操作成功";
        echo json_encode($result);
    }

    /*
     * 上架、下架促销模版
     * 涉及Promotion和promotion_manage
     */
    public function actionTogglepromotion(){
        $this->check_key();
        $user = $this->check_user();
        if (!$this->storevip($user['id'])) {
            $result['ret_num'] = 1110;
            $result['ret_msg'] = "您已过商城试用期";
            echo json_encode($result);
            die();
        }
        $promotionid = Frame::getIntFromRequest('promotionid');
        $is_close = Frame::getIntFromRequest('is_close');

        if(empty($promotionid)){
            $result['ret_num'] = 2015;
            $result['ret_msg'] = "缺少参数";
            echo json_encode($result);
            die();
        }

        //判断是否达到模版限制
        $pminfo = PromotionManage::model()->find("member_id={$user['id']}");
        $pinfo = Promotion::model()->findAll("pm_id={$pminfo['id']}");
        $now = time();
        $off = 0;//下线模版数量
        $on = 0;//上线模版数量
        foreach ($pinfo as $kp => $vp) {
            $pinfo[$kp]['poster_st'] = $vp['poster_st'] ? URL . $vp['poster_st'] : "";

            //下线模版为被手动下线，或者不再有效期内
            if ($vp['is_close'] == 0) {
                if ($vp['valid_left'] > $now) {
                    //促销未开始
                    if ($vp['is_close'] != 1) {
                        $pinfo[$kp]['is_close'] = 1;
                    }
                    $off++;
                } elseif ($vp['valid_right'] <= $now) {
                    //促销已结束
                    if ($vp['is_close'] != 1) {
                        $pinfo[$kp]['is_close'] = 1;
                    }
                    $off++;
                } elseif ($vp['valid_left'] <= $now && $now < $vp['valid_right']) {
                    //促销正在进行中
                    if ($vp['is_close'] != 0) {
                        $pinfo[$kp]['is_close'] = 0;
                    }
                    $on++;
                }
            } else {
                $off++;
            }
        }

        //下架
        if($is_close==1){
            if($off>=$pminfo['offline_restrict']){
                $result['ret_num'] = 1210;
                $result['ret_msg'] = "没有更多的窗口可以下架！";
                echo json_encode($result);
                die();
            }
            Promotion::model()->updateAll(array("is_close"=>$is_close),"id={$promotionid}");
        }

        //上架
        if($is_close==0){
            if ($on >= $pminfo['online_restrict']) {
                $result['ret_num'] = 1310;
                $result['ret_msg'] = "没有更多的窗口可以上架！";
                echo json_encode($result);
                die();
            }
            Promotion::model()->updateAll(array("is_close"=>$is_close),"id={$promotionid}");
        }

        $result['ret_num'] = 0;
        $result['ret_msg'] = "操作成功";
        echo json_encode($result);
    }

//    /*
//     * 删除促销图片
//     * id=1删除poster_st；id=2删除poster_nd；id=3删除poster_rd
//     */
//    public function actionDelpic(){
//        $this->check_key();
//        $user = $this->check_user();
//        $id = Frame::getIntFromRequest('id');
//        $promotionid = Frame::getIntFromRequest('promotionid');
//        if(empty($id)){
//            $result['ret_num'] = 2015;
//            $result['ret_msg'] = "缺少参数";
//            echo json_encode($result);
//            die();
//        }
//
//        $pinfo=Promotion::model()->find("pm_id={$promotionid}");
//        if($id==1){
//            unlink($pinfo->poster_st);
//            unlink($this->getThumb($pinfo->poster_st));
//            $pinfo->poster_st="";
//            $pinfo->update();
//        }
//        if($id==2){
//            unlink($pinfo->poster_nd);
//            unlink($this->getThumb($pinfo->poster_nd));
//            $pinfo->poster_nd="";
//            $pinfo->update();
//        }
//        if($id==3){
//            unlink($pinfo->poster_rd);
//            unlink($this->getThumb($pinfo->poster_rd));
//            $pinfo->poster_rd="";
//            $pinfo->update();
//        }
//
//        $result['ret_num'] = 0;
//        $result['ret_msg'] = "操作成功";
//        echo json_encode($result);
//    }
}