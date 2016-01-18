<?php

class TopAuctionController extends PublicController
{
    public $layout = false;

    /*
     * 获取所有拍卖场
     * 涉及auction表
     */
    public function actionGetAuctionList()
    {
        $this->check_key();
        $user = $this->check_user();
        $connection = Yii::app()->db;

        $traininfo = NumberTrain::model()->find("member_id={$user['id']}");
        if (!$traininfo) {
            $result ['ret_num'] = 511;
            $result ['ret_msg'] = '号码直通车不存在！';
            echo json_encode($result);
            die();
        }
        $province = $traininfo['province'];
        $city = $traininfo['city'];
        $area = $traininfo['area'];
        $now_time = time();
        //获取1级行业
        $iifno = Industry::model()->find("id={$traininfo['industry']}");
        $parent_id = $iifno['parent_id'];
        $industry_id = $iifno['id'];
        for ($i = 0; $i < $iifno['level'] - 1; $i++) {
            $industryinfo = Industry::model()->find("id={$parent_id}");
            $parent_id = $industryinfo['parent_id'];
            $industry_id = $industryinfo['id'];
        }
        $search_str = "is_close=0 and pid=0 and end_time>" . $now_time . " and province=" . $province . " and city=" . $city . " and area=" . $area . "
         and (industry=0 or industry=" . $industry_id . ")";
        $toplist = TopAuction::model()->findAll("$search_str");
        foreach ($toplist as $k => $v) {
            //判断是否结束拍卖
            if ($v['end_time'] <= $now_time) {
                $is_close = 1;
            } else {
                $is_close = 0;
            }
            //判断是否开始拍卖
            if ($v['start_time'] <= $now_time) {
                $is_start = 1;
            } else {
                $is_start = 0;
            }
            //获取剩余时间
            if ($is_start == 1 && $is_close == 0) {
                $rest_time = $v['end_time'] - $now_time;
            } else {
                $rest_time = 0;
            }
            //获取地域
            $area_arr['province'] = $province;
            $area_arr['city'] = $city;
            $area_arr['area'] = $area;
            $district = $this->ProCity(array(0 => $area_arr));
            $district_str = $district[$v['province']] . "-" . $district[$v['city']] . "-" . $district[$v['area']];
            //获取行业
            $industry_info = Industry::model()->find("id={$v['industry']}");
            //参与人数
            $sql = "select count(1) as num from auction_log as a left join top_auction as b on a.auction_id=b.auction_id where b.pid=" . $v['auction_id'];
            $command = $connection->createCommand($sql);
            $result1 = $command->queryAll();
            $partin = $result1[0]['num'];
            //用户所有可参与的拍卖
            $auction_arr[] = $v['auction_id'];

            $info[] = array(
                "auction_id" => $v['auction_id'],
                "start_price" => $v['start_price'],
                "top_period" => date("Y-m-d", $v['top_start_period']) . "至" . date("Y-m-d", $v['top_end_period']),
                "start_time" => $v['start_time'],
                "end_time" => $v['end_time'],
                "rest_time" => $rest_time ? $rest_time : 0,
                "is_close" => $is_close,
                "is_start" => $is_start,
                "district" => $district_str,
                "industry" => $industry_info ? $industry_info['name'] : "",
                "num" => $partin,
                "guarantee" => $v['guarantee'],
                "add_step" => $v['add_step'],
            );
        }
        //用户缴纳的保证金
        if ($auction_arr) {
            $sql1 = "select sum(a.guarantee) as num from auction_log as a left join top_auction as b on a.auction_id=b.auction_id where a.member_id={$user['id']} and b.pid in (" . implode(",", $auction_arr) . ")";
            $command = $connection->createCommand($sql1);
            $result0 = $command->queryAll();
            $guarantee = $result0[0]['num'];
        }
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result ['info'] = $info ? $info : array();
        $result ['fee'] = $user['fee'];
        $result ['guarantee'] = $guarantee ? $guarantee : "0";
        echo json_encode($result);
    }

