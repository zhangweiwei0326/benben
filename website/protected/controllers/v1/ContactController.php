<?php

class ContactController extends PublicController
{
    public $layout = false;
    /**
     * 修复百姓网数据
     */
    // public function actionBaixing(){
    // 	$this->check_key();
    // 	$short = Bxapply::model()->findAll("short_phone >0 and status = 3");
    // 	$connection = Yii::app()->db;
    // 	foreach ($short as $value){
    // 		$sql = "update group_contact_phone set is_baixing={$value->short_phone} where phone = {$value->phone}";
    // 		echo $sql;
    // 		echo '<br><br><br>';
    // 		$command = $connection->createCommand($sql);
    // 		$re1 = $command->execute();
    // 	}
    // }
    /**
     * 修复通讯录benben_id数据
     */
    public function actionCon()
    {
        $this->check_key();
        $connection = Yii::app()->db;
        $sql = "select a.id,a.benben_id,b.is_benben from group_contact_info a left join group_contact_phone b on a.id = b.contact_info_id 
				where b.is_benben > 0";
        $command = $connection->createCommand($sql);
        $re1 = $command->queryAll();
        foreach ($re1 as $va) {
            $info = GroupContactInfo::model()->find("id = {$va['id']}");
            if ($info->benben_id) {
                continue;
            }
            $sql1 = "update group_contact_info set benben_id = {$va['is_benben']} where id = {$va['id']}";
            $command = $connection->createCommand($sql1);
            echo $sql1 . "</br>";
            $re2 = $command->execute();
        }
    }

    public function actionMatch()
    {
        if ((Yii::app()->request->isPostRequest)) {
            // if(1){
            $PinYin = new tpinyin();
            $this->check_key();
            $group = Frame::getStringFromRequest('group');
            $phone = Frame::getStringFromRequest('phone');
            $phone = str_replace('\u00a0', "", $phone);
            $phone = str_replace(' ', "", $phone);
            $phone = trim($phone);
            //$group = "同学,朋友,学生,好友,公司,客户";
            //$phone = "15358105507::小明|18608976789#18019558873::小花|13592753478::小学|18752753478::小小|::小花|13978569048::花花";
            // $group = "";
            // $phone = "18966053050#56321::犇犇|15821439257::陈婧骅|18016077898::陈龙|18748880336::陈旭|18756987661::陈芳乐|13761086372::陈珑|13501782193::邓斌|18516291806::冯仁刚|18609190380::何欣|13892848043::韩晨龙|13918136633::胡佳贤|15856993363::季婷婷|13619191873::驾校|18992956908::驾校办公室|13916958641::李冰璠|13571412186::李朝月|15229227844::李冰瑶|13991593213::李翠显|13154096255::李秀霞|13571573444::李三月|18609190710::李玉霞|18292393510%2317792384998::刘俨|18790010191::吕金龙|13319194127::李蕊娥|18329808356::刘淑洁|13008505824::卢教练|15256978325::李朝阳|18019558873::李冰璠|18067650988::吕豪|18991922342::刘先生|13959608200::李教练|18966055308::李海涛|13120866567::孙尤杰|13956262255::吴导游|15802986821::王智章|18609191822::王石凹韵达|13095452590::吴可|055165530031::迅傲-合肥|13636351631#17756064871::姚艳辉|18609199199::杨军|15855189169::移动宽带安装|15358105543::叶翔宇|15156033796::移动宽带-和昌|13564552991::张浩|18018688469::张文延|15901945155::张和胜|18702939161::张毅兰|13120866867::孙尤杰";
            //$phone ="15555142507#18888888888::你好你噶|13333333333#13333333334+测试";
            $user = $this->check_user();
            //是否是百姓网用户
            $info = Bxapply::model()->count("phone = '{$user->phone}' and status = 3");

            if (!$group) {
                $arr_group = array("朋友", "家人", "同事", "未分组");
                $arr_group_all = explode(",", ',,,' . $phone);
            } else {
                $arr_group = explode(",", $group);
                $arr_group_all = explode(",", $phone);
            }
            $connection = Yii::app()->db;
            //查询犇犇号码
            $currentDetail0 = explode("|", $phone);
            //取出名字和号码存2个数组
            foreach ($currentDetail0 as $v1) {
                $person_info1 = explode("::", $v1);
                $allname[] = $person_info1[1];
                $allphone[] = $person_info1[0];
            }
            foreach ($allphone as $v2) {
                $aphone = explode("#", $v2);
                foreach ($aphone as $v3) {
                    $v3 = trim($v3);
                    if (strstr($v3, "+86")) {
                        $err_phone[] = "'" . trim(substr($v3, 3, strlen($v3))) . "'";
                    } else {
                        $err_phone[] = "'" . $v3 . "'";
                    }
                }
            }
            //根据号码获取信息
            $sql = "select benben_id,poster,huanxin_username,phone from member where phone in (" . implode(",", $err_phone) . ")";
            $command = $connection->createCommand($sql);
            $res0 = $command->queryAll();
            $benben_array = array();
            if ($res0) {
                foreach ($res0 as $va) {
                    $benben_array[$va['phone']] = $va;
                }
            }

            $group_id = array();
            $return_group_info = array();
            if (is_array($arr_group)) {

                $group_detail = array();
                $group_contact_info = array();
                $search_phone = array();
                foreach ($arr_group as $key => $val) {
                    $group_u = new GroupContact();
                    $group_u->group_name = $val;
                    $group_u->member_id = $user->id;
                    $group_u->created_time = time();
                    if ($group_u->save()) {    //保存分组信息
                        $group_id[] = $group_u->id;
                        $return_group_info[] = array('id' => $group_u->id, 'name' => $val);
                    }
                    if ($arr_group_all[$key]) {
                        $currentDetail = explode("|", $arr_group_all[$key]);
                        foreach ($currentDetail as $cKey => $cValue) {
                            $person_info = explode("::", $cValue);
                            $per_ph = explode("#", $person_info[0]);
                            if (!$person_info[1]) {
                                $person_info[1] = $per_ph[0];
                            }
                            $benben_id = 0;
                            foreach ($per_ph as $key1 => $v) {
                                $per_ph[$key1] = trim($v);
                                if (strstr($v, "+86")) {
                                    $per_ph[$key1] = substr($v, 3, strlen($v));
                                }
                                $per_ph[$key1] = trim($per_ph[$key1]);
                                if (!$benben_id) {
                                    $benben_id = $benben_array[$per_ph[$key1]]['benben_id'] ? $benben_array[$per_ph[$key1]]['benben_id'] : 0;
                                }
                            }
                            $group_contact_info[] = '(' . $group_u->id . ', "' . $person_info[1] . '", "' . $PinYin->str2sort($person_info[1]) . '", ' . time() . ', ' . $user->id . ', ' . $benben_id . ',"' . $PinYin->str2py($person_info[1]) . '")';
                            $group_detail[$group_u->id][] = $person_info;

                            $search_phone = array_merge($search_phone, $per_ph);
                        }
                    }
                }

                //将姓名插入到数据库
                if (count($group_contact_info)) {
                    $sqlgrp = "insert into group_contact_info (group_id,name,pinyin,created_time,member_id,benben_id,allpinyin) values " . implode($group_contact_info, ",");
                    $command = $connection->createCommand($sqlgrp);
                    $result = $command->execute();
                }


                //查找出新增加的姓名，将姓名与ID之前的关系通过数据存放
                $criteria = new CDbCriteria;
                $criteria->select = 'id,group_id,name';
                $criteria->addInCondition('group_id', $group_id);
                $find_result = GroupContactInfo::model()->findAll($criteria);
                $relation_name_id = array();
                $relation_name_all_info = array();
                if (count($find_result)) {
                    foreach ($find_result as $key => $value) {
                        $relation_name_id[$value->name] = $value->id;
                        $relation_name_all_info[] = array('name' => $value->name, 'id' => $value->id);
                    }
                }

                //去查找是否是犇犇用户,并将用户信息通过数组关系存放
                $criteria2 = new CDbCriteria;
                $criteria2->select = 'id,phone, nick_name, benben_id,poster,huanxin_username';
                $criteria2->addInCondition('phone', $search_phone);
                $criteria2->addCondition('id_enable=1');
                $find_result2 = Member::model()->findAll($criteria2);
                $benben_phone_id = array();
                $t = time();
                $ve = "";
                if (count($find_result2)) {
                    foreach ($find_result2 as $key => $value) {
                        $poster = $value->poster ? URL . $value->poster : "";
                        $value->huanxin_username = $value->huanxin_username ? $value->huanxin_username : "";
                        $benben_phone_id[$value->phone] = array('id' => $value->id, 'benben_id' => $value->benben_id, 'nick_name' => $value->nick_name, 'poster' => $poster, 'huanxin_username' => $value->huanxin_username);
                        $ve .= "({$user->id},{$value->id},1,{$t}),";
                    }
                }
                //添加好友记录(friend_relate)表
                /*	if($ve){
					$ve = trim($ve);
					$ve =trim($ve,',');
					$sqlf = "replace into friend_relate (friend_id1,friend_id2,status,created_time) values {$ve}";
					$command = $connection->createCommand($sqlf);
					$resul = $command->execute();
				}	*/
                //去查找是否是百姓网用户,并将用户信息通过数组关系存放
                $criteria3 = new CDbCriteria;
                $criteria3->select = 'id,phone,short_phone';
                $criteria3->addInCondition('phone', $search_phone);
                $criteria3->addCondition('status=3');
                $find_result3 = Bxapply::model()->findAll($criteria3);
                $baixing_phone_id = array();
                if (count($find_result3)) {
                    foreach ($find_result3 as $key => $value) {
                        $baixing_phone_id[$value->phone] = array('id' => $value->id, 'baixing' => $value->short_phone);
                    }
                }

                //拼号码入库数据以及接口返回数据
                $insert_contact_phone = array();
                $return_person_info = array();
                $auto_index = 0;
                foreach ($group_id as $key => $val) {
                    $currentDetail = explode("|", $arr_group_all[$key]);
                    if (!$currentDetail[0]) continue;
                    foreach ($currentDetail as $cKey => $cValue) {
                        $person_info = explode("::", $cValue);
                        $person_name = $person_info[1];
                        $person_phone = explode("#", $person_info[0]);
                        if (!$person_name) {
                            $person_name = trim($person_phone[0]);
                        }
                        $return_phone_info = array();
                        $benben = 0;
                        $baixing = 0;
                        $hxn = "";
                        $po = "";
                        if (isset($relation_name_id[$person_name])) {
                            $insert_info_id = $relation_name_id[$person_name];
                            if (isset($relation_name_all_info[$auto_index])) {
                                if ($relation_name_all_info[$auto_index]['name'] == $person_name) {
                                    $insert_info_id = $relation_name_all_info[$auto_index]['id'];
                                }
                            }
                            $auto_index++;

                            foreach ($person_phone as $each_phone) {
                                $each_phone = trim($each_phone);
                                if (strstr($each_phone, "+86")) {
                                    $each_phone = substr($each_phone, 3, strlen($each_phone));
                                }
                                $each_phone = trim($each_phone);
                                if (!$each_phone) continue;
                                if ($benben_array[$each_phone]) {
                                    if (!$benben) {
                                        $benben = $benben_array[$each_phone]['benben_id'];
                                    }
                                    if (!$hxn) {
                                        $hxn = $benben_array[$each_phone]['huanxin_username'];
                                    }
                                    if (!$po) {
                                        $po = $benben_array[$each_phone]['poster'] ? URL . $benben_array[$each_phone]['poster'] : "";
                                    }
                                }

                                $is_benben = 0;
                                $is_baixing = 0;
                                if (isset($baixing_phone_id[$each_phone])) {
                                    $is_baixing = $baixing_phone_id[$each_phone]['baixing'];
                                    $is_baixing = intval($is_baixing);
                                    if (!$baixing) {
                                        $baixing = $is_baixing;
                                    }
                                }
                                if (!$info) {
                                    $is_baixing = 0;
                                    $baixing = 0;
                                }
                                if (isset($benben_phone_id[$each_phone])) {
                                    $is_benben = $benben_phone_id[$each_phone]['benben_id'];
                                    $return_phone_info[] = array('phone' => $each_phone, 'is_benben' => $is_benben, 'is_baixing' => $is_baixing, 'poster' => $benben_phone_id[$each_phone]['poster'], 'nick_name' => $benben_phone_id[$each_phone]['nick_name']);
                                } else {
                                    $return_phone_info[] = array('phone' => $each_phone, 'is_benben' => $is_benben, 'is_baixing' => $is_baixing, 'poster' => '', 'nick_name' => '');
                                }

                                $insert_contact_phone[] = '(' . $insert_info_id . ', "' . $each_phone . '", ' . $is_benben . ', ' . $is_baixing . ')';
                            }
                            if (!$return_phone_info) {
                                $return_phone_info = array();
                            }
                        }
                        $py = $PinYin->str2sort($person_name);
                        $allpy = $PinYin->str2py($person_name);
                        $py = strtoupper($py);
                        $allpy = strtoupper($allpy);
//						$reg = '/[A-Z]{1}/s';
//						if(!(preg_match($reg, $py,$c) and $py==$c[0])){
//							$py = "#";
//						}
                        $return_person_info[] = array('id' => $insert_info_id, 'group_id' => $val, 'name' => $person_name, 'pinyin' => $py, 'is_benben' => $benben, 'is_baixing' => $baixing, 'huanxin_username' => $hxn, 'poster' => $po, 'phone' => $return_phone_info, 'allpinyin' => $allpy);
                    }
                }
                //将手机号码插入到数据库
                if (count($insert_contact_phone)) {
                    $sqlPhone = "insert into group_contact_phone (contact_info_id,phone,is_benben, is_baixing) values " . implode($insert_contact_phone, ",");
                    $connection = Yii::app()->db;
                    $command = $connection->createCommand($sqlPhone);
                    $result = $command->execute();
                }

                $return_group_info[] = array('id' => 10000, 'name' => '常用号码直通车');
                $return['ret_num'] = 0;
                $return['ret_msg'] = '操作成功';
                $return['group'] = $return_group_info;
                $return['contact'] = $return_person_info;
                echo json_encode($return);
                $m = new Memcached();
                $m->addServer('localhost', 11211);
                $snapshot = $m->get("addrsversion:" . $user['id']) ? $m->get("addrsversion:" . $user['id']) : 1;
                $m->set("addrsversion:" . $user['id'], ($snapshot + 1));
            }
        }
    }

