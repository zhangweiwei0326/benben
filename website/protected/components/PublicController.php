<?php

class PublicController extends Controller
{
    /**
     * 用户验证
     * @return $user(用户信息)
     */
    public function check_user()
    {
        $token = Yii::app()->session['memberid'];
        $user_token = Frame::getStringFromRequest('token');
        $token = empty($user_token) ? $token : $user_token;
        if (empty($token)) {
            $result['ret_num'] = 2001;
            $result['ret_msg'] = '用户未登录';
            echo json_encode($result);
            die ();
        }
        $user = Member::model()->find("token = '{$token}'");
        if (empty($user)) {
            $result['ret_num'] = 2015;
            $result['ret_msg'] = '奔犇账号在其他手机上登录';
            echo json_encode($result);
            die ();
        }
        return $user;
    }

    /*
     * 手机号规范
     * 去除手机号中和前后空格，+，-等字符
     * */
    public function standarize_phone($phone)
    {
        preg_match_all("/[0-9]+/", $phone, $match);
        $phone = implode("", $match[0]);
        return $phone;
    }

    /**
     * 优选账号规则
     */
    public function checkbenben($benben_id)
    {
        //1.号码全相同，如222222;
        $str0 = "/^(\d)\\1{1,}$/";
        $string = $benben_id;
        if (preg_match($str0, $string)) {
            return false;
        }
        //2.每个号段的起始号,如30001、400001;
        $str0 = "/^[1-9]{1}[0]{1,}[1]$/";
        $string = $benben_id;
        if (preg_match($str0, $string)) {
            return false;
        }
        //3.尾号是88的,如50188;
        $su_str = substr($string, -2);
        if ($su_str == 88) {
            return false;
        }
        //4.尾号是AABB,如551188;
        $str0 = "/^(\d)\\1{1}(\d)\\2{1}$/";
        $su_str = substr($string, -4);
        if (preg_match($str0, $su_str)) {
            return false;
        }

        //5.尾号是ABAB,如551818;
        $str0 = "/([0-9]{2})\\1/";
        $su_str = substr($string, -4);
        if (preg_match($str0, $su_str)) {
            return false;
        }

        //6.尾号是AAA/AAAA/AAAAA/AAAAAA等等
        $str0 = "/^[1-9]{1,}(\d)\\1{2,}$/";
        if (preg_match($str0, $string)) {
            return false;
        }

        //7.尾号是ABC,如20123
        $su_str = substr($string, -3);
        $flag = 0;
        $len = strlen($su_str);
        $current = $su_str[0];
        for ($i = 1; $i < $len; $i++) {
            if ($current + 1 != $su_str[$i]) {
                $flag = 1;
                break;
            }
            $current = $su_str[$i];
        }
        if ($flag == 0) {
            return false;
        }

        //8.号码降序排列,如54321
        $flag = 0;
        $len = strlen($string);
        $current = $string[0];
        for ($i = 1; $i < $len; $i++) {
            if ($current - 1 != $string[$i]) {
                $flag = 1;
                break;
            }
            $current = $string[$i];
        }
        if ($flag == 0) {
            return false;
        }
        //2.号码升序排列,如12345
        $flag = 0;
        $len = strlen($string);
        $current = $string[0];
        for ($i = 1; $i < $len; $i++) {
            if ($current + 1 != $string[$i]) {
                $flag = 1;
                break;
            }
            $current = $string[$i];
        }
        if ($flag == 0) {
            return false;
        }

        //是否是保留账号
        $reserve_phone = getphone();
        if (in_array($benben_id, $reserve_phone)) {
            return false;
        }
        return true;
    }

    /**
     * @return array(省市代码信息)
     *
     */
    public function pcinfo()
    {
        $connection = Yii::app()->db;
        $sql = "SELECT bid,area_name FROM area where parent_bid = 0";
        $command = $connection->createCommand($sql);
        $result0 = $command->queryAll();
        $all = "";
        $province = array();
        foreach ($result0 as $value) {
            $province[$value['bid']] = $value['area_name'];
            $all .= $value['bid'] . ",";
        }
        $all = trim($all);
        $all = trim($all, ',');
        $sql = "SELECT bid,area_name FROM area where parent_bid in ($all)";
        $command = $connection->createCommand($sql);
        $result1 = $command->queryAll();
        $city = array();
        foreach ($result1 as $value) {
            $city[$value['bid']] = $value['area_name'];
            $all .= $value['bid'] . ",";
        }
        $all = trim($all);
        $all = trim($all, ',');
        $sql = "SELECT bid,area_name FROM area where parent_bid in ($all)";
        $command = $connection->createCommand($sql);
        $result1 = $command->queryAll();
        $area = array();
        foreach ($result1 as $value) {
            $area[$value['bid']] = $value['area_name'];
            $all .= $value['bid'] . ",";
        }
        $all = trim($all);
        $all = trim($all, ',');
        $sql = "SELECT bid,area_name FROM area where parent_bid in ($all)";
        $command = $connection->createCommand($sql);
        $result1 = $command->queryAll();
        $street = array();
        foreach ($result1 as $value) {
            $street[$value['bid']] = $value['area_name'];
            $all .= $value['bid'] . ",";
        }
        return array($province, $city, $area, $street);
    }

