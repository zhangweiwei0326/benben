<?php

class StoreController extends PublicController
{
    public $layout = false;

    public function actionDeclaration(){
        return $this->render('declaration');
    }

    /**
     * 号码直通车搜索
     */
    public function actionSearch()
    {
// 		$distance = $this->getDistanceBetweenPointsNew(31.8374, 117.309352, 32.837465, 117.309431);
// 		var_dump($distance);
// 		exit();
        $this->check_key();
        $user = $this->check_user();
        $province = Frame::getIntFromRequest('province');
        $city = Frame::getIntFromRequest('city');
        $area = Frame::getIntFromRequest('area');
        $street = Frame::getIntFromRequest('street');
        $longitude = Frame::getStringFromRequest('longitude');//经度
        $latitude = Frame::getStringFromRequest('latitude');//经度
        $industry = Frame::getStringFromRequest('industry');//行业


        $keyword = Frame::getStringFromRequest('keyword');
        //$last_time = Frame::getIntFromRequest('last_time');
        $page = Frame::getIntFromRequest('page');
        $connection = Yii::app()->db;
        $asql = "";
        $auctionSqlAddr="";

        $distance_sql = "";
        $distance_order = "";

        //默认位置信息
        if(empty($latitude)&&empty($longitude)&&empty($province)&&empty($city)&&empty($area)&&($street)){
            //没有位置默认浙江省杭州市上杭区
            $asql .= "province = 33 and city = 3301 and area = 330102 and ";
            $auctionSqlAddr.="province = 33 and city = 3301 and area = 330102 and ";
        }

        //地理位置距离查询
        if ($latitude && $longitude) {
            if($latitude && $longitude&&empty($province)&&empty($city)&&empty($area)) {
                //存在地理位置反查询省，城市，地区
                $url = "http://api.map.baidu.com/geocoder/v2/?ak=Wzget22boBxEfIhHj53pgnpq&output=json&pois=0&location=" . $latitude . "," . $longitude;
                $ownLocation = $this->openRequest($url, "");
                $ownLocation = json_decode($ownLocation, true);
                if (empty($province) && $ownLocation['result']['addressComponent']['province']) {
                    $provinceName = $ownLocation['result']['addressComponent']['province'];
                    $tplArea = Area::model()->find("area_name='{$provinceName}'");
                    $province_top = $tplArea['bid'];
                }
                if (empty($city) && $ownLocation['result']['addressComponent']['city']) {
                    $cityName = $ownLocation['result']['addressComponent']['city'];
                    $tplArea = Area::model()->find("area_name='{$cityName}'");
                    if ($tplArea) {
                        $city_top = $tplArea['bid'];
                    } elseif ($province_top) {
                        $tplArea = Area::model()->find("parent_bid='{$province_top}'");
                        $city_top = $tplArea['bid'];
                    }
                }
                if (empty($area) && $ownLocation['result']['addressComponent']['district']) {
                    $areaName = $ownLocation['result']['addressComponent']['district'];
                    $tplArea = Area::model()->find("area_name='{$areaName}'");
                    if ($tplArea) {
                        $area_top = $tplArea['bid'];
                    } elseif ($city_top) {
                        $tplArea = Area::model()->find("parent_bid='{$city_top}'");
                        $area_top = $tplArea['bid'];
                    }
                }
            }

            $distance_sql = ",round(6378.138*2*asin(sqrt(pow(sin( (lat*pi()/180-" . $latitude . "*pi()/180)/2),2)+cos(lat*pi()/180)*cos(" . $latitude . "*pi()/180)* pow(sin( (lng*pi()/180-" . $longitude . "*pi()/180)/2),2)))*1000) as distance";
            $distance_order = "distance desc,";
        }

        //搜索省市位置/置顶行业
        if ($province) {
            $asql .= "province = {$province} and ";
            $province_top=$province;
        }

        if ($city) {
            $asql .= "city = {$city} and ";
            $city_top=$city;
        }

        if ($area) {
            $asql .= "area = {$area} and ";
            $area_top=$area;
        }

        if ($street) {
            $asql .= "street = {$street} and ";
        }

        //置顶查询
        if($province_top&&$city_top&&$area_top){
            $auctionSqlAddr.="province = {$province_top} and city = {$city_top} and area = {$area_top} and ";
        }else{
            $auctionSqlAddr.="province = 0 and city = 0 and area = 0 and ";
        }
        //行业处理，获取所有主行业所属最后一层行业，置顶需要倒推第一层
        if($industry){
            $industryLastLevel=array();
            $levelTwo=Industry::model()->findAll("parent_id={$industry}");
            if($levelTwo) {
                foreach ($levelTwo as $k => $v) {
                    if ($v['last'] != 1) {
                        $levelThree = Industry::model()->findAll("parent_id={$v['id']}");
                        foreach ($levelThree as $kk => $vv) {
                            $industryLastLevel[] = $vv['id'];
                        }
                    } else {
                        $industryLastLevel[] = $v['id'];
                    }
                }
            }else{
                $industryLastLevel[]=$industry;
            }
            $asql.="industry in (".implode(",",$industryLastLevel).") and ";

            //置顶需要倒推第一层
            $industry_pre=Industry::model()->find("id={$industry}");
            if($industry_pre['level']!=1){
                $industry_two=Industry::model()->find("id={$industry_pre['parent_id']}");
                if($industry_two['level']==1){
                    $auctionSqlAddr.="industry=".$industry." and ";
                }else{
                    $industry_three=Industry::model()->find("id={$industry_two['parent_id']}");
                    if($industry_three['level']==1){
                        $auctionSqlAddr.="industry=".$industry." and ";
                    }
                }
            }else{
                $auctionSqlAddr.="industry=".$industry." and ";
            }
        }
        $auctionSqlAddr.= "pid!=0";
        $asql .= "is_close = 0 and status = 0 and ";
        $asql = trim($asql);
        $limit = $page * 100;
        if ($keyword) {
            $shop = $this->changeTrain($keyword);
            $sqlc = "select count(*) as num from number_train where {$asql} (short_name like '%{$keyword}%' or tag like '%{$keyword}%' or id={$shop})";
            $command = $connection->createCommand($sqlc);
            $resultc = $command->queryAll();
            if (!$resultc[0]['num']) {
                $result ['ret_num'] = 2222;
                $result ['ret_msg'] = '暂无与其相关内容';
                echo json_encode($result);
                die();
            }
        }

        if ($keyword) {
            $shop = $this->changeTrain($keyword);
            $sql = "select member_id,id,name,short_name,poster,phone,telephone,tag,description,lat,lng,industry,istop,views,created_time {$distance_sql}
			from number_train where {$asql} (short_name like '%{$keyword}%' or tag like '%{$keyword}%' or id={$shop})
			order by istop desc, {$distance_order} created_time desc limit {$limit},100";
        } else {
            if ($asql) {
                $asql = "where " . $asql;
                $asql = trim($asql, 'and');
            }
            $sql = "select member_id,id,name,short_name,poster,phone,telephone,tag,description,lat,lng,industry,istop,views,created_time {$distance_sql}
			from number_train {$asql} order by istop desc,{$distance_order} created_time desc limit {$limit},100";
        }

        $command = $connection->createCommand($sql);
        $result0 = $command->queryAll();
        $num = count($result0);
        if (!$result0) {
            $result ['ret_num'] = 2020;
            $result ['ret_msg'] = '已无更多内容';
            $result ['number_info'] = $result0;
            echo json_encode($result);
            die();
        }
        //获取商店号shop
        $mbid = array();
        $member = array();

        foreach ($result0 as $k => $v) {
            $mbid[] = $v['member_id'];
        }

        //置顶拍卖，置顶处理
        $nowTime=time();
        $auctionSql="owner_id in (".implode(",",$mbid).") and top_start_period<=".$nowTime." and top_end_period>".$nowTime." and is_close=1 and is_paid=1 and ".$auctionSqlAddr." order by place asc";
        $topPlace=TopAuction::model()->findAll($auctionSql);
        $topMember=array();
        $topMemberPlace=array();
        if($topPlace) {
            foreach ($topPlace as $kt => $vt) {
                //处理重复中标的人
                if(in_array($vt['owner_id'],$topMember)) {
                    $topMember[] = $vt['owner_id'];
                    $topMemberPlace[$vt['owner_id']] .=",".intval($vt['place']);
                }else{
                    $topMember[] = $vt['owner_id'];
                    $topMemberPlace[$vt['owner_id']]=intval($vt['place']);
                }
            }
        }

        $sql_m = "select b.id, b.benben_id from member as b where b.id in (" . implode(",", $mbid) . ")";
        $command = $connection->createCommand($sql_m);
        $resultm = $command->queryAll();
        foreach ($resultm as $kk => $vv) {
            $member[$vv['id']] = "hz" . $vv['benben_id'];
        }
        //计算距离
        $industry_arr = $this->Industry($result0);
        foreach ($result0 as $key => $value) {
            $result0[$key]['place']=100;//初始化拍卖置顶位置信息
            $result0[$key]['shop'] = $member[$value['member_id']] ? $member[$value['member_id']] : "";
            $result0[$key]['industry'] = $industry_arr[$value['industry']] ? $industry_arr[$value['industry']] : "";
            $result0[$key]['poster'] = $value['poster'] ? URL . $value['poster'] : "";
            $result0[$key]['description'] = $value['description'] ? $value['description'] : "";
            $storeVipInfo=PromotionManage::model()->find("store_id={$value['id']}");
            $result0[$key]['auth_grade']=$storeVipInfo ? $storeVipInfo['vip_type']+1 : 0;
            $result0[$key]['is_valid']=$this->storevip($value['member_id'])?1:0;
            if ($latitude && $longitude) {
                //$distance = $this->getDistanceBetweenPointsNew($latitude, $longitude, $value['lat'], $value['lng']);
                $distance = round(($value['distance'] / 1000), 1);
                if ($distance < 1) {
                    $result0[$key]['distance_kilometers'] = $value['distance'] . "m";
                } else {
                    $result0[$key]['distance_kilometers'] = $distance . "km";
                }
            }
            if(in_array($value['member_id'],$topMember)){
                //判断重复中标的用户
                if(is_string($topMemberPlace[$value['member_id']])) {
                    $place=explode(",",$topMemberPlace[$value['member_id']]);
                    foreach($place as $kp=>$vp){
                        if($kp==0) {
                            $result0[$key]['place'] = $vp;
                        }else{
                            $result0[($num+$kp-1)]=$result0[$key];
                            $result0[($num+$kp-1)]['place'] = $vp;
                        }
                    }
                }else{
                    $result0[$key]['place'] = $topMemberPlace[$value['member_id']];
                }
                $topMemberKey=array_search($value['member_id'],$topMember);
                unset($topMember[$topMemberKey]);
            }
        }
        usort($result0, function($a, $b) {
            $al = ($a['place']);
            $bl = ($b['place']);
            if ($al == $bl){
                if($a['distance']==$b['distance']){
                    return 0;
                }else{
                    return ($a['distance']>$b['distance']) ? 1 :  -1;
                }
            }else {
                return ($al > $bl) ? 1 : -1;
            }
        });
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result ['num'] = $num;
        $result ['number_info'] = $result0;
        echo json_encode($result);
    }

