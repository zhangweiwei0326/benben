<?php

class AddressController extends PublicController
{
    public $layout = false;

    /*
     * 收货地址详情
     * 涉及address表
     */
    public function actionAddressdetail()
    {
        $this->check_key();
        $user = $this->check_user();
        $sql = "select * from user_address where member_id=" . $user['id'] . " order by is_default Desc";
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $result0 = $command->queryAll();
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result ['address'] = $result0 ? $result0 : array();
        echo json_encode($result);
    }

    /*
     * 新增收货地址
     */
    public function actionAddaddress()
    {
        $this->check_key();
        $user = $this->check_user();
        $consignee = Frame::getStringFromRequest('consignee');
        $province = Frame::getIntFromRequest('province');
        $city = Frame::getIntFromRequest('city');
        $area = Frame::getIntFromRequest('district');
        $street = Frame::getIntFromRequest('street');
        $address = Frame::getStringFromRequest('address');
//        $zipcode = Frame::getStringFromRequest('zipcode');
        $mobile = Frame::getStringFromRequest('mobile');
        $is_default = Frame::getIntFromRequest('is_default');
        if (empty($consignee) || empty($province) || empty($address) || empty($mobile)) {
            $result ['ret_num'] = 2015;
            $result ['ret_msg'] = '参数不全';
            echo json_encode($result);
            die();
        }

        $uad = new UserAddress();
        $uad->consignee = $consignee;

        $dis = array("province" => $province, "city" => $city, "area" => $area, "street" => $street);
        $district = $this->ProCity(array(0 => $dis));
        $uad->address_name = $district[$province] . $district[$city] . $district[$area] . $district[$street] . $address;

        $uad->province = $province;
        $uad->city = $city;
        $uad->area = $area;
        $uad->street = $street;
        $uad->address = $address;
//        $uad->zipcode=$zipcode;
        $uad->mobile = $mobile;
        $uad->is_default = $is_default;
        $uad->member_id = $user['id'];
        if ($uad->save()) {
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            //因为前端偷懒，需要传全部预留字段。。。
            $result['address_id']=$uad->address_id;
            $result['address_name']=$uad->address_name;
            $result['member_id']=$uad->member_id;
            $result['consignee']=$uad->consignee;
            $result['email']=$uad->email;
            $result['country']=$uad->country;
            $result['province']=$uad->province;
            $result['city']=$uad->city;
            $result['area']=$uad->area;
            $result['street']=$uad->street;
            $result['address']=$uad->address;
            $result['zipcode']=$uad->zipcode;
            $result['tel']=$uad->tel;
            $result['mobile']=$uad->mobile;
            $result['best_time']=$uad->best_time;
            $result['is_default']=$uad->is_default;

            echo json_encode($result);
        }
    }

    /*
     * 删除收货地址
     */
    public function actionDeladdress()
    {
        $this->check_key();
        $user = $this->check_user();
        $address_id = Frame::getIntFromRequest('address_id');
        if (empty($address_id)) {
            $result ['ret_num'] = 2015;
            $result ['ret_msg'] = '参数不全';
            echo json_encode($result);
            die();
        }
        UserAddress::model()->deleteAll("address_id={$address_id} and member_id={$user['id']}");
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        echo json_encode($result);
    }

    /*
     * 编辑收货地址
     */
    public function actionEditaddress()
    {
        $this->check_key();
        $user = $this->check_user();
        $address_id = Frame::getIntFromRequest('address_id');

        $consignee = Frame::getStringFromRequest('consignee');
        $province = Frame::getStringFromRequest('province');
        $city = Frame::getStringFromRequest('city');
        $area = Frame::getStringFromRequest('district');
        $street = Frame::getStringFromRequest('street');
        $address = Frame::getStringFromRequest('address');
//        $zipcode = Frame::getStringFromRequest('zipcode');
        $mobile = Frame::getStringFromRequest('mobile');
        $is_default = Frame::getIntFromRequest('is_default');
        if (empty($address_id)) {
            $result ['ret_num'] = 2015;
            $result ['ret_msg'] = '参数不全';
            echo json_encode($result);
            die();
        }
        $addressinfo = UserAddress::model()->find("address_id={$address_id} and member_id={$user['id']}");
        if ($consignee) {
            $addressinfo->consignee = $consignee;
        }
        if ($province||$province=="0") {
            $addressinfo->province = $province;
        }
        if ($city||$city==="0") {
            $addressinfo->city = $city;
        }
        if ($area||$area==="0") {
            $addressinfo->area = $area;
        }
        if ($street||$street==="0") {
            $addressinfo->street = $street;
        }
        if ($address) {
            $addressinfo->address = $address;
        }
//        if($zipcode){
//            $addressinfo->zipcode=$zipcode;
//        }
        if ($mobile) {
            $addressinfo->mobile = $mobile;
        }
        if ($is_default) {
            UserAddress::model()->updateAll(array("is_default"=>0),"member_id={$user['id']}");
            $addressinfo->is_default = $is_default;
        }
        if ($province || $city || $area || $street || $address) {
            //地区修改必须全部一起传
            $tplprovince=$province?$province:$addressinfo['province'];
            $tplcity=$city?$city:$addressinfo['city'];
            $tplarea=$area?$area:$addressinfo['area'];
            $tplstreet=$street?$street:$addressinfo['street'];
            $dis = array(
                "province" =>$tplprovince ,
                "city" => $tplcity,
                "area" => $tplarea,
                "street" => $tplstreet
            );
            $district = $this->ProCity(array(0 => $dis));
            $addressinfo->address_name = $district[$tplprovince] . $district[$tplcity] . $district[$tplarea] . $district[$tplstreet] . ($address?$address:$addressinfo['address']);
        }
        $addressinfo->update();
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        echo json_encode($result);
    }

    //查看默认收货地址
    public function actionDefaultaddress()
    {
        $this->check_key();
        $user = $this->check_user();
        $address = UserAddress::model()->find("member_id={$user['id']} order by is_default Desc");
        $result['address_id'] = $address['address_id']?$address['address_id']:"";
        $result['address_name'] = $address['address_name']?$address['address_name']:"";
        $result['consignee'] = $address['consignee']?$address['consignee']:"";
        $result['province'] = $address['province']? $address['province']:"";
        $result['city'] = $address['city']?$address['city']:"";
        $result['area'] = $address['area']?$address['area']:"";
        $result['street'] = $address['street']?$address['street']:"";
        $result['address'] = $address['address']?$address['address']:"";
        $result['mobile'] = $address['mobile']?$address['mobile']:"";
        $result['address_id'] = $address['address_id']? $address['address_id']:"";
        $result['address_id'] = $address['address_id']?$address['address_id']:"";
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        echo json_encode($result);
    }
}