    /**
     * 根据ID获取省市信息
     */
    public function getProCity($bid)
    {
        if (!$bid) {
            return false;
        }
        $connection = Yii::app()->db;
        $sql = "SELECT bid,area_name FROM area WHERE bid in ({$bid})";
        $command = $connection->createCommand($sql);
        $area = $command->queryAll();
        return $area;
    }

    public function ProCity($users)
    {
        //省市代码获取
        $pro = array();
        $pro_arr = array();
        foreach ($users as $value) {
            if ($value['province']) {
                $pro[] = $value['province'];
            }
            if ($value['city']) {
                $pro[] = $value['city'];
            }
            if ($value['area']) {
                $pro[] = $value['area'];
            }
            if ($value['street']) {
                $pro[] = $value['street'];
            }
        }
        $pro_name = $this->getProCity(implode(",", $pro));
        if ($pro_name) {
            foreach ($pro_name as $val) {
                $pro_arr[$val['bid']] = $val['area_name'];
            }
        }
        return $pro_arr;
    }

    /**
     * @return array(行业代码信息)--v1使用，v2废弃
     *
     */
    public function industryinfo()
    {
        $connection = Yii::app()->db;
        $sql = "SELECT id,name FROM industry where parent_id = 0";
        $command = $connection->createCommand($sql);
        $result0 = $command->queryAll();
        $all = "";
        $province = array();
        foreach ($result0 as $value) {
            $province[$value['id']] = $value['name'];
            $all .= $value['id'] . ",";
        }
        $all = trim($all);
        $all = trim($all, ',');
        $sql = "SELECT id,name FROM industry where parent_id in ({$all})";
        $command = $connection->createCommand($sql);
        $result1 = $command->queryAll();
        $city = array();
        foreach ($result1 as $value) {
            $city[$value['id']] = $value['name'];
        }
        return array($province, $city);
    }

    /*
     *获取行业信息
     * 返回array(){[0]=>array(5) {["id"]=>string(2) "73", ["name"]=>string(6) "农业", ["parent_id"]=>string(1) "0", ["last"]=>string(1) "0", ["level"]=>string(1) "1"}, ...}
     * */
    public function newindustryinfo()
    {
        $connection = Yii::app()->db;
        $sql = "SELECT id,name,parent_id,last,level FROM industry where level!=0";
        $command = $connection->createCommand($sql);
        $result = $command->queryAll();
        $list = Tree::create($result);
        return $list;
    }

    /*
     * 获取地址信息
     */
    public function areainfo(){
        $connection = Yii::app()->db;
        $sql = "SELECT bid as id,parent_bid as parent_id,area_name,last,level FROM area where del_flag!=1";
        $command = $connection->createCommand($sql);
        $result = $command->queryAll();
        $list = Tree::create($result);
        return $list;
    }

    /**
     * 根据ID获取行业信息
     * 返回array(),数据库查询的列表
     */
    public function getIndustryinfo($bid)
    {
        if (!$bid) {
            return false;
        }
        $connection = Yii::app()->db;
        $sql = "SELECT id,name,parent_id,last,level FROM industry WHERE id in ({$bid})";
        $command = $connection->createCommand($sql);
        $area = $command->queryAll();
        return $area;
    }

    /**
     * 根据ID获取行业信息(多个)
     * 返回array("id"=>"industry_name")
     */
    public function Industry($users)
    {
        $industry = array();
        $industry_arr = array();
        foreach ($users as $value) {
            if ($value['industry']) {
                $industry[] = $value['industry'];
            }
        }
        $industry_name = $this->getIndustryinfo(implode(",", $industry));
        if ($industry_name) {
            foreach ($industry_name as $val) {
                $industry_arr[$val['id']] = $val['name'];
            }
        }
        return $industry_arr;
    }

    public function check_key()
    {
        //判断接口调用出处，iPhone or Android
        $key = Frame::getStringFromRequest('key');
        Frame::appkey($key);
        return $key;
    }

    public function  eraseNull($string)
    {
        if ($string == null) {
            $string = "";
        }
        return $string;
    }

    /*
     * 将店铺号转换为号码直通车号train_id
     * 返回train_id
     * */
    public function changeTrain($shop)
    {
        $shop = trim($shop);
        if (strpos($shop, 'hz') === 0) {
            $benben = substr($shop, 2);
            if (is_numeric($benben)) {
                $sql = "select a.id, b.benben_id from number_train as a LEFT join member as b on a.member_id=b.id where a.is_close=0 and a.status=0 and b.benben_id={$benben}";
                $connection = Yii::app()->db;
                $command = $connection->createCommand($sql);
                $result = $command->queryAll();
                if (count($result)) {
                    foreach ($result as $k) {
                        $train_id = $k['id'] ? $k['id'] : 0;
                    }
                } else {
                    $train_id = 0;
                }
            } else {
                $train_id = 0;
            }
        } else {
            $train_id = 0;
        }
        return $train_id;
    }