    /**
     * 号码直通车添加(修改)
     */
    public function actionAdd()
    {
        $this->check_key();
        $name = Frame::getStringFromRequest('name');
        $short_name = Frame::getStringFromRequest('short_name');

        $pic[] = Frame::saveImage('pic1', 1);
        $pic[] = Frame::saveImage('pic2', 1);
        $pic[] = Frame::saveImage('pic3', 1);
        $pic[] = Frame::saveImage('pic4', 1);
        $pic[] = Frame::saveImage('pic5', 1);
        $pic[] = Frame::saveImage('pic6', 1);

        $phone = Frame::getStringFromRequest('phone');
        $telephone = Frame::getStringFromRequest('telephone');
        $industry = Frame::getIntFromRequest('industry');
        $province = Frame::getIntFromRequest('province');
        $city = Frame::getIntFromRequest('city');
        $area = Frame::getIntFromRequest('area');
        $street = Frame::getIntFromRequest('street');
        $lng = Frame::getStringFromRequest('lng');//经度
        $lat = Frame::getStringFromRequest('lat');//经度
        $tag = Frame::getStringFromRequest('tag');
        $poster=Frame::getStringFromRequest('poster');//用于删除该图片
        $address = Frame::getStringFromRequest('address');
        $description = Frame::getStringFromRequest('description');
        $ids=Frame::getStringFromRequest('ids');//需要删除的多张图片，以逗号分割
        $tag_arr = explode(" ", $tag);
        // foreach ($tag_arr as $value){
        // 	if(mb_strlen($value,"utf-8")>6){
        // 		$result['ret_num'] = 5295;
        // 		$result['ret_msg'] = '每个服务项目不能超过6个字';
        // 		echo json_encode( $result );
        // 		die();
        // 	}
        // }
        $connection=Yii::app()->db;

        $user = $this->check_user();
//        if (($user->userinfo & 1) > 0 || ($user->userinfo & 2) > 0) {
//
//        } else {
//            $result['ret_num'] = 1616;
//            $result['ret_msg'] = '请先完善个人资料';
//            echo json_encode($result);
//            die ();
//        }
        $re = NumberTrain::model()->find("member_id = {$user->id}");

        if ($re) {
            //删除旧版本图片,或将旧版图片移至新数据库
            if($poster==1){
                if(file_exists(ROOT.$re['poster'])){
                    unlink(ROOT.$re['poster']);
                }
                if(file_exists(ROOT.$this->getThumb($re['poster']))){
                    unlink(ROOT.$this->getThumb($re['poster']));
                }
            }else{
                $attcount=NumberTrainAttachment::model()->count("train_id={$re['id']}");
                if(!$attcount){
                    $att=new NumberTrainAttachment();
                    $att->train_id=$re['id'];
                    $att->poster=$re['poster'];
                    $att->save();
                }
            }

            //需要删除
            if($ids){
                $attinfo=NumberTrainAttachment::model()->findAll("id in ({$ids})");
                foreach($attinfo as $va){
                    if($va['poster']) {
                        if(file_exists(ROOT.$va['poster'])){
                            unlink(ROOT.$va['poster']);
                        }
                        if(file_exists(ROOT.$this->getThumb($va['poster']))){
                            unlink(ROOT.$this->getThumb($va['poster']));
                        }
                    }
                }
                NumberTrainAttachment::model()->deleteAll("id in ({$ids})");
            }

            //修改
            if ($name) {
                $re->name = $name;
            }
            if ($short_name) {
                $re->short_name = $short_name;
            }
            if (!empty($pic)) {
                foreach($pic as $v){
                    if($v){
                        $pic_insert_arr[]="(".$re['id'].",'".$v."')";
                    }
                }
            }
            if ($phone) {
                $re->phone = $phone;
            }
            if ($telephone) {
                $re->telephone = $telephone;
            } else {
                $re->telephone = '';
            }
            if ($industry) {
                $re->industry = $industry;
            }
            if ($province) {
                $re->province = $province;
            }
            if ($city) {
                $re->city = $city;
            }
            if ($area) {
                $re->area = $area;
            }
            if ($street) {
                $re->street = $street;
            }
            if ($lng) {
                $re->lng = $lng;
            }
            if ($lat) {
                $re->lat = $lat;
            }
            if ($tag) {
                $re->tag = $tag;
            }
            if ($address) {
                $re->address = $address;
            }
            if ($description) {
                $re->description = $description;
            }
            if ($re->update()) {
                //插入商城附件表
                if($pic_insert_arr) {
                    $sql_a = "insert into number_train_attachment(train_id,poster) values " . implode(",", $pic_insert_arr);
                    $command=$connection->createCommand($sql_a);
                    $result00=$command->execute();
                }
                //是否更新头像
                $traininfo=NumberTrainAttachment::model()->find("train_id={$re['id']} order by id asc");
                if($traininfo){
                    if($re->poster!=$traininfo['poster']) {
                        $re->poster = $traininfo['poster'];
                        $re->update();
                    }
                }
                $result['ret_num'] = 0;
                $result['ret_msg'] = '操作成功';
                $result['number_info'] = array(
                    "NumberId" => $re->id,
                    "NumberName" => $re->name,
                    "NumberMemberid" => $user->id,
                    "NumberDescription" => $re->description,
                    "Numbertag" => $re->tag,
                    "NumberCreated_time" => $re->created_time,
                );
            } else {
                $result['ret_num'] = 1004;
                $result['ret_msg'] = '号码直通车信息修改失败';
            }
        } else {
            if (empty($pic)) {
                $result['ret_num'] = 5236;
                $result['ret_msg'] = '头像不能为空';
                echo json_encode($result);
                die();
            }
            //新增
            $ename = NumberTrain::model()->find("name = '{$name}'");
            if ($ename) {
                $result['ret_num'] = 5235;
                $result['ret_msg'] = '号码直通车名称已存在';
                echo json_encode($result);
                die();
            }
            $number_info = new NumberTrain();
            $number_info->name = $name;
            $number_info->short_name = $short_name;
            $number_info->member_id = $user->id;
            $number_info->phone = $phone;
            $number_info->telephone = $telephone;
            $number_info->industry = $industry;
            $number_info->province = $province;
            $number_info->city = $city;
            $number_info->area = $area;
            $number_info->street = $street;
            $number_info->lng = $lng;
            $number_info->lat = $lat;
            $number_info->tag = $tag;
            $number_info->address = $address;
            $number_info->description = $description;
            $number_info->created_time = time();
            $number_info->poster = $pic[0];
            if ($number_info->save()) {
                $train_id=$number_info->id;
                $this->addIntegral($user->id, 2);
                //插入头像附件表
                if (!empty($pic)) {
                    foreach($pic as $v){
                        if($v){
                            $pic_insert_arr[]="(".$train_id.",'".$v."')";
                        }
                    }
                }
                if($pic_insert_arr) {
                    $sql_b = "insert into number_train_attachment(train_id,poster) values " . implode(",", $pic_insert_arr);
                    $command=$connection->createCommand($sql_b);
                    $result0=$command->execute();
                }

                $result['ret_num'] = 0;
                $result['ret_msg'] = '操作成功';
                $result['number_info'] = array(
                    "NumberId" => $number_info->id,
                    "NumberName" => $number_info->name,
                    "NumberMemberid" => $user->id,
                    "NumberDescription" => $number_info->description,
                    "Numbertag" => $number_info->tag,
                    "NumberCreated_time" => $number_info->created_time
                );
            } else {
                $result['ret_num'] = 121;
                $result['ret_msg'] = '新建号码直通车失败';
            }
        }

        echo json_encode($result);

    }

