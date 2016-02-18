<?php

class service
{
    public $type;
    public $member_id;
    public $is_vip;
    public $renewals;
    public $names;
    public $duration;
    public $overdue_date;
    public $content;
    public $content_title;
    public $info;
    public $vip_info;
    public $vip_pro;
    public $vip_price;
    public $duration_re;
    public $duration_price;
    public $re2;

    /**
     * #设置会员ID
     * @param int $id ID
     */
    public function set_member_id($id)
    {
        $this->member_id = $id;
    }

    /**
     * #设置会员是否续费
     * @param int $renewals 是否续费
     */
    public function set_renewals($renewals)
    {
        $this->renewals = $renewals;
    }

    /**
     * #设置套餐价格
     */
    public function set_type()
    {
        //价格
        if ($this->type == 0) {
            $this->duration_price = 10;
        } else if (in_array($this->type, array(1, 10))) {
            $this->duration_price = 20;
        } else if ($this->type == 12) {
            $this->duration_price = 1;
        } else if ($this->type == 13) {
            $this->duration_price = 2;
        } else {
            $this->duration_price = 0;
        }
        $this->duration_re = array(1 => "1个月", 3 => "3个月", 6 => "半年", 12 => "1年", 24 => "2年");
    }

    /**
     * #设置会员已购服务信息及套餐余额
     * @param int $type 服务类型
     */
    public function set_vip_info($type)
    {
        $vip = PromotionManage::model()->findByAttributes(array("member_id" => $this->member_id));
        $vip_info = PromotionManageAttach::model()->findByAttributes(array("member_id" => $this->member_id));
        $store_info = StoreRights::model()->findByAttributes(array("member_id" => $this->member_id));
        $this->is_vip = 0;
        $this->info = StorePriceAdmin::model()->findByAttributes(array("type" => $type));
        $this->vip_pro = $vip;
        if (!$vip && (in_array($type, array(12, 14)))) {
            $this->type = $type;
            if ($store_info) {
                $this->vip_info = $store_info;
//                $name_arr = explode(",", $this->info->names);
//                foreach ($name_arr as $va) {
//                    $va_arr = explode(":", $va);
//                        if (($this->vip_info->person_num != $va_arr[0])) {
//                            if ($this->vip_info->overdue_date < time()) {
//                                $this->vip_price = 0;
//                            } else {
//                                $this->vip_price = ceil(($va_arr[1] / 30) * (($this->vip_info->overdue_date - time()) / 3600 / 24));
//                            }
//                        } else {
//                            $this->vip_price = 0;
//                        }
////					if ($this->vip_info->person_num == $va_arr[0]) {
////						$this->vip_price = ceil(($va_arr[1] / 30) * (($this->vip_info->overdue_date - time()) / 3600 / 24));
////					}
//
//
//                }
            } else {
                $this->vip_price = 0;
            }

        }
        if ($vip) {
            $this->vip_info = ($type == 14) ? $store_info : $vip_info;
            $this->is_vip = 1;
            $this->type = $vip->store_type;
            if (in_array($type, array(1, 10, 11, 12, 13, 14))) {
                $this->type = $type;
            }
            $this->info = StorePriceAdmin::model()->findByAttributes(array("type" => $this->type));
            //剩余的钱
            if (1) {//$this->vip_info
                if (($this->vip_pro->vip_time < time())) {
                    $this->vip_price = 0;
                } else {
                    switch ($this->type) {

                        case 0:
                            $this->vip_price = 0;//ceil((10/30)*(($this->vip_info->overdue_date - time())/3600/24));
                            break;
                        case 1:
                            if ($this->vip_pro->store_type == 0) {
                                $this->vip_price = ceil((10 / 30) * (($this->vip_pro->vip_time - time()) / 3600 / 24));
                            } else {
                                $this->vip_price = 0;
                            }
                            //$this->vip_price = 0;//ceil((20/30)*(($this->vip_info->overdue_date - time())/3600/24));
                            break;
                        case 10:
                            $this->vip_price = 0;
                            if ($this->vip_pro->vip_time <= time()) {
                                $this->re2 = 1;
                            } else {
                                $this->re2 = round((($this->vip_pro->vip_time - time()) / 3600 / 24 / 30), 2);
                            }
                            break;
                        case 11:
                            /*if ($this->renewals) {
                                $this->vip_price = 0;
                            } else {
                                if ($this->vip_pro->store_type == 1) {
                                    $a = 20;
                                } else {
                                    $a = 10;
                                }
                                $this->vip_price = ceil(($a / 30) * (($this->vip_pro->vip_time - time()) / 3600 / 24));
                            }*/

                            break;
                            /*$connection = Yii::app()->db;
                            $sql = "select money_paid from store_order_info a left join store_order_goods b on a.order_id = b.order_id
                                    where a.member_id = {$this->member_id} and a.order_status = 1 and b.extension_code = {$this->type} order by a.pay_time desc limit 1";
						$command = $connection->createCommand($sql);
						$re = $command->queryAll();
						$name_arr = explode(",", $this->info->names);
						foreach ($name_arr as $va) {
							$va_arr = explode(":", $va);
							if ($this->vip_info->person_num == $va_arr[0]) {
								$this->vip_price = ceil(($re[0]['money_paid'] / 30) * (($this->vip_info->overdue_date - time()) / 3600 / 24));
							}
						}*/
                            break;
                        case 14:
//                            if ($this->vip_info) {
//                                $name_arr = explode(",", $this->info->names);
//                                foreach ($name_arr as $va) {
//                                    $va_arr = explode(":", $va);
//                                    if ($this->vip_info->person_num != $va_arr[0]) {
//                                        if ($this->vip_info->overdue_date < time()) {
//                                            $this->vip_price = 0;
//                                        } else {
//                                            $this->vip_price = ceil(($va_arr[1] / 30) * (($this->vip_info->overdue_date - time()) / 3600 / 24));
//                                        }
//                                    } else {
//                                        $this->vip_price = 0;
//                                    }
//
//                                }
//                            }
//                            else
//                            {
//                                $this->vip_price = 0;
//
//                            }
                            break;
                        default:
                            $this->vip_price = 0;
                    }
                }
            } else {
                $this->vip_price = 0;
            }
        }

    }