    /*
     * 某个拍卖场详情
     * 涉及top_auction表
     */
    public function actionAuctionDetail()
    {
        $this->check_key();
        $user = $this->check_user();
        $auction_id = Frame::getIntFromRequest('auction_id');
        if (empty($auction_id)) {
            $result ['ret_num'] = 2016;
            $result ['ret_msg'] = '缺少参数！';
            echo json_encode($result);
            die();
        }
        $tinfo = TopAuction::model()->findAll("pid={$auction_id} and start_time<" . time() . " and end_time>" . time() . " order by place asc");
        if (!$tinfo) {
            $result ['ret_num'] = 116;
            $result ['ret_msg'] = '拍卖未开始或者已经结束！';
            echo json_encode($result);
            die();
        }
        foreach ($tinfo as $k => $v) {
            $ninfo = array();
            $pinfo = array();
            $pinfo = AuctionLog::model()->find("auction_id={$v['auction_id']} and top=1");
            if ($pinfo) {
                $ninfo = NumberTrain::model()->find("member_id={$pinfo['member_id']}");
            }
            $num = AuctionLog::model()->count("auction_id={$v['auction_id']}");
            $info[] = array(
                "auction_id" => $v['auction_id'],
                "place" => $v['place'],
                "now_price" => $pinfo ? $pinfo['price'] : "",
                "num" => $num,
                "from" => $ninfo ? $ninfo['short_name'] : "",
            );
        }
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result ['info'] = $info ? $info : array();
        echo json_encode($result);
    }

    /*
     * 缴纳保证金
     * 涉及auction_log表
     * 直接扣账户余额，不足自己去充值
     */
    public function actionPayGuarantee()
    {
        $this->check_key();
        $user = $this->check_user();
        $auction_id = Frame::getIntFromRequest('auction_id');
        if (empty($auction_id)) {
            $result ['ret_num'] = 2016;
            $result ['ret_msg'] = '缺少参数！';
            echo json_encode($result);
            die();
        }
        $now = time();
        $topinfo = TopAuction::model()->find("auction_id={$auction_id} and is_close=0 and pid!=0 and start_time<" . $now . " and end_time>" . $now);
        if (!$topinfo) {
            $result ['ret_num'] = 1116;
            $result ['ret_msg'] = '该拍卖已经结束！';
            echo json_encode($result);
            die();
        }
        $loginfo = AuctionLog::model()->find("auction_id={$auction_id} and member_id={$user['id']}");
        if ($loginfo) {
            $result ['ret_num'] = 1277;
            $result ['ret_msg'] = '保证金已缴纳，请勿重复缴纳！';
            echo json_encode($result);
            die();
        }
        if ($user['fee'] < $topinfo['guarantee']) {
            $result ['ret_num'] = 2116;
            $result ['ret_msg'] = '账户余额不足，请充值后再缴纳！';
            echo json_encode($result);
            die();
        }
        //保证金缴纳
        $user->fee = $user['fee'] - $topinfo['guarantee'];
        $user->guarantee = $user['guarantee'] + $topinfo['guarantee'];
        if ($user->update()) {
            $newlog = new AuctionLog();
            $newlog->member_id = $user['id'];
            $newlog->auction_id = $auction_id;
            $newlog->guarantee = $topinfo['guarantee'];
            $newlog->time = $now;
            $newlog->top = 0;
            $newlog->save();
        }
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        echo json_encode($result);
    }

    /*
     * 拍卖建立socket连接，产生token
     */
    public function actionSet(){
        $this->check_key();
        $user = $this->check_user();
        $key = Frame::getStringFromRequest('key');
        $m = new Memcached();
        $m->addServer('localhost', 11211);
        //判断该用户token过期否
        if($m->get("token:".$user->id)){
            $token=$m->get("token:".$user->id);
        }else {
            //缓存每个人的token
            $token = md5($key . time()) . md5($key . $user->id);
            $m->set("token:" . $user->id, $token, time() + 300);
        }
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result ['token'] = $token;
        echo json_encode($result);
    }