    /**
     * 号码直通车信息完善
     */
    public function actionInfo()
    {
        $this->check_key();
        $name = Frame::getStringFromRequest('name');
        $id_card = Frame::getStringFromRequest('id_card');
        $poster1 = Frame::saveImage('poster1');
        $poster2 = Frame::saveImage('poster2');
        $phone = Frame::getStringFromRequest('phone');
        $province = Frame::getIntFromRequest('province');
        $city = Frame::getIntFromRequest('city');
        $area = Frame::getIntFromRequest('area');
        $street = Frame::getIntFromRequest('street');
        $user = $this->check_user();

        if ($user->userinfo & 1 > 0) {
            $apply_info = ApplyComplete::model()->find("member_id = {$user->id} and type = 2");
            if ($apply_info) {
                if ($name) {
                    $apply_info->name = $name;
                }
                if ($id_card) {
                    $apply_info->id_card = $id_card;
                }
                if ($poster1) {
                    $apply_info->poster1 = $poster1;
                }
                if ($poster2) {
                    $apply_info->poster2 = $poster2;
                }
                if ($phone) {
                    $apply_info->phone = $phone;
                }
                if ($province) {
                    $apply_info->province = $province;
                }
                if ($city) {
                    $apply_info->city = $city;
                }
                if ($area) {
                    $apply_info->area = $area;
                }
                if ($street) {
                    $apply_info->street = $street;
                }
                if ($apply_info->update()) {
                    $result['ret_num'] = 0;
                    $result['ret_msg'] = '操作成功';
                    $result['apply_info'] = array(
                        "ApplyId" => $apply_info->id,
                        "ApplyName" => $apply_info->name,
                        "ApplyPhone" => $apply_info->phone,
                        "ApplyCreated_time" => $apply_info->created_time
                    );
                } else {
                    $result['ret_num'] = 1003;
                    $result['ret_msg'] = '号码直通车信息完善失败';
                }
            }
        } else {
            if (empty($id_card)) {
                $result['ret_num'] = 1005;
                $result['ret_msg'] = '证件号为空';
                echo json_encode($result);
                die ();
            }
            if (empty($poster1)) {
                $result['ret_num'] = 1006;
                $result['ret_msg'] = '身份证正面照没有上传';
                echo json_encode($result);
                die ();
            }
            if (empty($poster2)) {
                $result['ret_num'] = 1007;
                $result['ret_msg'] = '身份证反面照没有上传';
                echo json_encode($result);
                die ();
            }
            $apply_info = new ApplyComplete();
            $apply_info->name = $name;
            //$apply_info->apply_id = 0;
            $apply_info->id_card = $id_card;
            $apply_info->member_id = $user->id;
            $apply_info->poster1 = $poster1;
            $apply_info->poster2 = $poster2;
            $apply_info->phone = $phone;
            $apply_info->province = $province;
            $apply_info->city = $city;
            $apply_info->area = $area;
            $apply_info->street = $street;
            $apply_info->type = 2;
            $apply_info->created_time = time();
            if ($apply_info->save()) {
                //添加百姓网详细信息
                /*if($user->userinfo & 2 == 0){
                    $applyc = new ApplyComplete();
                    //$applyc->apply_id = $bxapply->id;
                    $applyc->id_card = $id_card;
                    $applyc->poster1 = $poster1;
                    $applyc->poster2 = $poster2;
                    $applyc->type = 1;
                    //$applyc->member_id = $bxapply->member_id;
                    $applyc->province = $province;
                    $applyc->phone = $phone;
                    $applyc->city = $city;
                    $applyc->area = $area;
                    $applyc->street = $street;
                    $applyc->created_time = time();
                    if($applyc->save()){
                        $user->userinfo = $user->userinfo + 2;
                        $user->update();
                    }
                }*/
                if (!$user->name) $user->name = $name;
                if (!$user->id_card) $user->id_card = $id_card;
                if (!$user->province) $user->province = $province;
                if (!$user->city) $user->city = $city;
                if (!$user->area) $user->area = $area;
                if (!$user->street) $user->street = $street;
                $user->userinfo = $user->userinfo + 1;

                $user->update();
                $result['ret_num'] = 0;
                $result['ret_msg'] = '操作成功';
                $result['apply_info'] = array(
                    "ApplyId" => $apply_info->id,
                    "ApplyName" => $apply_info->name,
                    "ApplyPhone" => $apply_info->phone,
                    "ApplyCreated_time" => $apply_info->created_time
                );
            } else {
                $result['ret_num'] = 1003;
                $result['ret_msg'] = '号码直通车信息完善失败';
            }
        }
        echo json_encode($result);

    }

