<?php

class Enterservice
{
    public $member_info;
    public $names;
    public $duration;
    public $overdue_date;
    public $content;
    public $info;
    public $info_time;
    public $vip_info;
    public $view;
    public $vip_price;
    public $duration_re;

    /**
     * #设置会员信息
     * @param apply_register $info
     */
    public function set_member_id($info)
    {
        $this->member_info = $info;
    }

    /**
     * #设置套餐信息
     */
    public function set_info()
    {
        if($this->member_info->apply_type == 3){
            $this->info = array("50000:200:0");
        }else{
            $this->info = array("2000:30:80","5000:60:200","30000:110:500");//"500:10:0",
        }
        $this->info_time = array("12","24","36","48","60");
    }


    /**
     * #设置套餐名称
     */
    public function set_names()
    {
        $this->vip_info = EnterpriseRole::model()->findByAttributes(array("enterprise_id" => $this->member_info->enterprise_id));
        $name_arr = $this->info;
        foreach ($name_arr as $va) {
            $va_arr = explode(":", $va);
            $this->names[$va]['num'] = $va_arr[0] . "人";
            $this->names[$va]['big_horn'] = $va_arr[1];
            $this->names[$va]['money'] = $va_arr[2];
            $this->names[$va]['show_time'] = time();
            if ($va_arr[0] == $this->vip_info->member_limit) {
                $this->names[$va]['show1'] = 1;
                $this->names[$va]['show_time'] = ($this->vip_info->overdue_date >= time()) ? $this->vip_info->overdue_date : time();
            } else {
                $this->names[$va]['show1'] = 0;
            }

        }
    }

    /**
     * #设置开通时长
     */
    public function set_duration()
    {
        $this->duration_re = array(12 => "1年", 24 => "2年", 36 => "3年", 48 => "4年", 60 => "5年");
        $numbers_str = $this->info_time;
        $duration = array();
        foreach ($numbers_str as $va) {
            $duration[$va] = $this->duration_re[$va];
        }
        $this->duration = $duration;
    }

    /**
     * #设置样式
     */
    public function set_view()
    {

        $name = "";
        foreach($this->names as $va){
            $a = "";
            if($va['show1'] == 1){
                $a = 'ex-add-clk';
            }
            $name .= '<li big_horn="'.$va['big_horn'].'" money="'.$va['money'].'" showtime="'.$va['show_time'].'" class="ex-add-li exone '.$a.'">'.$va['num'].'</li>';
        }
        $time = "";
        $i = 0;
        foreach($this->duration as $key=>$va){
            $a = "";
            if($i == 4){
                $a = 'margin-right: 0;';
            }
            $time .= '<li data="'.$key.'" class="ex-add-li extwo" style="padding: 0 20px;'.$a.'">'.$va.'</li>';
            $i++;
        }
        if($this->vip_info->overdue_date>=time()){
            $now0 = $this->vip_info->overdue_date;
            $now = date("Y-m-d",$this->vip_info->overdue_date);
        }else{
            $now0 = time();
            $now = date("Y-m-d",time());
        }

        $str = '<div class="export-add" style="display: none;" ></div>
        <div class="export-into-add" style="display: none;">
        	<div class="export-into-add-mian">
        		<dl class="export-into-add-dl">
        			<dt>提升上限</dt>
        			<dd></dd>
        		</dl>
        		<ul class="export-add-ul1">
        			<li class="ex-add-oneli">选择套餐：</li>
        			'.$name.'
        		</ul>
        		<ul class="export-add-ul1 ">
        			<li class="ex-add-oneli">开通时长：</li>
        			'.$time.'
        		</ul>
        		<ul class="export-add-ul1 ">
        			<li class="ex-add-oneli">有 效 期：</li>
        			<li data="'.$now0.'" class="ex-add-oneli shop-time">'.$now.'</li>
        		</ul>
        		<ul class="export-add-ul1 ">
        			<li class="ex-add-oneli">套餐内容：</li>
        			<li class="ex-add-li " style="background: #eaeaea;padding: 0 5px;"><span class="num0"></span>容量</li>
        			<li class="ex-add-li " style="background: #eaeaea;padding: 0 5px;">政企电脑版客户端</li>
        			<li class="ex-add-li " style="background: #eaeaea;padding: 0 5px;"><span class="num1"></span>个大喇叭</li>
        		</ul>
        		<dl class="export-into-add-dl2">
        		     <dt><span>应付金额：</span><font class="all_money">0元</font></dt>
        		     <dd><a href="javascript:;"><img id="sure" src="/themes/enterprise/images/com-la_7.jpg"/></a></dd>
        		</dl>
        	</div>
        </div>';
        $this->view = $str;
    }