    /**
     * 添加积分
     */
    public function addIntegral($memberId, $type, $param = '')
    {
        $integral_array = array(1 => 50, 2 => 100, 3 => 20, 4 => 50, 5 => 50, 6 => 50, 7 => 200, 8 => 50,
            9 => 20, 10 => 20, 11 => 50, 12 => 20, 13 => 10, 14 => 2, 15 => 2, 16 => 1, 17 => 2, 18 => 2, 19 => 2, 20 => 1);
        //只需要增加一次积分
        $integralValue = 0;
        $member = Member::model()->findByPk($memberId);
        $oldValue = $member->integral;
        if ($type <= 12) {
            $my = MemberIntegralLog::model()->find("member_id = {$memberId} and type = {$type}");
            if ($my) {
                return;
            } else {
                //$member = Member::model()->findByPk($memberId);
                if ($member) {
                    $integralValue = $member->integral + $integral_array[$type];
                    $member->integral = $member->integral + $integral_array[$type];
                    $member->save();
                    $log = new MemberIntegralLog();
                    $log->member_id = $memberId;
                    $log->integral = $integral_array[$type];
                    $log->created_time = time();
                    $log->type = $type;
                    $log->save();
                }

            }
        } else if ($type == 13) {    //邀请好友加入犇犇
            $integralValue = $member->integral + $integral_array[$type] * intval($param);
            $member->integral = $member->integral + $integral_array[$type] * intval($param);
            $member->save();
            $log = new MemberIntegralLog();
            $log->member_id = $memberId;
            $log->integral = $integral_array[$type] * intval($param);
            $log->created_time = time();
            $log->type = $type;
            $log->save();
        } else if ($type == 14) {        //用犇犇拨号
            $connection = Yii::app()->db;
            $sql = "SELECT count(*) as c FROM member_integral_log where member_id = {$memberId} and type = {$type}";
            $command = $connection->createCommand($sql);
            $result0 = $command->queryAll();
            if ($result0[0]['c'] < 40) {
                $integralValue = $member->integral + $integral_array[$type];
                $member->integral = $member->integral + $integral_array[$type];
                $member->save();
                $log = new MemberIntegralLog();
                $log->member_id = $memberId;
                $log->integral = $integral_array[$type];
                $log->created_time = time();
                $log->type = $type;
                $log->save();
            }
        } else if ($type == 17) {        //接受我要买
            $connection = Yii::app()->db;
            $sql = "SELECT count(*) as c FROM member_integral_log where member_id = {$memberId} and type = {$type} and created_time >= " . strtotime(date('Y-m-01', strtotime(date("Y-m-d"))));
            $command = $connection->createCommand($sql);
            $result0 = $command->queryAll();
            if ($result0[0]['c'] < 10) {
                $integralValue = $member->integral + $integral_array[$type];
                $member->integral = $member->integral + $integral_array[$type];
                $member->save();
                $log = new MemberIntegralLog();
                $log->member_id = $memberId;
                $log->integral = $integral_array[$type];
                $log->created_time = time();
                $log->type = $type;
                $log->save();
            }
            $sql = "SELECT count(*) as c FROM member_integral_log where member_id = {$param} and type = {$type} and created_time >= " . strtotime(date('Y-m-01', strtotime(date("Y-m-d"))));
            $command = $connection->createCommand($sql);
            $result0 = $command->queryAll();
            if ($result0[0]['c'] < 10) {
                $member2 = Member::model()->findByPk($param);
                $member2->integral = $member2->integral + $integral_array[$type];
                $member2->save();
                $log = new MemberIntegralLog();
                $log->member_id = $param;
                $log->integral = $integral_array[$type];
                $log->created_time = time();
                $log->type = $type;
                $log->save();
            }

        } else if ($type == 16) {        //将好友加入政企
            $connection = Yii::app()->db;
            $sql = "SELECT count(*) as c FROM member_integral_log where member_id = {$memberId} and type = {$type} and created_time >= " . strtotime(date('Y-m-01', strtotime(date("Y-m-d"))));
            $command = $connection->createCommand($sql);
            $result0 = $command->queryAll();
            if ($result0[0]['c'] < 20) {
                $integralValue = $member->integral + $integral_array[$type] * intval($param);
                $member->integral = $member->integral + $integral_array[$type] * intval($param);
                $member->save();
                $log = new MemberIntegralLog();
                $log->member_id = $memberId;
                $log->integral = $integral_array[$type] * intval($param);
                $log->created_time = time();
                $log->type = $type;
                $log->save();
            }
        } else if ($type == 18) {        //报价我要买
            $connection = Yii::app()->db;
            $sql = "SELECT count(*) as c FROM member_integral_log where member_id = {$memberId} and type = {$type} and created_time >= " . strtotime(date('Y-m-01', strtotime(date("Y-m-d"))));
            $command = $connection->createCommand($sql);
            $result0 = $command->queryAll();
            if ($result0[0]['c'] < 20) {
                $integralValue = $member->integral + $integral_array[$type];
                $member->integral = $member->integral + $integral_array[$type];
                $member->save();
                $log = new MemberIntegralLog();
                $log->member_id = $memberId;
                $log->integral = $integral_array[$type];
                $log->created_time = time();
                $log->type = $type;
                $log->save();
            }
        } else if ($type == 20) {    //被收藏
            $connection = Yii::app()->db;
            $sql = "SELECT count(*) as c FROM member_integral_log where member_id = {$memberId} and type = {$type}";
            $command = $connection->createCommand($sql);
            $result0 = $command->queryAll();
            if ($result0[0]['c'] < 1000) {
                $integralValue = $member->integral + $integral_array[$type];
                $member->integral = $member->integral + $integral_array[$type];
                $member->save();
                $log = new MemberIntegralLog();
                $log->member_id = $memberId;
                $log->integral = $integral_array[$type];
                $log->created_time = time();
                $log->type = $type;
                $log->save();
            }
        }
        $spanValue = floor($integralValue / 100) - floor($oldValue / 100);
        if ($integralValue > 0 && $spanValue > 0) {
            $member->coin = $member->coin + $spanValue;
            $member->save();
        }

    }