    /*public function set_info() {
        $this->info = StorePriceAdmin::model()->findByAttributes(array("type" => $this->type));
    }*/

    /**
     * #设置套餐名称
     */
    public function set_names()
    {
        if ($this->type == 11) {
            $name_arr = explode(",", $this->info->names);
            foreach ($name_arr as $va) {
                $va_arr = explode(":", $va);
                $this->names[$va]['num'] = $va_arr[0] . "人";
                $this->names[$va]['money'] = $va_arr[1];
                if ($va_arr[0] == 3000) {
                    $this->names[$va]['big_horn'] = 20;
                    $this->names[$va]['sale_consultant'] = 0;
                } else if ($va_arr[0] == 5000) {
                    $this->names[$va]['big_horn'] = 30;
                    $this->names[$va]['sale_consultant'] = 1;
                } else if ($va_arr[0] == 10000) {
                    $this->names[$va]['big_horn'] = 30;
                    $this->names[$va]['sale_consultant'] = 2;
                } else if ($va_arr[0] > 10000) {
                    $this->names[$va]['big_horn'] = 50;
                    $this->names[$va]['sale_consultant'] = 2;
                }
                $this->names[$va]['show_time'] = time();
                if ($va_arr[0] == $this->vip_info->person_num) {
                    $this->names[$va]['show1'] = 1;
                    $this->names[$va]['show_time'] = ($this->vip_info->overdue_date >= time()) ? $this->vip_info->overdue_date : time();
                } else {
                    $this->names[$va]['show1'] = 0;
                }

                if ($this->renewals == 1) {
                    if ($va_arr[0] == $this->vip_info->person_num) {
                        $this->names[$va]['show'] = 1;
                        $this->names[$va]['class'] = "shop-num-k";
                    } else {
                        $this->names[$va]['show'] = 1;
                    }
                } else {
                    $this->names[$va]['show'] = 1;
                }
            }
        } else if ($this->type == 14) {
            $name_arr = explode(",", $this->info->names);
            foreach ($name_arr as $va) {
                $va_arr = explode(":", $va);
                $this->names[$va]['num'] = "增加到" . $va_arr[0] . "个堂";
                $this->names[$va]['money'] = $va_arr[1];
                $this->names[$va]['show_time'] = time();
                if ($va_arr[0] == $this->vip_info->person_num) {
                    $this->names[$va]['show1'] = 1;
                    $this->names[$va]['show_time'] = ($this->vip_info->overdue_date >= time()) ? $this->vip_info->overdue_date : time();
                } else {
                    $this->names[$va]['show1'] = 0;
                }
                if ($this->renewals == 1) {
                    if ($va_arr[0] == $this->vip_info->person_num) {
                        $this->names[$va]['show'] = 1;
                        $this->names[$va]['class'] = "shop-num-k";
                    } else {
                        $this->names[$va]['show'] = 1;
                    }
                } else {
                    $this->names[$va]['show'] = 1;
                }
            }
        } else {
            $name_arr = explode(",", $this->info->names);
            foreach ($name_arr as $va) {
                $this->names[$va]['num'] = $va;
                $this->names[$va]['money'] = 1;
                $this->names[$va]['class'] = "shop-num-k";
                $this->names[$va]['show'] = 1;
                if (($this->type == 10)) {
                    $this->names[$va]['show_time'] = ($this->vip_pro->vip_time > time()) ? $this->vip_pro->vip_time : strtotime("+1 months", time());
                } else {
                    $this->names[$va]['show_time'] = ($this->vip_pro->vip_time >= time()) ? $this->vip_pro->vip_time : time();
                    if (($this->vip_pro->store_type == 0) && ($this->type == 1)) {
                        $this->names[$va]['show_time'] = time();
                    }
                }
            }
            //$this->names = explode(",",$this->info->names);
        }
    }