    /*
     * 商家查看自己店铺详情
     */
    public function actionOwnerdetail(){
        $this->check_key();
        $longitude = Frame::getStringFromRequest('longitude');//经度
        $latitude = Frame::getStringFromRequest('latitude');//经度

        $user = $this->check_user();

        $number_info = NumberTrain::model()->find("member_id={$user['id']}");
        $id=$number_info['id'];
        $shopnum=NumberTrain::model()->count("pid={$id} and is_close=0 and status=0");
        if ($number_info) {
            $connection = Yii::app()->db;

            $attinfo=NumberTrainAttachment::model()->findAll("train_id={$id} order by id asc limit 6");
            if($attinfo) {
                $headposter=$attinfo[0]['poster'];
                foreach ($attinfo as $v) {
                    if($v['poster']) {
                        $att_arr[] = array("id"=>$v['id'],"pic"=>URL . $v['poster']);
                    }
                }
            }

            $distance = 0;
            $distance_kilometers = '';
            if ($latitude && $longitude) {
                $distance_sql = "round(6378.138*2*asin(sqrt(pow(sin( (lat*pi()/180-" . $latitude . "*pi()/180)/2),2)+cos(lat*pi()/180)*cos(" . $latitude . "*pi()/180)* pow(sin( (lng*pi()/180-" . $longitude . "*pi()/180)/2),2)))*1000) as distance";
                $sql = "select {$distance_sql} from number_train where id={$id}";

                $command = $connection->createCommand($sql);
                $distanceReault = $command->queryAll();
                if ($distanceReault) {
                    $distance = $distanceReault[0]['distance'];

                    $kmDistance = round(($distance / 1000), 1);
                    if ($kmDistance < 1) {
                        $distance_kilometers = $distance . "m";
                    } else {
                        $distance_kilometers = $kmDistance . "km";
                    }
                }
            }

            $industry = "";
            //$industy_arr = $this->industryinfo();
            $industry = $this->getIndustryinfo($number_info->industry);
            //自己是否收藏
            $conllection = 0;
            $con = NumberTrainCollect::model()->find("number_train_id = {$id} and member_id = {$user->id}");
            if ($con) {
                $conllection = 1;
            }
            //收藏人数
            $sql = "select count(id) num from number_train_collect where number_train_id = {$id}";
            $command = $connection->createCommand($sql);
            $result0 = $command->queryAll();

            //查询认证情况
            $storeinfo=StoreAuth::model()->find("member_id={$number_info['member_id']}");
            //未认证
            if(!$storeinfo){
                $notauth=1;
            }

            //查看促销/团购情况
            $promotion=array();
            if($this->storevip($number_info['member_id'])){
                $is_promotion=1;
            }else{
                $is_promotion=0;
            }
            $pid=PromotionManage::model()->find("member_id={$number_info['member_id']}");
            if($pid) {
                $pinfo = Promotion::model()->findAll("pm_id={$pid['id']} and is_close=0 order by vip_time Desc,valid_right Desc limit " . $pid['online_restrict']);
                foreach ($pinfo as $kp => $vp) {
                    $goodsinfo=GoodsGallery::model()->find("goods_id={$vp['id']} order by img_desc asc");
                    $promotion[] = array(
                        "promotionid" => $vp['id'] ? $vp['id'] : "",
                        "poster" => $vp['poster_st'] ? URL.$vp['poster_st'] :($goodsinfo['img_url'] ? URL . $goodsinfo['img_url'] : ""),
                        "small_poster" => $vp['poster_st'] ? (file_exists(ROOT.$this->getSmall($vp['poster_st']))?URL.$this->getSmall($vp['poster_st']):URL.$this->getThumb($vp['poster_st'])):URL . $goodsinfo['img_url'],
                        "vip_time" => $vp['vip_time'] ? $vp['vip_time'] : "",
                        "name" => $vp['name'] ? $vp['name'] : "",
                        "description" => $vp['description'] ? $vp['description'] : "",
                        "is_down" => $vp['valid_right'] >= time() ? 0 : 1,
                        "is_reach" => $vp['valid_left'] < time() ? 1 : 0,
                        "promotion_price"=>$vp['promotion_price'],
                        "origion_price"=>$vp['origion_price'],
                        "sellcount"=>$vp['sellcount'],
                    );
                }
            }

            //评论数，好评率
            $sql12 = "select a.comment_rank from store_comment as a left join promotion as b on a.promotion_id=b.id
            left join promotion_manage as c on b.pm_id=c.id where c.store_id={$number_info['id']} and a.parent_id=0 and a.is_seller=0";
            $command = $connection->createCommand($sql12);
            $result12 = $command->queryAll();
            $good=0;
            foreach($result12 as $kr=>$vr){
                if($vr['comment_rank']==3){
                    $good++;
                }
            }

            $result['ret_num'] = 0;
            $result['ret_msg'] = '操作成功';
            $result['number_info'] = array(
                "NumberId" => $number_info->id,
                "NumberName" => $number_info->name,
                "NumberShortName" => $number_info->short_name,
                "NumberMemberid" => $user->id,
                "huanxin_username" => $user ? $user->huanxin_username : "",
                "shop" => $user ? "hz" . $user['benben_id'] : "",
                "NumberPoster" => $number_info->poster ? URL . $number_info->poster:($headposter ? URL.$headposter : ""),
                "allposter"=> $att_arr ? $att_arr : array(),
                "NumberPhone" => $number_info->phone,
                "NumberTelephone" => $number_info->telephone,
                "NumberCollection" => $conllection,
                "NumberLat" => $number_info->lat,
                "NumberLng" => $number_info->lng,
                "NumberAddress" => $number_info->address,
                "NumberInd" => $number_info->industry ? $number_info->industry : "",
                "NumberIndustry" => $industry[0]['name'] ? $industry[0]['name'] : "",
                "NumberDescription" => $number_info->description,
                "Numbertag" => $number_info->tag,
                "NumberViews" => $number_info->views,
                "CollectionNumber" => $result0[0]['num'],
                "NumberCreated_time" => $number_info->created_time,
                'distance' => $distance,
                'distance_kilometers' => $distance_kilometers,
                'auth_status' => $storeinfo['status'] ? $storeinfo['status'] : 0,
                "no_auth"=> $notauth ? $notauth : 0,
                "is_promotion"=>$is_promotion,
                "promotion"=>$promotion,
                "vip_time"=>$storeinfo['status']==2 ? ($pid['vip_time'] ? $pid['vip_time'] : 0) : ($pid['time']?$pid['time']:0),
                "is_overtime"=>$is_promotion?0:1,
                'vip_type'=>$pid['store_type'] ? $pid['store_type'] : 0,
                'type'=>$storeinfo['type']?$storeinfo['type']:0,
                'shopnum'=>$shopnum,
                'auth_grade'=>$pid ? $pid['vip_type']+1 : 0,
                "rank"=>$number_info->score ? $this->score2rank($number_info->score) : 0,
                "num"=>count($result12)?count($result12):0,
                "mean_rate"=>count($result12) ? (number_format($good/count($result12),4,".","")*100)."%" : "100%",
            );
        } else {
            $result['ret_num'] = 123;
            $result['ret_msg'] = '号码直通车信息不存在';
        }
        echo json_encode($result);
    }

    /**
     * 号码直通车查看详情
     */
    public function actionDetail()
    {
        $this->check_key();
        $id = Frame::getIntFromRequest('id');//号码直通车id
        $storeid = Frame::getIntFromRequest('storeid');//"hz"+奔犇号
        $longitude = Frame::getStringFromRequest('longitude');//经度
        $latitude = Frame::getStringFromRequest('latitude');//经度
        if (empty($id)) {
            $result['ret_num'] = 122;
            $result['ret_msg'] = '号码直通车ID为空';
            echo json_encode($result);
            die ();
        }
        $user = $this->check_user();

        if ($storeid) {
            $id = $this->changeTrain($storeid);
        }
        $number_info = NumberTrain::model()->findByPk($id);
        $shopnum=NumberTrain::model()->count("pid={$id} and is_close=0 and status=0");
        if ($number_info) {
            $connection = Yii::app()->db;

            $attinfo=NumberTrainAttachment::model()->findAll("train_id={$id} order by id asc limit 6");
            if($attinfo) {
                $headposter=$attinfo[0]['poster'];
                foreach ($attinfo as $v) {
                    if($v['poster']) {
                        $att_arr[] = array("id"=>$v['id'],"pic"=>URL . $v['poster']);
                    }
                }
            }

            $distance = 0;
            $distance_kilometers = '';
            if ($latitude && $longitude) {
                $distance_sql = "round(6378.138*2*asin(sqrt(pow(sin( (lat*pi()/180-" . $latitude . "*pi()/180)/2),2)+cos(lat*pi()/180)*cos(" . $latitude . "*pi()/180)* pow(sin( (lng*pi()/180-" . $longitude . "*pi()/180)/2),2)))*1000) as distance";
                $sql = "select {$distance_sql} from number_train where id={$id}";

                $command = $connection->createCommand($sql);
                $distanceReault = $command->queryAll();
                if ($distanceReault) {
                    $distance = $distanceReault[0]['distance'];

                    $kmDistance = round(($distance / 1000), 1);
                    if ($kmDistance < 1) {
                        $distance_kilometers = $distance . "m";
                    } else {
                        $distance_kilometers = $kmDistance . "km";
                    }
                }
            }
            //查询号码直通车的创建人环信ID
            if ($number_info->member_id) {
                $nuser = Member::model()->findByPk($number_info->member_id);
            }
            $industry = "";
            //$industy_arr = $this->industryinfo();
            $industry = $this->getIndustryinfo($number_info->industry);
            //自己是否收藏
            $conllection = 0;
            $con = NumberTrainCollect::model()->find("number_train_id = {$id} and member_id = {$user->id}");
            if ($con) {
                $conllection = 1;
            }
            //收藏人数
            $sql = "select count(id) num from number_train_collect where number_train_id = {$id}";
            $command = $connection->createCommand($sql);
            $result0 = $command->queryAll();
            //增加浏览量
            $number_info->views = $number_info->views + 1;
            $number_info->update();

            //查询认证情况
            $storeinfo=StoreAuth::model()->find("member_id={$number_info['member_id']}");
            //未认证
            if(!$storeinfo){
                $notauth=1;
            }

            //查看促销/团购情况
            $promotion=array();
            if($this->storevip($number_info['member_id'])){
                $is_promotion=1;
                $pid=PromotionManage::model()->find("member_id={$number_info['member_id']}");
                if($pid) {
                    $pinfo = Promotion::model()->findAll("pm_id={$pid['id']} and is_close=0 and valid_right>=".time()." and valid_left<".time()." order by vip_time Desc,valid_right Desc limit " . $pid['online_restrict']);
                    foreach ($pinfo as $kp => $vp) {
                        $goodsinfo=GoodsGallery::model()->find("goods_id={$vp['id']} order by img_desc asc");
                        $promotion[] = array(
                            "promotionid" => $vp['id'] ? $vp['id'] : "",
                            "poster" => $vp['poster_st'] ? URL.$vp['poster_st'] :($goodsinfo['img_url'] ? URL . $goodsinfo['img_url'] : ""),
                            "small_poster" =>$vp['poster_st'] ? (file_exists(ROOT.$this->getSmall($vp['poster_st']))?URL.$this->getSmall($vp['poster_st']) :  URL . $this->getThumb($vp['poster_st'])):URL . $goodsinfo['img_url'],
                            "vip_time" => $vp['vip_time'] ? $vp['vip_time'] : "",
                            "name" => $vp['name'] ? $vp['name'] : "",
                            "description" => $vp['description'] ? $vp['description'] : "",
                            "is_down" => $vp['valid_right'] >= time() ? 0 : 1,
                            "is_reach" => $vp['valid_left'] < time() ? 1 : 0,
                            "promotion_price"=>$vp['promotion_price'],
                            "origion_price"=>$vp['origion_price'],
                            "sellcount"=>$vp['sellcount'],
                        );
                    }
                }
            }else{
                $is_promotion=0;
            }

            //评论数，好评率
            $sql12 = "select a.comment_rank from store_comment as a left join promotion as b on a.promotion_id=b.id
            left join promotion_manage as c on b.pm_id=c.id where c.store_id={$number_info['id']} and a.parent_id=0 and a.is_seller=0";
            $command = $connection->createCommand($sql12);
            $result12 = $command->queryAll();
            $good=0;
            foreach($result12 as $kr=>$vr){
                if($vr['comment_rank']==3){
                    $good++;
                }
            }

            $result['ret_num'] = 0;
            $result['ret_msg'] = '操作成功';
            $result['number_info'] = array(
                "NumberId" => $number_info->id,
                "NumberName" => $number_info->name,
                "NumberShortName" => $number_info->short_name,
                "NumberMemberid" => $user->id,
                "huanxin_username" => $nuser ? $nuser->huanxin_username : "",
                "shop" => $nuser ? "hz" . $nuser['benben_id'] : "",
                "NumberPoster" => $number_info->poster ? URL . $number_info->poster:($headposter ? URL.$headposter : ""),
                "allposter"=> $att_arr ? $att_arr : array(),
                "NumberPhone" => $number_info->phone,
                "NumberTelephone" => $number_info->telephone,
                "NumberCollection" => $conllection,
                "NumberLat" => $number_info->lat,
                "NumberLng" => $number_info->lng,
                "NumberAddress" => $number_info->address,
                "NumberInd" => $number_info->industry ? $number_info->industry : "",
                "NumberIndustry" => $industry[0]['name'] ? $industry[0]['name'] : "",
                "NumberDescription" => $number_info->description,
                "Numbertag" => $number_info->tag,
                "NumberViews" => $number_info->views,
                "CollectionNumber" => $result0[0]['num'],
                "NumberCreated_time" => $number_info->created_time,
                'distance' => $distance,
                'distance_kilometers' => $distance_kilometers,
                'auth_status' => $storeinfo['status'] ? $storeinfo['status'] : 0,
                "no_auth"=> $notauth ? $notauth : 0,
                "is_promotion"=>$is_promotion,
                "promotion"=>$promotion,
                "vip_time"=>$pid['vip_time'] ? $pid['vip_time'] : 0,
                "is_overtime"=>$pid['vip_time'] ? (($pid['vip_time']>=time()) ? 0 : 1) : 1,
                'vip_type'=>$pid['store_type'] ? $pid['store_type'] : 0,
                'type'=>$storeinfo['type']?$storeinfo['type']:0,
                'shopnum'=>$shopnum?$shopnum:0,
                'auth_grade'=>$pid ? $pid['vip_type']+1 : 0,
                "rank"=>$number_info->score ? $this->score2rank($number_info->score) : 0,
                "num"=>count($result12)?count($result12):0,
                "mean_rate"=>count($result12) ? (number_format($good/count($result12),4,".","")*100)."%" : "100%",
            );
        } else {
            $result['ret_num'] = 123;
            $result['ret_msg'] = '号码直通车信息不存在';
        }
        echo json_encode($result);

    }