    public function publicTimeDeal($created_time)
    {
        $day = strtotime(date("Y-m-d 0:0:0", time()));
        $year = strtotime(date("Y-1-1 0:0:0", time()));
        if ($created_time > $day) {
            return date("H:i:s", $created_time);
        } else if ($created_time > $year) {
            return date("m月d日", $created_time);
        } else {
            return date("Y年m月d日", $created_time);
        }
    }

    /**通过手机号获取用户id**/
    public function getMemberIdWithPhone($phone)
    {
        if (!empty($phone)) {
            $user = Member::model()->find("phone='{$phone}'");
            if ($user) {
                return $user->id;
            }
        }
        return 0;
    }

    /*获取通讯录中，benben号对应的名字*/
    public function getBenbenName($member_id)
    {
        if (!$connection) {
            $connection = Yii::app()->db;
        }
        $sql1 = "select id,group_name name from group_contact where member_id = {$member_id}";
        $command = $connection->createCommand($sql1);
        $result1 = $command->queryAll();
        $groupId = array();
        if ($result1) {
            foreach ($result1 as $key => $value) {
                $groupId[] = $value['id'];
            }
        }

        if (count($groupId) > 0) {
            $sql = "select b.is_benben, a.name from group_contact_info as a left join group_contact_phone as b on a.id = b.contact_info_id where b.is_benben > 0 and a.group_id in (" . implode(",", $groupId) . ") group by a.id";
            $command = $connection->createCommand($sql);
            $friend = $command->queryAll();
        }

        $fri = array();
        $benbenName = array();
        if (count($friend) > 0) {
            foreach ($friend as $v) {
                $benbenName[$v['is_benben']] = $v['name'];
            }
        }
        return $benbenName;
    }

    public function getContactIdName($member_id, $type = 0)
    {
        global $connection;
        if (!$connection) {
            $connection = Yii::app()->db;
        }
        $sql1 = "select id,group_name name from group_contact where member_id = {$member_id}";
        $command = $connection->createCommand($sql1);
        $result1 = $command->queryAll();
        $groupId = array();
        if ($result1) {
            foreach ($result1 as $key => $value) {
                $groupId[] = $value['id'];
            }
        }

        if (count($groupId) > 0) {
            $sql = "select c.id, b.is_benben, a.name,a.group_id from group_contact_info as a left join group_contact_phone as b on a.id = b.contact_info_id  left join member c on c.benben_id = b.is_benben where b.is_benben > 0 and a.group_id in (" . implode(",", $groupId) . ") group by a.id";
            $command = $connection->createCommand($sql);
            $friend = $command->queryAll();
        }

        $fri = array();
        $benbenName = array();
        if (count($friend) > 0) {
            foreach ($friend as $v) {
                if ($type) {
                    $benbenName[$v['id']] = array($v['name'], $v['group_id']);
                } else {
                    $benbenName[$v['id']] = $v['name'];
                }

            }
        }
        return $benbenName;
    }

    /*判断是否是好友*/
    public function isfriend($huanxin_username)
    {
        $add_user = Member::model()->find("huanxin_username = '{$huanxin_username}'");
    }

    /*查询通讯录好友id*/
    public function myfriend($user_id)
    {
        global $connection;
        if (!$connection) {
            $connection = Yii::app()->db;
        }
        $sql = "select b.id,a.benben_id,a.name,a.group_id from group_contact_info a left join member b on a.benben_id = b.benben_id
		where a.member_id = {$user_id} and a.benben_id > 0";
        $command = $connection->createCommand($sql);
        $result1 = $command->queryAll();
        $arr_id = array();
        foreach ($result1 as $value) {
            if ($value['id']) {
                $arr_id[$value['id']] = array($value['name'], $value['group_id']);
            }
        }
        return $arr_id;
    }

    /*
     * 查询所有好友
     */
    public function allfriend($user_id){
        $connection = Yii::app()->db;
        $sql="select b.is_benben as benben_id,a.name,a.group_id from group_contact_info as a left join group_contact_phone as b
        on a.id=b.contact_info_id where a.member_id={$user_id} and b.is_benben > 0";
        $command = $connection->createCommand($sql);
        $result1 = $command->queryAll();

        foreach($result1 as $k=>$v){
            $minfo=Member::model()->find("benben_id={$v['benben_id']} and benben_id>0");
            $result1[$k]['id']=$minfo['id'];
        }
        $arr_id = array();
        foreach ($result1 as $value) {
            if ($value['id']) {
                $arr_id[$value['id']] = array($value['name'], $value['group_id']);
            }
        }
        return $arr_id;
    }

