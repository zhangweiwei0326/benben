<?php
class CollectGoodsController extends PublicController{
    public $layout = false;

    /*
     * 添加商品收藏
     * 涉及collect_goods表
     */
    public function actionAddCollect(){
        $this->check_key();
        $user = $this->check_user();
        $promotion_id = Frame::getIntFromRequest('promotion_id');
        $type = Frame::getStringFromRequest('type');
        if(empty($promotion_id)){
            $result['ret_num'] = 2015;
            $result['ret_msg'] = "缺少参数";
            echo json_encode($result);
            die();
        }
        $cinfo=CollectGoods::model()->find("promotion_id={$promotion_id} and member_id={$user['id']}");
        if($cinfo){
            $result['ret_num'] = 115;
            $result['ret_msg'] = "请勿重复收藏！";
            echo json_encode($result);
            die();
        }
        $collect=new CollectGoods();
        $collect->member_id=$user['id'];
        $collect->promotion_id=$promotion_id;
        $collect->add_time=time();
        $collect->is_attention=1;
        $collect->type=$type;
        if($collect->save()){
            $result['ret_num'] = 0;
            $result['ret_msg'] = "操作成功";
            echo json_encode($result);
        }else{
            $result['ret_num'] = 10;
            $result['ret_msg'] = "保存失败，请重新尝试！";
            echo json_encode($result);
        }
    }

    /*
     * 商品收藏列表
     * 涉及collect_goods表
     */
    public function actionCollectGoodsList(){
        $this->check_key();
        $user = $this->check_user();
        $collectlist=CollectGoods::model()->findAll("member_id={$user['id']}");
        foreach($collectlist as $k=>$v){
            if($v['type']==0){
                //促销
                $cparr[]=$v['promotion_id'];
            }elseif($v['type']==1){
                //团购
                $tgarr[]=$v['promotion_id'];
            }
        }
        //促销详情
        if($cparr) {
            $cpinfo = Promotion::model()->findAll("id in (" . implode(",", $cparr) . ")");
            foreach($cpinfo as $kc=>$vc){
                $cp[]=array(
                    "promotion_id"=>$vc['id'],
                    "name"=>$vc['name'],
                    "poster"=>URL.$vc['poster_st'],
                    "valid_left"=>$vc['valid_left'],
                    "valid_right"=>$vc['valid_right'],
                    "is_close"=>$vc['is_close'],
                    'price'=>$vc['promotion_price']
                );
            }
        }
        //团购详情
        if($tgarr){
            $tginfo = Promotion::model()->findAll("id in (" . implode(",", $tgarr) . ")");
            foreach($tginfo as $kg=>$vg){
                $gb[]=array(
                    "promotion_id"=>$vg['id'],
                    "name"=>$vg['name'],
                    "poster"=>URL.$vg['poster_st'],
                    "origion_price"=>$vg['origion_price'],
                    "promotion_price"=>$vg['promotion_price'],
                    "sellcount"=>$vg['sellcount'],
                    "valid_left"=>$vg['valid_left'],
                    "valid_right"=>$vg['valid_right'],
                    "is_close"=>$vg['is_close'],
                );
            }
        }
        $result['ret_num'] = 0;
        $result['ret_msg'] = "操作成功";
        $result['cp'] = $cp?$cp:array();
        $result['gb'] = $gb?$gb:array();
        echo json_encode($result);
    }

    /*
     * 删除收藏
     * 涉及collect_goods表
     */
    public function actionDelCollect(){
        $this->check_key();
        $user = $this->check_user();
        $promotion_id = Frame::getStringFromRequest('promotion_id');//以逗号隔开
        if(empty($promotion_id)){
            $result['ret_num'] = 2015;
            $result['ret_msg'] = "缺少参数";
            echo json_encode($result);
            die();
        }
        $cinfo=CollectGoods::model()->findAll("promotion_id in ({$promotion_id}) and member_id={$user['id']}");
        if(!$cinfo){
            $result['ret_num'] = 115;
            $result['ret_msg'] = "该收藏已经删除！请勿重复操作";
            echo json_encode($result);
            die();
        }
        if(CollectGoods::model()->deleteAll("promotion_id in ({$promotion_id}) and member_id={$user['id']}")){
            $result['ret_num'] = 0;
            $result['ret_msg'] = "操作成功";
            echo json_encode($result);
        }else{
            $result['ret_num'] = 4310;
            $result['ret_msg'] = "删除失败，请重新尝试";
            echo json_encode($result);
        }
    }
}