    /*
     * 拍卖出价
     * 涉及auction_log表
     */
    public function actionGivePrice()
    {
        $this->check_key();
        $user = $this->check_user();

        $auction_id = Frame::getIntFromRequest('auction_id');
        $price = Frame::getStringFromRequest('price');
        if (empty($auction_id) || empty($price)) {
            $result ['ret_num'] = 2016;
            $result ['ret_msg'] = '缺少参数！';
            echo json_encode($result);
            die();
        }
        $connection = Yii::app()->db;
        $now = time();
        $topinfo = TopAuction::model()->find("auction_id={$auction_id} and is_close=0 and pid!=0 and start_time<" . $now . " and end_time>" . $now);
        if (!$topinfo) {
            $result ['ret_num'] = 216;
            $result ['ret_msg'] = '该拍卖已经结束！';
            echo json_encode($result);
            die();
        }
        $auinfo = AuctionLog::model()->find("member_id={$user['id']} and auction_id={$auction_id}");
        if (!$auinfo) {
            $result ['ret_num'] = 116;
            $result ['ret_msg'] = '请先缴纳保证金！';
            echo json_encode($result);
            die();
        }
        $auinfo->price = $price;
        $auinfo->time = $now;
        $isToBroadcast = 0;//是否需要全参与者推送
        //查询该拍卖场第一
        $top = AuctionLog::model()->find("top=1 and auction_id={$auction_id}");
        if ($top) {
            if ($top['price'] >= $price) {
                $auinfo->top = 0;
                $top_price = $top['price'];
                $top_person = $top['member_id'];
            } else {
                $top->top=0;
                $top->update();

                $auinfo->top = 1;
                $top_price = $price;
                $top_person = $user['id'];
                //需要全员推送
                $isToBroadcast = 1;
            }
        } else {
            $auinfo->top = 1;
            $top_price = $price;
            $top_person = $user['id'];
        }
        $traininfo = NumberTrain::model()->find("member_id={$top_person}");
        $auinfo->update();
        //推送
        if ($isToBroadcast) {
            $tpl_data = $auction_id."&".$top_person."&".$price."&".$traininfo['short_name'];
            $tpl_key=md5($tpl_data);
            $url="http://112.124.101.177:8080/broadcast?user_id=".$top_person."&auction_id=".$auction_id."&price=".$price."&from=".urlencode($traininfo['short_name'])."&key=".$tpl_key;
            $yes=$this->openRequest($url,"");
            $broadcastBack=json_decode($yes,true);
            if($broadcastBack['ret_num']==2016){
                $result ['ret_num'] = 217;
                $result ['ret_msg'] = '缺少参数！';
                echo json_encode($result);
                die();
            }
        }
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result ['top_price'] = $top_price;
        $result ['top_person'] = $traininfo['short_name'];
        echo json_encode($result);
    }

    /*
     * 拍卖历史记录
     * 涉及top_auction表
     */
    public function actionGetLog()
    {
        $this->check_key();
        $user = $this->check_user();
        $connection=Yii::app()->db;
        $sql="select * from top_auction where owner_id={$user['id']} and is_paid=1 and is_close=1 and pid!=0";
        $command=$connection->createCommand($sql);
        $auinfo=$command->queryAll();
        if ($auinfo) {
            $district = $this->ProCity($auinfo);
            foreach ($auinfo as $k => $v) {
                //获取行业
                $industry_info = Industry::model()->find("id={$v['industry']}");
                //获取地区
                $district_str = $district[$v['province']] . "-" . $district[$v['city']] . "-" . $district[$v['area']];
                $loginfo = AuctionLog::model()->find("member_id={$user['id']} and auction_id={$v['auction_id']}");
                $info[] = array(
                    "top_period" => date("Y-m-d", $v['top_start_period']) . "至" . date("Y-m-d", $v['top_end_period']),
                    "district" => $district_str,
                    "industry" => $industry_info ? $industry_info['name'] : "",
                    "auction_time" => $v['end_time'],
                    "auction_price" => $loginfo ? $loginfo['price'] : "0",
                    "place"=>$v['place'],
                );
            }
        }
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result ['info'] = $info ? $info : array();
        echo json_encode($result);
    }

    /*
     * 置顶拍卖声明
     */
    public function actionAuctionDeclaration(){
        $this->render("auctionDeclaration");
    }
}

?>