    /**
     * 我的号码直通车
     */
    public function actionMydetail()
    {
        $this->check_key();
        $user = $this->check_user();
        //$pinfo = $this->pcinfo();
        $number_info = NumberTrain::model()->find("member_id = {$user->id}");
        if ($number_info) {
            //取出所有附件头像
            $numatt=NumberTrainAttachment::model()->findAll("train_id={$number_info['id']} order by id asc limit 6");
            if($numatt){
                $headposter=$numatt[0]['poster'];
                foreach($numatt as $va){
                    if($va['poster']) {
                        $att_arr[] = array("id"=>$va['id'],"pic"=>URL . $va['poster']);
                    }
                }
            }
            $industry = "";
            $industry = $this->getIndustryinfo($number_info->industry);
            //省市
            $pro = array("province" => $number_info->province, "city" => $number_info->city,
                "area" => $number_info->area, "street" => $number_info->street);
            $pro_arr = $this->ProCity(array($pro));
            //增加浏览量
            $number_info->views = $number_info->views + 1;
            $number_info->update();
            $connection = Yii::app()->db;
            $command = $connection->createCommand("select count(*) as c from broadcasting_log where member_id = {$user->id}  and type = 1");
            $authority = $command->queryAll();
            $haveRight = 2;
            if ($authority) {
                $haveRight = 2 - $authority[0]['c'];
            }
            if (!$content) {
                $content = '我创建了直通车，来看看吧～';
            }
            $result['ret_num'] = 0;
            $result['ret_msg'] = '操作成功';
            $result['number_info'] = array(
                "NumberId" => $number_info->id,
                "NumberName" => $number_info->name,
                "NumberShort_name" => $number_info->short_name,
                "NumberPhone" => $number_info->phone,
                "NumberTel" => $number_info->telephone,
                "NumberLat" => $number_info->lat,
                "NumberLng" => $number_info->lng,
                "NumberProvince" => $number_info->province,
                "NumberCity" => $number_info->city,
                "NumberArea" => $number_info->area,
                "NumberStreet" => $number_info->street,
                "NumberInd" => $number_info->industry ? $number_info->industry : "",
                "NumberIndustry" => $industry[0]['name'] ? $industry[0]['name'] : "",
                "NumberPoster" => $number_info->poster ? URL . $number_info->poster:($headposter ? URL.$headposter : ""),
                "allposter"=>$att_arr ? $att_arr : array(),
                "NumberMemberid" => $user->id,
                "NumberPro_city" => $pro_arr[$number_info->province] . " " . $pro_arr[$number_info->city] . " " . $pro_arr[$number_info->area] . " " . $pro_arr[$number_info->street],
                "NumberDescription" => $number_info->description,
                "NumberAddress" => $number_info->address,
                "Numbertag" => $number_info->tag,
                "NumberViews" => $number_info->views,
                "NumberStatus" => $number_info->status,
                "NumberCreated_time" => $number_info->created_time,
                "HaveRight" => $haveRight,
                'is_close' => $number_info->is_close
            );
        } else {
            $result['ret_num'] = 123;
            $result['ret_msg'] = '您还没有创建号码直通车！';
        }
        echo json_encode($result);

    }

    /**
     * 号码直通车收藏
     */
    public function actionCollect()
    {
        $this->check_key();
        $id = Frame::getIntFromRequest('id');
        if (empty($id)) {
            $result['ret_num'] = 122;
            $result['ret_msg'] = '号码直通车ID为空';
            echo json_encode($result);
            die ();
        }
        $user = $this->check_user();

        $number_info = NumberTrain::model()->findByPk($id);
        if (empty($number_info)) {
            $result['ret_num'] = 123;
            $result['ret_msg'] = '号码直通车信息不存在';
            echo json_encode($result);
            die ();
        }
        if ($number_info->member_id == $user->id) {
            $result['ret_num'] = 1298;
            $result['ret_msg'] = '不能收藏自己的号码直通车';
            echo json_encode($result);
            die ();
        }
        $str = "";
        $re = NumberTrainCollect::model()->find("number_train_id = {$id} and member_id = {$user->id}");
        if (!$re) {
            $numtc = new NumberTrainCollect();
            $numtc->number_train_id = $id;
            $numtc->member_id = $user->id;
            $numtc->created_time = time();
            if ($numtc->save()) {
                $this->addIntegral($user->id, 9);
                $this->addIntegral($number_info->member_id, 20);
                $result['ret_num'] = 0;
                $result['ret_msg'] = "收藏成功！";
                $m = new Memcached();
                $m->addServer('localhost', 11211);
                $snapshot = $m->get("addrsversion:" . $user['id']);
                $m->set("addrsversion:" . $user['id'],($snapshot+1));
                $phone = array(
                    "id" => "",
                    "contact_info_id" => $number_info->id + 1000000,
                    "phone" => $number_info->phone,
                    "is_benben" => 0,
                    "is_baixing" => 0,
                    "poster" => $number_info->poster ? URL . $number_info->poster : "",
                    "huanxin_username" => ""
                );
                $short_phone = array(
                    "id" => "",
                    "contact_info_id" => $number_info->id + 1000000,
                    "phone" => $number_info->telephone,
                    "is_benben" => 0,
                    "is_baixing" => 0,
                    "poster" => $number_info->poster ? URL . $number_info->poster : "",
                    "huanxin_username" => ""
                );
                $collect = array(
                    "id" => $number_info->id + 1000000,
                    "group_id" => 10000,
                    "name" => $number_info->short_name,
                    "short_name" => $number_info->name,
                    "pinyin" => "",
                    "created_time" => "",
                    "is_benben" => 0,
                    "is_baixing" => 0,
                    "poster" => $number_info->poster ? URL . $number_info->poster : "",
                    "huanxin_username" => "",
                    "phone" => $number_info->telephone ? array($phone, $short_phone) : array($phone)
                );
                $result['collect'] = $collect;
            }
        } else {
            $result['ret_num'] = 5236;
            $result['ret_msg'] = "已经收藏该号码直通车";
        }
        echo json_encode($result);

    }