    /**
     * 写日志，写在根目录下的log文件夹内
     *
     * @param 日志内容 $msg
     * @param 文件夹名 $folder
     * @param 文件名 （不带后缀） $file
     */
    public function GameLog($logmsg, $folderName = 'log', $fileName = 'log')
    {
        $basdPath = dirname(dirname(__FILE__));
        $arr_base = explode('/', $basdPath);
        //检查文件夹
        $arr = explode('/', $folderName);
        $arr = array_merge($arr_base, $arr);
        $dt = date("Y-m");
        array_push($arr, $dt);
        $path = implode('/', $arr);
        $this->_mkdirs($path);

        //文件名-按日期
        $fileName = $fileName . '_' . date("Ymd") . ".txt";
        array_push($arr, $fileName);
        $path = implode('/', $arr);

        //写日志
        $fp = fopen($path, "a+");
        flock($fp, LOCK_EX);
        fwrite($fp, "日期：" . date("Y-m-d h:i:s") . "\n" . $logmsg . "\n\n");
        flock($fp, LOCK_UN);
        fclose($fp);
    }

    /**
     * 按指定路径生成目录
     *
     * @paramstring $path路径
     */
    public function _mkdirs($path)
    {
        $adir = explode('/', $path);
        $dirlist = '';
        $rootdir = array_shift($adir);
        if (($rootdir != '.' || $rootdir != '..') && !file_exists($rootdir)) {
            @mkdir($rootdir);
        }
        foreach ($adir as $key => $val) {
            if ($val != '.' && $val != '..') {
                $dirlist .= "/" . $val;
                $dirpath = $rootdir . $dirlist;
                if (!file_exists($dirpath)) {
                    @mkdir($dirpath);
                    @chmod($dirpath, 0777);
                }
            }
        }
    }

    /**
     * 记录通讯录添加联系人
     */
    public function actionNewmatchlog()
    {
        $this->check_key();
        $phone = Frame::getStringFromRequest('phone');
        $user = $this->check_user();
        //记录同步历史
        $log = $user->id . '  == ' . $phone;
        $this->GameLog($log);
    }

    /**
     * 通讯录添加联系人
     */
    public function actionNewmatch()
    {
        $this->check_key();
        $phone = Frame::getStringFromRequest('phone');
        $phone = str_replace('\u00a0', "", $phone);
        $phone = str_replace(' ', "", $phone);
        $phone = trim($phone);
        //$phone = "15656976325#13335976090::小明26|18029950453::小花26|15305896768::小米26|18867946568#13978689048::花花26";
        // $phone = "18966053050#56321::犇犇|13816958641::李冰璠|13571212186::李朝月|15229228844::李冰瑶|15256978325::李朝阳|18919558873::李冰璠|18067650988::吕豪|18991922342::刘先生|13959608200::李教练|18966055308::李海涛|13908066567#19012345678::孙尤杰0|13636351631#17756064871::姚艳辉|15855189169::移动宽带安装|13920866867::孙尤杰0";
        $PinYin = new tpinyin();
        $user = $this->check_user();
        //是否是百姓网用户
        $info = Bxapply::model()->count("phone = '{$user->phone}' and status = 3");

        //记录同步历史	
        // $log = $user->id.'  == '.$phone;
        // $this->GameLog($log);

        //查出未分组ID
        $connection = Yii::app()->db;
        $own = GroupContact::model()->find("group_name = '未分组' and member_id = {$user->id}");
        $beginTime = time();
        if (!$own) {
            //添加分组
            $arr_group = array("朋友", "家人", "同事", "未分组");
            $t1 = time();
            $in = array();
            foreach ($arr_group as $va) {
                $in[] = "('{$va}',{$t1},{$user->id})";
            }

            $sql = "insert into group_contact (group_name,created_time,member_id) values " . implode(",", $in);
            $command = $connection->createCommand($sql);
            $re1 = $command->execute();
            $own = GroupContact::model()->find("group_name = '未分组' and member_id = {$user->id}");
        }

        $group_contact_info = array();
        $search_phone = array();
        $currentDetail = explode("|", $phone);
        //取出名字和号码存2个数组
        foreach ($currentDetail as $v1) {
            $person_info1 = explode("::", $v1);
            $allname[] = $person_info1[1];
            $allphone[] = $person_info1[0];
        }
        foreach ($allphone as $v2) {
            $aphone = explode("#", $v2);
            foreach ($aphone as $v3) {
                $v3 = trim($v3);
                if (strstr($v3, "+86")) {
                    $err_phone[] = "'" . trim(substr($v3, 3, strlen($v3))) . "'";
                } else {
                    $err_phone[] = "'" . $v3 . "'";
                }
            }
        }
        //根据号码获取信息
        $sql = "select benben_id,poster,huanxin_username,phone,nick_name from member where phone in (" . implode(",", $err_phone) . ")";
        $command = $connection->createCommand($sql);
        $res0 = $command->queryAll();
        $benben_array = array();
        if ($res0) {
            foreach ($res0 as $va) {
                $benben_array[$va['phone']] = $va;
            }
        }

        //已经在现有通讯录的电话
        $friend_phone = array();
        if (count($err_phone) > 0) {
            $sql = "select phone,name from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id where b.member_id = {$user->id} and a.phone in (" . implode(",", $err_phone) . ")";
            $command = $connection->createCommand($sql);
            $re2 = $command->queryAll();
            foreach ($re2 as $va) {
                $friend_phone[] = $va['phone'];
                $friend_phone_relation[$va['phone']] = $va['name'];
            }
        }

        foreach ($currentDetail as $cKey => $cValue) {
            $person_info = explode("::", $cValue);
            $per_ph = explode("#", $person_info[0]);
            if (!$person_info[1]) {
                $person_info[1] = $per_ph[0];
            }
            $benben_id = 0;
            foreach ($per_ph as $key1 => $v) {
                $per_ph[$key1] = trim($v);
                if (strstr($v, "+86")) {
                    $per_ph[$key1] = substr($v, 3, strlen($v));
                }
                $per_ph[$key1] = trim($per_ph[$key1]);
                if (!$benben_id) {
                    $benben_id = $benben_array[$per_ph[$key1]]['benben_id'] ? $benben_array[$per_ph[$key1]]['benben_id'] : 0;
                }
            }
            $flag = 0;//电话在好友通讯录且姓名一致
            foreach ($per_ph as $val) {
                if (in_array($val, $friend_phone) && $person_info[1] == $friend_phone_relation[$val]) {
                    $flag = 0;
                    break;
                } else {
                    $flag = 1;
                }
                // if(!in_array($val, $friend_phone)){
                //        $flag = 1;
                // }
            }
            if ($flag) {
                $group_contact_info[] = '(' . $own->id . ', "' . $person_info[1] . '", "' . $PinYin->str2sort($person_info[1]) . '", ' . time() . ', ' . $user->id . ', ' . $benben_id . ',"' . $PinYin->str2py($person_info[1]) . '")';
                $search_phone = array_merge($search_phone, $per_ph);
            }
            //$group_detail[$group_u->id][] = $person_info;								
        }
// 			var_dump($group_contact_info);echo "---------------";exit;
// 			var_dump($search_phone);exit();
        if (!$group_contact_info) {
            $return['ret_num'] = 1802;
            $return['ret_msg'] = '联系人已在通讯录';
            echo json_encode($return);
            exit();
        }
        //将姓名插入到数据库
        $sqlgrp = "insert into group_contact_info (group_id,name,pinyin,created_time,member_id,benben_id,allpinyin) values " . implode($group_contact_info, ",");
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sqlgrp);
        $result = $command->execute();

        //查找出新增加的姓名，将姓名与ID之前的关系通过数据存放
        $criteria = new CDbCriteria;
        $criteria->select = 'id,group_id,name';
        $criteria->addCondition('group_id=' . $own->id);
        $criteria->addCondition('created_time>=' . $beginTime);
        $find_result = GroupContactInfo::model()->findAll($criteria);
        $relation_name_id = array();
        if (count($find_result)) {
            foreach ($find_result as $key => $value) {
                $relation_name_id[$value->name] = $value->id;
                $relation_name_all_info[] = array('name' => $value->name, 'id' => $value->id);
            }
        }
        //去查找是否是犇犇用户,并将用户信息通过数组关系存放
        $criteria2 = new CDbCriteria;
        $criteria2->select = 'id,phone, nick_name, benben_id,poster,huanxin_username';
        $criteria2->addInCondition('phone', $search_phone);
        $find_result2 = Member::model()->findAll($criteria2);
        $benben_phone_id = array();
        $t = time();
        $ve = "";
        if (count($find_result2)) {
            $tpl_memberid = array();//加入通讯录中存在犇犇号的电话的id
            foreach ($find_result2 as $key => $value) {
                $tpl_memberid[] = $value['id'];
                $poster = $value['poster'] ? URL . $value['poster'] : "";
                $value->huanxin_username = $value->huanxin_username ? $value->huanxin_username : "";
                $benben_phone_id[$value->phone] = array('id' => $value->id, 'benben_id' => $value->benben_id, 'nick_name' => $value->nick_name, 'poster' => $poster, 'huanxin_username' => $value->huanxin_username);
                $ve .= "({$user->id},{$value->id},1,{$t}),";
            }
        }


        //查找是否开通号码直通车
        if (!empty($tpl_memberid)) {
            $tpl_memberid1 = implode(",", $tpl_memberid);
            $sql_t = "select a.poster, a.short_name, a.id, a.tag, b.phone from number_train as a LEFT join member as b on b.id=a.member_id where a.member_id in ({$tpl_memberid1}) and a.status=0 and a.is_close=0";
            $command = $connection->createCommand($sql_t);
            $result_t = $command->queryAll();
            foreach ($result_t as $kt => $vt) {
                $train_phone_id[$vt['phone']] = array("train_id" => $vt['id'], "pic" => $vt['poster'], "tag" => $vt['tag'], "short_name" => $vt['short_name']);
            }
        }

        //查找是否开通好友联盟
        if (!empty($tpl_memberid)) {
            $tpl_memberid2 = implode(",", $tpl_memberid);
            $sql_l = "select a.poster, a.name, a.city, a.area, b.phone,a.id from friend_league as a left join member as b on a.member_id=b.id where a.member_id in ({$tpl_memberid2}) and a.status=0 and a.is_delete=0";
            $command = $connection->createCommand($sql_l);
            $result_l = $command->queryAll();
            foreach ($result_l as $kl => $vl) {
                $bids = $vl['city'] . "," . $vl['area'];
                $data_l = $this->getProCity($bids);
                $district = "";
                foreach ($data_l as $vv) {
                    $district .= $vv['area_name'] . " ";
                }
                $district = trim($district);
                $leg_phone_id[$vl['phone']] = array("leg_poster" => $vl['poster'], "leg_name" => $vl['name'], "district" => $district, "leg_id" => $vl['id']);
            }
        }
        //添加好友记录(friend_relate)表
        /*	if($ve){
				$ve = trim($ve);
				$ve =trim($ve,',');
				$sqlf = "replace into friend_relate (friend_id1,friend_id2,status,created_time) values {$ve}";
				$command = $connection->createCommand($sqlf);
				$resul = $command->execute();
			} */
        //去查找是否是百姓网用户,并将用户信息通过数组关系存放
        $criteria3 = new CDbCriteria;
        $criteria3->select = 'id,phone,short_phone';
        $criteria3->addInCondition('phone', $search_phone);
        $criteria3->addCondition('status=3');
        $find_result3 = Bxapply::model()->findAll($criteria3);
        $baixing_phone_id = array();
        if (count($find_result3)) {
            foreach ($find_result3 as $key => $value) {
                $baixing_phone_id[$value->phone] = array('id' => $value->id, 'baixing' => $value->short_phone);
            }
        }
        //拼号码入库数据以及接口返回数据
        $insert_contact_phone = array();
        $return_person_info = array();