    /*
     * 根据奔犇号或者用户id获取好友联盟信息和号码直通车信息
     * leg_id/leg_poster/leg_name/leg_district;train_id/pic/tag/short_name
     */
    public function getowninfo($benben, $member_id)
    {
        if ($benben) {
            $minfo = Member::model()->find("benben_id={$benben} and id_enable=1");
            if ($minfo) {
                $member_id = $minfo['id'];
            }
        }
        if (empty($member_id)) {
            return array(
                "legid" => "",
                "leg_poster" => "",
                "leg_name" => "",
                "leg_district" => "",
                "train_id" => "",
                "pic" => "",
                "tag" => "",
                "short_name" => "",
                "type" => ""
            );
        }
        $fl = FriendLeague::model()->find("member_id={$member_id} and status=0 and is_delete=0");
        $industry = $this->ProCity(array(0 => $fl));
        $leg_industry = $industry[$fl['city']] ? $industry[$fl['city']] . " " . $industry[$fl['area']] : "";

        $tl = NumberTrain::model()->find("member_id={$member_id} and status=0 and is_close=0");
        return array(
            "leg_id" => $fl['id'] ? $fl['id'] : "",
            "leg_poster" => $fl['poster'] ? URL . $fl['poster'] : "",
            "leg_name" => $fl['name'] ? $fl['name'] : "",
            "leg_district" => $leg_industry,
            "train_id" => $tl['id'] ? $tl['id'] : "",
            "pic" => $tl['poster'] ? URL . $tl['poster'] : "",
            "tag" => $tl['tag'] ? $tl['tag'] : "",
            "short_name" => $tl['short_name'] ? $tl['short_name'] : "",
            "type" => $fl['type'] ? ($fl['type'] == 1 ? '工作联盟' : ($fl['type'] == 2 ? '英雄联盟' : "")) : ""
        );
    }

    /*
     * 取出通讯录中所有好友的奔犇号,
     * 或者是其对应的名字
     * 返回数组$benben_arr
     */
    public function getfriend($mid, $type = 1)
    {
        //取出通讯录中所有好友
        $connection = Yii::app()->db;
        $benben_arr = array();
        $name = array();
        $nowid = array();
        $id = array();
        $sql_all = "select b.is_benben,a.name from group_contact_info as a left join group_contact_phone as b on a.id=b.contact_info_id where a.member_id={$mid} and b.is_benben>0 GROUP BY b.is_benben";
        $command = $connection->createCommand($sql_all);
        $result1 = $command->queryAll();
        foreach ($result1 as $k => $v) {
            $benben_arr[] = $v['is_benben'];
            $name[$v['is_benben']] = $v['name'];
        }
        if ($benben_arr) {
            $sql_id = "select id,benben_id from member where benben_id in (" . implode(",", $benben_arr) . ") and benben_id>0 and id_enable=1";
            $command = $connection->createCommand($sql_id);
            $resultid = $command->queryAll();
            foreach ($resultid as $kd => $vd) {
                $id[$vd['benben_id']] = $vd['id'];
            }
            foreach ($name as $kn => $vn) {
                $nowid[$id[$kn]] = $vn;
            }
        }
        if ($type == 1) {
            return $benben_arr;
        } elseif ($type == 2) {
            return $name;
        } elseif ($type == 3) {
            return $nowid;
        } else {
            return array();
        }
    }

    public function getlength($s)
    {
        $n = 0;
        preg_match_all("/./us", $s, $matchs);
        foreach ($matchs[0] as $p) {
            $n += preg_match('#^[' . chr(0x1) . '-' . chr(0xff) . ']$#', $p) ? 0.5 : 1;
        }
        return ceil($n);
    }

    /*
     * 通过直通车编号获得店铺号
     * member表的id，benben_id和number_train表的member_id，id
     * 在犇犇号的前面+hz为店铺号
     * 传入array(0=>$train_id,...)
     * 返回array($train_id=>"hz".$benben_id)
     * */
    public function getShopTrain($train_id)
    {
        $data = array();
        if ($train_id) {
            $train_str = implode(",", $train_id);
            $sql = "select b.benben_id,a.id from number_train as a left join member as b on a.member_id=b.id where a.id in ({$train_str})";
            $connection = Yii::app()->db;
            $command = $connection->createCommand($sql);
            $result = $command->queryAll();
            foreach ($result as $k => $v) {
                $data[$v['id']] = "hz" . $v['benben_id'];
            }
            return $data;
        } else {
            return 0;
        }
    }