    /**
     * #设置开通时长或购买数量
     */
    public function set_duration()
    {
        $numbers_str = explode(",", $this->info->numbers);
        $duration = array();
        foreach ($numbers_str as $va) {
            if (in_array($this->type, array(12, 13))) {
                $duration[$va] = $va . "个";
            } elseif ($this->type == 10) {
                $duration[$va] = $va . "家";
            } else {
                $duration[$va] = $this->duration_re[$va];
            }
        }
        $this->duration = $duration;
    }

    /**
     * #计算会员套餐价格
     * @param string $service_name 套餐名称
     * @param  int $service_duration 开通或购买时长
     * @return array
     */
    public function pay_price($service_name, $service_duration)
    {
        $result = array();
        $service_info = $this->info; //StorePriceAdmin::model()->findByAttributes(array("type"=>$this->type));
        if (!$service_info) {
            return $result;
        }
        if (!$this->names) {
            $this->set_names();
            $flag = 1;
            foreach ($this->names as $va) {
                if ($va['num'] == $service_name) {
                    $flag = 0;
                    break;
                }
            }
            if ($flag) {
                return $result;
            }
        }
        $result['type'] = $service_info->type;
        if ($this->type == 14) {
            $result['count'] = $service_duration;
            preg_match('/\d+/', $service_name, $arr);
            $result['name'] = "奔犇-好友联盟-" . $this->duration_re[$service_duration];
            if ($this->vip_info->person_num == $arr[0]) {
                $this->vip_price = 0;
            } else {
                $name_arr = explode(",", $this->info->names);

                if ($this->vip_info->overdue_date < time()) {
                    $this->vip_price = 0;
                } else {
                    foreach ($name_arr as $va) {
                        $va_arr = explode(":", $va);
                        if ($va_arr[0] == $this->vip_info->person_num) {
                            $old_price = $va_arr[1];
                            break;
                        }
                    }
                    $tmp_price = floor(($this->vip_info->overdue_date - time()) / 3600 / 24);
                    $this->vip_price = ceil(($old_price / 30) * $tmp_price);
                }


            }
            foreach (explode(",", $service_info->names) as $va) {
                $va_arr = explode(":", $va);
                if ($arr[0] == $va_arr[0]) {
                    $result['price'] = $va_arr[1] * $service_duration - $this->vip_price;
                }
            }
            $result['promotion_id'] = $service_info->id;
        }

        switch ($service_info->type) {
            case 0:
                $result['name'] = "奔犇-" . $service_info->names . "-" . $this->duration_re[$service_duration];
                $result['count'] = $service_duration;
                $result['price'] = 10 * $service_duration;// - $this->vip_price;
                $result['promotion_id'] = $service_info->id;
                break;
            case 1:
                $result['name'] = "奔犇-" . $service_info->names . "-" . $this->duration_re[$service_duration];
                $result['count'] = $service_duration;
                $result['price'] = 20 * $service_duration - $this->vip_price;
                $result['promotion_id'] = $service_info->id;
                break;
            case 10:
                if ($this->vip_pro->vip_time <= time()) {
                    $re = 1;
                } else {
                    $re = round((($this->vip_pro->vip_time - time()) / 3600 / 24 / 30), 2);
                }
                $result['name'] = "奔犇-" . $service_info->names . "-" . $service_duration . "家";
                $result['count'] = $service_duration;
                $result['price'] = 20 * $service_duration * $re;
                $result['promotion_id'] = $service_info->id;
                break;
            case 11:
                $result['count'] = $service_duration;
                preg_match('/\d+/', $service_name, $arr);
                $result['name'] = "奔犇-会员号-" . $arr[0] . "人";
                if ($this->vip_info->person_num == $arr[0]) {
                    $this->vip_price = 0;
                } else {
                    $name_arr = explode(",", $this->info->names);

                    if ($this->vip_info->overdue_date < time()) {
                        $this->vip_price = 0;
                    } else {
                        if ($this->vip_pro->store_type == 1) {
                            $a = 20;
                        } else {
                            $a = 10;
                        }
                        $tmp_price = floor(($this->vip_pro->vip_time - time()) / 3600 / 24);
                        $this->vip_price = ceil(($a / 30) * $tmp_price);
                    }
                }
                foreach (explode(",", $service_info->names) as $va) {
                    $va_arr = explode(":", $va);
                    if ($arr[0] == $va_arr[0]) {
                        $result['price'] = ($va_arr[1] + $this->vip_info->store_num * 20) * $service_duration - $this->vip_price;
                    }
                }
                $result['promotion_id'] = $service_info->id;
                break;
            case 12:
                $result['name'] = "奔犇-" . $service_info->names . "-" . $service_duration . "个";
                $result['count'] = $service_duration;
                $result['price'] = 1 * $service_duration;
                $result['promotion_id'] = $service_info->id;
                break;
            case 13:
                $result['name'] = "奔犇-" . $service_info->names . "-" . $service_duration . "个";
                $result['count'] = $service_duration;
                $result['price'] = 2 * $service_duration;
                $result['promotion_id'] = $service_info->id;
                break;
            default:
                ;

        }
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
        $vip = PromotionManage::model()->findByAttributes(array("member_id" => $order_info->member_id));
        $vip_info = PromotionManageAttach::model()->findByAttributes(array("member_id" => $order_info->member_id));
        if ($vip && (!$vip_info) && (!in_array($goods_info->extension_code, array(14)))) {
            $vip_info = new PromotionManageAttach();
            $vip_info->member_id = $order_info->member_id;
            $vip_info->manage_id = $vip->id;
            $vip_info->store_id = $vip->store_id;
            //$vip_info->service_type = $goods_info->extension_code;
            $vip_info->add_date = time();
        }
        switch ($goods_info->extension_code) {
            case 0:
                //$re = $vip->vip_time?ceil(($vip->vip_time - time()) / 3600 / 24 / 30):0;
                if ($vip->vip_time >= time()) {
                    $re = $vip->vip_time;
                } else {
                    $re = time();
                }
                $vip_info->pro_num = 5;
                $vip_info->is_activity = 1;
                //$vip_info->small_horn_num += 30;
                $vip_info->is_pro = 1;
                $overdue_date = strtotime("+$goods_info->goods_number months", $re);//$re + ($goods_info->goods_number) * 30 * 24 * 60 * 60;
                //$vip_info->service_type = $goods_info->extension_code;
                $vip_info->update_date = time();
                $vip->vip_time = $overdue_date;
                self::horn_log($order_info->member_id, $vip->store_id, $goods_info->extension_code, 2, 1, 30);
                break;
            case 1:
                //$re = $vip->vip_time?ceil(($vip->vip_time - time()) / 3600 / 24 / 30):0;
                if (($vip->vip_time >= time()) && ($vip->store_type == 1)) {
                    $re = $vip->vip_time;
                } else {
                    $re = time();
                }
                $vip_info->group_num = 5;
                $vip_info->is_activity = 1;
                $vip_info->is_computer = 1;
                //$vip_info->small_horn_num += 30;
                $vip_info->is_group_buying = 1;
                $overdue_date = strtotime("+$goods_info->goods_number months", $re);//;$re + ($goods_info->goods_number) * 30 * 24 * 60 * 60;
                //$vip_info->service_type = $goods_info->extension_code;
                $vip_info->update_date = time();
                $vip->store_type = 1;
                $vip->vip_type = 1;
                $vip->vip_time = $overdue_date;
                self::horn_log($order_info->member_id, $vip->store_id, $goods_info->extension_code, 2, 1, 30);
                break;
            case 10:
                $vip_info->group_num = 5;
                $vip_info->is_activity = 1;
                $vip_info->is_computer = 1;
                $vip_info->is_computers = 1;
                //$vip_info->small_horn_num += 30;
                $vip_info->is_group_buying = 1;
                $vip_info->store_num += $goods_info->goods_number;
                $vip_info->update_date = time();
                if ($vip->store_type == 0) {
                    $connection = Yii::app()->db;
                    $sql = "delete from promotion where pm_id={$vip->id}";
                    $command = $connection->createCommand($sql);
                    $re0 = $command->execute();
                }
                if ($vip->vip_time <= time()) {
                    $vip->vip_time = strtotime("+1 months", time());
                }
                $vip->store_type = 1;
                $vip->vip_type = 1;
                self::horn_log($order_info->member_id, $vip->store_id, $goods_info->extension_code, 2, 1, 30);
                //$vip_info->overdue_date = time()+$goods_info->goods_number*30*24*60*60;
                break;
            case 11:
                $horn = $vip_info->big_horn_num;
                preg_match('/\d+/', $goods_info->attr_name, $arr);
                if ($arr[0] == 3000) {
                    $vip_info->big_horn_num += 20;
                    $vip_info->sale_consultant_num = 0;
                } else if ($arr[0] == 5000) {
                    $vip_info->big_horn_num += 30;
                    $vip_info->sale_consultant_num = 1;
                } else if ($arr[0] == 10000) {
                    $vip_info->big_horn_num += 30;
                    $vip_info->sale_consultant_num = 2;
                } else if ($arr[0] > 10000) {
                    $vip_info->big_horn_num += 50;
                    $vip_info->sale_consultant_num = 2;
                }
                $renewals = 0;
                if ($vip_info->person_num == $arr[0]) {
                    $renewals = 1;
                }
                if (($vip_info->overdue_date >= time()) && ($renewals == 1)) {
                    $re = $vip_info->overdue_date;
                } else {
                    $re = time();
                }
                $horn1 = $vip_info->big_horn_num - $horn;
                $vip_info->person_num = $arr[0];
                $vip_info->is_computer = 1;
                $vip_info->is_member_ico = 1;
                $vip_info->overdue_date = strtotime("+$goods_info->goods_number months", $re);//$re + $goods_info->goods_number * 30 * 24 * 60 * 60;
                $vip_info->service_type = $goods_info->extension_code;
                $vip_info->update_date = time();
                if ($vip->store_type == 0) {
                    $connection = Yii::app()->db;
                    $sql = "delete from promotion where pm_id={$vip->id}";
                    $command = $connection->createCommand($sql);
                    $re0 = $command->execute();
                }
                $vip->store_type = 1;
                $vip->vip_type = 1;
                $vip->vip_time = $vip_info->overdue_date;
                self::horn_log($order_info->member_id, $vip->store_id, $goods_info->extension_code, 2, 1, $horn1);
                break;
            case 12:
                if ($vip_info) {
                    $vip_info->small_horn_num = $vip_info->small_horn_num + $goods_info->goods_number;
                    $store_id = $vip->store_id;
                } else {
                    $store_id = 0;
                    $vip_info = StoreRights::model()->findByAttributes(array("member_id" => $order_info->member_id));
                    if (!$vip_info) {
                        $vip_info = new StoreRights();
                        $vip_info->member_id = $order_info->member_id;
                        $vip_info->store_id = $store_id;
                        //$vip_info->service_type = $goods_info->extension_code;
                        $vip_info->add_date = time();
                    }
                    $vip_info->small_horn_num = $vip_info->small_horn_num + $goods_info->goods_number;
                }

                self::horn_log($order_info->member_id, $store_id, $goods_info->extension_code, 2, 1, $goods_info->goods_number);
                break;
            case 13:
                $vip_info->big_horn_num = $vip_info->big_horn_num + $goods_info->goods_number;
                self::horn_log($order_info->member_id, $vip->store_id, $goods_info->goods_number, 1, 1, $goods_info->goods_number);
                break;
            case 14:
                $vip_info = StoreRights::model()->findByAttributes(array("member_id" => $order_info->member_id));
                if (!$vip_info) {
                    $vip_info = new StoreRights();
                    $vip_info->member_id = $order_info->member_id;
                    $vip_info->store_id = $vip->store_id;
                    $vip_info->service_type = $goods_info->extension_code;
                    $vip_info->add_date = time();
                }
                $renewals = 0;
                preg_match('/\d+/', $goods_info->attr_name, $arr);
                if ($vip_info->person_num == $arr[0]) {
                    $renewals = 1;
                }
                if (($vip_info->overdue_date >= time()) && ($renewals == 1)) {
                    $re = $vip_info->overdue_date;
                } else {
                    $re = time();
                }

                $vip_info->person_num = $arr[0];
                $vip_info->service_type = $goods_info->extension_code;
                $vip_info->overdue_date = strtotime("+$goods_info->goods_number months", $re);//$re + $goods_info->goods_number * 30 * 24 * 60 * 60;
                $vip_info->update_date = time();
                break;
        }
        $vip_info->save();
        if ($vip) $vip->update();
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

    /**
     * #设置套餐包含内容或服务对象
     */
    public function set_content()
    {
        $this->content = array("5个促销窗口", "发布活动现场", "尊享促销图标");
        if ($this->type == 0) {
            $this->content_title = "让全世界都知道你在做促销";
        }
        if ($this->type == 1) {
            $this->content = array("5个团购窗口", "发布活动现场", "商家电脑版客户端", "尊享团购图标");
            $this->content_title = "团购在手，生意暴走";
        }
        if ($this->type == 10) {
            $this->content = array("5个团购窗口", "发布活动现场", "展示所有门店", "商家电脑版客户端", "尊享团购图标", "商家电脑版总账号");
            $this->content_title = "连锁保障，任性扩张";
        }
        if ($this->type == 11) {
            $this->content = array("<span class='a_num'>3000人</span>容量", "<span class='a_num1'>20</span>个大喇叭", "<span class='a_num2'>0</span>个销售顾问席位", "商家电脑版客户端", "赠送团购业务", "尊享促销图标");
            $this->content_title = "老用户不会走，新用户不用愁";
        }
        if ($this->type == 12) {
            $this->content = array("奔犇用户", "好友联盟盟主", "促销商家", "团购商家");
            $this->content_title = "我要给小伙伴们喊话";
        }
        if ($this->type == 13) {
            $this->content = array("政企管理员", "会员号管理员");
            $this->content_title = "推广新产品，一分钟搞定";
        }
        if ($this->type == 14) {
            $this->content = array("意见领袖", "老师", "老板等");
            $this->content_title = "联盟扩张利器";
        }
    }

}