        $currentDetail = explode("|", $phone);
//			if(!$currentDetail[0]) continue;//!!!Test
        $auto_index = 0;
        foreach ($currentDetail as $cKey => $cValue) {
            $person_info = explode("::", $cValue);
            $person_name = $person_info[1];
            $person_phone = explode("#", $person_info[0]);
            if (!$person_name) {
                $person_name = trim($person_phone[0]);
            }
            $return_phone_info = array();
            $benben = 0;
            $baixing = 0;
            $hxn = "";
            $po = "";
            $nc = "";
            if (isset($relation_name_id[$person_name])) {
                $insert_info_id = $relation_name_id[$person_name];
                if (isset($relation_name_all_info[$auto_index])) {
                    if ($relation_name_all_info[$auto_index]['name'] == $person_name) {
                        $insert_info_id = $relation_name_all_info[$auto_index]['id'];
                    }
                }
                $auto_index++;
                foreach ($person_phone as $each_phone) {
                    $each_phone = trim($each_phone);
                    if (strstr($each_phone, "+86")) {
                        $each_phone = substr($each_phone, 3, strlen($each_phone));
                    }
                    $each_phone = trim($each_phone);
                    if (!$each_phone || (in_array($each_phone, $friend_phone) && $friend_phone_relation[$each_phone] == $person_name)) continue;
                    if ($benben_array[$each_phone]) {
                        if (!$benben) {
                            $benben = $benben_array[$each_phone]['benben_id'];
                        }
                        if (!$hxn) {
                            $hxn = $benben_array[$each_phone]['huanxin_username'];
                        }
                        if (!$po) {
                            $po = $benben_array[$each_phone]['poster'] ? URL . $benben_array[$each_phone]['poster'] : "";
                        }
                        if (!$nc) {
                            $nc = $benben_array[$each_phone]['nick_name'] ? $benben_array[$each_phone]['nick_name'] : "";
                        }
                    }

                    if (isset($train_phone_id[$each_phone])) {
                        $train_id = $train_phone_id[$each_phone]['train_id'];
                        $pic = URL . $train_phone_id[$each_phone]['pic'];
                        $tag = $train_phone_id[$each_phone]['tag'];
                        $short_name = $train_phone_id[$each_phone]['short_name'];
                    } else {
                        $train_id = 0;
                        $pic = "";
                        $tag = "";
                        $short_name = "";
                    }

                    if (isset($leg_phone_id[$each_phone])) {
                        $leg_poster = URL . $leg_phone_id[$each_phone]['leg_poster'];
                        $leg_name = $leg_phone_id[$each_phone]['leg_name'];
                        $leg_id = $leg_phone_id[$each_phone]['leg_id'];
                        $leg_district = $leg_phone_id[$each_phone]['district'];
                    } else {
                        $leg_poster = "";
                        $leg_name = "";
                        $leg_district = "";
                        $leg_id = "";
                    }

                    $is_benben = $benben_phone_id[$each_phone]['benben_id'] ? $benben_phone_id[$each_phone]['benben_id'] : 0;
                    $is_baixing = 0;
                    if (isset($baixing_phone_id[$each_phone])) {
                        $is_baixing = $baixing_phone_id[$each_phone]['baixing'];
                        $is_baixing = intval($is_baixing);
                        if (!$baixing) {
                            $baixing = $is_baixing;
                        }
                    }
                    if (!$info) {
                        $is_baixing = 0;
                        $baixing = 0;
                    }

                    //将手机号码插入到数据库
                    $savegp = new GroupContactPhone;
                    $savegp->contact_info_id = $insert_info_id;
                    $savegp->phone = $each_phone;
                    $savegp->is_benben = $is_benben;
                    $savegp->is_baixing = $is_baixing;
                    $savegp->save();
                    if ($savegp) {
                        $gpid = $savegp->attributes['id'];
                    } else {
                        $gpid = 0;
                    }
//						$insert_contact_phone = '('.$insert_info_id.', "'.$each_phone.'", '.$is_benben.', '.$is_baixing.')';
//						$sqlPhone = "insert into group_contact_phone (contact_info_id,phone,is_benben, is_baixing) values ({$insert_contact_phone})";
//						$command = $connection->createCommand($sqlPhone);
//						$result = $command->execute();

                    if (isset($benben_phone_id[$each_phone])) {
                        $is_benben = $benben_phone_id[$each_phone]['benben_id'];
                        $return_phone_info[] = array(
                            "id" => $gpid,
                            "is_active" => 0,
                            "contact_info_id" => $insert_info_id,
                            "legid" => $leg_id,
                            "leg_poster" => $leg_poster,
                            "leg_name" => $leg_name,
                            "leg_district" => $leg_district,
                            "train_id" => $train_id,
                            "pic" => $pic,
                            "tag" => $tag,
                            "short_name" => $short_name,
                            'phone' => $each_phone,
                            'is_benben' => $is_benben,
                            'is_baixing' => $is_baixing,
                            'poster' => $benben_phone_id[$each_phone]['poster'],
                            'nick_name' => $benben_phone_id[$each_phone]['nick_name'],
                            'huanxin_username' => $hxn
                        );
                    } else {
                        $return_phone_info[] = array(
                            "id" => $gpid,
                            "is_active" => 0,
                            "contact_info_id" => $insert_info_id,
                            "legid" => $leg_id,
                            "leg_poster" => $leg_poster,
                            "leg_name" => $leg_name,
                            "leg_district" => $leg_district,
                            "train_id" => $train_id,
                            "pic" => $pic,
                            "tag" => $tag,
                            "short_name" => $short_name,
                            'phone' => $each_phone,
                            'is_benben' => $is_benben,
                            'is_baixing' => $is_baixing,
                            'poster' => '',
                            'nick_name' => $person_name,
                            'huanxin_username' => $hxn
                        );
                    }


                }
                if (!$return_phone_info) {
                    $return_phone_info = array();
                }

                $py = $PinYin->str2sort($person_name);
                $allpy = $PinYin->str2py($person_name);
                $py = strtoupper($py);
                $allpy = strtoupper($allpy);
//				$reg = '/[A-Z]/s';
//				if(!(preg_match($reg, $py,$c) and $py==$c[0])){
//					$py = "#";
//				}

                $return_person_info[] = array(
                    'created_time' => time(),
                    'nick_name' => $nc,
                    'id' => $insert_info_id,
                    'group_id' => $own->id,
                    'name' => $person_name,
                    'pinyin' => $py,
                    'allpinyin' => $allpy,
                    'is_benben' => $benben,
                    'is_baixing' => $baixing,
                    'huanxin_username' => $hxn,
                    'poster' => $po,
                    'phone' => $return_phone_info
                );

            }
        }
        //将手机号码插入到数据库
//			$sqlPhone = "insert into group_contact_phone (contact_info_id,phone,is_benben, is_baixing) values ".implode($insert_contact_phone, ",");
//			$connection = Yii::app()->db;
//			$command = $connection->createCommand($sqlPhone);
//			$result = $command->execute();