    /**
     * 号码直通车取消收藏
     */
    public function actionCancelcollect()
    {
        $this->check_key();
        $id = Frame::getIntFromRequest('id');
        if (empty($id)) {
            $result['ret_num'] = 122;
            $result['ret_msg'] = '号码直通车ID为空';
            echo json_encode($result);
            die ();
        }
        $user = $this->check_user();

        $number_info = NumberTrain::model()->findByPk($id);
        if (empty($number_info)) {
            $result['ret_num'] = 123;
            $result['ret_msg'] = '号码直通车信息不存在';
            echo json_encode($result);
            die ();
        }

        $re = NumberTrainCollect::model()->find("number_train_id = {$id} and member_id = {$user->id}");
        if ($re) {
            $re->delete();
            $result['ret_num'] = 0;
            $result['ret_msg'] = "操作成功";
            $m = new Memcached();
            $m->addServer('localhost', 11211);
            $snapshot = $m->get("addrsversion:" . $user['id']);
            $m->set("addrsversion:" . $user['id'],($snapshot+1));
        } else {
            $result['ret_num'] = 5232;
            $result['ret_msg'] = "没有收藏该号码直通车";
        }
        echo json_encode($result);

    }

    /**
     * 号码直通车收藏列表
     */
    public function actionCollectlist()
    {
        $this->check_key();
        $longitude = Frame::getStringFromRequest('longitude');//经度
        $latitude = Frame::getStringFromRequest('latitude');//经度
        $page = Frame::getIntFromRequest('page');
        $user = $this->check_user();

        $connection = Yii::app()->db;
        $limit = $page * 10;
        $sql = "select a.id,a.name,a.poster,a.phone,a.tag,a.lat,a.lng,a.industry,a.istop,a.views,a.created_time from number_train a inner join number_train_collect b on a.id = b.number_train_id where b.member_id = {$user->id} order by a.istop desc,a.created_time desc,a.id desc limit {$limit},10";
        $command = $connection->createCommand($sql);
        $result0 = $command->queryAll();
        //计算距离
        foreach ($result0 as $key => $value) {
            $result0[$key]['poster'] = $value['poster'] ? URL . $value['poster'] : "";
            if ($latitude && $longitude) {
                $distance = $this->getDistanceBetweenPointsNew($latitude, $longitude, $value['lat'], $value['lng']);
                $result0[$key]['distance_kilometers'] = round($distance['kilometers'], 1) . "km";
                $result0[$key]['distance_meters'] = round($distance['meters'], 1) . "m";
            }
        }

        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result ['number_info'] = $result0;
        echo json_encode($result);

    }

    /*
     * 收藏小助手
     * 涉及broadcasting_log
     */
    public function actionHelpcollect(){
        $this->check_key();
        $user = $this->check_user();
        $traininfo=NumberTrain::model()->find("member_id={$user['id']} and is_close=0");
        if(!$traininfo){
            $result['ret_num'] = 122;
            $result['ret_msg'] = '号码直通车可能已关闭或未开通！';
            echo json_encode($result);
            die ();
        }
        //判断可用小喇叭数
        $nowmonth=strtotime(date("Y-m",time())."-1 0:0:0");
        if(date("m",time())==12){
            $nextmonth=strtotime((date("Y",time())+1)."-1-1 0:0:0");
        }else{
            $nextmonth=strtotime(date("Y",time())."-".(date("m",time())+1)."-1 0:0:0");
        }
        $bcnum=BroadcastingLog::model()->count("type=1 and member_id={$user['id']} and created_time>={$nowmonth} and created_time<{$nextmonth}");
        $restnum=2-$bcnum>=0 ? 2-$bcnum:0;//剩余小喇叭数

        //判断好友联盟是否开通
        $fcount=FriendLeague::model()->count("member_id={$user['id']} and is_delete=0");
        if($fcount){
            $is_fl=1;
        }else{
            $is_fl=0;
        }

        //判断促销是否开通
        $is_open=$this->storevip($user['id']);
        if($is_open) {
            $pm1num=PromotionManage::model()->count("member_id={$user['id']} and vip_type=0");
            if($pm1num){
                $is_po=1;
            }else{
                $is_po=0;
            }
        }else{
            $is_po=0;
        }

        //判断团购是否开通
        if($is_open){
            $pm2num=PromotionManage::model()->count("member_id={$user['id']} and vip_type=1");
            if($pm2num){
                $is_gb=1;
            }else{
                $is_gb=0;
            }
        }else{
            $is_gb=0;
        }

        //判断保证金会员是否开通vip_type=2（由于保证金会员暂且没有，全部设为未开通）
        $is_vip=0;

        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result ['rest_num'] = $restnum;
        $result ['is_fl'] = $is_fl;
        $result ['is_po'] = $is_po;
        $result ['is_gb'] = $is_gb;
        $result ['is_vip'] = $is_vip;
        echo json_encode($result);
    }

    /*
     * 收藏小助手弹框
     */
    public function actionPopContent(){
        $this->check_key();
        $user = $this->check_user();
        $traininfo=NumberTrain::model()->find("member_id={$user['id']} and is_close=0");
        if($traininfo) {
            //收藏人数
            $connection = Yii::app()->db;
            $sql1 = "select count(id) num from number_train_collect where number_train_id = {$traininfo['id']}";
            $command = $connection->createCommand($sql1);
            $result0 = $command->queryAll();
            $smallnum = $result0[0]['num'];

            $sql2 = "select count(id) num from number_train_collect";
            $command = $connection->createCommand($sql2);
            $result1 = $command->queryAll();
            $allcount = $result1[0]['num'];
            $rate = $smallnum / $allcount;
            $rate = (number_format($rate, 4, '.', '') * 100);
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            $result ['views'] = $result0[0]['num'];
            //数据调整<=1%显示小于1%，>=99%显示大于99%
            $result ['over_rate'] = $rate ? ($rate <= 1 ? "小于1%" : ($rate >= 99 ? "大于99%" : $rate . "%")) : "0%";
            echo json_encode($result);
        }else{
            $result ['ret_num'] = 100;
            $result ['ret_msg'] = '号码直通车已关闭';
            echo json_encode($result);
        }
    }

    /**
     * 号码直通车信息完善
     */
    public function actionClose()
    {
        $this->check_key();
        $id = Frame::getIntFromRequest('id');
        if (empty($id)) {
            $result['ret_num'] = 122;
            $result['ret_msg'] = '号码直通车ID为空';
            echo json_encode($result);
            die ();
        }
        $user = $this->check_user();
        $connection=Yii::app()->db;
        $number_info = NumberTrain::model()->find("id = {$id} and member_id = {$user->id}");
        if (empty($number_info)) {
            $result['ret_num'] = 123;
            $result['ret_msg'] = '号码直通车信息不存在';
            echo json_encode($result);
            die ();
        }
        if ($number_info->is_close) {
            $number_info->is_close = 0;
        } else {
            //关闭号码直通车之前，需要查看历史订单，必须全部完成
            $pminfo=PromotionManage::model()->find("store_id={$id} and member_id = {$user->id}");
            if($pminfo){
                $sql="select count(1) as num from store_order_info as a left join store_order_goods as b on a.order_id =b.order_id
                left join promotion as c on c.id=b.promotion_id where c.pm_id=".$pminfo['id']." and a.shipping_status=2";
                $command=$connection->createCommand($sql);
                $result0=$command->queryAll();
                if($result0[0]['num']){
                    $result['ret_num'] = 136;
                    $result['ret_msg'] = "必须等待全部订单确认收货完毕才能关闭！";
                    echo json_encode($result);
                    die();
                }
            }
            $number_info->is_close = 1;
        }

        if ($number_info->update()) {

            if (!$number_info->is_close) {
                //打开直通车，返回通讯录直通车信息
                $phone = array(
                    "id" => "",
                    "contact_info_id" => $number_info['id'] + 1000000,
                    "phone" => $number_info['phone'],
                    "is_benben" => 0,
                    "is_baixing" => 0,
                    "poster" => $number_info['poster'] ? URL . $number_info['poster'] : "",
                    "huanxin_username" => ""
                );
                $short_phone = array(
                    "id" => "",
                    "contact_info_id" => $number_info['id'] + 1000000,
                    "phone" => $number_info['telephone'],
                    "is_benben" => 0,
                    "is_baixing" => 0,
                    "poster" => $number_info['poster'] ? URL . $number_info['poster'] : "",
                    "huanxin_username" => ""
                );
                $collect = array(
                    "id" => $number_info['id'] + 1000000,
                    "group_id" => 10000,
                    "name" => $number_info['short_name'],
                    "short_name" => $number_info['name'],
                    "pinyin" => "",
                    "created_time" => "",
                    "is_benben" => 0,
                    "is_baixing" => 0,
                    "poster" => $number_info['poster'] ? URL . $number_info['poster'] : "",
                    "huanxin_username" => "",
                    "phone" => $number_info['telephone'] ? array($phone, $short_phone) : array($phone)
                );
                $result['collect'] = $collect;
            }
            $result['ret_num'] = 0;
            $result['is_close'] = $number_info->is_close;
            $result['ret_msg'] = "操作成功";
        } else {
            $result['ret_num'] = 5036;
            $result['ret_msg'] = "号码直通车打开或关闭失败";
        }
        echo json_encode($result);
    }