    /*
     *系统推送消息
     * 传入$username=array ; $content=string; $arr额外信息(t1为是否显示在聊天栏中0no/1yes；t2为是否进入通知界面0no/1yes；t3为处理进度0wait/1ok/2no；t4为消息类型:1好友联盟，2群组消息，3好友请求,4.我要买,5.群组转让)
     * 返回array,其中data有用
     * */
    public function sendHXMessage($username, $content, $arr = array(), $from_user = "admin")
    {
        $target_type = "users";
        $ext = array("em_apns_ext" => array("em_push_title" => "{$content}"));
        $ext = array_merge($ext, $arr);
        $options = array(
            "client_id" => CLIENT_ID,
            "client_secret" => CLIENT_SECRET,
            "org_name" => ORG_NAME,
            "app_name" => APP_NAME
        );
        $huanxin = new Easemob($options);
        $re = $huanxin->yy_hxSend($from_user, $username, $content, $target_type, $ext);
        $re = json_decode($re, true);
        return $re;
    }

    /*
     * 环信发送文本消息
     * $from_user发送者环信id,$username接受者环信id:array,$content内容,$ext额外属性:array
     */
    public function sendTextMessage($from_user, $username, $content, $ext)
    {
        $target_type = 'users';
        $options = array(
            "client_id" => CLIENT_ID,
            "client_secret" => CLIENT_SECRET,
            "org_name" => ORG_NAME,
            "app_name" => APP_NAME
        );
        $huanxin = new Easemob($options);
        $re = $huanxin->yy_hxSend($from_user, $username, $content, $target_type, $ext);
        $re = json_decode($re, true);
        return $re;
    }

    /*
     * 环信透传消息
     */
    public function sendTCMessage($from_user = "admin", $username, $content, $ext)
    {
        $target_type = 'users';
        $options = array(
            "client_id" => CLIENT_ID,
            "client_secret" => CLIENT_SECRET,
            "org_name" => ORG_NAME,
            "app_name" => APP_NAME
        );
        $huanxin = new Easemob($options);
        $re = $huanxin->tc_hxSend($from_user, $username, $content, $target_type, $ext);
        $re = json_decode($re, true);
        return $re;
    }

    /*
     * 环信发送图片消息
     * $username array
     */
    public function sendIMGMessage($from_user, $username, $img, $url, $ext)
    {
        $options = array(
            "client_id" => CLIENT_ID,
            "client_secret" => CLIENT_SECRET,
            "org_name" => ORG_NAME,
            "app_name" => APP_NAME
        );
        $img1 = "https://a1.easemob.com/" . ORG_NAME . "/" . APP_NAME . "/chatfiles/" . $img['uuid'];
        $secret = $img['secret'];
        $tpl_size = getimagesize(URL . $url);
        $size = array("width" => $tpl_size[0], "height" => $tpl_size[1]);
        $huanxin = new Easemob($options);
        $re = $huanxin->img_hxSend($from_user, $username, $img1, $secret, $size, "users", $ext);
        $re = json_decode($re, true);
        return $re;
    }

    /*
     * 环信发送语音消息
     * @param $from_user
     * @param $username
     * @param $aud
     * @param $ext
     * @return mixed
     */
    public function sendAudMessage($from_user, $username, $aud, $length, $ext)
    {
        $options = array(
            "client_id" => CLIENT_ID,
            "client_secret" => CLIENT_SECRET,
            "org_name" => ORG_NAME,
            "app_name" => APP_NAME
        );
        $img1 = "https://a1.easemob.com/" . ORG_NAME . "/" . APP_NAME . "/chatfiles/" . $aud['uuid'];
        $secret = $aud['secret'];
        $huanxin = new Easemob($options);
        $re = $huanxin->aud_hxSend($from_user, $username, $img1, $secret, $length, "users", $ext);
        $re = json_decode($re, true);
        return $re;
    }

    /*
     * 环信上传图片/语音
     * $re=array(uuid,secret)
     */
    public function upload($img)
    {
        $options = array(
            "client_id" => CLIENT_ID,
            "client_secret" => CLIENT_SECRET,
            "org_name" => ORG_NAME,
            "app_name" => APP_NAME
        );
        $huanxin = new Easemob($options);
        $re = $huanxin->upload(URL . $img);
        return $re;
    }

    /*
     * 商店主查询会员认证功能是否到期
     */
    public function storevip($userid, $storeid=0)
    {
        if ($userid) {
            $is_auth=StoreAuth::model()->count("member_id={$userid} and status=2");
            if($is_auth) {
                $pinfo = PromotionManage::model()->find("member_id={$userid} and (is_close=0 or vip_time>=" . time() . ")");
                if ($pinfo) {
                    return true;
                } else {
                    return false;
                }
            }else{
                $pinfo = PromotionManage::model()->find("member_id={$userid} and (is_close=0 or time>=" . time() . ")");
                if ($pinfo) {
                    return true;
                } else {
                    return false;
                }
            }
        } else if ($storeid) {
            $member=NumberTrain::model()->find("id={$storeid}");
            $is_auth=StoreAuth::model()->count("member_id={$member['member_id']} and status=2");
            if($is_auth){
                $pinfo = PromotionManage::model()->find("store_id={$storeid} and (is_close=0 or vip_time>=" . time() . ")");
                if ($pinfo) {
                    return true;
                } else {
                    return false;
                }
            }else {
                $pinfo = PromotionManage::model()->find("store_id={$storeid} and (is_close=0 or time>=" . time() . ")");
                if ($pinfo) {
                    return true;
                } else {
                    return false;
                }
            }
        } else {
            return false;
        }
    }