    /**
     * #计算会员套餐价格
     * @param string $service_name 套餐名称
     * @param  int $service_duration 开通时长
     * @return array
     */
    public function pay_price($service_name, $service_duration)
    {
        $result = array();
        preg_match('/\d+/', $service_name, $arr);
        if ($this->names) {
            $flag = 1;
            $a_price = 0;
            foreach ($this->names as $va) {
                if($va['num'] == $this->vip_info->member_limit."人"){
                    $a_price = $va['money'];
                }
                if ($va['num'] == $service_name) {
                    $flag = 0;
                    if($a_price) break;
                }
            }
            if(!$this->duration_re[$service_duration]){
                $flag = 1;
            }

            if ($flag) {
                return $result;
            }
        }

        $result['count'] = $service_duration;
        $result['name'] = "奔犇-政企通讯录-" . $arr[0] . "人";
        $name_arr = $this->names;

        if ($this->vip_info->member_limit == $arr[0]) {
            $this->vip_price = 0;
        } else {
            if ($this->vip_info->overdue_date < time()) {
                $this->vip_price = 0;
            } else {
                $tmp_price = floor(($this->vip_info->overdue_date - time()) / 3600 / 24);
                $this->vip_price = ceil(($a_price / 365) * $tmp_price);
            }
        }

        foreach($name_arr as $va){
            if($service_name == $va['num']){
                $result['price'] = $va['money'] * ($service_duration/12) - $this->vip_price;
            }
        }

        $result['promotion_id'] = $this->member_info->enterprise_id;
        $result['gname'] = $service_name;
        $result['vip_price'] = $this->vip_price;
        return $result;
    }

    /**
     * #支付成功后的记录
     * @param int $order_sn 订单号
     */
    public static function PayRecode($order_sn = 0)
    {
        $order_info = StoreOrderInfo::model()->findByAttributes(array("order_sn" => $order_sn));
        $goods_info = StoreOrderGoods::model()->findByAttributes(array("order_id" => $order_info->order_id));
        $role = EnterpriseRole::model()->findByAttributes(array("enterprise_id" => $goods_info->promotion_id));
        $apply_info = ApplyRegister::model()->findByAttributes(array("enterprise_id" => $goods_info->promotion_id));
        if ($role) {
            //审核通过添加政企权限
            preg_match('/\d+/', $goods_info->attr_name, $arr);
            if ($role->member_limit == $arr[0]) {
                $renewals = 1;
            }
            if (($role->overdue_date >= time()) && ($renewals == 1)) {
                $re = $role->overdue_date;
            } else {
                $re = time();
            }
            if($arr[0] == 500){
                $broadcast_num = 10;
            }else if($arr[0] == 2000){
                $broadcast_num = 30;
            }else if($arr[0] == 5000){
                $broadcast_num = 60;
            }else if($arr[0] == 30000){
                $broadcast_num = 110;
            }else{
                $broadcast_num = 200;
            }
            if(($apply_info->apply_type==3)||($arr[0]>500)){
                if($apply_info->apply_type==3){
                    $role->member_limit=50000;
                }else{
                    $role->member_limit=$arr[0];
                }
                $role->broadcast_num=$broadcast_num;
                $role->broadcast_available=$broadcast_num;
                $role->group_level=4;
                $role->manage_num=5;
                $role->access_level_set=1;
            }else{
                $role->member_limit=$arr[0];
                $role->broadcast_num=$broadcast_num;
                $role->broadcast_available=$broadcast_num;
                $role->group_level=0;
                $role->manage_num=1;
                $role->access_level_set=0;
            }
            $role->created_time=time();
            $role->overdue_date = strtotime("+$goods_info->goods_number months", $re);
            $role->save();
        }
    }

    /**
     * #喇叭记录
     * @param int $member_id 会员号
     * @param int $store_id 商家号
     * @param int $buy_type 会员类型
     * @param int $horn_type 喇叭类型
     * @param int $action_type 状态
     * @param int $num 数量
     */
    public static function horn_log($member_id, $store_id, $buy_type, $horn_type, $action_type, $num)
    {
        $log = new HornLog();
        $log->member_id = $member_id;
        $log->store_id = $store_id;
        $log->add_date = time();
        $log->buy_type = $buy_type;
        $log->horn_type = $horn_type;
        $log->action_type = $action_type;
        $log->num = $num;
        $log->update_date = time();
        $log->save();
    }


}