        $return['ret_num'] = 0;
        $return['ret_msg'] = '操作成功';
        $return['contact'] = $return_person_info;
        echo json_encode($return);
        $m = new Memcached();
        $m->addServer('localhost', 11211);
        $snapshot = $m->get("addrsversion:" . $user['id']) ? $m->get("addrsversion:" . $user['id']) : 1;
        $m->set("addrsversion:" . $user['id'], ($snapshot + 1));

    }

    /*
     * 更新联系人电话
     * 可能涉及电话会含奔犇用户，百姓网用户，会追加显示号码直通车、好友联盟
     */
    public function actionUpdatematch()
    {
        $this->check_key();
        $user = $this->check_user();
        $phone = Frame::getStringFromRequest('phone');
        $phone = str_replace('\u00a0', "", $phone);
        $phone = str_replace(' ', "", $phone);
        $phone = trim($phone);

        //$phone = "15656976325#13335976090::小明26|18029950453::小花26|15305896768::小米26|18867946568#13978689048::花花26";
        // $phone = "18966053050#56321::犇犇|13816958641::李冰璠|13571212186::李朝月|15229228844::李冰瑶|15256978325::李朝阳|18919558873::李冰璠”

        $connection = Yii::app()->db;
        $sql1 = "select a.id,a.name,b.phone from group_contact_info as a left join group_contact_phone as b on a.id=b.contact_info_id where a.member_id={$user['id']}";
        $command = $connection->createCommand($sql1);
        $result1 = $command->queryAll();
        //获取该名字下的phone数组,用contact_info_id索引
        $infoid = array();
        foreach ($result1 as $kr => $vr) {
            $infoid[$vr['id']][] = $vr['phone'];
        }

        $currentDetail = explode("|", $phone);
        //取出名字和号码存2个数组
        $allphone = array();
        $allname = array();
        $insert_arr = array();
        foreach ($currentDetail as $v1) {
            $person_info1 = explode("::", $v1);
            $allname[] = $person_info1[1];
            $allphone[] = $person_info1[0];
        }
        foreach ($allphone as $k2 => $v2) {
            //单号码必是更新，因为只传相同号码的数组
//            if(strstr($v2,"#")) {
            $aphone = explode("#", $v2);
            foreach ($aphone as $v3) {
                $tpl_v3 = trim($v3);
                if (strstr($tpl_v3, "+86")) {
                    $tpl_v3 = trim(substr($tpl_v3, 3, strlen($v3)));
                }
                foreach ($result1 as $kk => $vv) {
                    $flag = 0;
                    //名字相同
                    if ($allname[$k2] == $vv['name']) {
                        //有号码相同的
                        foreach ($aphone as $ka => $va) {
                            $tpl_va = trim($va);
                            if (strstr($tpl_va, "+86")) {
                                $tpl_va = trim(substr($tpl_va, 3, strlen($va)));
                            }
                            if (in_array($tpl_va, $infoid[$vv['id']])) {
                                $flag = 1;
                            }
                        }
                        if ($flag == 1) {
                            //号码不同
                            if (!in_array($tpl_v3, $infoid[$vv['id']])) {
                                $is_benben = 0;
                                $is_baixing = 0;
                                $meminfo = Member::model()->find("phone={$tpl_v3} and id_enable=1");
                                if ($meminfo) {
                                    $is_benben = $meminfo['benben_id'] ? $meminfo['benben_id'] : 0;
                                    $huanxin = $meminfo['huanxin_username'] ? $meminfo['huanxin_username'] : "";
                                    $poster = $meminfo['poster'] ? URL . $meminfo['poster'] : "";
                                    //自己百姓号存在才能查询其他人
                                    $ownbaixing = Bxapply::model()->find("member_id={$user['id']} and status=3");
                                    if ($ownbaixing) {
                                        $baixinginfo = Bxapply::model()->find("member_id={$meminfo['id']} and status=3");
                                        if ($baixinginfo) {
                                            $is_baixing = $baixinginfo['short_phone'];
                                        } else {
                                            $is_baixing = 0;
                                        }
                                    }
                                }
                                $tpl_in = "(" . $vv['id'] . ",'" . $tpl_v3 . "'," . $is_benben . "," . $is_baixing . ",0)";
                                if (!in_array($tpl_in, $insert_arr)) {
                                    $insert_arr[] = "(" . $vv['id'] . ",'" . $tpl_v3 . "'," . $is_benben . "," . $is_baixing . ",0)";
                                    $insert[] = array(
                                        "contact_info_id" => $vv['id'],
                                        "phone" => $tpl_v3,
                                        "is_benben" => $is_benben,
                                        "is_baixing" => $is_baixing,
                                        "nick_name" => $vv['name'],
                                        "huanxin_username" => $huanxin ? $huanxin : "",
                                        "poster" => $poster ? $poster : "",
                                        "is_active" => 0
                                    );
                                }
                            }
                        }

                    }
                }
            }
//            }
        }
        if ($insert_arr) {
            $sql_in = "insert into group_contact_phone (contact_info_id,phone,is_benben,is_baixing,is_active) VALUES " . implode(",", $insert_arr);
            $command = $connection->createCommand($sql_in);
            $result_in = $command->execute();

            foreach ($insert as $ki => $vi) {
                $gci = GroupContactInfo::model()->count("id={$vi['contact_info_id']} and benben_id=0");
                if ($gci) {
                    GroupContactInfo::model()->updateAll(array("benben_id" => $vi['is_benben']), "id={$vi['contact_info_id']}");
                    $tpl_benben = $vi['is_benben'];
                    $tpl_baixing = $vi['is_baixing'];
                    $tpl_hx = $vi['huanxin_username'];
                    $tpl_poster = $vi['poster'];
                }
                $phoneinfo = GroupContactPhone::model()->find("contact_info_id={$vi['contact_info_id']} and phone={$vi['phone']}");
                $insert[$ki]['id'] = $phoneinfo['id'];
                $insert[$ki] = array_merge($insert[$ki], $this->getowninfo($vi['is_benben'], ""));
                $contact[] = array(
                    "phone" => $insert[$ki],
                    "is_benben" => $tpl_benben ? $tpl_benben : "",
                    "is_baixing" => $tpl_baixing ? $tpl_baixing : "",
                    "huanxin_username" => $tpl_hx ? $tpl_hx : "",
                    "poster" => $tpl_poster ? $tpl_poster : ""
                );
            }

            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            $result['contact'] = $contact;
            echo json_encode($result);
            $m = new Memcached();
            $m->addServer('localhost', 11211);
            $snapshot = $m->get("addrsversion:" . $user['id']);
            $m->set("addrsversion:" . $user['id'], ($snapshot + 1));
        } else {
            $result ['ret_num'] = 1000;
            $result ['ret_msg'] = '通讯录不存在该用户';
            echo json_encode($result);
            die();
        }


    }

    /**
     * 返回通讯录（舍弃）
     */
    public function actionContactinfo()
    {
        $this->check_key();
        $user = $this->check_user();
        //是否是百姓网用户
        $info = Bxapply::model()->count("phone = '{$user->phone}' and status = 3");

        $connection = Yii::app()->db;
        $sql1 = "select id,sort,group_name name from group_contact where member_id = {$user->id} order by sort asc";//取通讯录分组表中的分组名和编号
        $command = $connection->createCommand($sql1);
        $result1 = $command->queryAll();
        $gid = "";//所有群组编号集合
        if ($result1) {
            foreach ($result1 as $key => $value) {
                $gid .= $value['id'] . ",";
                if (!$value['sort']) {
                    $result1[$key]['sort'] = $key + 1;
                    GroupContact::model()->updateAll(array("sort" => ($key + 1)), "id={$value['id']}");
                }
            }
            $gid = trim($gid);
            $gid = trim($gid, ',');
            $result1[] = array("id" => 10000, "name" => "常用号码直通车");
        }
        $result_group = $result1;//分组编号=》组名，额外10000=》常用号码直通车

        $info_name = array();
        $result2 = array();
        if ($gid) {
            $sql2 = "select id,group_id,name,pinyin,benben_id is_benben,created_time,allpinyin from group_contact_info where group_id in ({$gid})";//取通讯录各个群组中的成员信息
            $command = $connection->createCommand($sql2);
            $result2 = $command->queryAll();
            $benben_id = array();//所有群组内成员默认联系的犇犇号集合（有犇犇号和没有犇犇号是0）
            $aid = "";//所有群组内成员编号集合，contact_info=>id
            foreach ($result2 as $val) {
                $aid .= $val['id'] . ",";
                if ($val['is_benben'] > 0) {//注册犇犇号>0
                    $benben_id[] = $val['is_benben'];
                }
                $info_name[$val['id']] = $val['name'];
            }
            $aid = trim($aid);
            $aid = trim($aid, ',');
        }

        //根据benben_id获取注册用户信息
        $benben_info = array();
        if ($benben_id) {
// 				$sql6 = "select a.id,a.benben_id,a.phone,a.poster,a.huanxin_username,b.short_phone from member a left join bxapply b on a.phone = b.phone  
// 						where a.benben_id in (".implode(",", $benben_id).") and b.status = 3";
            $sql6 = "select a.id,a.benben_id,a.phone,a.poster,a.huanxin_username,a.nick_name from member as a
						where a.benben_id in (" . implode(",", $benben_id) . ")";
            $command = $connection->createCommand($sql6);
            $result6 = $command->queryAll();
            foreach ($result6 as $v6) {
                $benben_info[$v6['benben_id']] = $v6;
                //$benben_info[$v6['benben_id']]['short_phone'] = $phoneArr[$v6['phone']];
            }
        }
        $contact_phone = array();//某用户所有组内成员用户信息
        $contact_phonea = array();//某组成员的默认联系犇犇号信息
        if ($aid) {
            //查询某用户所有组内成员用户信息，同时获取是犇犇注册用户的用户信息
            $sql3 = "select a.id,a.contact_info_id,a.phone,a.is_benben,a.is_baixing ,a.is_active,b.poster,b.huanxin_username,b.nick_name,c.is_close as num_close,c.status as num_status,
						c.id train_id,c.poster pic,c.short_name,c.tag,d.poster as leg_poster,d.name as leg_name,d.area,d.city,d.status,d.is_delete,d.id as legid,d.type from group_contact_phone a
						left join member b on a.is_benben=b.benben_id left join number_train as c on b.id = c.member_id  left join friend_league as d on b.id=d.member_id
						where a.contact_info_id in ({$aid}) GROUP BY a.id order by d.is_delete ASC, d.status asc";
            $command = $connection->createCommand($sql3);
            $result3 = $command->queryAll();
            foreach ($result3 as $key => $va) {
                if (!$info) {
                    $result3[$key]['is_baixing'] = 0;
                }
            }
            foreach ($result3 as $va) {
                $tmp_key = $va['contact_info_id'];
                $contact_phone[$tmp_key][] = $va;
            }
//				var_dump($contact_phone);exit;
            //$baixing = array();
            //$baixing = 0;
            foreach ($contact_phone as $k2 => $valu) {
                $baixing = 0;
                $tpl_id = array();//为了删除重复选出的phone信息
                foreach ($valu as $k => $ue) {
                    $bid = $benben_info[$ue['is_benben']];
                    $contact_phone[$k2][$k]['poster'] = $ue['poster'] ? URL . $ue['poster'] : "";
                    $contact_phone[$k2][$k]['huanxin_username'] = $ue['huanxin_username'] ? $ue['huanxin_username'] : "";
                    $contact_phone[$k2][$k]['nick_name'] = $ue['nick_name'] ? $ue['nick_name'] : $info_name[$k2];

                    //商家简情
                    if ($ue['num_close'] == 0 && $ue['num_status'] == 0) {
                        $contact_phone[$k2][$k]['train_id'] = $contact_phone[$k2][$k]['train_id'] ? $contact_phone[$k2][$k]['train_id'] : 0;
                        $contact_phone[$k2][$k]['pic'] = $contact_phone[$k2][$k]['pic'] ? URL . $contact_phone[$k2][$k]['pic'] : "";
                        $contact_phone[$k2][$k]['short_name'] = $contact_phone[$k2][$k]['short_name'] ? $contact_phone[$k2][$k]['short_name'] : "";
                        $contact_phone[$k2][$k]['tag'] = $contact_phone[$k2][$k]['tag'] ? $contact_phone[$k2][$k]['tag'] : "";
                    } else {
                        $contact_phone[$k2][$k]['train_id'] = "";
                        $contact_phone[$k2][$k]['pic'] = "";
                        $contact_phone[$k2][$k]['short_name'] = "";
                        $contact_phone[$k2][$k]['tag'] = "";
                    }
                    unset($contact_phone[$k2][$k]['num_close']);
                    unset($contact_phone[$k2][$k]['num_status']);

                    //未删除，未被禁用的，切为盟主的好友联盟显示
                    $contact_phone[$k2][$k]['type'] = $ue['type'] == 1 ? '工作联盟' : '英雄联盟';
                    if (!$contact_phone[$k2][$k]['status'] && !$contact_phone[$k2][$k]['is_delete'] && $contact_phone[$k2][$k]['leg_name']) {
                        $district = "";
                        $contact_phone[$k2][$k]['leg_poster'] = $contact_phone[$k2][$k]['leg_poster'] ? URL . $contact_phone[$k2][$k]['leg_poster'] : "";
                        $contact_phone[$k2][$k]['leg_name'] = $contact_phone[$k2][$k]['leg_name'] ? $contact_phone[$k2][$k]['leg_name'] : "";
                        $area_sql = "SELECT area_name FROM area WHERE bid IN ({$contact_phone[$k2][$k]['city']},{$contact_phone[$k2][$k]['area']}) ORDER BY bid ASC";
                        $command = $connection->createCommand($area_sql);
                        $area_result = $command->queryAll();
                        foreach ($area_result as $ka => $va) {
                            $district .= $va['area_name'] . " ";
                        }
                        $contact_phone[$k2][$k]['leg_district'] = trim($district);
                    } else if (($contact_phone[$k2][$k]['status'] || $contact_phone[$k2][$k]['is_delete']) && in_array($contact_phone[$k2][$k]['id'], $tpl_id)) {
                        unset($contact_phone[$k2][$k]);
                    } else {
                        $contact_phone[$k2][$k]['leg_poster'] = "";
                        $contact_phone[$k2][$k]['legid'] = "";
                        $contact_phone[$k2][$k]['leg_name'] = "";
                        $contact_phone[$k2][$k]['leg_district'] = "";
                    }
                    if (($contact_phone[$k2][$k]['status'] || $contact_phone[$k2][$k]['is_delete'])) {
                        $contact_phone[$k2][$k]['leg_poster'] = "";
                        $contact_phone[$k2][$k]['legid'] = "";
                        $contact_phone[$k2][$k]['leg_name'] = "";
                        $contact_phone[$k2][$k]['leg_district'] = "";
                    }
                    unset($contact_phone[$k2][$k]['city']);
                    unset($contact_phone[$k2][$k]['area']);
                    unset($contact_phone[$k2][$k]['is_delete']);
                    unset($contact_phone[$k2][$k]['status']);


                    //百姓号获取
                    if ((!$baixing) && $contact_phone[$k2][$k]['is_baixing']) {
                        $baixing = $contact_phone[$k2][$k]['is_baixing'];
                    }

                    //是否有默认号码设置,获取默认设置存入$contact_phonea
                    if ($contact_phone[$k2][$k]['is_active']) {
                        if ($contact_phone[$k2][$k]['is_baixing']) {
                            $contact_phonea[$k2]['is_baixing'] = $contact_phone[$k2][$k]['is_baixing'];
                        }
                        $contact_phonea[$k2]['nick_name'] = $contact_phone[$k2][$k]['nick_name'];
                        $contact_phonea[$k2]['is_benben'] = $contact_phone[$k2][$k]['is_benben'];
                        $contact_phonea[$k2]['huanxin_username'] = $contact_phone[$k2][$k]['huanxin_username'];
                        $contact_phonea[$k2]['poster'] = $ue['poster'];
                        $contact_phonea[$k2]['active'] = 1;
                    }

                    $tpl_id[] = $contact_phone[$k2][$k]['id'];//为了删除重复选出的phone信息
                }
                $contact_phone[$k2] = array_values($contact_phone[$k2]);//重排数组
                $contact_baixing[$k2]['is_baixing'] = $baixing;
            }

        }

        foreach ($result2 as $key => $v) {
            //取拼音首字母
//			$py = substr($v['pinyin'],0,1);
//			$py = strtoupper($py);
//			$reg = '/[A-Z]{1}/s';
//			if(!(preg_match($reg, $py,$c) and $py==$c[0])){
//				$py = "#";
//			}
//			$result2[$key]['pinyin'] = $py;
            //读取全拼
            $result2[$key]['pinyin'] = $v['pinyin'] ? $v['pinyin'] : $v['name'];
            $result2[$key]['allpinyin'] = $v['allpinyin'] ? $v['allpinyin'] : $v['name'];
            //判断是否设置默认号，优先读取默认号
            $result2[$key]['is_benben'] = $contact_phonea[$v['id']]['active'] ? $contact_phonea[$v['id']]['is_benben'] : $result2[$key]['is_benben'];
            /*昵称显示：是否有默认，默认显示默认的，没有则通讯录中没有犇犇号的显示手机备注(contact_info=>name)，有则显示犇犇号的昵称(nick_name)*/
            $result2[$key]['nick_name'] = $contact_phonea[$v['id']]['active'] ? $contact_phonea[$v['id']]['nick_name'] : ((!$result2[$key]['is_benben']) ? $result2[$key]['name'] : ($benben_info[$result2[$key]['is_benben']]['nick_name'] ? $benben_info[$result2[$key]['is_benben']]['nick_name'] : ""));

            //$result2[$key]['is_benben'] = $contact_phonea[$v['id']]['is_benben'] ? $contact_phonea[$v['id']]['is_benben'] : "0";
            $result2[$key]['is_baixing'] = $contact_phonea[$v['id']]['active'] ? $contact_phonea[$v['id']]['is_baixing'] : $contact_baixing[$v['id']]['is_baixing'] ? $contact_baixing[$v['id']]['is_baixing'] : "0";
            $result2[$key]['huanxin_username'] = $contact_phonea[$v['id']]['active'] ? $contact_phonea[$v['id']]['huanxin_username'] : $benben_info[$v['is_benben']]['huanxin_username'] ? $benben_info[$v['is_benben']]['huanxin_username'] : "";
            $result2[$key]['poster'] = $contact_phonea[$v['id']]['active'] ? URL . $contact_phonea[$v['id']]['poster'] : ($benben_info[$v['is_benben']]['poster'] ? URL . $benben_info[$v['is_benben']]['poster'] : "");
//			$result2[$key]['huanxin_username'] = $benben_info[$v['is_benben']]['huanxin_username'] ? $benben_info[$v['is_benben']]['huanxin_username'] : "";
            $result2[$key]['phone'] = $contact_phone[$v['id']] ? $contact_phone[$v['id']] : array();
        }
        //获取收藏的号码直通车
        $sql = "select a.id,a.name,a.short_name,a.poster,a.phone,a.telephone from number_train a inner join number_train_collect b
			on a.id = b.number_train_id where a.is_close = 0 and b.member_id = {$user->id} and a.status = 0 order by a.istop desc,a.created_time desc,a.id desc ";
        $command = $connection->createCommand($sql);
        $result0 = $command->queryAll();
        foreach ($result0 as $v1) {
            $phone = array(
                "id" => "",
                "contact_info_id" => $v1['id'] + 1000000,
                "phone" => $v1['phone'],
                "is_benben" => 0,
                "is_baixing" => 0,
                "poster" => $v1['poster'] ? URL . $v1['poster'] : "",
                "huanxin_username" => ""
            );
            $short_phone = array(
                "id" => "",
                "contact_info_id" => $v1['id'] + 1000000,
                "phone" => $v1['telephone'],
                "is_benben" => 0,
                "is_baixing" => 0,
                "poster" => $v1['poster'] ? URL . $v1['poster'] : "",
                "huanxin_username" => ""
            );
            $collect = array(
                "id" => $v1['id'] + 1000000,
                "group_id" => 10000,
                "name" => $v1['short_name'] ? $v1['short_name'] : ($v1['name'] ? $v1['name'] : ""),
                "short_name" => $v1['name'] ? $v1['name'] : "",
                "pinyin" => "",
                "created_time" => "",
                "is_benben" => 0,
                "is_baixing" => 0,
                "poster" => $v1['poster'] ? URL . $v1['poster'] : "",
                "huanxin_username" => "",
                "phone" => $v1['telephone'] ? array($phone, $short_phone) : array($phone)
            );
            $result2[] = $collect;
        }

        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result['group'] = $result_group;
        $result['contact'] = $result2;
        echo json_encode($result);
    }

    /*
     * 通讯录查询
     */
    public function actionAddressBook()
    {
        $this->check_key();
        $user = $this->check_user();
        $snapshot = Frame::getStringFromRequest('snapshot');//快照版本
        if (empty($snapshot)) {
            $result ['ret_num'] = 200;
            $result ['ret_msg'] = '用户不安全访问！';
            echo json_encode($result);
            die();
        }
        //申明缓存
        $m = new Memcached();
        $m->addServer('localhost', 11211);

        //用户百姓网状态变化情况
        $ownbx = Bxapply::model()->count("phone = '{$user->phone}' and status = 3");//自己是否是百姓用户
        if ($m->get("bxapply:" . $user['id']) != $ownbx) {
            $m->set("bxapply:" . $user['id'], $ownbx);
            $old = $m->get("addrsverion:" . $user['id']);
            $m->set("addrsverion:" . $user['id'], ($old + 1));
        }
        /*
         * 判断快照版本是否一致，一致则返回成功
         */
        if ($this->hasSnapShot("addrsversion:" . $user['id'], $snapshot, $m)) {
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            $result ['delete'] = (object)array();
            $result ['add'] = (object)array();
            $result ['snapshot'] = $snapshot;
            echo json_encode($result);
            die();
        }
        /*
         * 不一致情况处理
         * 如果没有缓存，查数据库，计入缓存，输出版本号
         * 如果有缓存
         * 如果初始快照版本则输出缓存，输出版本号
         * 否则，取数据库数据，对比缓存数据，给予更新数据，输出版本号
         */
        $contactsCache = Yii::app()->filecache->get("contacts:" . $user['id']);
        $connection = Yii::app()->db;
        if (!$contactsCache || $snapshot == 1) {
            //取通讯录分组表中的分组名和编号
            $dbContacts = $this->searchAddressBook($user, $ownbx, $connection);
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            $result['delete'] = (object)array();
            $result['add'] = $dbContacts;
            if ($m->get("addrsversion:" . $user['id'])) {
                $result['snapshot'] = $m->get("addrsversion:" . $user['id']);
            } else {
                $m->set("addrsversion:" . $user['id'], 2);
                $result['snapshot'] = 2;
            }
            $jsonResult = json_encode($result);
            Yii::app()->filecache->set("contacts:" . $user['id'], json_encode($dbContacts));
            echo($jsonResult);
            die();
        } else {
            $resultCache = json_decode($contactsCache, true);
            $resultDb = $this->searchAddressBook($user, $ownbx, $connection);
            //查找group不一致的地方
            $differentGroup = $this->dirtyCheck($resultCache['group'], $resultDb['group']);

            //查找contact不一致的地方
            $differentContact = $this->dirtyCheck($resultCache['contact'], $resultDb['contact']);

            //无差别
            if (!$differentGroup['add'] && !$differentGroup['delete'] && !$differentContact['add'] && !$differentContact['delete']) {
                $result ['ret_num'] = 0;
                $result ['ret_msg'] = '操作成功';
                $result ['delete'] = (object)array();
                $result ['add'] = (object)array();
                $result ['snapshot'] = $snapshot;
                echo json_encode($result);
                die();
            }

            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            $result ['delete'] = array(
                "group" => $differentGroup['delete'] ? $differentGroup['delete'] : array(),
                "contact" => $differentContact['delete'] ? $differentContact['delete'] : array()
            );
            $result ['add'] = array(
                "group" => $differentGroup['add'] ? $differentGroup['add'] : array(),
                "contact" => $differentContact['add'] ? $differentContact['add'] : array()
            );
            $result ['snapshot'] = $m->get("addrsversion:" . $user['id']);
            echo json_encode($result);
            Yii::app()->filecache->set("contacts:" . $user['id'], json_encode($resultDb));
            die();
        }
    }


    /*
     * 用户通讯录快照版本一致否
     */
    private function hasSnapShot($key, $version, $m)
    {
        //判断该用户contacts过期否
        if ($m->get($key) == $version) {
            return 1;
        } else {
            return 0;
        }
    }

    /*
     * 数据库通讯录查询
     */
    private function searchAddressBook($user, $ownbx, $connection)
    {
        //取通讯录分组表中的分组名和编号
        $sqlGroup = "select id,sort,group_name name from group_contact where member_id = {$user->id} order by sort asc";
        $command = $connection->createCommand($sqlGroup);
        $resultGroup = $command->queryAll();
        $gid = "";//所有群组编号集合
        if ($resultGroup) {
            foreach ($resultGroup as $key => $value) {
                $gid .= $value['id'] . ",";
                if (!$value['sort']) {
                    $resultGroup[$key]['sort'] = $key + 1;
                    GroupContact::model()->updateAll(array("sort" => ($key + 1)), "id={$value['id']}");
                }
            }
            $gid = trim($gid);
            $gid = trim($gid, ',');
            $resultGroup[] = array("id" => 10000, "name" => "常用号码直通车");
        }
        $result_group = $resultGroup;//分组编号=》组名，额外10000=》常用号码直通车

        //通讯录数据查询
        $benbenArr = array();
        $contactArr = array();
        $activeContact = array();
        $userTrainInfo = array();
        $contactRelateBenben = array();
        $hasGo = array();
        $sqlAddress = "select a.group_id,a.name,a.pinyin,a.created_time,b.id,b.contact_info_id,b.phone,b.is_benben,b.is_baixing,b.is_active from group_contact_info as a
            left join group_contact_phone as b on a.id=b.contact_info_id where a.member_id={$user['id']}";
        $resultAddress = $connection->createCommand($sqlAddress)->queryAll();
        foreach ($resultAddress as $k => $v) {
            if ($v['is_benben']) {
                if (!in_array($v['is_benben'], $benbenArr)) {
                    $benbenArr[] = $v['is_benben'];
                }
            }
            if ($v['contact_info_id']) {
                if (!in_array($v['contact_info_id'], $contactArr)) {
                    $contactArr[] = $v['contact_info_id'];
                }
            }
            if ($v['is_active'] == 1) {
                $activeContact[$v['contact_info_id']] = $v['is_active'];
            }
            if (!$contactRelateBenben[$v['contact_info_id']]) {
                $contactRelateBenben[$v['contact_info_id']] = $v['is_benben'];
            }
            $hasGo[$v['contact_info_id']] = 0;
        }

        if ($benbenArr) {
            $sqlTrain = "select a.id as train_id,b.benben_id,b.nick_name,b.poster,b.huanxin_username from member as b
            left join number_train as a on a.member_id=b.id where b.benben_id in (" . implode(",", $benbenArr) . ")";
            $resultTrain = $connection->createCommand($sqlTrain)->queryAll();
        } else {
            $resultTrain = array();
        }
        foreach ($resultTrain as $kt => $vt) {
            $userTrainInfo[$vt['benben_id']] = $vt;
        }
        //数据组织
        $phone = array();
        $contactTemple = array();
        foreach ($resultAddress as $ka => $va) {
            if (in_array($va['contact_info_id'], $contactArr)) {
                $go = 0;//标识能否执行通讯录数组插入
                /*
                 * 有active插入active的，没有判断phone中有is_benben，有取第一次，没有则取首个号码
                 */
                if ($activeContact[$va['contact_info_id']]) {
                    if ($activeContact[$va['contact_info_id']] == $va['is_active']) {
                        $go = 1;
                        $hasGo[$va['contact_info_id']] = 1;
                    }
                } else {
                    if ($contactRelateBenben[$va['contact_info_id']]) {
                        if ($contactRelateBenben[$va['contact_info_id']] == $va['is_benben']) {
                            $go = 1;
                            $hasGo[$va['contact_info_id']] = 1;
                        }
                    } elseif ($hasGo[$va['contact_info_id']] == 0) {
                        $go = 1;
                        $hasGo[$va['contact_info_id']] = 1;
                    }
                }
                if ($go == 1) {
                    $contactTemple[] = array(
                        "id" => $va['contact_info_id'],
                        "group_id" => $va['group_id'],
                        "name" => $va['name'],
                        "pinyin" => $va['pinyin'],
                        "is_benben" => $va['is_benben'],
                        "is_baixing"=> $va['is_baixing'],
                        "created_time" => $va['created_time'],
                        "nick_name" => $va['is_benben'] ? ($userTrainInfo[$va['is_benben']]['nick_name'] ? $userTrainInfo[$va['is_benben']]['nick_name'] : $va['name']) : $va['name'],
                        "huanxin_username" => $va['is_benben'] ? ($userTrainInfo[$va['is_benben']]['huanxin_username'] ? $userTrainInfo[$va['is_benben']]['huanxin_username'] : "") : "",
                        "poster" => $va['is_benben'] ? ($userTrainInfo[$va['is_benben']]['poster'] ? URL . $userTrainInfo[$va['is_benben']]['poster'] : "") : "",
                        "is_active" => $va['is_active'],
                    );
                    $tplKey = array_search($va['contact_info_id'], $contactArr);
                    unset($contactArr[$tplKey]);
                }
            }
            $phone[$va['contact_info_id']][] = array(
                "id" => $va['id'],
                "phone" => $va['phone'],
                "is_baixing" => $ownbx ? $va['is_baixing'] : 0,
                "train_id" => $va['is_benben'] ? ($userTrainInfo[$va['is_benben']]['train_id'] ? $userTrainInfo[$va['is_benben']]['train_id'] : "") : "",
            );
        }
        //二次组织数据
        foreach ($contactTemple as $kc => $vc) {
            $contactTemple[$kc]['phone'] = $phone[$vc['id']];
        }

        //获取收藏的号码直通车
        $sql = "select a.id,a.short_name,a.name,a.poster,a.phone,a.telephone from number_train a inner join number_train_collect b
			on a.id = b.number_train_id where a.is_close = 0 and b.member_id = {$user->id} and a.status = 0 order by a.istop desc,a.created_time desc,a.id desc ";
        $command = $connection->createCommand($sql);
        $result0 = $command->queryAll();
        foreach ($result0 as $v1) {
            $phone = array(
                "id" => "",
                "contact_info_id" => $v1['id'] + 1000000,
                "phone" => $v1['phone'],
                "is_baixing" => 0,
                "train_id" => "",
            );
            $short_phone = array(
                "id" => "",
                "contact_info_id" => $v1['id'] + 1000000,
                "phone" => $v1['telephone'],
                "is_baixing" => 0,
                "train_id" => "",
            );
            $collect = array(
                "id" => $v1['id'] + 1000000,
                "group_id" => 10000,
                "name" => $v1['short_name'] ? $v1['short_name'] : ($v1['name'] ? $v1['name'] : ""),
                "pinyin" => "",
                "is_benben" => "",
                "is_baixing" => "",
                "created_time" => "",
                "nick_name" => $v1['name'] ? $v1['name'] : "",
                "huanxin_username" => "",
                "poster" => $v1['poster'] ? URL . $v1['poster'] : "",
                "is_active" => 0,
                "phone" => $v1['telephone'] ? array($phone, $short_phone) : array($phone),
            );
            $contactTemple[] = $collect;
        }

        $result['group'] = $result_group;
        $result['contact'] = $contactTemple;
        return $result;
    }

    /*
     * 数组脏检查，针对2维数组
     * 返回需要删除的delete和新增add数组
     */
    private
    function dirtyCheck($origion, $new, $primaryKey = "id")
    {
        $delete = array();
        $add = array();
        foreach ($origion as $kc => $vc) {
            foreach ($new as $kd => $vd) {
                $flag = 0;
                foreach ($vd as $key => $value) {
                    if ($vc[$primaryKey] == $vd[$primaryKey]) {
                        if ($vc[$key] == $value) {
                            $flag++;
                            if ($flag == count($vd)) {
                                unset($new[$kd]);
                                unset($origion[$kc]);
                            }
                        }
                    }
                }
            }
        }
        $new = array_values($new);
        $origion = array_values($origion);
        $add = $new;
        foreach ($origion as $value) {
            $delete[] = $value[$primaryKey];
        }
        $result["add"] = $add;
        $result["delete"] = $delete;
        return $result;
    }

    /*
     * 设置默认犇犇号
     * 需要更新到group_contact_info=>benben_id,和group_contact_phone=>is_active
     * */
    public
    function actionSetactive()
    {
        $this->check_key();
        $user = $this->check_user();
        $groupphoneid = Frame::getStringFromRequest('id');
        $groupinfoid = Frame::getStringFromRequest('infoid');
        if (empty($groupphoneid)) {
            $result ['ret_num'] = 101;
            $result ['ret_msg'] = '请选择';
            echo json_encode($result);
            die ();
        }
        $connection = Yii::app()->db;
        //更新通讯录，去除默认电话group_contact_phone
        $sql1 = "update group_contact_phone set is_active = 0 where contact_info_id = {$groupinfoid}";
        $command = $connection->createCommand($sql1);
        $result1 = $command->execute();

        //设置通讯录默认电话group_contact_phone
        $sql2 = "update group_contact_phone set is_active = 1 where id = {$groupphoneid}";
        $command = $connection->createCommand($sql2);
        $result2 = $command->execute();

        $info = GroupContactPhone::model()->find("is_active=1 and contact_info_id = {$groupinfoid}");
        //设置默认电话group_contact_info
        if ($info['is_benben']) {
            $sql3 = "update group_contact_info set benben_id = {$info['is_benben']} where id = {$groupinfoid}";
            $command = $connection->createCommand($sql3);
            $result3 = $command->execute();
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            $m = new Memcached();
            $m->addServer('localhost', 11211);
            $snapshot = $m->get("addrsversion:" . $user['id']);
            $m->set("addrsversion:" . $user['id'], ($snapshot + 1));
//			if ($result3) {
//				$result ['ret_num'] = 0;
//				$result ['ret_msg'] = '操作成功';
//			} else {
//				$result ['ret_num'] = 1001;
//				$result ['ret_msg'] = '该用户已经为默认联系人';
//			}
        } else {
            $result ['ret_num'] = 1011;
            $result ['ret_msg'] = '该用户不是注册用户';
        }
        echo json_encode($result);
    }

    /**
     * 添加分组
     */
    public
    function actionAddgroup()
    {
        $this->check_key();
        $user = $this->check_user();
        $groupname = Frame::getStringFromRequest('group');
        if (empty ($groupname)) {
            $result ['ret_num'] = 101;
            $result ['ret_msg'] = '请输入分组名';
            echo json_encode($result);
            die ();
        }
        if ($groupname == "未分组") {
            $result ['ret_num'] = 182;
            $result ['ret_msg'] = '请输入其他的分组名';
            echo json_encode($result);
            die ();
        }
        $info = GroupContact::model()->find("group_name = '{$groupname}' and member_id = {$user->id}");
        if ($info) {
            $result ['ret_num'] = 5207;
            $result ['ret_msg'] = '分组名已存在';
            echo json_encode($result);
            die ();
        }
        $group_num = GroupContact::model()->count("member_id = {$user->id}");

        $group = new GroupContact();
        $group->group_name = $groupname;
        $group->member_id = $user->id;
        $group->created_time = time();
        $group->sort = $group_num;
        if ($group->save()) {
            GroupContact::model()->updateAll(array("sort" => $group_num + 1), "group_name='未分组'");
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            $result['group_id'] = $group->id;
            $m = new Memcached();
            $m->addServer('localhost', 11211);
            $snapshot = $m->get("addrsversion:" . $user['id']);
            $m->set("addrsversion:" . $user['id'], ($snapshot + 1));
        } else {
            $result ['ret_num'] = 116;
            $result ['ret_msg'] = '分组添加失败';
        }
        echo json_encode($result);
    }

    /**
     * 编辑分组
     */
    public
    function actionEditgroup()
    {
        $this->check_key();
        $user = $this->check_user();
        $groupname = Frame::getStringFromRequest('group');
        $groupid = Frame::getIntFromRequest('group_id');
        if (empty ($groupname)) {
            $result ['ret_num'] = 101;
            $result ['ret_msg'] = '请输入分组名';
            echo json_encode($result);
            die ();
        }
        $trimname = trim($groupname);
        if ($trimname == '未分组') {
            $result ['ret_num'] = 100;
            $result ['ret_msg'] = '分组名已存在';
            echo json_encode($result);
            die();
        }
        $info = GroupContact::model()->find("group_name = '{$groupname}' and member_id = {$user->id}");
        if ($info) {
            $result ['ret_num'] = 5207;
            $result ['ret_msg'] = '分组名已存在';
            echo json_encode($result);
            die ();
        }
        if (empty ($groupid)) {
            $result ['ret_num'] = 102;
            $result ['ret_msg'] = '分组ID为空';
            echo json_encode($result);
            die ();
        }
        $group = GroupContact::model()->find("id = {$groupid}");
        if (empty($group)) {
            $result ['ret_num'] = 103;
            $result ['ret_msg'] = '分组不存在';
        } else {
            $group->group_name = $groupname;
            if ($group->update()) {
                $result ['ret_num'] = 0;
                $result ['ret_msg'] = '操作成功';
                $m = new Memcached();
                $m->addServer('localhost', 11211);
                $snapshot = $m->get("addrsversion:" . $user['id']);
                $m->set("addrsversion:" . $user['id'], ($snapshot + 1));
            } else {
                $result ['ret_num'] = 104;
                $result ['ret_msg'] = '分组修改失败';
            }
        }
        echo json_encode($result);
    }

    /**
     * 删除分组
     */
    public
    function actionDeletegroup()
    {
        $this->check_key();
        $user = $this->check_user();
        $target = Frame::getStringFromRequest('target');
        $groupid = Frame::getIntFromRequest('group_id');
        if (empty ($groupid)) {
            $result ['ret_num'] = 102;
            $result ['ret_msg'] = '分组ID为空';
            echo json_encode($result);
            die ();
        }
        $group = GroupContact::model()->find("id = {$groupid}");
        if (empty($group)) {
            $result ['ret_num'] = 103;
            $result ['ret_msg'] = '分组不存在';
        } else {
            $connection = Yii::app()->db;
            $sql = "select id from group_contact_info where group_id = {$groupid}";
            $command = $connection->createCommand($sql);
            $result0 = $command->queryAll();
            $phone_id = array();
            foreach ($result0 as $va) {
                $phone_id[] = $va['id'];
            }
            $count = count($result0);
            if ($count && $target) {
                $sql = "update group_contact_info set group_id = {$target} where group_id = {$groupid}";
                $command = $connection->createCommand($sql);
                $result0 = $command->execute();

                if ($result0) {
                    if ($group['group_name'] != "未分组") {
                        $group->delete();
                        //重新排序
                        $sql_c = "select id from group_contact where member_id={$user->id} order by sort asc";
                        $command = $connection->createCommand($sql_c);
                        $result_c = $command->queryAll();
                        foreach ($result_c as $k_c => $v_c) {
                            GroupContact::model()->updateAll(array("sort" => $k_c + 1), "id={$v_c['id']}");
                        }
                    }
                    $m = new Memcached();
                    $m->addServer('localhost', 11211);
                    $snapshot = $m->get("addrsversion:" . $user['id']);
                    $m->set("addrsversion:" . $user['id'], ($snapshot + 1));
                    $result ['ret_num'] = 0;
                    $result ['ret_msg'] = '操作成功';
                }
            } else {
                if ($count) {
                    $sql = "delete from group_contact_info where group_id = {$groupid}";
                    $command = $connection->createCommand($sql);
                    $result0 = $command->execute();
                    //删除联系人号码
                    if ($phone_id) {
                        $sql = "delete from group_contact_phone where contact_info_id in(" . implode(",", $phone_id) . ")";
                        $command = $connection->createCommand($sql);
                        $result0 = $command->execute();
                    }
                    if ($group['group_name'] != "未分组") {
                        $group->delete();
                        //重新排序
                        $sql_d = "select id from group_contact where member_id={$user->id} order by sort asc";
                        $command = $connection->createCommand($sql_d);
                        $result_d = $command->queryAll();
                        foreach ($result_d as $k_d => $v_d) {
                            GroupContact::model()->updateAll(array("sort" => $k_d + 1), "id={$v_d['id']}");
                        }
                    }
                    if ($result0) {
                        $m = new Memcached();
                        $m->addServer('localhost', 11211);
                        $snapshot = $m->get("addrsversion:" . $user['id']);
                        $m->set("addrsversion:" . $user['id'], ($snapshot + 1));
                        $result ['ret_num'] = 0;
                        $result ['ret_msg'] = '操作成功';
                    }
                } else {
                    if ($group->group_name != "未分组") {
                        $group->delete();
                        //重新排序
                        $sql_d = "select id from group_contact where member_id={$user->id} order by sort asc";
                        $command = $connection->createCommand($sql_d);
                        $result_d = $command->queryAll();
                        foreach ($result_d as $k_d => $v_d) {
                            GroupContact::model()->updateAll(array("sort" => $k_d + 1), "id={$v_d['id']}");
                        }
                    }
                    $m = new Memcached();
                    $m->addServer('localhost', 11211);
                    $snapshot = $m->get("addrsversion:" . $user['id']);
                    $m->set("addrsversion:" . $user['id'], ($snapshot + 1));
                    $result ['ret_num'] = 0;
                    $result ['ret_msg'] = '操作成功';
                }
            }
        }
        echo json_encode($result);
    }

    /*
     * 分组排序
     * 根据group_contact的sort重新排序
     * sort值：id;index|id;index
     * type=2,为了解决未分组不参与排序
     * */
    public
    function actionSortgroup()
    {
        $this->check_key();
        $user = $this->check_user();
        $sort = Frame::getStringFromRequest('sort');
        $type = Frame::getStringFromRequest('type');
        $tpl_sort = explode("|", $sort);
        if (count($tpl_sort) >= 2) {
            foreach ($tpl_sort as $k => $v) {
                $tpl = array();
                $tpl = explode(";", $v);
                GroupContact::model()->updateAll(array("sort" => $tpl[1]), "id={$tpl[0]}");
            }
//            if($type==2){
            $memberinfo = GroupContact::model()->findAll("member_id={$user['id']} order by sort asc");
            $flag = 0;
            foreach ($memberinfo as $kk => $vv) {
                if ($vv['group_name'] == '未分组') {
                    GroupContact::model()->updateAll(array("sort" => count($memberinfo)), "id={$vv['id']}");
                } else {
                    $flag++;
                    GroupContact::model()->updateAll(array("sort" => $flag), "id={$vv['id']}");
                }
            }
//            }
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            $m = new Memcached();
            $m->addServer('localhost', 11211);
            $snapshot = $m->get("addrsversion:" . $user['id']);
            $m->set("addrsversion:" . $user['id'], ($snapshot + 1));
        } else {
            $result ['ret_num'] = 100;
            $result ['ret_msg'] = '缺少参数';
        }

        echo json_encode($result);
    }

    /**
     * 编辑分组成员
     */
    public
    function actionEditmember()
    {
        $this->check_key();
        $user = $this->check_user();
        $userid = Frame::getStringFromRequest('user_id');
        $groupid = Frame::getIntFromRequest('group_id');
        $connection = Yii::app()->db;
        $sql = "select id from group_contact where group_name = '未分组' and member_id = {$user->id}";
        $command = $connection->createCommand($sql);
        $gid = $command->queryAll();
        $sql = "update group_contact_info set group_id = {$gid[0]['id']} where group_id = {$groupid}";
        $command = $connection->createCommand($sql);
        $result0 = $command->execute();
        if ($userid && $groupid) {
            $sql = "update group_contact_info set group_id = {$groupid} where id in ({$userid})";
            $command = $connection->createCommand($sql);
            $result0 = $command->execute();

            // $result ['ret_num'] = 0;
            // $result ['ret_msg'] = '操作成功';

            // if($result0){
            // 	$result ['ret_num'] = 0;
            // 	$result ['ret_msg'] = '操作成功';
            // }else{
            // 	$result ['ret_num'] = 106;
            // 	$result ['ret_msg'] = '编辑分组成员失败';
            // }
        }

        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $m = new Memcached();
        $m->addServer('localhost', 11211);
        $snapshot = $m->get("addrsversion:" . $user['id']);
        $m->set("addrsversion:" . $user['id'], ($snapshot + 1));
        echo json_encode($result);
    }

    /**
     *  单个移动分组
     **/
    public
    function actionChangegroup()
    {
        $this->check_key();
        $user = $this->check_user();
        $userid = Frame::getStringFromRequest('user_id');
        $groupid = Frame::getIntFromRequest('group_id');
        $connection = Yii::app()->db;
        if ($userid && $groupid) {
            $sql = "update group_contact_info set group_id = {$groupid} where id in ({$userid})";
            $command = $connection->createCommand($sql);
            $result0 = $command->execute();
            if ($result0) {
                $result ['ret_num'] = 0;
                $result ['ret_msg'] = '操作成功';
                $m = new Memcached();
                $m->addServer('localhost', 11211);
                $snapshot = $m->get("addrsversion:" . $user['id']);
                $m->set("addrsversion:" . $user['id'], ($snapshot + 1));
            } else {
                $result ['ret_num'] = 106;
                $result ['ret_msg'] = '编辑分组成员失败';
            }
        } else {
            $result ['ret_num'] = 106;
            $result ['ret_msg'] = '参数错误';
        }

        echo json_encode($result);
    }

    /**
     * 定时匹配
     */
    public
    function actionIntervalmatch()
    {
        $this->check_key();
        $user = $this->check_user();
        $phone = Frame::getStringFromRequest('phone');
        if (empty ($phone)) {

        }
        $aphone = explode(",", $phone);
        $re = array();
        $benbenp = array();
        $baixingp = array();
        $connection = Yii::app()->db;
        $sql1 = "select phone from member where phone in ({$phone})";
        $command = $connection->createCommand($sql1);
        $result1 = $command->queryAll();
        foreach ($result1 as $va) {
            $benbenp[] = $va['phone'];
        }

        $sql2 = "select phone from bxapply where phone in ({$phone})";
        $command = $connection->createCommand($sql2);
        $result2 = $command->queryAll();
        foreach ($result2 as $va) {
            $baixingp[] = $va['phone'];
        }

        foreach ($aphone as $val) {
            if (in_array($val, $benbenp) && in_array($val, $baixingp)) {
                $re[] = array(
                    'phone' => $val,
                    'is_benben' => 1,
                    'is_baixing' => 1
                );
            } elseif (in_array($val, $benbenp)) {
                $re[] = array(
                    'phone' => $val,
                    'is_benben' => 1,
                    'is_baixing' => 0
                );
            } elseif (in_array($val, $baixingp)) {
                $re[] = array(
                    'phone' => $val,
                    'is_benben' => 0,
                    'is_baixing' => 1
                );
            } else {
                $re[] = array(
                    'phone' => $val,
                    'is_benben' => 0,
                    'is_baixing' => 0
                );
            }
        }
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result ['info'] = $re;
        echo json_encode($result);
    }

    /**
     * 修改通讯录联系人姓名
     */
    public
    function actionEditname()
    {
        $this->check_key();
        $user = $this->check_user();
        $id = Frame::getIntFromRequest('id');
        $name = Frame::getStringFromRequest('name');
        if (!$name) {
            $result ['ret_num'] = 5238;
            $result ['ret_msg'] = '姓名为空';
            echo json_encode($result);
            die();
        }
        $contact = GroupContactInfo::model()->findByPk($id);
        if ($contact) {
            if ($contact->name == $name) {
                $result ['ret_num'] = 5237;
                $result ['ret_msg'] = '姓名已存在';
                echo json_encode($result);
                die();
            }
            $contact->name = $name;
            $PinYin = new tpinyin();
            $contact->pinyin = $PinYin->str2sort($name);
            $contact->allpinyin = $PinYin->str2py($name);
            if ($contact->update()) {
                $result ['ret_num'] = 0;
                $result ['ret_msg'] = '操作成功';
                $result ['name'] = $name;
                $result ['pinyin'] = $contact->pinyin;
                $result ['allpinyin'] = $contact->allpinyin;
                $m = new Memcached();
                $m->addServer('localhost', 11211);
                $snapshot = $m->get("addrsversion:" . $user['id']);
                $m->set("addrsversion:" . $user['id'], ($snapshot + 1));
            }
        } else {
            $result ['ret_num'] = 1803;
            $result ['ret_msg'] = '联系人信息不存在';
        }
        echo json_encode($result);
    }

    /**
     * 添加联系人到通讯录
     */

    public
    function actionAddcontact()
    {

        $this->check_key();
        $user = $this->check_user();
        $name = Frame::getStringFromRequest('name');
        $phone = Frame::getStringFromRequest('phone');
        $groupid = Frame::getStringFromRequest('group_id');
        if (!$name || !$phone) {
            $result ['ret_num'] = 1692;
            $result ['ret_msg'] = '姓名或手机号码为空';
            echo json_encode($result);
            die();
        }

        if ($phone == $user['phone']) {
            $result ['ret_num'] = 1992;
            $result ['ret_msg'] = '此号为本机号，请勿添加';
            echo json_encode($result);
            die();
        }

        $connection = Yii::app()->db;
        //是否与自己通讯录好友的号码、或者百姓网短号重复
        $sql = "select a.id from group_contact_info a inner join group_contact_phone b on a.id = b.contact_info_id
                 where a.member_id = {$user->id} and (b.phone = '{$phone}' or b.is_baixing = '{$phone}')";
        $command = $connection->createCommand($sql);
        $result1 = $command->queryAll();
        if ($result1[0]) {
            $result ['ret_num'] = 1693;
            $result ['ret_msg'] = '该联系人号码已存在';
            echo json_encode($result);
            die();
        }

        if (empty($groupid)) {
            $group = GroupContact::model()->find("group_name = '未分组' and member_id = {$user->id}");
            $groupid = $group['id'];
            if (!count($group)) {
                $groupcontact = new GroupContact();
                $groupcontact->group_name = '未分组';
                $groupcontact->created_time = time();
                $groupcontact->member_id = $user->id;
                $groupcontact->save();
                $groupid = $groupcontact['id'];
            }
        } else {
            $group = GroupContact::model()->find("id = {$groupid} and member_id = {$user->id}");
        }
        if (!$group) {
            $result ['ret_num'] = 1695;
            $result ['ret_msg'] = '分组ID不正确';
            echo json_encode($result);
            die();
        }
        //该号码是否是犇犇用户
        $info = Member::model()->find("phone = '{$phone}'");
        $leg_id = "";
        $leg_poster = "";
        $leg_type = "";
        $leg_name = "";
        $leg_district = "";
        $trainid = "";
        $pic = "";
        $shortname = "";
        $tag = "";
        if ($info) {
            $is_benben = $info->benben_id;
            $poster = $info->poster ? URL . $info->poster : "";
            $huanxin_username = $this->eraseNull($info->huanxin_username);

            //好友联盟信息
            $leagueinfo = FriendLeague::model()->find("member_id={$info['id']} and status=0 and is_delete=0");
            if ($leagueinfo) {
                $leg_id = $leagueinfo['id'];
                $leg_poster = $leagueinfo['poster'] ? URL . $leagueinfo['poster'] : "";
                $leg_type = $leagueinfo['type'] ? ($leagueinfo['type'] == 1 ? '工作联盟' : '英雄联盟') : "";
                $leg_name = $leagueinfo['name'] ? $leagueinfo['name'] : "";
                $districtinfo = $this->ProCity(array(0 => $leagueinfo));
                $leg_district = $districtinfo[$leagueinfo['city']] . " " . $districtinfo[$leagueinfo['area']];
            } else {
                $leg_id = "";
                $leg_poster = "";
                $leg_type = "";
                $leg_name = "";
                $leg_district = "";
            }

            //号码直通车信息
            $traininfo = NumberTrain::model()->find("status=0 and is_close=0 and member_id={$info['id']}");
            if ($traininfo) {
                $trainid = $traininfo['id'];
                $pic = $traininfo['poster'] ? URL . $traininfo['poster'] : "";
                $shortname = $traininfo['short_name'] ? $traininfo['short_name'] : "";
                $tag = $traininfo['tag'] ? $traininfo['tag'] : "";
            } else {
                $trainid = "";
                $pic = "";
                $shortname = "";
                $tag = "";
            }
        } else {
            $is_benben = 0;
            $poster = "";
            $huanxin_username = "";
        }
        //该号码是否是百姓网用户
        $binfo = Bxapply::model()->find("phone = '{$phone}' and status = 3");
        if ($binfo) {
            $is_baixing = $binfo->short_phone;
        } else {
            $is_baixing = 0;
        }
        $PinYin = new tpinyin();
        $py = $PinYin->str2sort($name);
        $allpy = $PinYin->str2py($name);

        $contact = new GroupContactInfo();
        $contact->group_id = $groupid;
        $contact->name = $name;
        $contact->pinyin = $py;
        $contact->allpinyin = $allpy;
        $contact->created_time = time();
        $contact->member_id = $user->id;
        $contact->benben_id = $is_benben;
        if ($contact->save()) {
            $contactphone = new GroupContactPhone();
            $contactphone->phone = $phone;
            $contactphone->contact_info_id = $contact->id;
            $contactphone->is_benben = $is_benben;
            $contactphone->is_baixing = $is_baixing;
            $contactphone->save();
            $phoneid = $contactphone['id'];

            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            $result['user'] = array(
                "name" => $name,
                "nick_name" => $info ? $info->nick_name : "",
                "group_id" => $groupid,
                "id" => $contact->id,
                "poster" => $poster,
                "pinyin" => $py,
                "allpinyin" => $allpy,
                "is_benben" => $is_benben,
                "is_baixing" => $is_baixing,
                "created_time" => time(),
                "huanxin_username" => $info ? $info->huanxin_username : "",
                "phone" => array(
                    0 => array(
                        "is_active" => 0,
                        "huanxin_username" => $info ? $info->huanxin_username : "",
                        "contact_info_id" => $contact->id,
                        "legid" => $leg_id,
                        "leg_poster" => $leg_poster,
                        "type" => $leg_type,
                        "leg_name" => $leg_name,
                        "leg_district" => $leg_district,
                        "train_id" => $trainid,
                        "pic" => $pic,
                        "short_name" => $shortname,
                        "tag" => $tag,
                        "is_baixing" => $is_baixing,
                        "is_benben" => $is_benben,
                        "nick_name" => $info ? $info->nick_name : "",
                        "phone" => $phone,
                        "poster" => $poster,
                        "id" => $phoneid
                    )
                )
            );
            $m = new Memcached();
            $m->addServer('localhost', 11211);
            $snapshot = $m->get("addrsversion:" . $user['id']);
            $m->set("addrsversion:" . $user['id'], ($snapshot + 1));
        } else {
            $result ['ret_num'] = 1694;
            $result ['ret_msg'] = '添加失败';
        }
        echo json_encode($result);

    }


    /**
     * 添加通讯录联系人手机号
     */
    public
    function actionAddphone()
    {
        $this->check_key();
        $user = $this->check_user();
        $id = Frame::getIntFromRequest('id');
        $phone = Frame::getStringFromRequest('phone');

        if (empty($phone) || strlen($phone) < 3 || strlen($phone) > 17) {
            $result ['ret_num'] = 1692;
            $result ['ret_msg'] = '号码最少3位,最多17位';
            echo json_encode($result);
            die();
        }
        //自己是否是百姓网用户
        $owninfo = Bxapply::model()->find("member_id = {$user['id']} and status = 3");
        if ($owninfo) {
            //该号码是否是百姓网用户
            $binfo = Bxapply::model()->find("phone = '{$phone}' and status = 3");
            if ($binfo) {
                $is_baixing = $binfo->short_phone;
            } else {
                $is_baixing = 0;
            }
        } else {
            $is_baixing = 0;
        }

        $contact = GroupContactInfo::model()->findByPk($id);
        if ($contact) {
            // $re = GroupContactPhone::model()->find("contact_info_id = {$id} and phone = '{$phone}'");
            // if($re){
            // 	$result ['ret_num'] = 5230;
            // 	$result ['ret_msg'] = '手机号码已存在';
            // 	echo json_encode( $result );
            // 	die();
            // }

            $connection = Yii::app()->db;
            //是否与自己通讯录好友的号码、或者百姓网短号重复（不要求验证了）
            $sql = "select a.id from group_contact_info a inner join group_contact_phone b on a.id = b.contact_info_id
					     where a.member_id = {$user->id} and b.phone = '{$phone}'";
            $command = $connection->createCommand($sql);
            $result1 = $command->queryAll();


            //该号码是否是犇犇用户,再判断是否和通讯录中犇犇号重复
            $flag = 0;//表示是否搜索到该人的通讯录中已存在这条犇犇号
            $info = Member::model()->find("phone = '{$phone}'");
            if ($contact->benben_id) {
                $or_info = Member::model()->find("benben_id = {$contact->benben_id}");
            }
            if ($info) {
                $nick_name = $info->nick_name;
                $is_benben = $info->benben_id;
                $poster = $info->poster ? URL . $info->poster : "";
                $huanxin_username = $this->eraseNull($info->huanxin_username);
                if (!$contact->benben_id) {
                    $contact->benben_id = $info->benben_id;
                    $contact->update();
                } else {
                    $contact_phone = GroupContactPhone::model()->findAll('contact_info_id=' . $id);
                    foreach ($contact_phone as $kp => $vp) {
                        if ($vp['is_benben'] == $is_benben) {
                            $updatephone = GroupContactPhone::model()->updateAll(array("phone" => $phone), "id={$vp['id']}");
                            $phoneid = $vp['id'];
                            $flag = 1;
                        }
                    }
                }
            } else {
                $nick_name = "";
                $is_benben = 0;
                $poster = "";
                $huanxin_username = "";
            }

            if ($flag == 0) {
                $contactphone = new GroupContactPhone();
                $contactphone->phone = $phone;
                $contactphone->contact_info_id = $id;
                $contactphone->is_benben = $is_benben;
                $contactphone->is_baixing = $is_baixing;
                $savephone = $contactphone->save();

                if ($info) {
                    //该号码是否有号码直通车
                    $train = NumberTrain::model()->find("member_id=" . $info->id . " and status=0 and is_close=0");

                    //该号码是否有好友联盟
                    $leg_friend = FriendLeague::model()->find("member_id=" . $info->id . " and status=0 and is_delete=0");

                    $district = "";
                    if (count($leg_friend)) {
                        $area_sql = "SELECT area_name FROM area WHERE bid IN ({$leg_friend['city']},{$leg_friend['area']}) ORDER BY bid ASC";
                        $command = $connection->createCommand($area_sql);
                        $area_result = $command->queryAll();
                        foreach ($area_result as $ka => $va) {
                            $district .= $va['area_name'] . " ";
                        }
                    }
                    $district = trim($district);
                }
            }

            if ($updatephone || $savephone) {
                $result ['ret_num'] = 0;
                $result ['ret_msg'] = '操作成功';
                $m = new Memcached();
                $m->addServer('localhost', 11211);
                $snapshot = $m->get("addrsversion:" . $user['id']);
                $m->set("addrsversion:" . $user['id'], ($snapshot + 1));
                $result['phone_info'] = array(
                    "id" => $contactphone->id ? $contactphone->id : $phoneid,
                    "contact_info_id" => $contactphone->contact_info_id ? $contactphone->contact_info_id : $id,
                    "phone" => $phone,
                    "is_benben" => $is_benben,
                    "is_baixing" => $is_baixing ? $is_baixing : 0,
                    "poster" => $poster,
                    "huanxin_username" => $huanxin_username,
                    "is_active" => 0,
                    "nick_name" => $nick_name,
                    "pic" => $train['poster'] ? URL . $train['poster'] : "",
                    "short_name" => $train['short_name'] ? $train['short_name'] : "",
                    "train_id" => $train['id'] ? $train['id'] : 0,
                    "tag" => $train['tag'] ? $train['tag'] : "",
                    "leg_poster" => $leg_friend['poster'] ? $leg_friend['poster'] : "",
                    "leg_name" => $leg_friend['name'] ? $leg_friend['name'] : "",
                    "leg_id" => $leg_friend['id'] ? $leg_friend['id'] : "",
                    "leg_district" => $district ? $district : "",
                );
                $result['contact_info'] = array(
                    "id" => $id,//group_contact_info表的ID
                    "group_id" => $contact->group_id,//分组ID
                    "name" => $contact->name,
                    "pinyin" => $contact->pinyin,
                    "allpinyin" => $contact->allpinyin,
                    "created_time" => $contact->created_time,
                    "is_benben" => $or_info ? $or_info->benben_id : $is_benben,
                    "is_baixing" => $is_baixing ? $is_baixing : 0,
                    "poster" => $or_info ? ($or_info->poster ? URL . $or_info->poster : "") : $poster,
                    "huanxin_username" => $or_info ? $or_info->huanxin_username : $huanxin_username,
                    /*"phone"=>array(
                            "phone"=>$phone,
                            "is_benben"=>$is_benben,
                            "is_baixing"=> $is_baixing,
                            "poster"=>$poster,
                            "nick_name"=>$nick_name
                    )*/
                );
            } else {
                $result ['ret_num'] = 1803;
                $result ['ret_msg'] = '保存失败，换一个手机号试试';
            }
        } else {
            $result ['ret_num'] = 1803;
            $result ['ret_msg'] = '联系人信息不存在';
        }
        echo json_encode($result);
    }

    /**
     * 删除通讯录联系人手机号
     * 有犇犇号只删除手机号，没有如果只有1个号码，全清，否则删除该记录
     */
    public
    function actionDelphone()
    {
        $this->check_key();
        $user = $this->check_user();
        $data = array();
        $id = Frame::getIntFromRequest('id');//contact_phone
        $connection = Yii::app()->db;
        $phoneinfo = GroupContactPhone::model()->find("id={$id}");

        $data['id'] = $id;
        $data['infoid'] = $phoneinfo['contact_info_id'];
        $data['is_phone'] = 0;//清空手机号
        $data['is_del'] = 0;//清空该联系记录
        $data['is_alldel'] = 0;//清空联系人

        if ($phoneinfo) {
            if ($phoneinfo['is_benben']) {
                $sql1 = "update group_contact_phone set phone='' where id={$id}";
                $command = $connection->createCommand($sql1);
                $result1 = $command->execute();
                $result ['ret_num'] = 0;
                $result ['ret_msg'] = '操作成功';
                $data['is_phone'] = 1;
                $m = new Memcached();
                $m->addServer('localhost', 11211);
                $snapshot = $m->get("addrsversion:" . $user['id']);
                $m->set("addrsversion:" . $user['id'], ($snapshot + 1));
            } else {
                $num_phone = GroupContactPhone::model()->count("contact_info_id={$phoneinfo['contact_info_id']}");
                if ($num_phone <= 1) {
                    GroupContactPhone::model()->deleteAll("contact_info_id={$phoneinfo['contact_info_id']}");
                    GroupContactInfo::model()->deleteAll("id={$phoneinfo['contact_info_id']}");
                    $result ['ret_num'] = 0;
                    $result ['ret_msg'] = '操作成功';
                    $data['is_alldel'] = 1;
                    $m = new Memcached();
                    $m->addServer('localhost', 11211);
                    $snapshot = $m->get("addrsversion:" . $user['id']);
                    $m->set("addrsversion:" . $user['id'], ($snapshot + 1));
                } else {
                    $result2 = GroupContactPhone::model()->deleteAll("id={$id}");
                    if ($result2) {
                        $m = new Memcached();
                        $m->addServer('localhost', 11211);
                        $snapshot = $m->get("addrsversion:" . $user['id']);
                        $m->set("addrsversion:" . $user['id'], ($snapshot + 1));
                        $result ['ret_num'] = 0;
                        $result ['ret_msg'] = '操作成功';
                        $data['is_del'] = 1;
                    } else {
                        $result ['ret_num'] = 10001;
                        $result ['ret_msg'] = '请勿重复操作';
                    }
                }
            }
        } else {
            $result ['ret_num'] = 1803;
            $result ['ret_msg'] = '联系人信息不存在';
        }
        $result['contact'] = $data;
//		$phone = Frame::getStringFromRequest('phone');
//		if (empty ( $phone )) {
//			$result ['ret_num'] = 1802;
//			$result ['ret_msg'] = '联系人号码为空';
//			echo json_encode( $result );
//			die ();
//		}

//		$contact = GroupContactInfo::model()->findByPk($id);
//		 $contact = GroupContactInfo::model()->find("id = {$id} and member_id = {$user->id}");
//		if($contact){
//			$contactphone = GroupContactPhone::model()->find("phone = '{$phone}' and contact_info_id = {$id} ");
//			if($contactphone){
//				$is_benben = $contactphone->is_benben;
//				if($contactphone->delete()){

        //删除好友关系暂时没用，不代表以后没用
        /*					if($is_benben){
                        $sql = "select c.contact_info_id,c.phone from
                                    (select a.contact_info_id,a.phone from benben.group_contact_phone a
                                    left join benben.group_contact_info b
                                    on a.contact_info_id=b.id
                                    where b.member_id={$user->id} and
                                    is_benben>0 order by a.id asc
                                    ) c group by c.contact_info_id;";
                        $command = $connection->createCommand($sql);
                        $result1 = $command->queryAll();
                        $flag = 1;
                        if($result1[0]){
                            foreach ($result1 as $va){
                                if($va['phone'] == $phone){
                                    $flag = 0;
                                    break;
                                }
                            }
                        }
                        if($flag){
                            $friend1 = Member::model()->find("benben_id = {$is_benben}");
                            $id1 = $friend1->id;
                            $id2 = $contact->member_id;
                            $re = FriendRelate::model()->find("(friend_id1 = {$id1} and friend_id2 = {$id2}) or (friend_id1 = {$id2} and friend_id2 = {$id1})");
                            if($re){
                                $re->delete();
                            }
                        }

                    }*/
        //看是否有第二个号码
        /*	    if($result1[0]){
                            foreach ($result1 as $val){
                                if(($val['contact_info_id'] == $id) && $val['phone']){
                                    //添加好友关系
                                    $f1 = Member::model()->find("benben_id = {$contactphone->is_benben}");
                                    $t = time();
                                    $sql = "insert into friend_relate (friend_id1,friend_id2,created_time) values ({$contact->member_id},{$f1->id},{$t})";
                                    $command = $connection->createCommand($sql);
                                    $result2 = $command->execute();
                                    break;
                                }
                            }
                    }*/
//				}
//				$result ['ret_num'] = 0;
//				$result ['ret_msg'] = '操作成功';
//			}else{
//				$result ['ret_num'] = 1802;
//				$result ['ret_msg'] = '联系人号码为空';
//			}
//		}else{
//			$result ['ret_num'] = 1803;
//			$result ['ret_msg'] = '联系人信息不存在';
//		}
        echo json_encode($result);
    }

    /*
     * 删除通讯录犇犇号
     * 如果该账户只有唯一犇犇号，则删除通讯录group_contact_info和group_contact_phone
     * 否则删除通讯录该手机号记录group_contact_phone中记录
     * 检查group_contact_info中的犇犇号需要更新
     * */
    public
    function actionDelbenben()
    {
        $this->check_key();
        $user = $this->check_user();

        $benben_id = Frame::getIntFromRequest('benben');//contact_phone
        $infoid = Frame::getIntFromRequest('infoid');//contact_phone


        $data['infoid'] = $infoid;
        $data['benben_id'] = $benben_id;
        $data['is_del'] = 0;//清空该联系记录
        $data['is_alldel'] = 0;//清空联系人
        if ($benben_id > 0 && $infoid > 0) {
            $benben_num = GroupContactPhone::model()->count("is_benben={$benben_id} and contact_info_id={$infoid}");
            $contact_info_num = GroupContactPhone::model()->count("contact_info_id={$infoid}");
            $contact_info = GroupContactInfo::model()->find("id={$infoid}");
            if ($contact_info_num > 0) {
                if (($contact_info_num - $benben_num) > 0) {
                    GroupContactPhone::model()->deleteAll("is_benben={$benben_id} and contact_info_id={$infoid}");
                    //检查group_contact_info中的犇犇号需要更新
                    if ($contact_info->benben_id == $benben_id) {
                        $tpl_benben = 0;
                        $contact_phone = GroupContactPhone::model()->findAll("contact_info_id={$infoid}");//->OrderBy(['id'=>SORT_ASC])
                        foreach ($contact_phone as $k => $v) {
                            if ($v['is_benben']) {
                                $tpl_benben = $v['is_benben'];
                                break;
                            }
                        }
                        GroupContactInfo::model()->updateAll(array('benben_id' => $tpl_benben), "id={$infoid}");
                    }
                    $result ['ret_num'] = 0;
                    $result ['ret_msg'] = '操作成功';
                    $data['is_del'] = 1;
                    $m = new Memcached();
                    $m->addServer('localhost', 11211);
                    $snapshot = $m->get("addrsversion:" . $user['id']);
                    $m->set("addrsversion:" . $user['id'], ($snapshot + 1));
                } else {
                    GroupContactPhone::model()->deleteAll("contact_info_id={$infoid}");
                    GroupContactInfo::model()->deleteAll("id={$infoid}");
                    $result ['ret_num'] = 0;
                    $result ['ret_msg'] = '操作成功';
                    $data['is_alldel'] = 1;
                    $m = new Memcached();
                    $m->addServer('localhost', 11211);
                    $snapshot = $m->get("addrsversion:" . $user['id']);
                    $m->set("addrsversion:" . $user['id'], ($snapshot + 1));
                }
            } else {
                $result ['ret_num'] = 1803;
                $result ['ret_msg'] = '联系人信息不存在';
            }
        } else {
            $result ['ret_num'] = 1003;
            $result ['ret_msg'] = '非奔犇用户';
        }
        $result['contact'] = $data;
        echo json_encode($result);
    }

    /**
     * 删除通讯录联系人
     */
    public
    function actionDelcontact()
    {
        $this->check_key();
        $user = $this->check_user();
        $id = Frame::getStringFromRequest('id');
        $connection = Yii::app()->db;
        $bid = "";

        $id_arr = explode(";", $id);
        $ids = implode(",", ($id_arr));


        if (!$id && $bid) {
            $userid = Member::model()->find("benben_id = {$bid}");
            $sql1 = "select id,member_id from group_contact where member_id = {$userid->id}";
            $command = $connection->createCommand($sql1);
            $result1 = $command->queryAll();

            $sql2 = "select a.id,a.contact_info_id
			from group_contact_phone a inner join group_contact_info b on a.contact_info_id = b.id
			inner join group_contact d on d.id = b.group_id inner join member c on a.phone = c.phone
			where d.member_id = {$userid->id} and c.phone = '{$userid->phone}' limit 1";
            $command = $connection->createCommand($sql2);
            $result2 = $command->queryAll();
        }

        $sql = "select a.id from group_contact_info a inner join group_contact b on a.group_id = b.id
		where a.id in ({$ids}) and b.member_id = {$user->id}";
        $command = $connection->createCommand($sql);
        $r1 = $command->queryAll();//var_dump($r1);exit();
        if (!$r1) {
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            echo json_encode($result);
            die();
        }

        foreach ($id_arr as $k => $v) {
            $contact = GroupContactInfo::model()->findByPk($v);
            if ($contact) {
                $connection = Yii::app()->db;
                $sql = "select id, huanxin_username from member where phone in (select phone from group_contact_phone where contact_info_id = {$contact->id})";
                $command = $connection->createCommand($sql);
                $re1 = $command->queryAll();//var_dump($re1);exit();
                if ($re1) {
                    foreach ($re1 as $value) {
                        $sqlf = "delete from friend_relate where ((friend_id1 = {$value['id']} and friend_id2 = {$user->id}) or (friend_id1 = {$user->id} and friend_id2 = {$value['id']})) and status = 1";
                        $command = $connection->createCommand($sqlf);
                        $re2 = $command->execute();
                        //删除环信好友
                        $options = array(
                            "client_id" => CLIENT_ID,
                            "client_secret" => CLIENT_SECRET,
                            "org_name" => ORG_NAME,
                            "app_name" => APP_NAME
                        );
                        $huanxin = new Easemob($options);
                        $resulh = $huanxin->deleteFriend($user->huanxin_username, $value['huanxin_username']);
                        $reh = json_decode($resulh, true);
                    }
                }

                $sql = "delete from group_contact_phone where contact_info_id = {$contact->id}";
                $command = $connection->createCommand($sql);
                $re = $command->execute();
                if ($contact->delete()) {
                    $result ['ret_num'] = 0;
                    $result ['ret_msg'] = '操作成功';
                }
            } else {
                $result ['ret_num'] = 0;
                $result ['ret_msg'] = '操作成功';
            }
        }
        $m = new Memcached();
        $m->addServer('localhost', 11211);
        $snapshot = $m->get("addrsversion:" . $user['id']);
        $m->set("addrsversion:" . $user['id'], ($snapshot + 1));
        echo json_encode($result);
    }

    /**
     * 用户查找
     */
    public
    function actionSearch()
    {
        $this->check_key();
        $user = $this->check_user();
        $keyword = Frame::getStringFromRequest('keyword');
        $connection = Yii::app()->db;
        if ($keyword) {
            $sql = "select id,nick_name,poster,phone,sex from member where nick_name like '%{$keyword}%' or phone like '%{$keyword}%' or benben_id like '%{$keyword}%' order by id desc";
        } else {
            $sql = "select id,nick_name,poster,phone,sex from member order by id desc";
        }
        $command = $connection->createCommand($sql);
        $result0 = $command->queryAll();
        foreach ($result0 as $key => $valu) {
            if ($valu['poster']) {
                $result0[$key]['poster'] = URL . $valu['poster'];
            } else {
                $result0[$key]['poster'] = "";
            }
        }
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result ['member_info'] = $result0;
        echo json_encode($result);
    }

    /*
     * 用户详情
     */
    public
    function actionAddressDetail()
    {
        $this->check_key();
        $user = $this->check_user();
        $infoid = Frame::getStringFromRequest('infoid');
        if (!$infoid) {
            $result['ret_num'] = 410;
            $result['ret_msg'] = '用户不能为空';
            echo json_encode($result);
            die();
        }
        $connection = Yii::app()->db;
        //用户百姓网状态变化情况
        $ownbx = Bxapply::model()->count("phone = '{$user->phone}' and status = 3");//自己是否是百姓用户
        //查出好友在自己通讯录里的名字
        $sql2 = "select a.id as phoneid,a.phone,a.is_benben,a.is_baixing,a.contact_info_id,a.is_active,b.name,b.pinyin,b.id,b.allpinyin,b.group_id from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id
		where b.member_id = {$user->id} and a.contact_info_id={$infoid}";
        $command = $connection->createCommand($sql2);
        $res2 = $command->queryAll();
        if ($res2 && count($res2) > 0) {
            $activeArr = array();
            //数据处理
            $benbenArray = array();
            $ids = array();
            $userid2benben = array();
            foreach ($res2 as $kr => $vr) {
                if (!in_array($vr['is_benben'], $benbenArray)) {
                    $benbenArray[] = $vr['is_benben'];
                }
            }
            if ($benbenArray) {
                $sqlmember = "select nick_name,poster,huanxin_username,benben_id,id from member where benben_id!=0 and benben_id in (" . implode(",", $benbenArray) . ")";
                $command = $connection->createCommand($sqlmember);
                $resmember = $command->queryAll();
                $contactsInfo = array();
                foreach ($resmember as $km => $vm) {
                    $contactsInfo[$vm['benben_id']] = $vm;
                    $userid2benben[$vm['id']] = $vm['benben_id'];
                    $ids[] = $vm['id'];
                }
                $flagIn = 0;
                foreach ($res2 as $krr => $vrr) {
                    $res2[$krr]['nick_name'] = $contactsInfo[$vrr['is_benben']]['nick_name'] ? $contactsInfo[$vrr['is_benben']]['nick_name'] : "";
                    $res2[$krr]['poster'] = $contactsInfo[$vrr['is_benben']]['poster'] ? $contactsInfo[$vrr['is_benben']]['poster'] : "";
                    $res2[$krr]['huanxin_username'] = $contactsInfo[$vrr['is_benben']]['huanxin_username'] ? $contactsInfo[$vrr['is_benben']]['huanxin_username'] : "";
                    $res2[$krr]['member_id'] = $contactsInfo[$vrr['is_benben']]['id'] ? $contactsInfo[$vrr['is_benben']]['id'] : "";
                    //找出激活项，没有则取第一个奔犇号
                    if ($flagIn != 1) {
                        if ($vrr['is_active']) {
                            $activeArr['name'] = $vrr['name'];
                            $activeArr['nick_name'] = $res2[$krr]['nick_name'];
                            $activeArr['poster'] = $res2[$krr]['poster'];
                            $activeArr['is_benben'] = $vrr['is_benben'];
                            $activeArr['is_baixing'] = $ownbx ? $vrr['is_baixing'] : "0";
                            $activeArr['huanxin_username'] = $res2[$krr]['huanxin_username'];
                            $flagIn = 1;
                        } elseif ($vrr['is_benben']) {
                            $activeArr['name'] = $vrr['name'];
                            $activeArr['nick_name'] = $res2[$krr]['nick_name'];
                            $activeArr['poster'] = $res2[$krr]['poster'];
                            $activeArr['is_benben'] = $vrr['is_benben'];
                            $activeArr['is_baixing'] = $ownbx ? $vrr['is_baixing'] : "0";
                            $activeArr['huanxin_username'] = $res2[$krr]['huanxin_username'];
                            $flagIn = 1;
                        }
                    }
                }
            }

            if ($ids) {
                //查询该用户的号码直通车
                $trainArr = array();
                $traininfo = NumberTrain::model()->findAll("member_id in (" . implode(",", $ids) . ") and status=0 and is_close=0");
                foreach ($traininfo as $ktr => $vtr) {
                    if ($userid2benben[$vtr['member_id']]) {
                        $trainArr[$userid2benben[$vtr['member_id']]] = $vtr;
                    }
                }

                //查询该用户的好友联盟
                $friendArr = array();
                $friendinfo = FriendLeague::model()->findAll("member_id in (" . implode(",", $ids) . ") and status=0 and is_delete=0");
                $districtinfo = $this->ProCity($friendinfo);
                foreach ($friendinfo as $kk => $vv) {
                    if ($userid2benben[$vv['member_id']]) {
                        $friendArr[$userid2benben[$vv['member_id']]] = $vv;
                        $districtArr[$userid2benben[$vv['member_id']]] = $districtinfo[$friendinfo['city']] . " " . $districtinfo[$friendinfo['area']];
                    }
                }
            }
        }

        $phone = array();
        if (count($res2)) {
            foreach ($res2 as $v) {
                $phone[] = array(
                    "infoid" => $v['id'],
                    "id" => $v['phoneid'],
                    "nick_name" => $v['nick_name'] ? $v['nick_name'] : "",
                    "poster" => $v['poster'] ? URL . $v['poster'] : "",
                    "is_benben" => $v['is_benben'] ? $v['is_benben'] : "0",
                    "is_baixing" => $v['is_baixing'] ? $v['is_baixing'] : "0",
                    "phone" => $v['phone'] ? $v['phone'] : "",
                    "huanxin_username" => $v['huanxin_username'] ? $v['huanxin_username'] : "",
                    "is_active" => $v['is_active'] ? $v['is_active'] : "0",
                    "train_id" => $trainArr[$v['is_benben']]['id'] ? $trainArr[$v['is_benben']]['id'] : "",
                    "pic" => $trainArr[$v['is_benben']]['poster'] ? URL . $trainArr[$v['is_benben']]['poster'] : "",
                    "short_name" => $trainArr[$v['is_benben']]['short_name'] ? $trainArr[$v['is_benben']]['short_name'] : "",
                    "tag" => $trainArr[$v['is_benben']]['tag'] ? $trainArr[$v['is_benben']]['tag'] : "",
                    "legid" => $friendArr[$v['is_benben']]['id'] ? $friendArr[$v['is_benben']]['id'] : "",
                    "leg_district" => $districtArr[$v['is_benben']] ? $districtArr[$v['is_benben']] : "",
                    "leg_poster" => $friendArr[$v['is_benben']]['poster'] ? URL . $friendArr[$v['is_benben']]['poster'] : "",
                    "leg_name" => $friendArr[$v['is_benben']]['name'] ? $friendArr[$v['is_benben']]['name'] : "",
                    "type" => $friendArr ? ($friendArr[$v['is_benben']]['type'] == 1 ? '工作联盟' : '英雄联盟') : ""
                );
            }
        }

        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        $result['user'] = array(
            "member_id" => $res2[0]['member_id'],
            "infoid" => $res2[0]['contact_info_id'] ? $res2[0]['contact_info_id'] : ($res2[0]['id'] ? $res2[0]['id'] : ""),
            "name" => $activeArr['name'] ? $activeArr['name'] : ($res2[0]['name']?$res2[0]['name']:""),
            "nick_name" => $activeArr['nick_name'] ? $activeArr['nick_name'] :($res2[0]['nick_name']?$res2[0]['nick_name']:$res2[0]['name']),
            "group_id" => $res2[0]['group_id'] ? $res2[0]['group_id'] : "",
            "poster" => $activeArr['poster'] ? URL . $activeArr['poster'] : "",
            "is_benben" => $activeArr['is_benben'] ? $activeArr['is_benben'] : "0",
            "is_baixing" => $activeArr['is_baixing'] ? $activeArr['is_baixing'] : "0",
            "huanxin_username" => $activeArr['huanxin_username']?$activeArr['huanxin_username']:"",
            "phone" => $phone,
            "is_friend" => 1,
            "created_time" => date("Y-m-d", $user->created_time),
            "pinyin" => $res2[0]['pinyin'] ? $res2[0]['pinyin'] : "",
            "allpinyin" => $res2[0]['allpinyin'] ? $res2[0]['allpinyin'] : "",
        );

        echo json_encode($result);
    }

    /*
     * 重建分组索引
     */
//    public function actionRestindex(){
//        set_time_limit(1800) ;
//        $connection = Yii::app()->db;
//        $sql="SELECT member_id FROM group_contact GROUP BY member_id";
//        $command = $connection->createCommand($sql);
//        $result0 = $command->queryAll();
//        foreach($result0 as $kk=>$vv){
//            $member[]=$vv['member_id'];
//        }
//        foreach($member as $k=>$v){
//            $infogroup=GroupContact::model()->findAll("member_id={$v}");
//            $flag=0;
//            foreach($infogroup as  $ka=>$vb){
//                if($vb['group_name']=="未分组"){
//                    GroupContact::model()->updateAll(array("sort"=>count($infogroup)),"id={$vb['id']}");
//                }else{
//                    $flag++;
//                    GroupContact::model()->updateAll(array("sort"=>$flag),"id={$vb['id']}");
//                }
//            }
//            echo "Yes<br>";
//        }
//    }

}