    /*
     * 根据商品号查询商店情况
     * 多个promotionid用逗号隔开
     */
    public function getShopinfo($promotionid){
        $pinfo=Promotion::model()->findAll("id in ({$promotionid})");
        foreach($pinfo as $k=>$v){
            $pmids[]=$v['pm_id'];
            $goods[$v['id']]=$v['pm_id'];
        }
        if($pmids) {
            $ids = implode(",", $pmids);
        }else{
            return array();
        }
        $connection=Yii::app()->db;
        $sql="select a.poster,a.id,a.short_name,a.member_id,b.id as pm_id from number_train as a left join promotion_manage as b on a.id=b.store_id where b.id in ({$ids})";
        $command=$connection->createCommand($sql);
        $result0=$command->queryAll();
        foreach($result0 as $kk=>$vv){
            $info[$vv['pm_id']]=$vv;
        }
        foreach($goods as $kg=>$vg){
            $goods[$kg]=$info[$vg];
        }
        return $goods;
    }

    /*
     * 取小图
     */
    public function getThumb($url){
        $tpl=explode("/",$url);
        $num=count($tpl);
        if($num>1){
            $tpl[$num-1]="small".$tpl[$num-1];
        }
        $outtpl=implode("/",$tpl);
        return $outtpl;
    }


    /*
     * 取缩略图
     */
    public function getSmall($url){
        $tpl=explode("/",$url);
        $num=count($tpl);
        if($num>1){
            $tpl[$num-1]="square".$tpl[$num-1];
        }
        $outtpl=implode("/",$tpl);
        return $outtpl;
    }

