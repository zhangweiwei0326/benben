<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/30
 * Time: 13:48
 */
class GroupBuyController extends PublicController
{
    public $layout = false;

    /*
     * 团购管理
     * 涉及Promotion和promotion_manage
     */
    public function actionGroupmanage()
    {
        $this->check_key();
        $user = $this->check_user();
        if (!$this->storevip($user['id'])) {
            $result['ret_num'] = 1110;
            $result['ret_msg'] = "您已过商城试用期";
            echo json_encode($result);
            die();
        }
        $pminfo = PromotionManage::model()->find("member_id={$user['id']} and store_type=1");
        if(empty($pminfo)){
            $result['ret_num'] = 1220;
            $result['ret_msg'] = "您暂未开通团购";
            echo json_encode($result);
            die();
        }
        $pinfo = Promotion::model()->findAll("pm_id={$pminfo['id']} and type=1");

        //读取团购模版的封面图和名称,同时处理有效期的促销
        $now = time();
        $off = 0;//下线模版数量
        $on = 0;//上线模版数量
        $out_tpl = array();
        foreach ($pinfo as $kp => $vp) {
            $goodsinfo=GoodsGallery::model()->find("goods_id={$vp['id']} order by img_desc asc");
            $pinfo[$kp]['poster_st']=$goodsinfo['img_url']?URL.$goodsinfo['img_url']:"";
            //下线模版为被手动下线，或者超有效期
            if ($vp['is_close'] == 0) {
                if ($vp['valid_left'] > $now) {
                    //团购未开始
//                    if ($vp['is_close'] != 1) {
//                        $pinfo[$kp]['is_close'] = 1;
//                    }
//                    $off++;
                    $info_tpl[$kp]['is_reach'] = 0;
                    $on++;
                } elseif ($vp['valid_right'] <= $now) {
                    //团购已结束
//                    if ($vp['is_close'] != 1) {
//                        $pinfo[$kp]['is_close'] = 1;
//                    }
//                    $off++;
                    $info_tpl[$kp]['is_down'] = 1;
                    $on++;
                } elseif ($vp['valid_left'] <= $now && $now < $vp['valid_right']) {
                    //团购正在进行中
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
                "origion_price" => $pinfo[$kp]['origion_price'],
                "promotion_price" => $pinfo[$kp]['promotion_price'],
                "sellcount"=>$pinfo[$kp]['sellcount'],
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
     * 获取团购详情
     * 涉及Promotion和promotion_manage,payment
     */
    public function actionGetgroupbuy()
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

        $promotion = Promotion::model()->find("id={$promotionid} and is_del=0 and type=1");
        if($promotion) {
            $imginfo = GoodsGallery::model()->findAll("goods_id={$promotionid} order by img_desc asc");
            foreach ($imginfo as $v) {
                $pic[] = array(
                    "img_id" => $v['img_id'],
                    "img_url" => $v['img_url'] ? URL . $v['img_url'] : "",
                    "small_img" => $v['img_url'] ? URL . $this->getThumb($v['img_url']) : "",
                );
            }
            if ($promotion['pay_ids']) {
                $pay_arr = explode(",", $promotion['pay_ids']);
                $paytpl = Payment::model()->findAll("enabled=1");
                foreach ($paytpl as $kpay => $vpay) {
                    $payinfo[$kpay]['pay_id'] = $vpay['pay_id'];
                    $payinfo[$kpay]['pay_name'] = $vpay['pay_name'];

                    if (in_array($vpay['pay_id'], $pay_arr)) {
                        $payinfo[$kpay]['is_chosen'] = 1;
                    } else {
                        $payinfo[$kpay]['is_chosen'] = 0;
                    }
                }
            }

            $result['ret_num'] = 0;
            $result['ret_msg'] = "操作成功";
            $result['name'] = $promotion['name'];
            $result['origion_price'] = $promotion['origion_price'];
            $result['promotion_price'] = $promotion['promotion_price'];
            $result['valid_left'] = $promotion['valid_left'];
            $result['valid_right'] = $promotion['valid_right'];
            $result['description'] = $promotion['description'];
            $result['is_close'] = $promotion['is_close'];
            $result['sellcount'] = $promotion['sellcount'];
            $result['mustknow'] = $promotion['mustknow'];
            $result['model'] = $promotion['model'];
            $result['shipping_fee'] = $promotion['shipping_fee'];
            $result['poster'] = $pic ? $pic : array();
            $result['paymethods'] = $payinfo ? $payinfo : array();
        }else{
            $result['ret_num'] = 1000;
            $result['ret_msg'] = "该团购已被禁用";
        }

        echo json_encode($result);
    }

    /*
     * 团购详情页显示
     * 待完善
     */
    public function actiongroupDetail()
    {
        $this->check_key();
        $user = $this->check_user();
        $promotionid = Frame::getIntFromRequest('promotionid');
        $promotion = Promotion::model()->find("id={$promotionid} and is_del=0 and is_close=0 and type=1");

        if($promotion) {
            $imginfo = GoodsGallery::model()->findAll("goods_id={$promotionid} order by img_desc asc");
            foreach ($imginfo as $v) {
                $pic[] = array(
                    "img_id" => $v['img_id'],
                    "img_url" => $v['img_url'] ? URL . $v['img_url'] : "",
                    "small_img" => $v['img_url'] ? URL . $this->getThumb($v['img_url']) : "",
                );
            }
            if ($promotion['pay_ids']) {
                $pay_arr = explode(",", $promotion['pay_ids']);
                $paytpl = Payment::model()->findAll("enabled=1");
                foreach ($paytpl as $kpay => $vpay) {
                    if (in_array($vpay['pay_id'], $pay_arr)) {
                        if ($vpay['pay_id'] == 1) {
                            $payinfo[] = array("pay_id"=>1,"pay_name"=>"快递发货");
                        }
                        if ($vpay['pay_id'] == 2) {
                            $payinfo[] = array("pay_id"=>2,"pay_name"=>"在线付");
                        }
                        if ($vpay['pay_id'] == 3) {
                            $payinfo[] = array("pay_id"=>3,"pay_name"=>"到店付");
                        }
                    }
                }
            }

            if (preg_match('/(benben)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
                $result['shownext'] = 1;
            } else {
                $result['shownext'] = 0;
            }

            $result['ret_num'] = 0;
            $result['ret_msg'] = "操作成功";
            $result['name'] = $promotion['name'];
            $result['origion_price'] = $promotion['origion_price'];
            $result['promotion_price'] = $promotion['promotion_price'];
            $result['valid_left'] = $promotion['valid_left'];
            $result['valid_right'] = $promotion['valid_right'];
            $result['description'] = $promotion['description'];
            $result['is_close'] = $promotion['is_close'];
            $result['sellcount'] = $promotion['sellcount'];
            $result['mustknow'] = $promotion['mustknow'];
            $result['model'] = $promotion['model'];
            $result['shipping_fee'] = $promotion['shipping_fee'];
            $result['poster'] = $pic ? $pic : array();
            $result['pay_methods'] = $payinfo ? $payinfo : array();
            $result['coins'] = $user ? $user->coin : 0;
            $result['fee'] = $user ? $user->fee : 0;
            $result['comment_num'] = 112;
        }else{
            $result['ret_num'] = 1000;
            $result['ret_msg'] = "该团购已被禁用或已关闭";
        }
        echo json_encode($result);
    }

    public function actiongroupbuyDetail()
    {
        header("Content-type: text/html; charset=utf-8");
        $this->check_key();
        $promotionid = Frame::getIntFromRequest('promotionid');
        $promotion = Promotion::model()->find("id={$promotionid} and is_del=0 and is_close=0");

        if($promotion) {
            $imginfo = GoodsGallery::model()->findAll("goods_id={$promotionid} order by img_desc asc");
            $pminfo=PromotionManage::model()->find("id={$promotion['pm_id']}");
            if($pminfo){
                //获取商城拥有者的信息
                $traininfo=NumberTrain::model()->find("id={$pminfo['store_id']}");
                $ownerInfo=Member::model()->find("id={$pminfo['member_id']}");
            }
            foreach ($imginfo as $v) {
                $pic[] = array(
                    "img_id" => $v['img_id'],
                    "img_url" => $v['img_url'] ? URL . $v['img_url'] : "",
                    "small_img" => $v['img_url'] ? URL . $this->getThumb($v['img_url']) : "",
                );
            }
            if ($promotion['pay_ids']) {
                $pay_arr = explode(",", $promotion['pay_ids']);
                $paytpl = Payment::model()->findAll("enabled=1");
                foreach ($paytpl as $kpay => $vpay) {
                    if (in_array($vpay['pay_id'], $pay_arr)) {
                        if ($vpay['pay_id'] == 1) {
                            $payinfo[] = array("pay_id"=>1,"pay_name"=>"快递发货");
                        }
                        if ($vpay['pay_id'] == 2) {
                            $payinfo[] = array("pay_id"=>2,"pay_name"=>"在线付");
                        }
                        if ($vpay['pay_id'] == 3) {
                            $payinfo[] = array("pay_id"=>3,"pay_name"=>"到店付");
                        }
                    }
                }
            }

            if (preg_match('/(benben)/i', strtolower($_SERVER['HTTP_USER_AGENT']))) {
                $result['shownext'] = 1;
            } else {
                $result['shownext'] = 0;
            }

            $connection = Yii::app()->db;
            $tpl = array();
            //查询非商家的主回复
            $sql = "select a.parent_id,a.comment_rank from store_comment as a left join member as b on a.member_id=b.id where a.promotion_id=" . $promotionid . " and ((a.parent_id=0 and a.is_seller!=1) or
        (a.parent_id not in (select comment_id from store_comment where parent_id=0 and is_seller=1) and a.parent_id!=0)) order by a.comment_id Desc";
            $command = $connection->createCommand($sql);
            $result0 = $command->queryAll();
            if ($result0) {
                //取主评论
                $good=0;
                $allComment=0;
                foreach ($result0 as $ko => $vo) {
                    if ($vo['parent_id'] == 0) {
                       $allComment++;
                        //取好评数
                        if($vo['comment_rank']==3){
                            $good++;
                        }
                    }
                }
            }

            $result['ret_num'] = 0;
            $result['ret_msg'] = "操作成功";
            $result['promotionid'] = $promotionid;
            $result['name'] = $promotion['name'];
            $result['origion_price'] = $promotion['origion_price'];
            $result['promotion_price'] = $promotion['promotion_price'];
            $result['valid_left'] = $promotion['valid_left'];
            $result['valid_right'] = $promotion['valid_right'];
            $result['description'] = nl2br($promotion['description']);
            $result['is_close'] = $promotion['is_close'];
            $result['sellcount'] = $promotion['sellcount'];
            $result['mustknow'] = nl2br($promotion['mustknow']);
            $result['model'] = $promotion['model'];
            $result['shipping_fee'] = $promotion['shipping_fee'];
            $result['poster'] = $pic ? $pic : array();
            $result['pay_methods'] = $payinfo ? $payinfo : array();
            $result['good_rate'] = $allComment ? (number_format($good/$allComment,4,".","")*100)."%" : "100%";
            $result['comment_num'] = 112;
            $result['huanxin_username'] = $ownerInfo ? $ownerInfo['huanxin_username'] : "";//商家消息
            $result['tel'] = $traininfo ? ( $traininfo['phone'] ? ($traininfo['telephone'] ? $traininfo['phone']."#".$traininfo['telephone']:$traininfo['phone']): ($traininfo['telephone'] ?$traininfo['telephone']:"")):"";//商店电话
            $result['train_id'] = $pminfo ? $pminfo['store_id'] : "";//商店号
            $result['type'] = 1;//1是团购
            $this->render("detail", array("result" => $result));
        }else{
            $result['ret_num'] = 1000;
            $result['ret_msg'] = "该团购已被禁用或已关闭";
            echo("<h1 style='text-align: center;font-size:60px;'>404 页面不存在！</h1>");
        }
    }

    /*
     * 添加团购模版
     * 涉及Promotion和promotion_manage
     */
    public function  actionAddgroupbuy()
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
        $pic[] = Frame::saveThumb('pic1', 1);//封面照
        $pic[] = Frame::saveThumb('pic2', 1);
        $pic[] = Frame::saveThumb('pic3', 1);
        $pic[] = Frame::saveThumb('pic4', 1);
        $pic[] = Frame::saveThumb('pic5', 1);
        $pic[] = Frame::saveThumb('pic6', 1);
        $pay_ids=Frame::getStringFromRequest('pay_ids');
        $shipping_fee=Frame::getStringFromRequest('shipping_fee');
        $mustknow=Frame::getStringFromRequest('mustknow');
        $model=Frame::getStringFromRequest('model');

        if (empty($mustknow)||empty($pay_ids)||empty($name) || empty($origion_price) || empty($promotion_price) || empty($valid_left) || empty($valid_right) || empty($description) || (empty($pic) )) {
            $result['ret_num'] = 2015;
            $result['ret_msg'] = "缺少参数";
            echo json_encode($result);
            die();
        }

        $connection=Yii::app()->db;
        //判断是否达到模版限制
        $pminfo = PromotionManage::model()->find("member_id={$user['id']} and store_type=1");
        if(empty($pminfo)){
            $result['ret_num'] = 1220;
            $result['ret_msg'] = "您暂未开通团购";
            echo json_encode($result);
            die();
        }
        $pinfo = Promotion::model()->findAll("pm_id={$pminfo['id']} and type=1");
        $now = time();
        $off = 0;//下线模版数量
        $on = 0;//上线模版数量
        foreach ($pinfo as $kp => $vp) {
            //下线模版为被手动下线，或者不再有效期内
            if ($vp['is_close'] == 0) {
                if ($vp['valid_left'] > $now) {
                    //团购未开始
//                    if ($vp['is_close'] != 1) {
//                        $pinfo[$kp]['is_close'] = 1;
//                    }
                    $on++;
                } elseif ($vp['valid_right'] <= $now) {
                    //团购已结束
//                    if ($vp['is_close'] != 1) {
//                        $pinfo[$kp]['is_close'] = 1;
//                    }
                    $on++;
                } elseif ($vp['valid_left'] <= $now && $now < $vp['valid_right']) {
                    //团购正在进行中
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
        $promotion_add->poster_st = $pic[0];
        $promotion_add->valid_left = $valid_left;
        $promotion_add->valid_right = $valid_right;
        $promotion_add->description = $description;
        $promotion_add->goods_sn="BB".time();
        $promotion_add->pay_ids=$pay_ids;
        if(in_array(1,explode(",",$pay_ids))) {
            if ($shipping_fee) {
                $promotion_add->shipping_fee=$shipping_fee;
            }
        }
        $promotion_add->is_close = 0;
        $promotion_add->vip_time = $pminfo['vip_time'];//有效期至2016年3月31日00:00:00
        $promotion_add->mustknow = $mustknow;
        $promotion_add->model = $model;
        $promotion_add->type=1;
        if($promotion_add->save()){
             $pp=0;//图片序号
             foreach($pic as $v){
                 if($v){
                     $pp++;
                     $insert_arr[]="(".$promotion_add->id.",'".$v."','".$pp."')";
                 }
             }
            if($insert_arr) {
                $sqlc = "insert into goods_gallery (goods_id,img_url,img_desc) values " . implode(",", $insert_arr);
                $command = $connection->createCommand($sqlc);
                $resultc = $command->execute();
            }
        }

        $result['ret_num'] = 0;
        $result['ret_msg'] = "操作成功";
        echo json_encode($result);
    }

    /*
     * 编辑团购模版
     * 涉及Promotion和promotion_manage
     */
    public function actionEditgroupbuy()
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
        $origion_price = Frame::getStringFromRequest('origion_price');
        $promotion_price = Frame::getStringFromRequest('promotion_price');
        $valid_left = Frame::getIntFromRequest('valid_left');
        $valid_right = Frame::getIntFromRequest('valid_right');
        $description = Frame::getStringFromRequest('description');
        $ids = Frame::getStringFromRequest('ids');//团购图片id，以逗号分割,用于删除
        $pic[] = $pic1=Frame::saveThumb('pic1', 1);//封面照
        $pic[] = Frame::saveThumb('pic2', 1);
        $pic[] = Frame::saveThumb('pic3', 1);
        $pic[] = Frame::saveThumb('pic4', 1);
        $pic[] = Frame::saveThumb('pic5', 1);
        $pic[] = Frame::saveThumb('pic6', 1);
        $pay_ids=Frame::getStringFromRequest('pay_ids');//团购支付id，以逗号分割
        $shipping_fee=Frame::getStringFromRequest('shipping_fee');
        $mustknow=Frame::getStringFromRequest('mustknow');
        $model=Frame::getStringFromRequest('model');

        if (empty($promotionid)) {
            $result['ret_num'] = 2015;
            $result['ret_msg'] = "缺少参数";
            echo json_encode($result);
            die();
        }

        $connection=Yii::app()->db;
        $pinfo = Promotion::model()->find("id={$promotionid} and is_del=0 and type=1");
        if($pinfo) {
            if ($pay_ids) {
                $pinfo->pay_ids = $pay_ids;
            }
            if(in_array(1,explode(",",$pay_ids))) {
                if ($shipping_fee) {
                    $pinfo->shipping_fee = $shipping_fee;
                }
            }
            if($mustknow){
                $pinfo->mustknow = $mustknow;
            }
            if($model){
                $pinfo->model = $model;
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
            if($pic1){
                $pinfo->poster_st = $pic1;
            }

            //有图片批量删除,先删除服务器文件
            if($ids) {
                $goods_img=GoodsGallery::model()->findAll("img_id in ({$ids}) and goods_id={$promotionid}");
                foreach($goods_img as $vg){
                    $img_desc[]=$vg['img_desc'];
                    if($vg['img_url']){
                        if(file_exists(ROOT.$vg['img_url'])){
                            unlink(ROOT.$vg['img_url']);
                        }
                        if(file_exists(ROOT.$this->getThumb($vg['img_url']))){
                            unlink(ROOT.$this->getThumb($vg['img_url']));
                        }
                    }
                }
                GoodsGallery::model()->deleteAll("img_id in ({$ids}) and goods_id={$promotionid}");
            }

            //保存图片
            foreach($pic as $vpp){
                if($vpp) {
                    //如果有删除图片
                    if ($img_desc) {
                        //如果出现图片顺序1的说明需要改封面
                        if (in_array(1, $img_desc)) {
                            $insert_arr[] = "(" . $promotionid . ",'" . $vpp . "','1')";
                            $key=array_search(1,$img_desc);
                            unset($img_desc[$key]);
                        } elseif(in_array(2, $img_desc)) {
                            $insert_arr[] = "(" . $promotionid . ",'" . $vpp . "','2')";
                            $key=array_search(2,$img_desc);
                            unset($img_desc[$key]);
                        }elseif(in_array(3, $img_desc)) {
                            $insert_arr[] = "(" . $promotionid . ",'" . $vpp . "','3')";
                            $key=array_search(3,$img_desc);
                            unset($img_desc[$key]);
                        }elseif(in_array(4, $img_desc)) {
                            $insert_arr[] = "(" . $promotionid . ",'" . $vpp . "','4')";
                            $key=array_search(4,$img_desc);
                            unset($img_desc[$key]);
                        }elseif(in_array(5, $img_desc)) {
                            $insert_arr[] = "(" . $promotionid . ",'" . $vpp . "','5')";
                            $key=array_search(5,$img_desc);
                            unset($img_desc[$key]);
                        }elseif(in_array(6, $img_desc)) {
                            $insert_arr[] = "(" . $promotionid . ",'" . $vpp . "','6')";
                            $key=array_search(6,$img_desc);
                            unset($img_desc[$key]);
                        }
                    }else{
                        $insert_arr[] = "(" . $promotionid . ",'" . $vpp . "','6')";
                    }
                }
            }
            if($insert_arr) {
                $sqlc = "insert into goods_gallery (goods_id,img_url,img_desc) values " . implode(",", $insert_arr);
                $command = $connection->createCommand($sqlc);
                $resultc = $command->execute();

                $gginfo = GoodsGallery::model()->findAll("goods_id={$promotionid} order by img_desc asc");
                foreach ($gginfo as $kg => $vg) {
                    GoodsGallery::model()->updateAll(array("img_desc" => $kg + 1), "img_id={$vg['img_id']}");
                }
            }

            //立即上线
            //$pinfo->is_close = 0;

            $pinfo->update();
            $result['ret_num'] = 0;
            $result['ret_msg'] = "操作成功";
            echo json_encode($result);
        }else{
            $result['ret_num'] = 1115;
            $result['ret_msg'] = "该团购项目已被禁用或者不存在";
            echo json_encode($result);
            die();
        }
    }

    /*
     * 删除团购模版
     * 涉及Promotion和promotion_manage
     */
    public function actionDelgroupbuy(){
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
        $pinfo=Promotion::model()->find("id={$promotionid} and type=1");
        if($pinfo){
            //排空服务器图集,首张封面图不清空
            $ginfo=GoodsGallery::model()->findAll("goods_id={$promotionid} order by img_desc asc");
            foreach($ginfo as $k=>$v){
                if($v['img_url'] && $v['img_desc']!=1){
                    if(file_exists(ROOT.$v['img_url'])){
                        unlink(ROOT.$v['img_url']);
                        unlink(ROOT.$this->getThumb($v['img_url']));
                    }
                }
            }
            GoodsGallery::model()->deleteAll("goods_id={$promotionid}");
            $pinfo->delete();
        }
        $result['ret_num'] = 0;
        $result['ret_msg'] = "操作成功";
        echo json_encode($result);
    }

    /*
     * 上架、下架团购模版
     * 涉及Promotion和promotion_manage
     */
    public function actionTogglegroupbuy(){
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
        if(empty($pminfo)){
            $result['ret_num'] = 1220;
            $result['ret_msg'] = "您暂未开通团购";
            echo json_encode($result);
            die();
        }
        $pinfo = Promotion::model()->findAll("pm_id={$pminfo['id']} and type=1");
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