    /**
     * 计算距离
     * @param $latitude1 ,$longitude1 第一点的经纬度
     * @param $latitude2 ,$longitude2 第二点的经纬度
     * @return Array
    (
     * [kilometers] => kilometers
     * [meters] => meters
     * )
     */
    function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2)
    {
        $theta = $longitude1 - $longitude2;
        $miles = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
        $miles = acos($miles);
        $miles = rad2deg($miles);
        $miles = $miles * 60 * 1.1515;
        $feet = $miles * 5280;
        $yards = $feet / 3;
        $kilometers = $miles * 1.609344;
        $meters = $kilometers * 1000;
        return compact('miles', 'feet', 'yards', 'kilometers', 'meters');
    }

    /*
     * 商家认证信息填写
     * 涉及表store_auth
     */
    public function actionSetauth()
    {
        $this->check_key();
        $type = Frame::getIntFromRequest('type');//1为个人；2为商家
        $real_name = Frame::getStringFromRequest('real_name');
        $idcard = Frame::getStringFromRequest('idcard');
        $front = Frame::saveImage('front', 1);
        $back = Frame::saveImage('back', 1);
        $licence = Frame::saveImage('licence', 1);
        $company = Frame::getStringFromRequest('company');
        $user = $this->check_user();

        if (empty($real_name) || empty($idcard)) {
            $result['ret_num'] = 2005;
            $result['ret_msg'] = "缺少参数";
            echo json_encode($result);
            die();
        }

        $traininfo=NumberTrain::model()->find("member_id={$user['id']}");
        if(!$traininfo){
            $result['ret_num'] = 2115;
            $result['ret_msg'] = "未开通号码直通车";
            echo json_encode($result);
            die();
        }

        $storeinfo = StoreAuth::model()->find("member_id={$user['id']}");

        if ($storeinfo) {
            //复审
            if($storeinfo['status']==0){
                $result['ret_num'] = 1015;
                $result['ret_msg'] = "您的商城正在审核中，请勿重复提交";
                echo json_encode($result);
                die();
            }
            if($storeinfo['status']==2){
                $result['ret_num'] = 1115;
                $result['ret_msg'] = "您已审核通过，请勿重复提交";
                echo json_encode($result);
                die();
            }

            $storeinfo->real_name = $real_name;
            $storeinfo->id_card = $idcard;
            $storeinfo->member_id = $user['id'];
            $storeinfo->status = 0;
            $storeinfo->time = time();
            $storeinfo->type = $type;
            if($front) {
                unlink(ROOT.$storeinfo->poster_front);
                unlink(ROOT.$this->getThumb($storeinfo->poster_front));
                $storeinfo->poster_front = $front;
            }
            if($back) {
                unlink(ROOT.$storeinfo->poster_back);
                unlink(ROOT.$this->getThumb($storeinfo->poster_back));
                $storeinfo->poster_back = $back;
            }
            if ($type == 2) {
                if($licence) {
                    unlink(ROOT.$storeinfo->poster_licence);
                    unlink(ROOT.$this->getThumb($storeinfo->poster_licence));
                    $storeinfo->poster_licence = $licence;
                }
                if($company){
                    $storeinfo->company =$company;
                }
            }else{
                if($storeinfo->poster_licence){
                    unlink(ROOT.$storeinfo->poster_licence);
                    unlink(ROOT.$this->getThumb($storeinfo->poster_licence));
                    $storeinfo->poster_licence = "";
                }
            }
            $storeinfo->update();
        } else {
            //初审
            if(empty($front)||empty($back)){
                $result['ret_num'] = 2005;
                $result['ret_msg'] = "缺少参数";
                echo json_encode($result);
                die();
            }

            //商家认证，营业执照必须
            if ($type == 2) {
                if (empty($licence)||empty($company)) {
                    $result['ret_num'] = 2005;
                    $result['ret_msg'] = "缺少参数";
                    echo json_encode($result);
                    die();
                }
            }
            //首次提交认证
            $storeauth = new StoreAuth();
            $storeauth->real_name = $real_name;
            $storeauth->id_card = $idcard;
            $storeauth->member_id = $user['id'];
            $storeauth->first_uptime = time();
            $storeauth->status = 0;
            $storeauth->time = time();
            $storeauth->type = $type;
            $storeauth->poster_front = $front;
            $storeauth->poster_back = $back;
            if ($type == 2) {
                $storeauth->poster_licence = $licence;
                $storeauth->company =$company;
            }
            $storeauth->save();

            //首次会有7天自动开通时间
            $pminfo=PromotionManage::model()->find("member_id={$user['id']}");
            if(!$pminfo){
                $pcreate=new PromotionManage();
                $pcreate->store_id=$traininfo['id'];
                $pcreate->member_id=$user['id'];
                $pcreate->offline_restrict=5;
                $pcreate->online_restrict=5;
                $pcreate->is_close=1;
                $pcreate->time=time()+7*24*3600;
                $pcreate->vip_time=1459353600;//有效期至2016年3月31日00:00:00
                $pcreate->save();
            }
        }
        $result['ret_num'] = 0;
        $result['ret_msg'] = "操作成功";
        echo json_encode($result);
    }

    /*
     * 查看认证状态
     * 涉及表store_auth，store_auth_record
     */
    public function actionGetauth()
    {
        $this->check_key();
        $user = $this->check_user();
        $storeinfo = StoreAuth::model()->find("member_id={$user['id']}");
        if ($storeinfo) {
            $authinfo=StoreAuthRecord::model()->find("auth_id={$storeinfo['id']} order by time Desc");
            $result['info'] = array(
                "authid"=>$storeinfo['id'],
                "real_name"=>$storeinfo->real_name,
                "idcard"=>$storeinfo->id_card,
                "memberid" => $user['id'],
                "status"=>$storeinfo->status,
                "time"=>$storeinfo->time,
                "type"=>$storeinfo->type,
                "front"=>URL.$storeinfo->poster_front,
                "back"=>URL.$storeinfo->poster_back,
                "licence"=>$storeinfo->poster_licence ? URL.$storeinfo->poster_licence : "",
                "company"=>$storeinfo->company ? $storeinfo->company : "",
                "reason"=>$authinfo['reason'] ? $authinfo['reason'] : "",
            );
        }else{
            $result['ret_num'] = 1110;
            $result['ret_msg'] = "未申请认证";
            echo json_encode($result);
            die();
        }
        $result['ret_num'] = 0;
        $result['ret_msg'] = "操作成功";
        echo json_encode($result);
    }

    /*
     * 所有门店
     * 涉及number_train
     */
    public function actionAlldeparts(){
        $this->check_key();
        $user = $this->check_user();
        $trainid = Frame::getIntFromRequest('trainid');
        $longitude = Frame::getStringFromRequest('longitude');//经度
        $latitude = Frame::getStringFromRequest('latitude');//维度
        $traininfo=NumberTrain::model()->findAll("pid={$trainid} and is_close=0 and status=0");
        foreach($traininfo as $k=>$v){
            $distance=round(6378.138*2*asin(sqrt(pow(sin( ($v['lat']*pi()/180- $latitude *pi()/180)/2),2)+cos($v['lat']*pi()/180)*cos($latitude *pi()/180)* pow(sin( ($v['lng']*pi()/180-$longitude *pi()/180)/2),2)))*1000);
//            $districtinfo=$this->ProCity(array(0=>$v));
            $info[$k]['short_name']=$v['short_name'];
            if($distance/1000>=1){
                $info[$k]['distance']=($distance/1000)."km";
            }elseif($distance/100>=1){
                $info[$k]['distance']=$distance."m";
            }else{
                $info[$k]['distance']="<100m";
            }
            $info[$k]['area']=$v['address'];
            $info[$k]['phone']=$v['phone'];
            $info[$k]['telephone']=$v['telephone'];
        }

        $result['ret_num'] = 0;
        $result['ret_msg'] = "操作成功";
        $result['shops'] = $info;
        echo json_encode($result);
    }

    /*
     * 号码直通车转让申请
     * 涉及store_transfer，number_train，member表
     */
    public function actionApplyStoreTransfer(){
        $this->check_key();
        $user = $this->check_user();
        $benben_id = Frame::getIntFromRequest('benben_id');
        $memo = Frame::getStringFromRequest('memo');
        if(empty($benben_id)){
            $result['ret_num'] = 2015;
            $result['ret_msg'] = "缺少参数";
            echo json_encode($result);
            die();
        }
        $apply_store=NumberTrain::model()->find("member_id={$user['id']}");
        if(!$apply_store){
            $result['ret_num'] = 2115;
            $result['ret_msg'] = "未开通号码直通车不允许转让！";
            echo json_encode($result);
            die();
        }
        //受转让者被禁用
        $receive_info=Member::model()->find("benben_id={$benben_id} and id_enable=1 and store_disable=0");
        if(!$receive_info){
            $result['ret_num'] = 2215;
            $result['ret_msg'] = "受转让者处于被禁状态，需要解封后才能使用！";
            echo json_encode($result);
            die();
        }
        //受转让者需要关闭号码直通车
        $receive_store=NumberTrain::model()->find("member_id={$receive_info['id']}");
        if($receive_store && $receive_store['is_close']==0){
            $result['ret_num'] = 2315;
            $result['ret_msg'] = "受转让者号码直通车需要关闭才能转让！";
            echo json_encode($result);
            die();
        }
        //如果有申请中，则不能再次申请
        $transfer_num=StoreTransfer::model()->count("apply_id={$user['id']} and status=0");
        if($transfer_num){
            $result['ret_num'] = 2415;
            $result['ret_msg'] = "您的转让申请已经在处理中，请勿重复操作！";
            echo json_encode($result);
            die();
        }
        $trans_apply=new StoreTransfer();
        $trans_apply->apply_id=$user['id'];
        $trans_apply->receive_id=$receive_info['id'];
        $trans_apply->store_id=$apply_store['id'];
        $trans_apply->memo=$memo;
        $trans_apply->status=0;
        $trans_apply->created_time=time();
        if($trans_apply->save()){
            //发送环信请求消息
            $content="{$user->nick_name}把店铺{$apply_store['short_name']}转让给您";
            $gpic=$user['poster']?URL.$user['poster']:"";
            $arr=array(
                "t1"=>1,
                "t2"=>1,
                "t3"=>0,
                "t4"=>6,
                "apply_nickname"=>$user['nick_name'],
                "apply_poster"=>$gpic,
                "store_id"=>"hz".$user['benben_id'],
                "store_name"=>$apply_store['short_name'],
                "transfer_id"=>$trans_apply['id'],
                "content"=>$content,
                "vip_account"=>3400.00//号码直通车会员剩余金钱，目前暂无
            );
            $this->sendTextMessage($user['huanxin_username'],array(0=>$receive_info['huanxin_username']),$memo,$arr);
            $result['ret_num'] = 0;
            $result['ret_msg'] = "操作成功";
            echo json_encode($result);
        }else{
            $result['ret_num'] = 2000;
            $result['ret_msg'] = "保存失败，需要重新上传！";
            echo json_encode($result);
        }
    }

    /*
     * 拒绝转让
     * 涉及store_transfer
     */
    public function actionRefuseTransfer(){
        $this->check_key();
        $user = $this->check_user();
        $transfer_id = Frame::getIntFromRequest('transfer_id');
        if(empty($transfer_id)){
            $result['ret_num'] = 2015;
            $result['ret_msg'] = "缺少参数";
            echo json_encode($result);
            die();
        }
        $stinfo=StoreTransfer::model()->find("id={$transfer_id} and receive_id={$user['id']} and status=0");
        if(!$stinfo){
            $result['ret_num'] = 2115;
            $result['ret_msg'] = "申请已经处理！";
            echo json_encode($result);
            die();
        }
        $stinfo->status=2;
        $stinfo->deal_time=time();
        if($stinfo->update()){
            //发送环信消息
            $traininfo=NumberTrain::model()->find("id={$stinfo['store_id']}");
            $minfo=Member::model()->find("id={$stinfo['apply_id']}");
            $arr=array(
                "t1"=>1,
                "t2"=>1,
                "t3"=>2,
                "t4"=>6,
                "receive_nickname"=>$user['nick_name'],
                "receive_poster"=>$user['poster']?URL.$user['poster']:"",
                "store_id"=>"hz".$minfo['benben_id'],
                "store_name"=>$traininfo['short_name'],
                "transfer_id"=>$transfer_id
            );
            $content=$user['nick_name']."拒绝了您的转让号码直通车:".$traininfo['short_name'];
            $this->sendTextMessage($user['huanxin_username'],array(0=>$minfo['huanxin_username']),$content,$arr);

            $result['ret_num'] = 0;
            $result['ret_msg'] = "操作成功";
            echo json_encode($result);
        }else{
            $result['ret_num'] = 100;
            $result['ret_msg'] = "保存失败，请重新提交";
            echo json_encode($result);
        }
    }

    /*
     * 接受转让
     * 涉及store_transfer
     */
    public function actionAgreeTransfer(){
        $this->check_key();
        $user = $this->check_user();
        $transfer_id = Frame::getIntFromRequest('transfer_id');
        if(empty($transfer_id)){
            $result['ret_num'] = 2015;
            $result['ret_msg'] = "缺少参数";
            echo json_encode($result);
            die();
        }
        $stinfo=StoreTransfer::model()->find("id={$transfer_id} and receive_id={$user['id']} and status=0");
        if(!$stinfo){
            $result['ret_num'] = 2115;
            $result['ret_msg'] = "申请已经处理！";
            echo json_encode($result);
            die();
        }
        $receive_store=NumberTrain::model()->find("member_id={$user['id']}");
        if($receive_store) {
            if ($receive_store['status'] != 0) {
                $result['ret_num'] = 2215;
                $result['ret_msg'] = "您的商城处于禁用状态，请等待封禁时间到期！";
                echo json_encode($result);
                die();
            }
            if ($receive_store['is_close'] == 0) {
                $result['ret_num'] = 2315;
                $result['ret_msg'] = "关闭号码直通车之后才能接收转让！";
                echo json_encode($result);
                die();
            }
            //转让，将自己原来的号码直通车删除
            if($receive_store->poster){
                if(file_exists(ROOT.$receive_store->poster)){
                    unlink(ROOT.$receive_store->poster);
                }
            }
            $receive_store->delete();
        }
        //将转让者的号码直通车转至受让者
        $apply_store=NumberTrain::model()->find("member_id={$stinfo['apply_id']}");
        if($apply_store){
            $apply_store->member_id=$user['id'];
            $apply_store->update();
        }else{
            $result['ret_num'] = 2415;
            $result['ret_msg'] = "该号码直通车不存在！";
            echo json_encode($result);
            die();
        }
        //查询转让者商家认证
        $apply_auth=StoreAuth::model()->find("member_id={$stinfo['apply_id']}");
        //查询接受者商家认证
        $receive_auth=StoreAuth::model()->find("member_id={$stinfo['receive_id']}");
        if($receive_auth){
            $receive_auth->status=1;
            $receive_auth->type=$apply_auth['type'];
            $receive_auth->update();
        }
        //转让者商家认证删除操作
        if($apply_auth){
            if(file_exists(ROOT.$apply_auth['poster_front'])){
                unlink(ROOT.$apply_auth['poster_front']);
                if(file_exists(ROOT.$this->getThumb($apply_auth['poster_front']))){
                    unlink(ROOT.$this->getThumb($apply_auth['poster_front']));
                }
            }
            if(file_exists(ROOT.$apply_auth['poster_back'])){
                unlink(ROOT.$apply_auth['poster_back']);
                if(file_exists(ROOT.$this->getThumb($apply_auth['poster_back']))){
                    unlink(ROOT.$this->getThumb($apply_auth['poster_back']));
                }
            }
            if($apply_auth['type']==2) {
                if (file_exists(ROOT . $apply_auth['poster_licence'])) {
                    unlink(ROOT . $apply_auth['poster_licence']);
                    if (file_exists(ROOT . $this->getThumb($apply_auth['poster_licence']))) {
                        unlink(ROOT . $this->getThumb($apply_auth['poster_licence']));
                    }
                }
            }
            $apply_auth->delete();
        }
        //将自己团购或促销认证删除，使用转让者团购或促销认证
        $receive_pm=PromotionManage::model()->find("member_id={$user['id']}");
        if($receive_pm){
            $receive_pm->delete();
        }
        $receive_pm=PromotionManage::model()->find("member_id={$stinfo['apply_id']}");
        if($receive_pm){
            $receive_pm->member_id=$user['id'];
            $receive_pm->time=time()+7*86400;//重置试用时间
            $receive_pm->update();
        }
        $stinfo->status=1;
        if($stinfo->update()) {
            //发送环信消息
            $minfo=Member::model()->find("id={$stinfo['apply_id']}");
            $arr=array(
                "t1"=>1,
                "t2"=>1,
                "t3"=>1,
                "t4"=>6,
                "receive_nickname"=>$user['nick_name'],
                "receive_poster"=>$user['poster']?URL.$user['poster']:"",
                "store_id"=>"hz".$user['benben_id'],
                "store_name"=>$apply_store['short_name'],
                "transfer_id"=>$transfer_id
            );
            $content=$user['nick_name']."同意了您的转让号码直通车:".$apply_store['short_name'];
            $this->sendTextMessage($user['huanxin_username'],array(0=>$minfo['huanxin_username']),$content,$arr);

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
     * 清除转让某用户信息
     */
    public function actionDelAll(){
        $this->check_key();
        $user_id= Frame::getIntFromRequest('user_id');
        $stinfo=StoreTransfer::model()->findAll("apply_id={$user_id}");
        if($stinfo){
            foreach($stinfo as $k=>$v){
                if($v['status']==0){
                    StoreTransfer::model()->updateAll(array("status"=>2),"apply_id={$user_id} and id={$v['id']}");
                }
            }
        }
        echo "Complete!";
    }

}