    /*
     * 发起post请求
     */
    function openRequest($url,$parameter)
    {
        $curl = curl_init(); // 启动一个CURL会话
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, FALSE); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, FALSE); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0; Trident/4.0)'); // 模拟用户使用的浏览器
        if (!empty ($parameter)) {
            $options = json_encode($parameter);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $options); // Post提交的数据包
        }
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 设置超时限制防止死循环
        // curl_setopt ( $curl, CURLOPT_HTTPHEADER, $header ); // 设置HTTP头
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, 'POST');
        $result = curl_exec($curl); // 执行操作

        curl_close($curl); // 关闭CURL会话
        return $result;
    }

    //iphone型号
    public function getmodel($model)
    {
        if ($model == "iPhone1,1") return "iPhone 2G (A1203)";
        if ($model == "iPhone1,2") return "iPhone 3G (A1241/A1324)";
        if ($model == "iPhone2,1") return "iPhone 3GS (A1303/A1325)";
        if ($model == "iPhone3,1") return "iPhone 4 (A1332)";
        if ($model == "iPhone3,2") return "iPhone 4 (A1332)";
        if ($model == "iPhone3,3") return "iPhone 4 (A1349)";
        if ($model == "iPhone4,1") return "iPhone 4S (A1387/A1431)";
        if ($model == "iPhone5,1") return "iPhone 5 (A1428)";
        if ($model == "iPhone5,2") return "iPhone 5 (A1429/A1442)";
        if ($model == "iPhone5,3") return "iPhone 5c (A1456/A1532)";
        if ($model == "iPhone5,4") return "iPhone 5c (A1507/A1516/A1526/A1529)";
        if ($model == "iPhone6,1") return "iPhone 5s (A1453/A1533)";
        if ($model == "iPhone6,2") return "iPhone 5s (A1457/A1518/A1528/A1530)";
        if ($model == "iPhone7,1") return "iPhone 6 Plus (A1522/A1524)";
        if ($model == "iPhone7,2") return "iPhone 6 (A1549/A1586)";
        if ($model == "iPhone8,1") return "iPhone 6s (A1688/A1700/A1633)";
        if ($model == "iPhone8,2") return "iPhone 6s Plus (A1687/A1699/A1634)";

        if ($model == "iPod1,1") return "iPod Touch 1G (A1213)";
        if ($model == "iPod2,1") return "iPod Touch 2G (A1288)";
        if ($model == "iPod3,1") return "iPod Touch 3G (A1318)";
        if ($model == "iPod4,1") return "iPod Touch 4G (A1367)";
        if ($model == "iPod5,1") return "iPod Touch 5G (A1421/A1509)";

        if ($model == "iPad1,1") return "iPad 1G (A1219/A1337)";
        if ($model == "iPad2,1") return "iPad 2 (A1395)";
        if ($model == "iPad2,2") return "iPad 2 (A1396)";
        if ($model == "iPad2,3") return "iPad 2 (A1397)";
        if ($model == "iPad2,4") return "iPad 2 (A1395+New Chip)";
        if ($model == "iPad2,5") return "iPad Mini 1G (A1432)";
        if ($model == "iPad2,6") return "iPad Mini 1G (A1454)";
        if ($model == "iPad2,7") return "iPad Mini 1G (A1455)";
        if ($model == "iPad3,1") return "iPad 3 (A1416)";
        if ($model == "iPad3,2") return "iPad 3 (A1403)";
        if ($model == "iPad3,3") return "iPad 3 (A1430)";
        if ($model == "iPad3,4") return "iPad 4 (A1458)";
        if ($model == "iPad3,5") return "iPad 4 (A1459)";
        if ($model == "iPad3,6") return "iPad 4 (A1460)";
        if ($model == "iPad4,1") return "iPad Air (A1474)";
        if ($model == "iPad4,2") return "iPad Air (A1475)";
        if ($model == "iPad4,3") return "iPad Air (A1476)";
        if ($model == "iPad4,4") return "iPad Mini 2G (A1489)";
        if ($model == "iPad4,5") return "iPad Mini 2G (A1490)";
        if ($model == "iPad4,6") return "iPad Mini 2G (A1491)";

        if ($model == "i386") return "iPhone Simulator";
        if ($model == "x86_64") return "iPhone Simulator";
        return $model;
    }

    //积分等级对照表
    public function score2rank($score){
        $scoretorank=array(
            "0"=>4,
            "1"=>10,
            "2"=>40,
            "3"=>90,
            "4"=>150,
            "5"=>250,
            "6"=>500,
            "7"=>1000,
            "8"=>2000,
            "9"=>5000,
            "10"=>10000,
            "11"=>20000,
            "12"=>30000,
            "13"=>40000,
            "14"=>60000,
            "15"=>80000,
            "16"=>100000,
            "17"=>200000,
            "18"=>300000,
            "19"=>500000,
            "20"=>0,
        );
        foreach($scoretorank as $k=>$v){
            if($score>500000){
                return 20;
            }elseif($score/$v>1&&$score/$scoretorank[$k+1]<=1){
                return $k+1;
            }else{
                return 0;
            }
        }
    }

    //公钥加密
    function encrypt_data_public($data_to_encrypt,$public_key_path)
    {
        header("Content-Type:text/html;charset=utf-8");
        date_default_timezone_set('Asia/Shanghai');
        $string_to_encrypt = $data_to_encrypt;


        if (! file_exists($public_key_path))
        {
            return 0;
        }

        $fp = fopen ( $public_key_path, "r" );
        $public_key_tmp = fread ( $fp, 8192 );
        fclose( $fp );


        $public_key = openssl_get_publickey($public_key_tmp);


        if (!$public_key)
        {
            openssl_free_key( $public_key );
            return 0;
        }


        openssl_public_encrypt( $string_to_encrypt, $encrypted_data_tmp, $public_key );


        if( empty( $encrypted_data_tmp))
        {
            openssl_free_key( $public_key );
            return 0;
        }

        $ret = base64_encode( $encrypted_data_tmp );
        return $ret;
    }

    //私钥解密
    function decrypt_data_private($encrypted_data,$private_key_path)
    {
        header("Content-Type:text/html;charset=utf-8");
        date_default_timezone_set('Asia/Shanghai');
        if(! file_exists($private_key_path))
        {
            return 001;
        }

        $fp=fopen ($private_key_path,"r");
        $private_key_tmp = fread( $fp, 8192 );
        fclose($fp);


        $private_key = openssl_get_privatekey( $private_key_tmp );



        if (!$private_key)
        {
            return 002;
        }

        $ret = openssl_private_decrypt( base64_decode($encrypted_data), $decrypted, $private_key );

        if (!$ret)
        {
            openssl_free_key($private_key);
            return 003;
        }

        openssl_free_key($private_key);
        return $decrypted;
    }

    //私钥加密
    function encrypt_data_private($data_to_encrypt,$private_key_path)
    {
        header("Content-Type:text/html;charset=utf-8");
        date_default_timezone_set('Asia/Shanghai');
        $string_to_encrypt = $data_to_encrypt;


        if (! file_exists($private_key_path))
        {
            return 0;
        }

        $fp = fopen ( $private_key_path, "r" );
        $private_key_tmp = fread ( $fp, 8192 );
        fclose( $fp );


        $private_key = openssl_get_privatekey($private_key_tmp);


        if (!$private_key)
        {
            openssl_free_key( $private_key );
            return 0;
        }


        openssl_private_encrypt( $string_to_encrypt, $encrypted_data_tmp, $private_key );


        if( empty( $encrypted_data_tmp))
        {
            openssl_free_key( $private_key );
            return 0;
        }

        $ret = base64_encode( $encrypted_data_tmp );
        return $ret;
    }

    //公钥解密
    function decrypt_data_public($encrypted_data,$public_key_path)
    {
        header("Content-Type:text/html;charset=utf-8");
        date_default_timezone_set('Asia/Shanghai');
        if(! file_exists($public_key_path))
        {
            return 0;
        }

        $fp=fopen ($public_key_path,"r");
        $public_key_tmp = fread( $fp, 8192 );
        fclose($fp);


        $public_key = openssl_get_publickey( $public_key_tmp );



        if (!$public_key)
        {
            return 0;
        }

        $ret = openssl_public_decrypt( base64_decode($encrypted_data), $decrypted, $public_key );

        if (!$ret)
        {
            openssl_free_key($public_key);
            return 0;
        }
        openssl_free_key($public_key);
        return $decrypted;
    }
}