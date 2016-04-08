<?php

class EnterpriseController extends PublicController
{
    public $layout = false;

    /**
     * 我加入的政企通讯录
     */
    public function actionMyenterprisein()
    {
        $this->check_key();
        $user = $this->check_user();

        $connection = Yii::app()->db;
        $asql = "select contact_id,sort,id from enterprise_member where member_id = {$user->id} order by sort DESC";
        $command = $connection->createCommand($asql);
        $enterprisein = $command->queryAll();
        $eid = array();
        foreach ($enterprisein as $key => $val) {
            $eid[] = $val['contact_id'];
            if ($val['sort'] == 0) {
                EnterpriseMember::model()->updateAll(array("sort" => ($key + 1)), "id={$val['id']}");
            }
        }
        $enterpriseall = array();
        if ($eid) {
            $sql = "select a.id,a.name,a.short_name,a.member_id,a.bulletin,a.number,a.type,
            a.status,a.created_time,a.origin, b.sort,b.id as enterprise_id
            from enterprise as a left join enterprise_member as b on a.id=b.contact_id
            where a.id in (" . implode(",", $eid) . ") and b.member_id = {$user->id}
            order by b.sort asc";//and status = 0
            $command = $connection->createCommand($sql);
            $enterprise = $command->queryAll();

            if ($enterprise) {
                foreach ($enterprise as $value) {
                    if($value['type']==3){
                        $type = "百姓网";
                    }elseif($value['type'] == 2){
                        $type = "虚拟";
                    }elseif($value['type'] == 1){
                        $type = "企业";
                    }
                    $enterp = array(
                        "id" => $value['id'],
                        'enterprise_id' => $value['enterprise_id'],
                        "name" => $value['name'],
                        "short_name" => $value['short_name'],
                        "member_id" => $value['member_id'],
                        "number" => $value['number'],
                        "type" => $value['type'],
                        "tag" => $type,
                        "status" => $value['status'],
                        "created_time" => $value['created_time'],
                        "sort" => $value['sort'],
                        "origin" => $value['origin'],
                    );
                    $enterpriseall[] = $enterp;
                }
            }
        }


        $result['ret_num'] = 0;
        $result['ret_msg'] = '操作成功';
        $result['enterprise_list'] = $enterpriseall;
        echo json_encode($result);

    }

    /*
	 * 我的政企通讯录排序sort
	 * 涉及enterprise_member
	 * sort值：id;index|id;index
	 */
    public function actionSortenterprise()
    {
        $this->check_key();
        $user = $this->check_user();
        $sort = Frame::getStringFromRequest('sort');
        if (empty($sort)) {
            $result['ret_num'] = 100;
            $result['ret_msg'] = '缺少参数';
            echo json_encode($result);
            die();
        }
        $tpl_sort = explode("|", $sort);
        $enter_member_arr = array();
        foreach ($tpl_sort as $v) {
            $tpl_en = explode(";", $v);
            $enter_member_arr[] = $tpl_en[0];
        }

        //传入参数是否是自己所在的政企通讯录中
        $enterprise_info = EnterpriseMember::model()->count("member_id={$user->id} and id in (" . implode(",", $enter_member_arr) . ")");
        if (($enterprise_info) != count($tpl_sort)) {
            $result['ret_num'] = 400;
            $result['ret_msg'] = '参数错误';
            echo json_encode($result);
            die();
        }

        //保存
        foreach ($tpl_sort as $k => $v) {
            $tpl = array();
            $tpl = explode(";", $v);
            EnterpriseMember::model()->updateAll(array("sort" => $tpl[1]), "id={$tpl[0]} and member_id={$user->id}");
        }
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        echo json_encode($result);
    }

    /**
     * 我的政企通讯录
     */
    public function actionMyenterprise()
    {
        $this->check_key();
        $user = $this->check_user();
        $connection = Yii::app()->db;
        $sql = "select id,name,member_id,number,type,created_time  from enterprise where member_id = {$user->id} and status = 0 order by created_time desc limit 50";
        $command = $connection->createCommand($sql);
        $enterprise = $command->queryAll();
        $enterpriseall = array();
        if ($enterprise) {
            foreach ($enterprise as $value) {
                if ($value['type'] == 2) {
                    $type = "虚拟通讯录";
                } else {
                    $type = "企业通讯录";
                }
                $enterp = array(
                    "id" => $value['id'],
                    "name" => $value['name'],
                    "member_id" => $value['member_id'],
                    "number" => $value['number'],
                    "type" => $type,
                    "created_time" => $value['created_time'],
                );
                $enterpriseall[] = $enterp;
            }
        }

        $result['ret_num'] = 0;
        $result['ret_msg'] = '操作成功';
        $result['enterprise_list'] = $enterpriseall;
        echo json_encode($result);

    }

    /**
     * 政企通讯录查找
     */
    public function actionSearch()
    {
        $this->check_key();
        $user = $this->check_user();
        $keyword = Frame::getStringFromRequest('keyword');
        $connection = Yii::app()->db;

        if ($keyword) {
            $sql = "select a.id,a.name,a.max_num,a.short_name,a.member_id,a.number,a.type,a.created_time,a.origin,a.description,b.enterprise_apply from enterprise as a
            left join enterprise_role as b on a.id=b.enterprise_id  where a.name like '%{$keyword}%' and a.status = 0 order by a.created_time desc limit 50";
        } else {
            $sql = "select a.id,a.name,a.max_num,a.short_name,a.member_id,a.number,a.type,a.created_time,a.origin,a.description,b.enterprise_apply from enterprise as a
            left join enterprise_role as b on a.id=b.enterprise_id  where a.status = 0 order by a.created_time desc limit 50";
        }
        $command = $connection->createCommand($sql);
        $enterprise = $command->queryAll();
        //我加入的政企通讯录
        $asql = "select contact_id from enterprise_member where member_id = {$user->id}";
        $command = $connection->createCommand($asql);
        $enterprisein = $command->queryAll();
        $eid = array();
        foreach ($enterprisein as $val) {
            $eid[] = $val['contact_id'];
        }

        $enterpriseall = array();
        if ($enterprise) {
            foreach ($enterprise as $value) {
                if (in_array($value['id'], $eid)) {
                    $in = 1;
                } else {
                    $in = 0;
                }
                if ($value['type'] == 2) {
                    $tag = "虚拟";
                } else if ($value['type'] == 3) {
                    $tag = "百姓网";
                } else if ($value['type'] == 1) {
                    $tag = "企业";
                }
                $enterp = array(
                    "id" => $value['id'],
                    "name" => $value['name'],
                    "short_name" => $value['short_name'],
                    "member_id" => $value['member_id'],
                    "number" => $value['number'],
                    "max_num" => $value['max_num'],
                    "in" => $in,
                    "type" => $value['type'],
                    "origin" => $value['origin'],
                    "created_time" => $value['created_time'],
                    "tag" => $tag,
                    "enterprise_apply" => $value['enterprise_apply']? $value['enterprise_apply']:1,
                    "description" => $value['description']? $value['description']:"",
                );
                $enterpriseall[] = $enterp;
            }
        }

        $result['ret_num'] = 0;
        $result['ret_msg'] = '操作成功';
        $result['enterprise_list'] = $enterpriseall;
        echo json_encode($result);
    }

    /**
     * 新建政企通讯录select a.phone,b.name,a.contact_info_id from  benben.group_contact_phone a left join benben.group_contact_info b
     * on a.contact_info_id=b.id
     * where a.contact_info_id in (select contact_info_id from benben.group_contact_phone where phone='13333333333')
     */
    public function actionAdd()
    {
        $this->check_key();
        $name = Frame::getStringFromRequest('name');
        $shortName = Frame::getStringFromRequest('short_name');
        $type = Frame::getIntFromRequest('type');
        $province = Frame::getIntFromRequest('province');
        $city = Frame::getIntFromRequest('city');
        $area = Frame::getIntFromRequest('area');
        $street = Frame::getIntFromRequest('street');
        $description = Frame::getStringFromRequest('description');
        $short_phone = Frame::getStringFromRequest('short_phone');
        if ($type == 2) {
            if (!$short_phone) {
                $result['ret_num'] = 505;
                $result['ret_msg'] = '短号为空';
                echo json_encode($result);
                die();
            }
        }
        if (empty($name)) {
            $result['ret_num'] = 501;
            $result['ret_msg'] = '政企通讯录名称为空';
            echo json_encode($result);
            die();
        }
        $user = $this->check_user();
        $connection = Yii::app()->db;
        // $asql = "select count(*) count from enterprise_member where member_id = {$user->id}";
// 		$asql = "select count(a.contact_id) count from enterprise_member as a left join enterprise as b on a.contact_id = b.id where b.id > 0 and a.member_id = {$user->id}";
// 		$command = $connection->createCommand($asql);
// 		$count = $command->queryAll();				
// 		if($count[0]['count'] >= 6){
// 			$result['ret_num'] = 5203;
// 			$result['ret_msg'] = '您已加入6个政企通讯录';
// 			echo json_encode( $result );
// 			die();
// 		}

        $ename = Enterprise::model()->find("name = '{$name}'");
        if ($ename) {
            $result['ret_num'] = 5201;
            $result['ret_msg'] = '政企通讯录名称已存在';
            echo json_encode($result);
            die();
        }
        if (empty($province)) {
            $result['ret_num'] = 5201;
            $result['ret_msg'] = '请先选择所在地区';
            echo json_encode($result);
            die();
        }
        $enterprise_info = new Enterprise();
        $enterprise_info->name = $name;
        $enterprise_info->short_name = $shortName;
        $enterprise_info->member_id = $user->id;
        $enterprise_info->type = $type;
        $enterprise_info->province = $province;
        $enterprise_info->city = $city;
        $enterprise_info->area = $area;
        $enterprise_info->street = $street;
        $enterprise_info->description = $description;
        $enterprise_info->number = 1;
        $enterprise_info->max_num = MAX_COMPANY;
        $enterprise_info->status = 0;
        $enterprise_info->created_time = time();
        $enterprise_info->short_length = strlen($short_phone);
        if ($enterprise_info->save()) {
            //将自己加入通讯录成员
            $pinyin = new tpinyin();
            $con = new EnterpriseMember();
            $con->contact_id = $enterprise_info->id;
            $con->member_id = $user->id;
            $name = $user->name;
            if (!$name) {
                $name = $user->nick_name;
            }
            $con->name = $name;
            $con->pinyin = strtoupper($pinyin->str2sort($name));
            $con->allpinyin = strtoupper($pinyin->str2py($name));
            $con->phone = $user->phone;
            $con->short_phone = $short_phone;
            $con->created_time = time();
            $con->save();

            $this->addIntegral($user->id, 4);
            $result['ret_num'] = 0;
            $result['ret_msg'] = '操作成功';
            $result['enterprise_info'] = array(
                "id" => $enterprise_info->id,
                "name" => $enterprise_info->name,
                "short_name" => $enterprise_info->short_name,
                "type" => $enterprise_info->type,
                "province" => $enterprise_info->province,
                "city" => $enterprise_info->city,
                "area" => $enterprise_info->area,
                "street" => $enterprise_info->street,
                "description" => $enterprise_info->description,
                "status" => $enterprise_info->status,
                "number" => $enterprise_info->number,
                "max_num" => $enterprise_info->max_num,
                "created_time" => $enterprise_info->created_time
            );
        } else {
            $result['ret_num'] = 502;
            $result['ret_msg'] = '新建通讯录失败';
        }
        echo json_encode($result);

    }

    /**
     * 查看政企通讯录成员
     */
    public function actionMember()
    {
        $maxShow = 50;
        $this->check_key();
        $user = $this->check_user();
        $enterpriseid = Frame::getIntFromRequest('enterpriseid');
        $keyword = Frame::getStringFromRequest('keyword');
        if (empty($enterpriseid)) {
            $result['ret_num'] = 503;
            $result['ret_msg'] = '通讯录ID为空';
            echo json_encode($result);
            die();
        }
        $enterprise = Enterprise::model()->findByPk($enterpriseid);
        if (empty($enterprise)) {
            $result['ret_num'] = 504;
            $result['ret_msg'] = '通讯录ID不存在';
            echo json_encode($result);
            die ();
        }
        $connection = Yii::app()->db;
        $enterpriseType = $enterprise['type'];
        $origin = $enterprise['origin'];
        //是否已在通讯录
        $my = EnterpriseMember::model()->find("contact_id = {$enterpriseid} and member_id = {$user->id}");
        $firstin = $my['firstin'];
        $name = $my['name'];
        $my->firstin = 0;
        $my->update();
        if (empty($my)) {
            $result['ret_num'] = 0;
            $result['ret_msg'] = '操作成功';
            echo json_encode($result);
            die();
        }
        $pinyin = new tpinyin();
        //查找是否在显示记录表中有记录，如果是第一次，会没有记录
        $logResult = EnterpriseDisplayMemberLog::model()->find("enterprise_id = {$enterpriseid} and member_id = {$user->id} order by created_time Desc");

        $displayList = EnterpriseDisplayMember::model()->findAll(array('select' => '*', 'condition' => "enterprise_id = {$enterpriseid} and user_id = {$user->id} and is_common=0"));
        //首次进入，或者百姓网每8h同步一次
        if (empty($logResult) || (time() - $logResult['created_time'] >= 28800 && $origin == 2 && $enterpriseType == 2)) {
            //虚拟通讯录
            if ($enterpriseType == 2) {
                if ($origin == 1) {
                    //查看当前通讯录中的所有用户
                    $enterpriseMemberList = EnterpriseMember::model()->findAll(array('select' => '*', 'condition' => "contact_id = {$enterpriseid}"));

                    $enterpriseMemberInfo = array();
                    if (count($enterpriseMemberList) > 0) {
                        foreach ($enterpriseMemberList as $key => $value) {
                            if ($value['short_phone']) {
                                $enterpriseMemberInfo[$value['short_phone']] = array(
                                    'id' => $value['id'],
                                    'member_id' => $value['member_id'],
                                    'short_phone' => $this->eraseNull($value['short_phone']),
                                    'phone' => ($enterpriseType == 2) ? "" : $this->eraseNull($value['phone']),
                                    'name' => $value['name'],
                                    'pinyin' => strtoupper($pinyin->str2sort($value['name'])),
                                    'allpinyin' => strtoupper($pinyin->str2py($value['name']))
                                );
                            }
                        }
                    }

                    $connection = Yii::app()->db;

                    // 查找通讯录,通讯录号码跟政企短号取交集，成为常用联系人
                    $friendPhone = array();
                    $sql1 = "select p.phone,p.is_baixing from group_contact_phone p left join group_contact_info g on p.contact_info_id=g.id  where g.member_id = {$user->id}";
                    $command = $connection->createCommand($sql1);
                    $friendQuery = $command->queryAll();
                    if (count($friendQuery) > 0) {
                        foreach ($friendQuery as $key => $value) {
                            if (mb_strlen($value['phone']) <= 6) {
                                $phone_string = $value['phone'];
                            } else {
                                $phone_string = $value['is_baixing'];
                            }

                            if ($phone_string) {//手机短号或百姓网号
                                if (isset($enterpriseMemberInfo[$phone_string])) {
                                    $friendPhone[] = $enterpriseMemberInfo[$phone_string];
                                    unset($enterpriseMemberInfo[$phone_string]);
                                }
                            }
                        }
                    }

                    $filter = 1;

                    if (count($friendPhone) < $maxShow) {
                        $filter = 0;
                    }


                    //如果数据少于50条，则将显示联系人数据写入数据库
                    if ($filter == 0) {
                        $insertArray = array();
                        if (count($friendPhone) > 0) {
                            foreach ($friendPhone as $key => $value) {
                                $item_values = '(' . $user->id . ', ' . $value['id'] . ', ' . $enterpriseid . ' )';
                                if (!in_array($item_values, $insertArray)) {
                                    $insertArray[] = $item_values;
                                }
                            }
                        }
                        if (count($insertArray) > 0) {
                            $insertSql = "insert into enterprise_display_member(user_id, member_id, enterprise_id) values " . implode(",", $insertArray);
                            $command = $connection->createCommand($insertSql);
                            $result1 = $command->execute();
                        }
                        $enterpriseDisplayMemberLog = new EnterpriseDisplayMemberLog();
                        $enterpriseDisplayMemberLog->member_id = $user->id;
                        $enterpriseDisplayMemberLog->enterprise_id = $enterpriseid;
                        $enterpriseDisplayMemberLog->created_time = time();
                        $enterpriseDisplayMemberLog->save();
                    }
                    $returnArray[] = array('id' => 0, 'groupname' => '未分组', 'member_info' => $friendPhone, 'number' => count($friendPhone));

                    //总人数
                    $result['common_count'] = count($insertArray);
                    $result['all_count'] = count($enterpriseMemberList);

                    $result['ret_num'] = 0;
                    $result['ret_msg'] = '操作成功';
                    $result['filter'] = $filter;
                    $result['max_show'] = $maxShow;
                    $result['member_ginfo'] = $returnArray;
                    $result['firstin'] = $firstin;
                    $result['name'] = $name;
                    echo json_encode($result);
                }

                if ($origin == 2) {
                    //取出我通讯录中所有有号码的联系人
                    $friendPhone = array();
                    $sql1 = "select p.phone from group_contact_phone p left join group_contact_info g on p.contact_info_id=g.id  where g.member_id = {$user->id} and LENGTH(p.phone)>=11";
                    $command = $connection->createCommand($sql1);
                    $friendQuery = $command->queryAll();

                    if (count($friendQuery) > 0) {
                        foreach ($friendQuery as $key => $value) {
                            $phone_string = $value['phone'];
                            if (mb_strlen($phone_string) >= 11) {//手机长号
                                $phone_tpl[] = $phone_string;
                            }
                        }
                    }


                    //检查更新个人百姓网数据,非首次进入则更新数据
                    if (!empty($logResult)) {
                        //非首次进入，更新百姓网数据
                        $logResult->created_time = time();
                        if ($logResult->update()) {
                            $myAllList = EnterpriseDisplayMember::model()->findAll(array('select' => '*', 'condition' => "enterprise_id = {$enterpriseid} and user_id = {$user->id}"));
                            if (count($myAllList)) {
                                foreach ($myAllList as $kk => $vv) {
                                    $tpl_member[] = $vv['member_id'];
                                }
                                $sql_et = "select * from enterprise_member where contact_id={$enterpriseid} and phone!='{$my['phone']}' and phone in (" . implode(",", $phone_tpl) . ") and id not in (" . implode(",", $tpl_member) . ")";
                                $command = $connection->createCommand($sql_et);
                                $enterpriseMemberList_tpl = $command->queryAll();
                            } else {
                                $sql_et = "select * from enterprise_member where contact_id={$enterpriseid} and phone!='{$my['phone']}' and phone in (" . implode(",", $phone_tpl) . ")";
                                $command = $connection->createCommand($sql_et);
                                $enterpriseMemberList_tpl = $command->queryAll();
                            }
                            $more = $maxShow - count($displayList);//剩余可加人数
                            if ($more > 0) {
                                //加人
                                $enterpriseMemberInfo_tpl = array();
                                if (count($enterpriseMemberList_tpl) > 0) {
                                    foreach ($enterpriseMemberList_tpl as $key => $value) {
                                        if ($value['phone']) {
                                            $enterpriseMemberInfo_tpl[$value['phone']] = array(
                                                'id' => $value['id'],
                                                'member_id' => $value['member_id'],
                                                'short_phone' => $this->eraseNull($value['short_phone']),
                                                'phone' => ($enterpriseType == 2) ? "" : $this->eraseNull($value['phone']),
                                                'name' => $value['name'],
                                                'pinyin' => strtoupper($pinyin->str2sort($value['name'])),
                                                'allpinyin' => strtoupper($pinyin->str2py($value['name']))
                                            );
                                        }
                                    }
                                }

                                if (count($friendQuery) > 0) {
                                    foreach ($friendQuery as $key => $value) {
                                        $phone_string = $value['phone'];
                                        if (mb_strlen($phone_string) >= 11) {//手机长号
                                            if (isset($enterpriseMemberInfo_tpl[$value['phone']])) {
                                                $friendPhone[] = $enterpriseMemberInfo_tpl[$value['phone']];
                                                unset($enterpriseMemberInfo_tpl[$value['phone']]);
                                            }
                                        }
                                    }
                                }

                                $insertArray = array();
                                if (count($friendPhone) > 0) {
                                    foreach ($friendPhone as $key => $value) {
                                        if ($more > 0) {
                                            $item_values = '(' . $user->id . ', ' . $value['id'] . ', ' . $enterpriseid . ' )';
                                            if (!in_array($item_values, $insertArray)) {
                                                $insertArray[] = $item_values;
                                            }
                                            $more = $more - 1;
                                        } else {
                                            break;
                                        }
                                    }
                                }
                                if (count($insertArray) > 0) {
                                    $insertSql = "insert into enterprise_display_member(user_id, member_id, enterprise_id) values " . implode(",", $insertArray);
                                    $command = $connection->createCommand($insertSql);
                                    $result1 = $command->execute();
                                }

                            }

                            //查找分组
                            $displayList = EnterpriseDisplayMember::model()->findAll(array('select' => '*', 'condition' => "enterprise_id = {$enterpriseid} and user_id = {$user->id} and is_common=0"));
                            $groupList = EnterpriseGroup::model()->findAll(array('select' => '*', 'condition' => "enterprise_id = {$enterpriseid} and member_id = {$my->id}"));
                            $displayListInfo = array();
                            $enterpriseMemberInfo = array();
                            $memberIds = array();
                            if (count($displayList) > 0) {
                                foreach ($displayList as $value) {
                                    $displayListInfo[$value['group_id']][] = array('member_id' => $value['member_id'], 'is_common' => $value['is_common']);
                                    $memberIds[] = $value['member_id'];
                                }
                                $enterpriseMemberList = EnterpriseMember::model()->findAll(array('select' => '*', 'condition' => "id in (" . implode(",", $memberIds) . ")"));
                                if (count($enterpriseMemberList) > 0) {
                                    foreach ($enterpriseMemberList as $key => $value) {
                                        $enterpriseMemberInfo[$value['id']] = array(
                                            'id' => $value['id'],
                                            'member_id' => $value['member_id'],
                                            'short_phone' => $this->eraseNull($value['short_phone']),
                                            'phone' => $this->eraseNull($value['phone']),
                                            'name' => $value['name'],
                                            'pinyin' => strtoupper($pinyin->str2sort($value['name'])),
                                            'allpinyin' => strtoupper($pinyin->str2py($value['name']))
                                        );
                                    }
                                }
                            }

                            $returnArray = array();
                            if (count($groupList) > 0) {
                                foreach ($groupList as $value) {
                                    $group_member = array();
                                    if (isset($displayListInfo[$value['id']])) {
                                        $existMember = $displayListInfo[$value['id']];
                                        foreach ($existMember as $each) {
                                            $eachId = $each['member_id'];
                                            if (isset($enterpriseMemberInfo[$eachId])) {
                                                $currentInfo = $enterpriseMemberInfo[$eachId];
                                                $group_member[] = array(
                                                    'id' => $currentInfo['id'],
                                                    'member_id' => $currentInfo['member_id'],
                                                    'short_phone' => $currentInfo['short_phone'],
                                                    'phone' => ($enterpriseType == 2) ? "" : $currentInfo['phone'],
                                                    'name' => $currentInfo['name'],
                                                    'pinyin' => $currentInfo['pinyin'],
                                                    'allpinyin' => $currentInfo['allpinyin']
                                                );
                                            }
                                        }
                                        unset($displayListInfo[$value['id']]);
                                    }
                                    $returnArray[] = array('id' => $value['id'], 'groupname' => $value['groupname'], 'member_info' => $group_member, 'number' => count($group_member), 'sort' => $value['sort']);
                                }
                            }
                            if (isset($displayListInfo[0])) {
                                $existMember = $displayListInfo[0];
                                $group_member = array();
                                foreach ($existMember as $each) {
                                    $eachId = $each['member_id'];
                                    if (isset($enterpriseMemberInfo[$eachId])) {
                                        $currentInfo = $enterpriseMemberInfo[$eachId];
                                        $group_member[] = array(
                                            'id' => $currentInfo['id'],
                                            'member_id' => $currentInfo['member_id'],
                                            'short_phone' => $currentInfo['short_phone'],
                                            'phone' => ($enterpriseType == 2) ? "" : $currentInfo['phone'],
                                            'name' => $currentInfo['name'],
                                            'pinyin' => $currentInfo['pinyin'],
                                            'allpinyin' => $currentInfo['allpinyin']
                                        );
                                    }
                                }
                                $returnArray[] = array('id' => 0, 'groupname' => '未分组', 'member_info' => $group_member, 'number' => count($group_member));
                            } else {
                                $returnArray[] = array('id' => 0, 'groupname' => '未分组', 'member_info' => array(), 'number' => 0);
                            }
                            //总人数
                            $all_count = EnterpriseMember::model()->count("contact_id = {$enterpriseid}");
                            $result['common_count'] = count($enterpriseMemberList);
                            $result['all_count'] = $all_count;

                            $result['ret_num'] = 0;
                            $result['ret_msg'] = '操作成功';
                            $result['filter'] = 0;
                            $result['member_ginfo'] = $returnArray;
                            $result['max_show'] = $maxShow;
                            $result['firstin'] = $firstin;
                            $result['name'] = $name;
                            echo json_encode($result);


                        }
                    } else {
                        //首次进入
                        $sql_e = "select * from enterprise_member where contact_id={$enterpriseid} and phone!='{$my['phone']}' and phone in (" . implode(",", $phone_tpl) . ")";
                        $command = $connection->createCommand($sql_e);
                        $enterpriseMemberList = $command->queryAll();

                        $enterpriseMemberInfo = array();
                        if (count($enterpriseMemberList) > 0) {
                            foreach ($enterpriseMemberList as $key => $value) {
                                if ($value['phone']) {
                                    $enterpriseMemberInfo[$value['phone']] = array(
                                        'id' => $value['id'],
                                        'member_id' => $value['member_id'],
                                        'short_phone' => $this->eraseNull($value['short_phone']),
                                        'phone' => ($enterpriseType == 2) ? "" : $this->eraseNull($value['phone']),
                                        'name' => $value['name'],
                                        'pinyin' => strtoupper($pinyin->str2sort($value['name'])),
                                        'allpinyin' => strtoupper($pinyin->str2py($value['name']))
                                    );
                                }
                            }
                        }

                        if (count($friendQuery) > 0) {
                            foreach ($friendQuery as $key => $value) {
                                $phone_string = $value['phone'];
                                if (mb_strlen($phone_string) >= 11) {//手机长号
                                    if (isset($enterpriseMemberInfo[$value['phone']])) {
                                        $friendPhone[] = $enterpriseMemberInfo[$value['phone']];
                                        unset($enterpriseMemberInfo[$value['phone']]);
                                    }
                                }
                            }
                        }

                        $filter = 1;

                        if (count($friendPhone) < $maxShow) {
                            $filter = 0;
                        }


                        //如果数据少于50条，则将显示联系人数据写入数据库
                        if ($filter == 0) {
                            $insertArray = array();
                            if (count($friendPhone) > 0) {
                                foreach ($friendPhone as $key => $value) {
                                    $item_values = '(' . $user->id . ', ' . $value['id'] . ', ' . $enterpriseid . ' )';
                                    if (!in_array($item_values, $insertArray)) {
                                        $insertArray[] = $item_values;
                                    }
                                }
                            }
                            if (count($insertArray) > 0) {
                                $insertSql = "insert into enterprise_display_member(user_id, member_id, enterprise_id) values " . implode(",", $insertArray);
                                $command = $connection->createCommand($insertSql);
                                $result1 = $command->execute();
                            }

                            $enterpriseDisplayMemberLog = new EnterpriseDisplayMemberLog();
                            $enterpriseDisplayMemberLog->member_id = $user->id;
                            $enterpriseDisplayMemberLog->enterprise_id = $enterpriseid;
                            $enterpriseDisplayMemberLog->created_time = time();
                            $enterpriseDisplayMemberLog->save();
                        }

                        $returnArray[] = array('id' => 0, 'groupname' => '未分组', 'member_info' => $friendPhone, 'number' => count($friendPhone));

                        //总人数
                        $result['common_count'] = count($insertArray);
                        $result['all_count'] = count($enterpriseMemberList);

                        $result['ret_num'] = 0;
                        $result['ret_msg'] = '操作成功';
                        $result['filter'] = $filter;
                        $result['max_show'] = $maxShow;
                        $result['member_ginfo'] = $returnArray;
                        $result['firstin'] = $firstin;
                        $result['name'] = $name;
                        echo json_encode($result);
                    }
                }
            } else {
                //查看当前通讯录中的所有用户
                $enterpriseMemberList = EnterpriseMember::model()->findAll(array('select' => '*', 'condition' => "contact_id = {$enterpriseid}"));

                $enterpriseMemberInfo = array();
                if (count($enterpriseMemberList) > 0) {
                    //如果是犇犇用户，根据犇犇用户id去取用户数据
                    $benbenId = array();
                    foreach ($enterpriseMemberList as $key => $value) {
                        if ($value['member_id']) {
                            $benbenId[] = $value['member_id'];
                            $enterpriseMemberInfo[$value['member_id']] = array(
                                'id' => $value['id'],
                                'member_id' => $value['member_id'],
                                'short_phone' => $this->eraseNull($value['short_phone']),
                                'phone' => $this->eraseNull($value['phone']),
                                'name' => $value['name'],
                                'pinyin' => strtoupper($pinyin->str2sort($value['name'])),
                                'allpinyin' => strtoupper($pinyin->str2py($value['name']))
                            );
                        } else {
                            $enterpriseMemberInfo[$value['phone']] = array(
                                'id' => $value['id'],
                                'member_id' => $value['member_id'],
                                'short_phone' => $this->eraseNull($value['short_phone']),
                                'phone' => $this->eraseNull($value['phone']),
                                'name' => $value['name'],
                                'pinyin' => strtoupper($pinyin->str2sort($value['name'])),
                                'allpinyin' => strtoupper($pinyin->str2py($value['name']))
                            );
                        }
                    }
                    if (count($benbenId) > 0) {
                        $benbenMember = Member::model()->findAll(array('select' => 'phone, name, id', 'condition' => "id in (" . implode(",", $benbenId) . ")"));
                        foreach ($benbenMember as $key => $value) {
                            $enterpriseMemberInfo[$value['phone']] = array(
                                'id' => $enterpriseMemberInfo[$value['id']]['id'],
                                'member_id' => $value['id'],
                                'short_phone' => $this->eraseNull($enterpriseMemberInfo[$value['id']]['short_phone']),
                                'phone' => ($enterpriseType == 2) ? "" : $this->eraseNull($enterpriseMemberInfo[$value['id']]['phone']),
                                'name' => $enterpriseMemberInfo[$value['id']]['name'],
                                'pinyin' => $enterpriseMemberInfo[$value['id']]['pinyin']
                            );
                            unset($enterpriseMemberInfo[$value['id']]);
                        }
                    }
                }

                $connection = Yii::app()->db;

                // 查找通讯录,通讯录号码跟政企长号取交集，成为常用联系人
                $friendPhone = array();
                $sql1 = "select p.phone from group_contact_phone p left join group_contact_info g on p.contact_info_id=g.id  where g.member_id = {$user->id} and LENGTH(p.phone)>=11";
                $command = $connection->createCommand($sql1);
                $friendQuery = $command->queryAll();
                if (count($friendQuery) > 0) {
                    foreach ($friendQuery as $key => $value) {
                        $phone_string = $value['phone'];
                        if (mb_strlen($phone_string) >= 11) {//手机长号
                            if (isset($enterpriseMemberInfo[$value['phone']])) {
                                $friendPhone[] = $enterpriseMemberInfo[$value['phone']];
                                unset($enterpriseMemberInfo[$value['phone']]);
                            }
                        }
                    }
                }

                $filter = 1;

                if (count($friendPhone) < $maxShow) {
                    $filter = 0;
                }
                // if (count($friendPhone) < $maxShow) {
                // 	$filter = 0;
                // 	$addMore = min(($maxShow - count($friendPhone)), count($enterpriseMemberInfo));
                // 	if ($addMore > 0) {
                // 		$index  = 0;
                // 		foreach ($enterpriseMemberInfo as $key => $value) {
                // 			$friendPhone[] = $value;
                // 			$index++;
                // 			if ($index >= $addMore) {
                // 				break;
                // 			}
                // 		}
                // 	}
                // }

                //如果数据少于50条，则将显示联系人数据写入数据库
                if ($filter == 0) {
                    $insertArray = array();
                    if (count($friendPhone) > 0) {
                        foreach ($friendPhone as $key => $value) {
                            $item_values = '(' . $user->id . ', ' . $value['id'] . ', ' . $enterpriseid . ' )';
                            if (!in_array($item_values, $insertArray)) {
                                $insertArray[] = $item_values;
                            }
                        }
                    }
                    if (count($insertArray) > 0) {
                        $insertSql = "insert into enterprise_display_member(user_id, member_id, enterprise_id) values " . implode(",", $insertArray);
                        $command = $connection->createCommand($insertSql);
                        $result1 = $command->execute();
                    }
                    $enterpriseDisplayMemberLog = new EnterpriseDisplayMemberLog();
                    $enterpriseDisplayMemberLog->member_id = $user->id;
                    $enterpriseDisplayMemberLog->enterprise_id = $enterpriseid;
                    $enterpriseDisplayMemberLog->created_time = time();
                    $enterpriseDisplayMemberLog->save();
                }
                $returnArray[] = array('id' => 0, 'groupname' => '未分组', 'member_info' => $friendPhone, 'number' => count($friendPhone));

                //总人数
                $result['common_count'] = count($insertArray);
                $result['all_count'] = count($enterpriseMemberList);

                $result['ret_num'] = 0;
                $result['ret_msg'] = '操作成功';
                $result['filter'] = $filter;
                $result['max_show'] = $maxShow;
                $result['member_ginfo'] = $returnArray;
                $result['firstin'] = $firstin;
                $result['name'] = $name;
                echo json_encode($result);
            }


        } else {
            //查找分组
            $groupList = EnterpriseGroup::model()->findAll(array('select' => '*', 'condition' => "enterprise_id = {$enterpriseid} and member_id = {$my->id}"));
            $displayListInfo = array();
            $enterpriseMemberInfo = array();
            $memberIds = array();
            if (count($displayList) > 0) {
                foreach ($displayList as $value) {
                    $displayListInfo[$value['group_id']][] = array('member_id' => $value['member_id'], 'is_common' => $value['is_common']);
                    $memberIds[] = $value['member_id'];
                }
                $enterpriseMemberList = EnterpriseMember::model()->findAll(array('select' => '*', 'condition' => "id in (" . implode(",", $memberIds) . ")"));
                if (count($enterpriseMemberList) > 0) {
                    foreach ($enterpriseMemberList as $key => $value) {
                        $enterpriseMemberInfo[$value['id']] = array(
                            'id' => $value['id'],
                            'member_id' => $value['member_id'],
                            'short_phone' => $this->eraseNull($value['short_phone']),
                            'phone' => $this->eraseNull($value['phone']),
                            'name' => $value['name'],
                            'pinyin' => strtoupper($pinyin->str2sort($value['name'])),
                            'allpinyin' => strtoupper($pinyin->str2py($value['name']))
                        );
                    }
                }
            }

            $returnArray = array();
            if (count($groupList) > 0) {
                foreach ($groupList as $value) {
                    $group_member = array();
                    if (isset($displayListInfo[$value['id']])) {
                        $existMember = $displayListInfo[$value['id']];
                        foreach ($existMember as $each) {
                            $eachId = $each['member_id'];
                            if (isset($enterpriseMemberInfo[$eachId])) {
                                $currentInfo = $enterpriseMemberInfo[$eachId];
                                $group_member[] = array(
                                    'id' => $currentInfo['id'],
                                    'member_id' => $currentInfo['member_id'],
                                    'short_phone' => $currentInfo['short_phone'],
                                    'phone' => ($enterpriseType == 2) ? "" : $currentInfo['phone'],
                                    'name' => $currentInfo['name'],
                                    'pinyin' => $currentInfo['pinyin'],
                                    'allpinyin' => $currentInfo['allpinyin']
                                );
                            }
                        }
                        unset($displayListInfo[$value['id']]);
                    }
                    $returnArray[] = array('id' => $value['id'], 'groupname' => $value['groupname'], 'member_info' => $group_member, 'number' => count($group_member), 'sort' => $value['sort']);
                }
            }
            if (isset($displayListInfo[0])) {
                $existMember = $displayListInfo[0];
                $group_member = array();
                foreach ($existMember as $each) {
                    $eachId = $each['member_id'];
                    if (isset($enterpriseMemberInfo[$eachId])) {
                        $currentInfo = $enterpriseMemberInfo[$eachId];
                        $group_member[] = array(
                            'id' => $currentInfo['id'],
                            'member_id' => $currentInfo['member_id'],
                            'short_phone' => $currentInfo['short_phone'],
                            'phone' => ($enterpriseType == 2) ? "" : $currentInfo['phone'],
                            'name' => $currentInfo['name'],
                            'pinyin' => $currentInfo['pinyin'],
                            'allpinyin' => $currentInfo['allpinyin']
                        );
                    }
                }
                $returnArray[] = array('id' => 0, 'groupname' => '未分组', 'member_info' => $group_member, 'number' => count($group_member));
            } else {
                $returnArray[] = array('id' => 0, 'groupname' => '未分组', 'member_info' => array(), 'number' => 0);
            }
            //总人数
            $all_count = EnterpriseDisplayMember::model()->count("enterprise_id = {$enterpriseid} and user_id={$user['id']} and is_common=0");
            $result['common_count'] = count($enterpriseMemberList);
            $result['all_count'] = $all_count;

            $result['ret_num'] = 0;
            $result['ret_msg'] = '操作成功';
            $result['filter'] = 0;
            $result['member_ginfo'] = $returnArray;
            $result['max_show'] = $maxShow;
            $result['firstin'] = $firstin;
            $result['name'] = $name;
            echo json_encode($result);

        }
    }

    /**
     * 查找政企通讯录成员
     */
    public function actionMemberSearch()
    {
        $maxShow = 50;
        $this->check_key();
        $user = $this->check_user();
        $enterpriseid = Frame::getIntFromRequest('enterpriseid');
        $keyword = Frame::getStringFromRequest('keyword');
        $enterprise = Enterprise::model()->findByPk($enterpriseid);
        //判断是否为后台创建的，才有分权限查看的可能性
        if(($enterprise['type']==1||$enterprise['type']==2)&&$enterprise['origin']==2){
            $is_back=1;
            $emInfo=EnterpriseMember::model()->find("contact_id={$enterpriseid} and member_id={$user['id']}");
            $readLevel=EnterpriseMemberManage::model()->find("member_id={$emInfo['id']}");
        }else{
            $is_back=0;
        }
        $maxCompany = $enterprise['max_num'];
        if (empty($enterprise)) {
            $result['ret_num'] = 504;
            $result['ret_msg'] = '通讯录ID不存在';
            echo json_encode($result);
            die ();
        }
        $enterpriseType = $enterprise['type'];
        $sql0 = "";
        if ($enterpriseType == 1) {
            $sql0 = "or a.phone like  '%{$keyword}%'";
        }
        $sql = "select a.id,a.contact_id,a.id as member_id,a.short_phone,a.remark_name, a.phone,
							a.name,a.created_time, b.nick_name,b.phone lphone
							 from enterprise_member a left join member b on a.member_id = b.id 
							where a.contact_id = {$enterpriseid} and (a.name like '%{$keyword}%' {$sql0} or a.short_phone like  '%{$keyword}%') order by a.id desc";
        $connection = Yii::app()->db;
        $command = $connection->createCommand($sql);
        $result = $command->queryAll();
        //判断搜索结果中，哪些已经是常用联系人
        $commonId = array();
        for ($i = 0; $i < count($result); $i++) {
            //阅读权限只能N+1级及以下
            if($readLevel) {
                $info = EnterpriseMemberManage::model()->find("member_id={$result[$i]['id']}");
                if($info['access_level']>$readLevel['access_level']+1){
                    unset($result[$i]);
                    continue;
                }
            }elseif($is_back){
                $info = EnterpriseMemberManage::model()->find("member_id={$result[$i]['id']}");
                if($info['access_level']>2){
                    unset($result[$i]);
                    continue;
                }
            }
            $commonId[] = $result[$i]['member_id'];
        }
        $inCommon = array();
        if (count($commonId) > 0) {

            $displayList = EnterpriseDisplayMember::model()->findAll(array('select' => 'member_id', 'condition' => "enterprise_id = {$enterpriseid} and user_id = {$user->id} and is_common=0 and member_id in (" . implode(",", $commonId) . ")"));
            if (count($displayList) > 0) {
                foreach ($displayList as $key => $value) {
                    $inCommon[] = $value['member_id'];
                }
            }
        }
        $returnArray = array();
        for ($i = 0; $i < count($result); $i++) {
            $common = 0;
            if (in_array($result[$i]['member_id'], $inCommon)) {
                $common = 1;
            }
            $returnArray[] = array('permit' => ($maxCompany - $enterprise['number']), 'id' => $result[$i]['id'], 'member_id' => $result[$i]['member_id'], 'short_phone' => $this->eraseNull($result[$i]['short_phone']), 'phone' => ($enterpriseType == 2) ? "" : $this->eraseNull($result[$i]['phone']), 'name' => $result[$i]['name'], 'common' => $common);
        }
        $return['ret_num'] = 0;
        $return['ret_msg'] = '操作成功';
        $return['member_ginfo'] = $returnArray;
        $return['max_show'] = $maxShow;
        echo json_encode($return);
    }

    /**
     * 查看政企通讯录成员我的
     */
    public function actionMymember()
    {
        $this->check_key();
        $user = $this->check_user();
        $enterpriseid = Frame::getIntFromRequest('enterpriseid');
        $keyword = Frame::getStringFromRequest('keyword');
        if (empty($enterpriseid)) {
            $result['ret_num'] = 503;
            $result['ret_msg'] = '通讯录ID为空';
            echo json_encode($result);
            die();
        }
        $enterprise = Enterprise::model()->findByPk($enterpriseid);
        if (empty($enterprise)) {
            $result['ret_num'] = 504;
            $result['ret_msg'] = '通讯录ID不存在';
            echo json_encode($result);
            die ();
        }
        //是否已在通讯录
        $my = EnterpriseMember::model()->find("contact_id = {$enterpriseid} and member_id = {$user->id}");
        if (empty($my)) {
            $result['ret_num'] = 0;
            $result['ret_msg'] = '操作成功';
            echo json_encode($result);
            die();
        }
        //查出常用联系人分组
        $groupname = md5($enterpriseid);
        $groupc = EnterpriseGroup::model()->find("groupname = '{$groupname}'");

        //查出分组
        $connection = Yii::app()->db;
        $sqla = "select id,groupname,created_time from enterprise_group where member_id = {$my->id} and groupname != '{$groupname}' order by id asc";
        $command = $connection->createCommand($sqla);
        $result0 = $command->queryAll();
        $result0[] = array("id" => 0, "groupname" => "未分组", "created_time" => time());//var_dump($result0);exit();
        $eid = "";
        foreach ($result0 as $va) {
            $eid .= $va['id'] . ",";
        }
        $eid = trim($eid);
        $eid = trim($eid, ',');

        //查出所有人
        $sql2 = "select a.id,a.contact_id,a.member_id,a.short_phone,a.remark_name, a.phone,a.name,a.created_time,
		b.nick_name,b.phone lphone from enterprise_member a left join member b on a.member_id = b.id
		where a.contact_id = {$enterpriseid} order by a.id desc";
        if ($keyword) {
            $sql2 = "select a.id,a.contact_id,a.member_id,a.short_phone,a.remark_name, a.phone,
		a.name,a.created_time, b.nick_name,b.phone lphone
		from enterprise_member a left join member b on a.member_id = b.id
		where a.contact_id = {$enterpriseid} and (a.name like '%{$keyword}%') order by a.id desc";
        }
        $command = $connection->createCommand($sql2);
        $result2 = $command->queryAll();//var_dump($result2);exit();
        //查出常用联系人分组成员
        $co_member = array();
        if ($groupc) {
            $sql = "select id,egroup_id,emember_id from enterprise_group_member  where egroup_id ={$groupc->id}";
            $command = $connection->createCommand($sql);
            $result3 = $command->queryAll();//var_dump($result3);
            foreach ($result3 as $val) {
                $co_member[] = $val['emember_id'];
            }
        }

        $all_member = array();
        $all_member1 = array();
        foreach ($result2 as $key => $va) {
            if (!$va['short_phone']) {
                $result2[$key]['short_phone'] = "";
            }
            if (!$va['name']) {
                $result2[$key]['name'] = $va['nick_name'];
            }
            if (!$va['phone']) {
                $result2[$key]['phone'] = $va['lphone'];
            }
            if ($groupc && (in_array($va['id'], $co_member))) {
                $result2[$key]['common'] = 1;
            } else {
                $result2[$key]['common'] = 0;
            }
            $all_member[$va['id']] = $va;
        }
        foreach ($result2 as $key => $va) {
            $all_member[$va['id']] = $va;
        }
        //var_dump($all_member);


        //查出分组下的人
        $sql = "select id,egroup_id,emember_id from enterprise_group_member  where egroup_id in ({$eid})";
        $command = $connection->createCommand($sql);
        $result1 = $command->queryAll();//var_dump($result1);

        $group_member = array();//var_dump($all_member);
        foreach ($result1 as $key => $va) {
            $group_member[$va['egroup_id']][] = $all_member[$va['emember_id']];
            unset($all_member[$va['emember_id']]);
        }
        foreach ($all_member as $v) {
            $all_member1[] = $v;
        }
        //var_dump($group_member);
        //$num = count($result1);
        $info = array();
        $ingroup = array();

        foreach ($result0 as $key => $va) {
            $result0[$key]['member_info'] = $group_member[$va['id']] ? $group_member[$va['id']] : array();
            if ($va['id'] == 0) {
                $result0[$key]['member_info'] = $all_member1 ? $all_member1 : array();
            }
            $result0[$key]['number'] = count($result0[$key]['member_info']);
        }
        //var_dump($result0);

        $result['ret_num'] = 0;
        $result['ret_msg'] = '操作成功';
        $result['member_ginfo'] = $result0;
        echo json_encode($result);

    }

    /**
     * 查看政企通讯录分组
     */
    public function actionEgroup()
    {
        $this->check_key();
        $user = $this->check_user();
        $enterpriseid = Frame::getIntFromRequest('enterpriseid');
        if (empty($enterpriseid)) {
            $result['ret_num'] = 503;
            $result['ret_msg'] = '通讯录ID为空';
            echo json_encode($result);
            die();
        }
        $enterprise = Enterprise::model()->findByPk($enterpriseid);
        if (empty($enterprise)) {
            $result['ret_num'] = 504;
            $result['ret_msg'] = '通讯录ID不存在';
            echo json_encode($result);
            die ();
        }

        //查出分组
        $my = EnterpriseMember::model()->find("contact_id = {$enterpriseid} and member_id = {$user->id}");
        $result0 = array();
        $connection = Yii::app()->db;
        if ($my) {
            $sqla = "select id,groupname,created_time,sort from enterprise_group where member_id = {$my->id} order by sort asc";
            $command = $connection->createCommand($sqla);
            $result0 = $command->queryAll();
        }

        //查出各分组里的总人数
        $sql = "select group_id, count(*) as c from enterprise_display_member where enterprise_id = {$enterpriseid} and is_common=0 and user_id = " . $user->id . " group by group_id";
        $command = $connection->createCommand($sql);
        $resultc = $command->queryAll();
        $group_num = array();
        $allmemberCount = 0;
        foreach ($resultc as $value) {
            $group_num[$value['group_id']] = $value['c'];
            $allmemberCount += $value['c'];
        }
        $totalNumber = 0;
        foreach ($result0 as $key => $va) {
            if ($va['sort'] == 0) {
                EnterpriseGroup::model()->updateAll(array("sort" => ($key + 1)), "id={$va['id']}");
            }
            $n = 0;
            if (isset($group_num[$va['id']])) {
                $n = $group_num[$va['id']];
            }
            $result0[$key]['num'] = $n;
            $result0[$key]['sort'] = $key + 1;
            $totalNumber += $n;
            $result0[$key]['all_num'] = $n . "/" . $allmemberCount;
        }
        $ungroupNumber = 0;
        if (isset($group_num[0])) {
            $ungroupNumber = $group_num[0];
        }
        $result0[] = array('id' => -1, 'groupname' => '未分组', 'num' => $ungroupNumber, 'all_num' => $ungroupNumber . "/" . $allmemberCount);

        $result['ret_num'] = 0;
        $result['ret_msg'] = '操作成功';
        $result['member_group'] = $result0;
        echo json_encode($result);

    }

    /*
	 * 通讯录分组排序
	 * 涉及enterprise_group
	 * sort:id;index|id;index
	 */
    public function actionSortegroup()
    {
        $this->check_key();
        $user = $this->check_user();
        $sort = Frame::getStringFromRequest('sort');
        $phoneid = Frame::getIntFromRequest('phoneid');
        if (empty($sort)) {
            $result['ret_num'] = 100;
            $result['ret_msg'] = '缺少参数';
            echo json_encode($result);
            die();
        }
        $tpl_sort = explode("|", $sort);
        $enter_group_arr = array();
        foreach ($tpl_sort as $v) {
            $tpl_en = explode(";", $v);
            $enter_group_arr[] = $tpl_en[0];
        }

        //传入参数是否是自己所在的政企通讯录中
        $enterprise_info = EnterpriseGroup::model()->count("member_id={$phoneid} and id in (" . implode(",", $enter_group_arr) . ")");
        if (($enterprise_info) != count($tpl_sort)) {
            $result['ret_num'] = 400;
            $result['ret_msg'] = '参数错误';
            echo json_encode($result);
            die();
        }

        //保存
        foreach ($tpl_sort as $k => $v) {
            $tpl = array();
            $tpl = explode(";", $v);
            EnterpriseGroup::model()->updateAll(array("sort" => $tpl[1]), "id={$tpl[0]} and member_id={$phoneid}");
        }
        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        echo json_encode($result);
    }

    /**
     * 查看政企通讯录分组成员
     */
    public function actionEgmember()
    {
        $this->check_key();
        $user = $this->check_user();
        $enterpriseid = Frame::getIntFromRequest('enterpriseid');
        $groupid = Frame::getIntFromRequest('groupid');
        $connection = Yii::app()->db;
        if ($groupid < 0) {
            $groupid = 0;
        }
        $sql = "select a.id,a.contact_id,a.member_id,a.short_phone,a.remark_name, a.phone,a.name,a.created_time,b.group_id as egroup_id,b.member_id as emember_id,c.nick_name,c.phone lphone from enterprise_display_member b left join enterprise_member a  on a.id = b.member_id left join member c on c.id = a.member_id where b.user_id={$user->id} and  b.group_id = {$groupid} and b.enterprise_id = " . $enterpriseid;
        $command = $connection->createCommand($sql);
        $result1 = $command->queryAll();
        $PinYin = new PYInitials('utf-8');
        foreach ($result1 as $key => $value) {
            if ($value['name']) {
                $result1[$key]['pinyin'] = strtoupper(substr($PinYin->getInitials($value['name']), 0, 1));
            } else {
                $result1[$key]['name'] = $value['nick_name'];
                $result1[$key]['pinyin'] = strtoupper(substr($PinYin->getInitials($value['nick_name']), 0, 1));
            }
            if (!$value['phone']) {
                $result1[$key]['phone'] = $value['lphone'];
            }
        }

        $result['ret_num'] = 0;
        $result['ret_msg'] = '操作成功';
        $result['member_info'] = $result1;
        echo json_encode($result);

    }

    /**
     * 查看政企通讯录所有成员
     */
    public function actionAllmember()
    {
        $maxShow = 50;
        $this->check_key();
        $user = $this->check_user();
        $enterpriseid = Frame::getIntFromRequest('enterpriseid');
        if (empty($enterpriseid)) {
            $result['ret_num'] = 503;
            $result['ret_msg'] = '通讯录ID为空';
            echo json_encode($result);
            die();
        }
        $enterprise = Enterprise::model()->findByPk($enterpriseid);
        if (empty($enterprise)) {
            $result['ret_num'] = 504;
            $result['ret_msg'] = '通讯录ID不存在';
            echo json_encode($result);
            die ();
        }
        $enterpriseType = $enterprise['type'];
        //是否已在通讯录
        $my = EnterpriseMember::model()->find("contact_id = {$enterpriseid} and member_id = {$user->id}");
        if (empty($my)) {
            $result['ret_num'] = 0;
            $result['ret_msg'] = '操作成功';
            echo json_encode($result);
            die();
        }
        //查找是否在显示记录表中有记录，如果是第一次，会没有记录
        $displayList = EnterpriseDisplayMember::model()->findAll(array('select' => '*', 'condition' => "enterprise_id = {$enterpriseid} and user_id = {$user->id} and is_common=0"));

        $PinYin = new PYInitials('utf-8');
        //查找分组
        $groupList = EnterpriseGroup::model()->findAll(array('select' => '*', 'condition' => "enterprise_id = {$enterpriseid} and member_id = {$my->id}"));
        $displayListInfo = array();
        $enterpriseMemberInfo = array();
        $memberIds = array();
        if (count($displayList) > 0) {
            foreach ($displayList as $value) {
                $displayListInfo[$value['group_id']][] = array('member_id' => $value['member_id'], 'is_common' => $value['is_common']);
                $memberIds[] = $value['member_id'];
            }
            $enterpriseMemberList = EnterpriseMember::model()->findAll(array('select' => '*', 'condition' => "id in (" . implode(",", $memberIds) . ")"));
            if (count($enterpriseMemberList) > 0) {
                foreach ($enterpriseMemberList as $key => $value) {
                    $pinyin = strtoupper(substr($PinYin->getInitials($value['name']), 0, 1));
                    $enterpriseMemberInfo[$value['id']] = array('id' => $value['id'], 'member_id' => $value['member_id'], 'short_phone' => $this->eraseNull($value['short_phone']), 'phone' => $this->eraseNull($value['phone']), 'name' => $value['name'], 'pinyin' => $pinyin);
                }
            }
        }
        $returnArray = array();
        if (count($groupList) > 0) {
            foreach ($groupList as $value) {
                $group_member = array();
                if (isset($displayListInfo[$value['id']])) {
                    $existMember = $displayListInfo[$value['id']];
                    foreach ($existMember as $each) {
                        $eachId = $each['member_id'];
                        if (isset($enterpriseMemberInfo[$eachId])) {
                            $currentInfo = $enterpriseMemberInfo[$eachId];
                            $pinyin = strtoupper(substr($PinYin->getInitials($currentInfo['name']), 0, 1));
                            $group_member[] = array('id' => $currentInfo['id'], 'member_id' => $currentInfo['member_id'], 'short_phone' => $currentInfo['short_phone'], 'phone' => $currentInfo['phone'], 'name' => $currentInfo['name'], 'pinyin' => $pinyin);
                        }
                    }
                    unset($displayListInfo[$value['id']]);
                }

                $returnArray[] = array('id' => $value['id'], 'groupname' => $value['groupname'], 'member_info' => $group_member, 'number' => count($group_member));
            }
        }
        if (isset($displayListInfo[0])) {
            $existMember = $displayListInfo[0];
            $group_member = array();
            foreach ($existMember as $each) {
                $eachId = $each['member_id'];
                if (isset($enterpriseMemberInfo[$eachId])) {
                    $currentInfo = $enterpriseMemberInfo[$eachId];
                    $pinyin = strtoupper(substr($PinYin->getInitials($currentInfo['name']), 0, 1));
                    $group_member[] = array('id' => $currentInfo['id'], 'member_id' => $currentInfo['member_id'], 'short_phone' => $currentInfo['short_phone'], 'phone' => $currentInfo['phone'], 'name' => $currentInfo['name'], 'pinyin' => $pinyin);
                }
            }
            $returnArray[] = array('id' => 0, 'groupname' => '未分组', 'member_info' => $group_member, 'number' => count($group_member));
        } else {
            $returnArray[] = array('id' => 0, 'groupname' => '未分组', 'member_info' => array(), 'number' => 0);
        }
        $result['ret_num'] = 0;
        $result['ret_msg'] = '操作成功';
        $result['filter'] = 0;
        $result['member_ginfo'] = $returnArray;
        $result['max_show'] = $maxShow;
        echo json_encode($result);


        // $this->check_key();
        // $user = $this->check_user();
        // $enterpriseid = Frame::getIntFromRequest('enterpriseid');
        // if(empty($enterpriseid)){
        // 	$result['ret_num'] = 503;
        // 	$result['ret_msg'] = '通讯录ID为空';
        // 	echo json_encode( $result );
        // 	die();
        // }
        // $enterprise = Enterprise::model()->findByPk($enterpriseid);
        // if(empty($enterprise)){
        // 	$result['ret_num'] = 504;
        // 	$result['ret_msg'] = '通讯录ID不存在';
        // 	echo json_encode( $result );
        // 	die ();
        // }
        // $connection = Yii::app()->db;
        // //查出常用联系人
        // $sql0 = "select member_id from enterprise_display_member where enterprise_id = {$enterpriseid}";
        // $command = $connection->createCommand($sql0);
        // $result0 = $command->queryAll();
        // $eid = array();
        // foreach ($result0 as $va){
        // 	$eid[] = $va['member_id'];
        // }

        // $sql = "select a.id,a.contact_id,a.member_id,a.short_phone, a.phone,a.name,a.created_time,c.nick_name from enterprise_member a  left join member c on c.id = a.member_id where a.contact_id = {$enterpriseid} ";
        // if($eid){
        // 	$sql .="and a.id in (".implode(",", $eid).")";
        // }
        // $sql .= "order by a.id desc";
        // $command = $connection->createCommand($sql);
        // $result1 = $command->queryAll();
        // $PinYin = new PYInitials('utf-8');
        // foreach ($result1 as $key=>$value){
        // 	if($value['name']){
        // 		$result1[$key]['pinyin'] = strtoupper(substr($PinYin->getInitials($value['name']),0,1));
        // 	}else{
        // 		$result1[$key]['name'] = $value['nick_name'];
        // 		$result1[$key]['pinyin'] = strtoupper(substr($PinYin->getInitials($value['nick_name']),0,1));
        // 	}
        // }

        // $result['ret_num'] = 0;
        // $result['ret_msg'] = '操作成功';	
        // $result['member_info'] = $result1;
        // echo json_encode( $result );

    }

    /**
     * 获取政企通讯录详情
     */
    public function actionDetail()
    {
        $this->check_key();
        $enterpriseid = Frame::getIntFromRequest('enterpriseid');
        $user = $this->check_user();
        if (empty($enterpriseid)) {
            $result['ret_num'] = 503;
            $result['ret_msg'] = '通讯录ID为空';
            echo json_encode($result);
            die();
        }
        $enterprise_info = Enterprise::model()->findByPk($enterpriseid);
        if (empty($enterprise_info)) {
            $result['ret_num'] = 504;
            $result['ret_msg'] = '通讯录ID不存在';
        } else {
            //$eid = $enterprise_info->member_id;
            $eid = $user->id;
            $re = EnterpriseMember::model()->find("contact_id = {$enterpriseid} and member_id = {$eid} ");
            //判断是否有权限将其他好友加入
            $addFriendAuth=EnterpriseRole::model()->find("enterprise_id={$enterpriseid}");
            $is_guard=0;//1表示允许管理员添加成员，0表示所有成员都可以添加
            $is_admin=0;//1表示是管理员，0表示不是管理员
            if($addFriendAuth){
                //查询是否是管理员
                $adminAuth=EnterpriseMemberManage::model()->find("member_id={$re['id']}");
                if($adminAuth){
                    $is_admin=$adminAuth['is_manage'];
                }else{
                    $is_admin=0;
                }

                if($addFriendAuth['member_add_other']==1){
                    $is_guard=1;
                }else{
                    $is_guard=0;
                }
            }else{
                $is_guard=0;
            }

            //判断是否是后台创建，显示公告
            $is_apply=ApplyRegister::model()->find("enterprise_id=".$enterpriseid);
            if($is_apply){
                $notice=EnterpriseNotice::model()->find("enterprise_id=".$enterpriseid." order by update_time Desc");
            }
            $phone = $user->phone;
            if ($re) {
                $short_phone = $re->short_phone ? $re->short_phone : "";
                $remark_name = $re->name ? $re->name : "";
                $phone = $re->phone ? $re->phone : $phone;
            }
            $pinfo = $this->pcinfo();
            $result['ret_num'] = 0;
            $result['ret_msg'] = '操作成功';
            $result['enterprise_info'] = array(
                "id" => $enterprise_info->id,
                "name" => $enterprise_info->name,
                "short_name" => $enterprise_info->short_name,
                "type" => $enterprise_info->type,
                "province" => $enterprise_info->province,
                "city" => $enterprise_info->city,
                "area" => $enterprise_info->area,
                "street" => $enterprise_info->street,
                "description" => $enterprise_info->description,
                "bulletin" => $notice?$notice->content:"",
                "update_time" => $notice?$notice->update_time:"",
                "status" => $enterprise_info->status,
                "number" => $enterprise_info->number,
                "ProCity" => $pinfo[0][$enterprise_info->province] . " " . $pinfo[1][$enterprise_info->city] . " " . $pinfo[2][$enterprise_info->area],
                "short_phone" => $short_phone,
                "phone" => $phone,
                "origin" => $enterprise_info->origin,
                "remark_name" => $remark_name,
                "created_time" => $enterprise_info->created_time,
                "short_length" => $enterprise_info->short_length,
                "firstin" => $re['firstin'],
                "is_guard"=>$is_guard,
                "is_admin"=>$is_admin,
            );
            //保证第一次弹出内容
            if ($re->firstin == 1) {
                $re->firstin = 0;
                $re->update();
            }
        }
        echo json_encode($result);
    }

    /**
     * 修改政企通讯录信息
     */
    public function actionEdit()
    {
        $this->check_key();
        $user = $this->check_user();

        $name = Frame::getStringFromRequest('name');
        $shortName = Frame::getStringFromRequest('short_name');
        $type = Frame::getIntFromRequest('type');
        $province = Frame::getIntFromRequest('province');
        $city = Frame::getIntFromRequest('city');
        $area = Frame::getIntFromRequest('area');
        $street = Frame::getIntFromRequest('street');
        $description = Frame::getStringFromRequest('description');
        $bulletin = Frame::getStringFromRequest('bulletin');
        $enterpriseid = Frame::getIntFromRequest('enterpriseid');
        if ($type == 2) {
            $short_phone = Frame::getStringFromRequest('short_phone');
            if (!$short_phone) {
                $result['ret_num'] = 505;
                $result['ret_msg'] = '短号为空';
                echo json_encode($result);
                die();
            }
        }
        if (empty($enterpriseid)) {
            $result['ret_num'] = 503;
            $result['ret_msg'] = '通讯录ID为空';
            echo json_encode($result);
            die();
        }
        $enterprise_info = Enterprise::model()->findByPk($enterpriseid);
        if (empty($enterprise_info)) {
            $result['ret_num'] = 504;
            $result['ret_msg'] = '通讯录ID不存在';
            echo json_encode($result);
            die();
        }

        if ($name) {
            $enterprise_info->name = $name;
        }
        if ($shortName) {
            $enterprise_info->short_name = $shortName;
        }
        if ($type) {
            $enterprise_info->type = $type;
        }
        if ($province) {
            $enterprise_info->province = $province;
        }
        if ($city) {
            $enterprise_info->city = $city;
        }
        if ($area) {
            $enterprise_info->area = $area;
        }
        if ($street) {
            $enterprise_info->street = $street;
        }
        if ($description) {
            $enterprise_info->description = $description;
        }
        if ($bulletin) {
            $applys=ApplyRegister::model()->find("enterprise_id=".$enterpriseid);
            $notice=new EnterpriseNotice();
            $notice->content=$bulletin;
            $notice->enterprise_id=$enterpriseid;
            $notice->update_time=time();
            $notice->created_time=time();
            if($applys) {
                $notice->apply_id = $applys->id;
            }
            $notice->save();
        }
        $enterprise_info->update();
        $result['ret_num'] = 0;
        $result['ret_msg'] = '操作成功';
        echo json_encode($result);
    }

    /**
     * 修改政企通讯录短号、备注名
     */
    public function actionEditphone()
    {
        $this->check_key();
        $user = $this->check_user();
        $enterpriseid = Frame::getIntFromRequest('enterprise_id');
        $shortphone = Frame::getStringFromRequest('short_phone');
        // $remarkname = Frame::getStringFromRequest('remark_name');
        $enterprise = Enterprise::model()->findByPk($enterpriseid);
        if (empty($enterprise)) {
            $result['ret_num'] = 1005;
            $result['ret_msg'] = '该政企通讯录不存在';
            echo json_encode($result);
            die ();
        }
        //如果是虚拟网，短号长度必须和创建人短号长度一致
        if ($enterprise['type'] == 2 && $shortphone) {
            $createdMember = EnterpriseMember::model()->find("contact_id = '{$enterpriseid}' and member_id = {$enterprise['member_id']}");
            if (strlen($createdMember['short_phone']) != strlen($shortphone)) {
                $result['ret_num'] = 1012;
                $result['ret_msg'] = '虚拟通讯录短号格式非法,请重新输入';
                echo json_encode($result);
                die ();
            }
        }
        //号码不能重复
        if ($shortphone) {
            $check_same = EnterpriseMember::model()->find("contact_id='{$enterpriseid}' and (phone='{$shortphone}' or short_phone='{$shortphone}')");
            if ($check_same) {
                $result['ret_num'] = 1013;
                $result['ret_msg'] = '号码已存在通讯录中,请重新输入';
                echo json_encode($result);
                die ();
            }
        }

        $info = EnterpriseMember::model()->find("contact_id = '{$enterpriseid}' and member_id = '{$user->id}'");
        if ($info) {
            if (empty($shortphone)) {
                if ($enterprise['type'] == 1) {
                    $info->short_phone = '';
                }
            } else {
                $info->short_phone = $shortphone;
            }
            $info->update();
            $result['ret_num'] = 0;
            $result['ret_msg'] = '操作成功';
        } else {
            $result['ret_num'] = 1065;
            $result['ret_msg'] = '未加入该政企通讯录';
        }
        echo json_encode($result);

    }

    //修改备注名称
    public function actionEditRemarkName()
    {
        $this->check_key();
        $user = $this->check_user();
        $enterpriseid = Frame::getIntFromRequest('enterprise_id');
        $remarkname = Frame::getStringFromRequest('remark_name');
        $enterprise = Enterprise::model()->findByPk($enterpriseid);
        if (empty($enterprise)) {
            $result['ret_num'] = 1005;
            $result['ret_msg'] = '该政企通讯录不存在';
            echo json_encode($result);
            die ();
        }
        // if(empty($remarkname)){
        // 	$result['ret_num'] = 1005;
        // 	$result['ret_msg'] = '通讯录名片不能为空';
        // 	echo json_encode( $result );
        // 	die ();
        // }

        $info = EnterpriseMember::model()->find("contact_id = '{$enterpriseid}' and member_id = '{$user->id}'");
        if ($info) {
            if ($remarkname) {
                $info->name = $remarkname;
                $info->firstin = 0;
            } else {
                $info->name = '';
                $info->firstin = 0;
            }
            $info->update();
            $result['ret_num'] = 0;
            $result['ret_msg'] = '操作成功';
            $result['firstin'] = 0;
        } else {
            $result['ret_num'] = 1065;
            $result['ret_msg'] = '未加入该政企通讯录';
        }
        echo json_encode($result);

    }

    /**
     * 加入政企通讯录-企业通讯录-废弃不用了
     */
    public function actionJoin()
    {
        $this->check_key();
        $user = $this->check_user();
        $enterpriseid = Frame::getIntFromRequest('enterprise_id');
        $contactid = Frame::getStringFromRequest('contact_id');
        $remark_name = Frame::getStringFromRequest('name');
        $shortphone = Frame::getStringFromRequest('short_phone');

        $enterprise = Enterprise::model()->findByPk($enterpriseid);
        $enterpriseType = $enterprise['type'];
        if (empty($enterprise)) {
            $result['ret_num'] = 1005;
            $result['ret_msg'] = '该政企通讯录不存在';
            echo json_encode($result);
            die ();
        }

        $contactArray = explode(",", $contactid);
        $remark_nameArray = explode("::", $remark_name);
        $contactRelateName = array();
        foreach ($remark_nameArray as $k => $e) {
            $contactRelateName[$contactArray[$k]] = $e;
        }
        //$shortphoneArray = explode(",", $shortphone);
        //如果是添加虚拟网用户，必须得要有短号
        if ($enterpriseType == 2) {
            $createdMember = EnterpriseMember::model()->find("contact_id = '{$enterpriseid}' and member_id = {$enterprise['member_id']}");
            if ($enterprise['short_length'] != strlen($shortphone)) {
                $result['ret_num'] = 1012;
                $result['ret_msg'] = '虚拟通讯录短号格式非法,请重新输入';
                echo json_encode($result);
                die ();
            }
        }
        //被动加入
        if ($contactArray[0]) {
            //添加手机通讯录成员到政企通讯录,支持多个用户
            //先查询传入的手机号是否是犇犇用户
            $benben = array();
            $notinen = array();
            $connection = Yii::app()->db;
            $sql = "select a.id,a.name,b.phone,b.is_benben,b.is_baixing from group_contact_info a inner join group_contact_phone b on a.id=b.contact_info_id where a.id in ({$contactid})";
            $command = $connection->createCommand($sql);
            $contactinfo = $command->queryAll();
            //var_dump($sql);exit();
            $all_phone = array();
            foreach ($contactinfo as $k => $val) {
                if (isset($contactRelateName[$val['id']])) {
                    $contactinfo[$k]['name'] = $contactRelateName[$val['id']];
                }
                $all_phone[] = "'" . $val['phone'] . "'";
            }
            if (count($all_phone) == 0) {
                $result['ret_num'] = 1665;
                $result['ret_msg'] = '邀请的用户号码不存在';
                echo json_encode($result);
                die ();
            }
            $sql = "select id,nick_name,phone from member where phone in (" . implode(",", $all_phone) . ")";
            $command = $connection->createCommand($sql);
            $result0 = $command->queryAll();
            //var_dump($result0);exit();			
            //添加犇犇用户到政企通讯录->申请表
            //先查询号码是否已加入政企通讯录
            if ($result0) {
                $inid = array();
                $memberin = array();
                foreach ($result0 as $v) {
                    $inid[] = $v['id'];
                }
                $sqlin = "select id,member_id from enterprise_member where contact_id = {$enterpriseid} and member_id in (" . implode(",", $inid) . ")";
                $command = $connection->createCommand($sqlin);
                $resultin = $command->queryAll();
                foreach ($resultin as $v) {
                    $memberin[] = $v['member_id'];  //已加入的会员
                }

                $va = array();
                $news_in = array();
                $i = 0;
                foreach ($result0 as $value) {
                    if (!in_array($value['id'], $memberin)) {
                        $t = time();
                        //发申请表的犇犇用户
                        // $benben[] = $value['phone'];
                        $phoneString = $value['phone'];
                        $benben[$phoneString] = $value['id'];
                        //邀请状态置为成功
                        $va[] = "({$value['id']},{$enterpriseid},1,{$t})";
                        //消息通知内容
                        // $content = $user->nick_name."邀请您加入政企通讯录:".$enterprise->name;
                        // if($remark_nameArray[$i]){
                        // 	$news_in[] = "(4,{$user->id},{$value['id']},'{$content}',{$enterpriseid},{$t}, {$enterpriseType},'{$remark_nameArray[$i]}')";
                        // }else{
                        // 	$news_in[] = "(4,{$user->id},{$value['id']},'{$content}',{$enterpriseid},{$t}, {$enterpriseType},'')";
                        // }
                        $i++;
                    }
                }

                // if(count($va)>0){
                // 	$sql1 = "insert into enterprise_invite (member_id,enterprise_id,status,created_time) values ".implode(",", $va);
                // 	$command = $connection->createCommand($sql1);
                // 	$result1 = $command->execute();
                // }
                //加入消息表--直接加入，不发消息了
                // if(count($news_in)>0){
                // 	$sql1 = "insert into news (type,sender,member_id,content,identity1,created_time, identity2,remark_name) values ".implode(",", $news_in);
                // 	$command = $connection->createCommand($sql1);
                // 	$result1 = $command->execute();
                // }				
                if (1) {
                    $result['ret_num'] = 0;
                    $result['ret_msg'] = '操作成功';
                }
            }
            //添加非犇犇用户到政企通讯录
            //先查询号码是否已加入该政企通讯录
            $inenenterprise = array();
            $arsql = "select id,phone from enterprise_member where contact_id = {$enterpriseid} and phone in (" . implode(",", $all_phone) . ")";
            $command = $connection->createCommand($arsql);
            $resultar = $command->queryAll();
            foreach ($resultar as $vra) {
                $inenenterprise[] = $vra['phone'];//已加入的用户				
            }
            $notphone = array();
            $num = 0;
            foreach ($contactinfo as $valu) {
                // if(!in_array($valu['phone'], $benben) && !in_array($valu['phone'], $inenenterprise)){
                //所有不在表里的用户直接加入
                if (!in_array($valu['phone'], $inenenterprise)) {
                    $phone_string = $valu['phone'];
                    $memberid = intval($benben[$phone_string]);
                    $t = time();
                    $notphone[] = "({$enterpriseid},'{$valu['phone']}',{$memberid},'{$valu['name']}',{$t}, $user->id)";
                    $num++;
                }
            }
            //判断总人数，不能超过上限人数
            if ($enterprise->number + $num > $enterprise->max_num) {
                $result['ret_num'] = 1700;
                $result['ret_msg'] = '已经达到政企数量上限,⽆法添加';
                echo json_encode($result);
                die ();
            }
            //插入申请表
            if (count($va) > 0) {
                $sql1 = "insert into enterprise_invite (member_id,enterprise_id,status,created_time) values " . implode(",", $va);
                $command = $connection->createCommand($sql1);
                $result1 = $command->execute();
            }
            //插入用户表
            if (count($notphone) > 0) {
                $sql2 = "insert into enterprise_member (contact_id,phone,member_id,name,created_time, invite_id) values " . implode(",", $notphone);
                $command = $connection->createCommand($sql2);
                $result2 = $command->execute();
            }

            if ($result2) {
                $enterprise->number = $enterprise->number + $num;
                $enterprise->update();
                $result['ret_num'] = 0;
                $result['ret_msg'] = '操作成功';
            } else {
                $result['ret_num'] = 0;
                $result['ret_msg'] = '操作成功';
            }
        } else {
            //主动加入
            //添加犇犇用户到政企通讯录成员表
            $connection = Yii::app()->db;
            $asql = "select count(a.contact_id) count from enterprise_member as a left join enterprise as b on a.contact_id = b.id where b.id > 0 and b.member_id = {$user->id}";
            // $asql = "select count(id) count from enterprise_member where member_id = {$user->id}";
            $command = $connection->createCommand($asql);
            $count = $command->queryAll();
            if ($count[0]['count'] >= 6) {
                $result['ret_num'] = 5203;
                $result['ret_msg'] = '您已加入6个政企通讯录';
                echo json_encode($result);
                die();
            }
            $euser = EnterpriseMember::model()->find("contact_id = {$enterpriseid} and member_id = {$user->id}");
            if ($euser) {
                $result['ret_num'] = 603;
                $result['ret_msg'] = '已加入该政企通讯录';
            } else {
                $guser = new EnterpriseMember();
                $guser->contact_id = $enterpriseid;
                $guser->member_id = $user->id;
                $guser->short_phone = $shortphone;
                $name = $user->name;
                if (!$name) {
                    $name = $user->nick_name;
                }
                $guser->name = $name;
                $guser->phone = $user->phone;
                $guser->created_time = time();
                if ($guser->save()) {
                    $enterprise->number = $enterprise->number + 1;
                    $enterprise->update();
                    $result['ret_num'] = 0;
                    $result['ret_msg'] = '操作成功';
                }
            }
        }
        echo json_encode($result);

    }

    /*
     * 政企通讯录申请加入接口
     * 涉及news表
     */
    public function actionApplyJoin(){
        $this->check_key();
        $user = $this->check_user();
        $enterpriseid = Frame::getIntFromRequest('enterprise_id');
        $remark_name = Frame::getStringFromRequest('remark_name');
        if(empty($enterpriseid)){
            $result['ret_num'] = 2016;
            $result['ret_msg'] = '缺少参数！';
            echo json_encode($result);
            die ();
        }
        $einfo=Enterprise::model()->find("id={$enterpriseid} and status=0");
        if(!$einfo){
            $result['ret_num'] = 1005;
            $result['ret_msg'] = '该政企通讯录不存在或者被禁用！';
            echo json_encode($result);
            die ();
        }

        if($remark_name) {
            //判断是否是政企预留号码，是则直接加入
            $eminfo = EnterpriseMember::model()->find("contact_id={$enterpriseid} and short_phone={$remark_name} and member_id=0");
            if ($eminfo && $eminfo['member_id'] == 0) {
                $eminfo->remark_name = $user['nick_name'];
                $eminfo->remark_name = $user['name'];
                $eminfo->phone = $user['phone'];
                $eminfo->member_id = $user['id'];
                if ($eminfo->save()) {
                    $result['ret_num'] = 1888;
                    $result['ret_msg'] = '加入成功！';
                    echo json_encode($result);
                    die ();
                }
            }
        }

        if($einfo['max_num']<=$einfo['number']){
            $result['ret_num'] = 2305;
            $result['ret_msg'] = '政企人数已达上限！';
            echo json_encode($result);
            die ();
        }

        $enterinfo=EnterpriseRole::model()->find("enterprise_id={$enterpriseid}");
        if(!$enterinfo){
            $result['ret_num'] = 1101;
            $result['ret_msg'] = '该政企通讯录不允许申请加入！';
            echo json_encode($result);
            die ();
        }
        if($enterinfo['enterprise_apply']==1||$enterinfo['enterprise_apply']==3){
            $result['ret_num'] = 1105;
            $result['ret_msg'] = '该政企通讯录不允许申请加入！';
            echo json_encode($result);
            die ();
        }

        $news=new News();
        $news->type=4;
        $news->sender=$user['id'];
        $news->content="申请加入政企".$einfo['name'];
        $news->created_time=time();
        $news->identity1=$einfo['id'];
        $news->remark_name=$remark_name;
        $news->display=0;
        if($news->save()){
            $result['ret_num'] = 0;
            $result['ret_msg'] = '操作成功！';
        }else{
            $result['ret_num'] = 100;
            $result['ret_msg'] = '保存失败！';
        }
        echo json_encode($result);
    }

    /**
     * 加入政企通讯录-企业通讯录
     */
    public function actionNewjoin()
    {
        $this->check_key();
        $user = $this->check_user();
        $enterpriseid = Frame::getIntFromRequest('enterprise_id');
        $contactinfoA = Frame::getStringFromRequest('contact_info');
        $manual = Frame::getIntFromRequest('manual');

        $enterprise = Enterprise::model()->findByPk($enterpriseid);

        if (empty($enterprise)) {
            $result['ret_num'] = 1005;
            $result['ret_msg'] = '该政企通讯录不存在';
            echo json_encode($result);
            die ();
        }
        if($enterprise['type']==3){
            $result['ret_num'] = 2225;
            $result['ret_msg'] = '百姓网不允许直接加入！';
            echo json_encode($result);
            die ();
        }
        if($enterprise['max_num']<=$enterprise['number']){
            $result['ret_num'] = 1215;
            $result['ret_msg'] = '该政企通讯录人数已到上限';
            echo json_encode($result);
            die ();
        }
        $pinyin = new tpinyin();
        //被动加入
        $contactinfoArray = explode("||", $contactinfoA);
        if ($contactinfoArray[0]) {
            //添加手机通讯录成员到政企通讯录,支持多个用户
            //先查询传入的手机号是否是犇犇用户			
            foreach ($contactinfoArray as $ve) {
                $info = explode(":", $ve);
                $contactArray[] = $info[0];
                $remark_nameArray[] = $info[1];
                $phoneArray[] = trim($info[2]);
                $shortphoneArray[] = trim($info[3]);
            }

            /*$contactRelateName = array();
			foreach($remark_nameArray as $k => $e){
				$contactRelateName[$contactArray[$k]] = $e;
			}
			$contactRelatePhone = array();
			foreach($phoneArray as $k => $e){
				$contactRelatePhone[$contactArray[$k]] = $e;
			}
			$contactRelateShortphone = array();
			foreach($shortphoneArray as $k => $e){
				$contactRelateShortphone[$contactArray[$k]] = $e;
			}*/

            $contactinfo = array();
            $all_phone = array();
            $havephone = array();
            foreach ($contactArray as $key => $val) {
                if (in_array($phoneArray[$key], $havephone) || ($shortphoneArray[$key] && in_array($shortphoneArray[$key], $havephone))) {
                    continue;
                }
                $contactinfo[$key]['id'] = $val;
                $contactinfo[$key]['name'] = $remark_nameArray[$key];
                $contactinfo[$key]['phone'] = $phoneArray[$key];
                $contactinfo[$key]['short_phone'] = $shortphoneArray[$key];
                $all_phone[] = "'" . $phoneArray[$key] . "'";
                $all_phone[] = "'" . $shortphoneArray[$key] . "'";

                //企业网只判断长号重复
                if (!empty($phoneArray[$key]) && strlen($phoneArray[$key]) >= 11) {
                    $havephone[] = $phoneArray[$key];
                }
                if (!empty($shortphoneArray[$key]) && strlen($shortphoneArray[$key]) >= 11) {
                    $havephone[] = $shortphoneArray[$key];
                }
            }

            // foreach ($contactArray as $key => $val){
            // 	$havephone = array();
            // 	$havephone1 = array();
            // 	foreach ($contactinfo as $v){
            // 		$havephone[] = $v['phone'];
            // 		$havephone1[] = $v['short_phone'];
            // 	}
            // 	if(in_array($phoneArray[$key], $havephone) || in_array($shortphoneArray[$key], $havephone1)) continue;
            // 	$contactinfo[$key]['id'] = $val;
            // 	$contactinfo[$key]['name'] = $remark_nameArray[$key];
            // 	$contactinfo[$key]['phone'] = $phoneArray[$key];
            // 	$contactinfo[$key]['short_phone'] = $shortphoneArray[$key];
            // 	$all_phone[] = "'".$phoneArray[$key]."'";
            // 	$all_phone[] = "'".$shortphoneArray[$key]."'";
            // }

            $benben = array();
            $notinen = array();
            $connection = Yii::app()->db;
            /*$sql = "select a.id,a.name,b.phone,b.is_benben,b.is_baixing from group_contact_info a inner join group_contact_phone b on a.id=b.contact_info_id where a.id in (".implode(",", $contactArray).")";
			$command = $connection->createCommand($sql);
			$contactinfo = $command->queryAll();
			//var_dump($sql);exit();
			$all_phone = array();
			foreach ($contactinfo as $k => $val){
				if (isset($contactRelateName[$val['id']])) {
					$contactinfo[$k]['name'] = $contactRelateName[$val['id']];					
				}
				if (isset($contactRelatePhone[$val['id']])) {					
					$contactinfo[$k]['phone'] = $contactRelatePhone[$val['id']];					
				}
				if (isset($contactRelateShortphone[$val['id']])) {				
					$contactinfo[$k]['short_phone'] = $contactRelateShortphone[$val['id']];
				}
				//$all_phone[] = "'".$val['phone']."'";
				$all_phone[] = "'".$contactRelatePhone[$val['id']]."'";
				$all_phone[] = "'".$contactRelateShortphone[$val['id']]."'";
			}*/
            if (count($all_phone) == 0) {
                $result['ret_num'] = 1665;
                $result['ret_msg'] = '邀请的用户号码不存在';
                echo json_encode($result);
                die ();
            }
            $sql = "select id,nick_name,phone from member where phone in (" . implode(",", $all_phone) . ")";
            $command = $connection->createCommand($sql);
            $result0 = $command->queryAll();
            //var_dump($result0);exit();
            //添加犇犇用户到政企通讯录->申请表
            //先查询号码是否已加入政企通讯录
            if ($result0) {
                $inid = array();
                $memberin = array();
                foreach ($result0 as $v) {
                    $inid[] = $v['id'];
                }
                $sqlin = "select id,member_id from enterprise_member where contact_id = {$enterpriseid} and member_id in (" . implode(",", $inid) . ")";
                $command = $connection->createCommand($sqlin);
                $resultin = $command->queryAll();
                foreach ($resultin as $v) {
                    $memberin[] = $v['member_id'];  //已加入的会员
                }

                $va = array();
                $news_in = array();
                $i = 0;
                foreach ($result0 as $value) {
                    if (!in_array($value['id'], $memberin)) {
                        $t = time();
                        //发申请表的犇犇用户
                        // $benben[] = $value['phone'];
                        $phoneString = $value['phone'];
                        $benben[$phoneString] = $value['id'];
                        //邀请状态置为成功
                        $va[] = "({$value['id']},{$enterpriseid},1,{$t})";

                        $i++;
                    }
                }
            }

            foreach ($contactinfo as $key => $valu) {
                $phoneone = $valu['phone'];
                $phonetwo = $valu['short_phone'];
                //选取号码作为主号
                $phone_string = $phoneone;
                $shortphone_string = $phonetwo;
                if ((strlen($phoneone) == 11) && (strlen($phonetwo) == 11)) {
                    if ($benben[$phoneone]) {
                        $phone_string = $phoneone;
                        $shortphone_string = $phonetwo;
                    } else {
                        if ($benben[$phonetwo]) {
                            $phone_string = $phonetwo;
                            $shortphone_string = $phoneone;
                        } else {
                            $phone_string = $phoneone;
                            $shortphone_string = $phonetwo;
                        }
                    }
                } else {
                    if (strlen($phonetwo) == 11) {
                        $phone_string = $phonetwo;
                        $shortphone_string = $phoneone;
                    }
                }
                $contactinfo[$key]['phone'] = $phone_string;
                $contactinfo[$key]['short_phone'] = $shortphone_string;
                //企业网只判断长号重复			
                if ((strlen($phone_string) < 11) && (strlen($shortphone_string) < 11)) {
                    $result['ret_num'] = 1662;
                    $result['ret_msg'] = '号码格式不正确';
                    echo json_encode($result);
                    die ();
                }
                if (!empty($phone_string) && strlen($phone_string) >= 11) {
                    $sqlphone[] = $phone_string;
                }
                if (!empty($shortphone_string) && strlen($shortphone_string) >= 11) {
                    $sqlphone[] = $shortphone_string;
                }
            }

            //添加非犇犇用户到政企通讯录
            //先查询号码是否已加入该政企通讯录
            $inenenterprise = array();
            $inenenterprise1 = array();
            $arsql = "select id,phone,short_phone from enterprise_member where contact_id = {$enterpriseid} and (phone in (" . implode(",", $sqlphone) . ") or short_phone in (" . implode(",", $sqlphone) . "))";
            $command = $connection->createCommand($arsql);
            $resultar = $command->queryAll();
            foreach ($resultar as $vra) {
                $inenenterprise[] = $vra['phone'];//已加入的用户
                $inenenterprise[] = $vra['short_phone'];
            }
            if ($manual) {
                //手动加入，有号码已存在
                if (count($inenenterprise) > 0) {
                    $result['ret_num'] = 1600;
                    $result['ret_msg'] = '号码已经存在通讯录中';
                    echo json_encode($result);
                    die ();
                }
            }
            //成员数组
            $notphone = array();
            //常用联系人数组
            $insertArray = array();
            //我的常用联系人数量
            $display_count = EnterpriseDisplayMember::model()->count("enterprise_id = {$enterpriseid} and user_id = {$user->id}");
            $num = 0;
            foreach ($contactinfo as $valu) {
                //所有不在表里的用户直接加入
                if (!in_array($valu['phone'], $inenenterprise) && !in_array($valu['short_phone'], $inenenterprise)) {
                    $tpl_py = strtoupper($pinyin->str2sort($valu['name']));
                    $tpl_allpy = strtoupper($pinyin->str2py($valu['name']));
                    $memberid = intval($benben[$valu['phone']]);
                    $t = time();
                    $notphone[] = "({$enterpriseid},'{$valu['phone']}','{$valu['short_phone']}',{$memberid},'{$valu['name']}',{$t}, {$user->id},'{$tpl_py}','{$tpl_allpy}')";
                    $num++;
                }
                //添加常用联系人，不超过50人
                if ($display_count < 50) {
                    if (!in_array($valu, $insertArray)) {
                        $insertArray[] = $valu;
                        $display_count++;
                    }
                }
            }
            //判断总人数，不能超过上限人数
            if ($enterprise->number + $num > $enterprise->max_num) {
                $result['ret_num'] = 1700;
                $result['ret_msg'] = '已经达到政企数量上限,⽆法添加';
                echo json_encode($result);
                die ();
            }
            //插入申请表
            if (count($va) > 0) {
                $sql1 = "insert into enterprise_invite (member_id,enterprise_id,status,created_time) values " . implode(",", $va);
                $command = $connection->createCommand($sql1);
                $result1 = $command->execute();
            }
            //插入用户表
            if (count($notphone) > 0) {
                // $sql2 = "insert into enterprise_member (contact_id,phone,short_phone,member_id,name,created_time, invite_id) values ".implode(",", $notphone);
                // $command = $connection->createCommand($sql2);
                // $result2 = $command->execute();
                for ($i = 0; $i < count($notphone); $i++) {
                    $value = $notphone[$i];
                    $sql2 = "insert into enterprise_member (contact_id,phone,short_phone,member_id,name,created_time, invite_id, pinyin,allpinyin) values " . $value;
                    $command = $connection->createCommand($sql2);
                    $result2 = $command->execute();
                    $lastid = Yii::app()->db->getLastInsertID();
                    if ($lastid > 0) {
                        //判断是否是后台创建的政企,是则加入enterprise_member_manage，权限表
                        if($enterprise['type']!=3&&$enterprise['origin']==2){
                            $is_emm=EnterpriseMemberManage::model()->count("member_id={$lastid}");
                            if($is_emm==0){
                                $enterMM=new EnterpriseMemberManage();
                                $enterMM->member_id=$lastid;
                                $enterMM->access_level=1;
                                $enterMM->created_time=time();
                                $enterMM->save();
                            }
                        }
                        //插入常用联系人
                        if (count($insertArray) > $i) {
                            $display_value = $insertArray[$i];
                            $memberid = intval($benben[$display_value['phone']]);
                            $item_values = '(' . $user->id . ', ' . $lastid . ', ' . $enterpriseid . ')';
                            $insertSql = "insert into enterprise_display_member (user_id, member_id, enterprise_id) values " . $item_values;
                            $command = $connection->createCommand($insertSql);
                            $result1 = $command->execute();
                        }
                    }
                }
            }

            if ($result2) {
                $enterprise->number = $enterprise->number + $num;
                $enterprise->update();
                $this->addIntegral($user->id, 16, $num);
                $result['ret_num'] = 0;
                $result['ret_msg'] = '操作成功';
            } else {
                $result['ret_num'] = 0;
                $result['ret_msg'] = '操作成功';
            }
        } else {
            //主动加入
            //添加犇犇用户到政企通讯录成员表
            $connection = Yii::app()->db;
// 		$asql = "select count(a.contact_id) count from enterprise_member as a left join enterprise as b on a.contact_id = b.id where b.id > 0 and b.member_id = {$user->id}";
            // $asql = "select count(id) count from enterprise_member where member_id = {$user->id}";
// 		$command = $connection->createCommand($asql);
// 		$count = $command->queryAll();
// 		if($count[0]['count'] >= 6){
// 			$result['ret_num'] = 5203;
// 			$result['ret_msg'] = '您已加入6个政企通讯录';
// 			echo json_encode( $result );
// 			die();
// 		}
            $euser = EnterpriseMember::model()->find("contact_id = {$enterpriseid} and member_id = {$user->id}");
            if ($euser) {
                $result['ret_num'] = 603;
                $result['ret_msg'] = '已加入该政企通讯录';
            } else {
                $guser = new EnterpriseMember();
                $guser->contact_id = $enterpriseid;
                $guser->member_id = $user->id;
                $name = $user->name;
                if (!$name) {
                    $name = $user->nick_name;
                }
                $guser->name = mb_substr($name, 0, 10, 'utf-8');
                $guser->pinyin = strtoupper($pinyin->str2sort($guser->name));
                $guser->allpinyin = strtoupper($pinyin->str2py($guser->name));
                $guser->phone = $user->phone;
                $guser->created_time = time();
                if ($guser->save()) {
                    //判断是否是后台创建的政企,是则加入enterprise_member_manage，权限表
                    if($enterprise['type']!=3&&$enterprise['origin']==2) {
                        $is_emm = EnterpriseMemberManage::model()->count("member_id={$guser->id}");
                        if ($is_emm == 0) {
                            $enterMM = new EnterpriseMemberManage();
                            $enterMM->member_id = $guser->id;
                            $enterMM->access_level = 1;
                            $enterMM->created_time = time();
                            $enterMM->save();
                        }
                    }
                    $enterprise->number = $enterprise->number + 1;
                    $enterprise->update();
                    $result['ret_num'] = 0;
                    $result['ret_msg'] = '操作成功';
                }
            }
        }
        echo json_encode($result);

    }

    /**
     * 加入政企通讯录(会员表ID)-虚拟通讯录(主动)
     */
    public function actionJoinvirtual()
    {
        $this->check_key();
        $user = $this->check_user();
        $enterpriseid = Frame::getIntFromRequest('enterprise_id');
        $shortphone = Frame::getStringFromRequest('short_phone');
        $enterprise = Enterprise::model()->findByPk($enterpriseid);
        $enterpriseType = $enterprise['type'];
        $shortphone = trim($shortphone);
        if (empty($enterprise)) {
            $result['ret_num'] = 1005;
            $result['ret_msg'] = '该政企通讯录不存在';
            echo json_encode($result);
            die ();
        }
        if (empty($shortphone)) {
            $result['ret_num'] = 1012;
            $result['ret_msg'] = '虚拟通讯录短号格式非法,请重新输入';
            echo json_encode($result);
            die ();
        }
        $pinyin = new tpinyin();
        //如果是添加虚拟网用户，必须得要有短号
        if ($enterpriseType == 2) {
            $createdMember = EnterpriseMember::model()->find("contact_id = '{$enterpriseid}' and (member_id = {$enterprise['member_id']} or member_id = -1)");
            if ($enterprise['short_length'] != strlen($shortphone)) {
                $result['ret_num'] = 1012;
                $result['ret_msg'] = '虚拟通讯录短号格式非法,请重新输入';
                echo json_encode($result);
                die ();
            }
        } else {
            $result['ret_num'] = 1668;
            $result['ret_msg'] = '该通讯录不是虚拟通讯录';
            echo json_encode($result);
            die ();
        }
        //添加犇犇用户到政企通讯录成员表
        $connection = Yii::app()->db;
// 		$asql = "select count(a.contact_id) count from enterprise_member as a left join enterprise as b on a.contact_id = b.id where b.id > 0 and b.member_id = {$user->id}";
        // $asql = "select count(id) count from enterprise_member where member_id = {$user->id}";
// 		$command = $connection->createCommand($asql);
// 		$count = $command->queryAll();
// 		if($count[0]['count'] >= 6){
// 			$result['ret_num'] = 5203;
// 			$result['ret_msg'] = '您已加入6个政企通讯录';
// 			echo json_encode( $result );
// 			die();
// 		}
        $euser = EnterpriseMember::model()->find("contact_id = {$enterpriseid} and member_id = {$user->id}");
        if ($euser) {
            $result['ret_num'] = 603;
            $result['ret_msg'] = '已加入该政企通讯录';
        } else {
            $check_same = EnterpriseMember::model()->find("contact_id='{$enterpriseid}' and (phone='{$shortphone}' or short_phone='{$shortphone}')");
            if (($check_same->short_phone == $shortphone) && !$check_same->member_id) {
                //认领短号
                $check_same->member_id = $user->id;
                $check_same->update();
                $result['ret_num'] = 0;
                $result['ret_msg'] = '操作成功';
                echo json_encode($result);
                die();
            }
            if (!empty($check_same)) {
                $result['ret_num'] = 1013;
                $result['ret_msg'] = '号码已存在通讯录中,请重新输入';
                echo json_encode($result);
                die ();
            }
            $guser = new EnterpriseMember();
            $guser->contact_id = $enterpriseid;
            $guser->member_id = $user->id;
            $guser->short_phone = $shortphone;
            $name = $user->name;
            if (!$name) {
                $name = $user->nick_name;
            }
            $guser->name = mb_substr($name, 0, 10, 'utf-8');
            $guser->pinyin = strtoupper($pinyin->str2sort($guser->name));
            $guser->allpinyin = strtoupper($pinyin->str2py($guser->name));
            $guser->phone = $user->phone;
            $guser->created_time = time();
            if ($guser->save()) {
                $enterprise->number = $enterprise->number + 1;
                $enterprise->update();
                $result['ret_num'] = 0;
                $result['ret_msg'] = '操作成功';
            }
        }
        echo json_encode($result);

    }

    /**
     * 加入政企通讯录(会员表ID)-虚拟通讯录
     */
    public function actionJoinmember()
    {
        $this->check_key();
        $user = $this->check_user();
        $enterpriseid = Frame::getIntFromRequest('enterprise_id');
        $contactinfoA = Frame::getStringFromRequest('contact_info');
        $manual = Frame::getIntFromRequest('manual');

        $enterprise = Enterprise::model()->findByPk($enterpriseid);

        if (empty($enterprise)) {
            $result['ret_num'] = 1005;
            $result['ret_msg'] = '该政企通讯录不存在';
            echo json_encode($result);
            die ();
        }
        if($enterprise['max_num']<=$enterprise['number']){
            $result['ret_num'] = 1125;
            $result['ret_msg'] = '政企人数已达上限';
            echo json_encode($result);
            die ();
        }
        //被动加入
        $contactinfoArray = explode("||", $contactinfoA);
        if ($contactinfoArray[0]) {

            foreach ($contactinfoArray as $ve) {
                $info = explode(":", $ve);
                $remark_nameArray[] = $info[0];
                $shortphoneArray[] = trim($info[1]);
            }

            $contactinfo = array();
            $all_phone = array();
            foreach ($remark_nameArray as $key => $val) {
                $havephone = array();
                foreach ($contactinfo as $v) {
                    $havephone[] = $v['short_phone'];
                }
                if (in_array($shortphoneArray[$key], $havephone)) continue;
                $contactinfo[$key]['name'] = $remark_nameArray[$key];
                $contactinfo[$key]['short_phone'] = $shortphoneArray[$key];
                $all_phone[] = "'" . $shortphoneArray[$key] . "'";
            }

            $benben = array();
            $notinen = array();
            $connection = Yii::app()->db;

            if (count($all_phone) == 0) {
                $result['ret_num'] = 1665;
                $result['ret_msg'] = '邀请的用户号码不存在';
                echo json_encode($result);
                die ();
            }

            //先查询号码是否已加入该政企通讯录
            $inenenterprise = array();
            $arsql = "select id,phone,short_phone from enterprise_member where contact_id = {$enterpriseid} and short_phone in (" . implode(",", $all_phone) . ")";
            $command = $connection->createCommand($arsql);
            $resultar = $command->queryAll();
            foreach ($resultar as $vra) {
                //已加入的用户
                $inenenterprise[] = $vra['short_phone'];
            }

            if ($manual) {
                //手动加入，有号码已存在
                if (count($inenenterprise) > 0) {
                    $result['ret_num'] = 1600;
                    $result['ret_msg'] = '号码已经存在通讯录中';
                    echo json_encode($result);
                    die ();
                }
            }
            //成员数组
            $notphone = array();
            //常用联系人数组
            $insertArray = array();
            //我的常用联系人数量
            $display_count = EnterpriseDisplayMember::model()->count("enterprise_id = {$enterpriseid} and user_id = {$user->id}");
            $num = 0;
            foreach ($contactinfo as $valu) {
                //所有不在表里的用户直接加入
                if (!in_array($valu['short_phone'], $inenenterprise)) {
                    $memberid = 0;
                    $t = time();
                    $pinyin = new tpinyin();
                    $tpl_py = strtoupper($pinyin->str2sort($valu['name']));
                    $tpl_allpy = strtoupper($pinyin->str2py($valu['name']));
                    $notphone[] = "({$enterpriseid},'','{$valu['short_phone']}',{$memberid},'{$valu['name']}',{$t},{$user->id},'{$tpl_py}','{$tpl_allpy}')";
                    $num++;
                }
                //添加常用联系人，不超过50人
                if ($display_count < 50) {
                    if (!in_array($valu, $insertArray)) {
                        $insertArray[] = $valu;
                        $display_count++;
                    }
                }
            }

            //判断总人数，不能超过上限人数
            if ($enterprise->number + $num > $enterprise->max_num) {
                $result['ret_num'] = 1700;
                $result['ret_msg'] = '已经达到政企数量上限,⽆法添加';
                echo json_encode($result);
                die ();
            }

            //插入用户表
            // if(count($notphone) > 0){
            // 	$sql2 = "insert into enterprise_member (contact_id,short_phone,member_id,name,created_time, invite_id) values ".implode(",", $notphone);
            // 	$command = $connection->createCommand($sql2);
            // 	$result2 = $command->execute();
            // }
            //插入用户表
            if (count($notphone) > 0) {
                for ($i = 0; $i < count($notphone); $i++) {
                    $value = $notphone[$i];
                    $sql2 = "insert into enterprise_member (contact_id,phone,short_phone,member_id,name,created_time, invite_id,pinyin,allpinyin) values " . $value;
                    $command = $connection->createCommand($sql2);
                    $result2 = $command->execute();
                    $lastid = Yii::app()->db->getLastInsertID();
                    if ($lastid > 0) {
                        //插入常用联系人
                        if (count($insertArray) > $i) {
                            $display_value = $insertArray[$i];
                            $memberid = intval($benben[$display_value['phone']]);
                            $item_values = '(' . $user->id . ', ' . $lastid . ', ' . $enterpriseid . ')';
                            $insertSql = "insert into enterprise_display_member (user_id, member_id, enterprise_id) values " . $item_values;
                            $command = $connection->createCommand($insertSql);
                            $result1 = $command->execute();
                        }
                    }
                }
            }

            if ($result2) {
                $enterprise->number = $enterprise->number + $num;
                $enterprise->update();
                $this->addIntegral($user->id, 16, $num);
                $result['ret_num'] = 0;
                $result['ret_msg'] = '操作成功';
            } else {
                $result['ret_num'] = 0;
                $result['ret_msg'] = '操作成功';
            }
        }
        echo json_encode($result);
    }

    /**
     * 加入政企通讯录(会员表ID)-虚拟通讯录
     */
    public function actionJoinmember1()
    {
        $this->check_key();
        $user = $this->check_user();
        $enterpriseid = Frame::getIntFromRequest('enterprise_id');
        $contactid = Frame::getStringFromRequest('member_id');
        $remark_name = Frame::getStringFromRequest('name');
        if (!$contactid) {
            $result['ret_num'] = 1667;
            $result['ret_msg'] = '邀请的用户ID为空';
            echo json_encode($result);
            die ();
        }

        $enterprise = Enterprise::model()->findByPk($enterpriseid);
        $enterpriseType = $enterprise['type'];
        if (empty($enterprise)) {
            $result['ret_num'] = 1005;
            $result['ret_msg'] = '该政企通讯录不存在';
            echo json_encode($result);
            die ();
        }
        if($enterprise['max_num']<=$enterprise['number']){
            $result['ret_num'] = 1125;
            $result['ret_msg'] = '政企人数已达上限';
            echo json_encode($result);
            die ();
        }
        $remark_nameArray = explode("::", $remark_name);
        $connection = Yii::app()->db;

        //通过通讯录id，查询犇犇id
        $sql = "select benben_id from group_contact_info where id in ({$contactid})";
        $command = $connection->createCommand($sql);
        $members = $command->queryAll();
        $benbenidArray = array();
        if ($members) {
            foreach ($members as $value) {
                $benbenidArray[] = $value['benben_id'];
            }
        }
        //查询犇犇用户信息
        if (count($benbenidArray) > 0) {
            // $sql = "select id,nick_name,phone from member where benben_id in ({$contactid})";
            $sql = "select id,nick_name,phone from member where benben_id in (" . implode(',', $benbenidArray) . ")";
            $command = $connection->createCommand($sql);
            $result0 = $command->queryAll();
        }
        if ($result0) {
            $va = array();
            $news_in = array();
            $i = 0;
            foreach ($result0 as $value) {
                $t = time();
                $benben[] = $value['phone'];
                $va[] = "({$value['id']},{$enterpriseid},0,{$t})";
                $content = $user->nick_name . "邀请您加入政企通讯录:" . $enterprise->name;
                if ($remark_nameArray[$i]) {
                    $news_in[] = "(4,{$user->id},{$value['id']},'{$content}',{$enterpriseid},{$t}, {$enterpriseType},'{$remark_nameArray[$i]}')";
                } else {
                    $news_in[] = "(4,{$user->id},{$value['id']},'{$content}',{$enterpriseid},{$t}, {$enterpriseType},'')";
                }
                $i++;
            }
            if (count($va) > 0) {
                $sql1 = "insert into enterprise_invite (member_id,enterprise_id,status,created_time) values " . implode(",", $va);
                $command = $connection->createCommand($sql1);
                $result1 = $command->execute();
            }
            //加入消息表
            if (count($news_in) > 0) {
                $sql1 = "insert into news (type,sender,member_id,content,identity1,created_time, identity2,remark_name) values " . implode(",", $news_in);
                $command = $connection->createCommand($sql1);
                $result1 = $command->execute();
            }
            if ($result1) {
                $result['ret_num'] = 0;
                $result['ret_msg'] = '操作成功';
            } else {
                $result['ret_num'] = 1666;
                $result['ret_msg'] = '邀请失败';
            }
        } else {
            $result['ret_num'] = 1665;
            $result['ret_msg'] = '邀请的用户号码不存在';
        }
        echo json_encode($result);
    }

    /**
     * 退出政企通讯录
     */
    public function actionQuit()
    {
        $this->check_key();
        $user = $this->check_user();
        $enterpriseid = Frame::getIntFromRequest('enterpriseid');
        $memberid = Frame::getIntFromRequest('member_id');

        $enterprise = Enterprise::model()->findByPk($enterpriseid);
        if (empty($enterprise)) {
            $result['ret_num'] = 1005;
            $result['ret_msg'] = '该政企通讯录不存在';
            echo json_encode($result);
            die ();
        }
        $applyid = $user->id;
        if ($memberid) {
            $ouser = Member::model()->findByPk($memberid);
            if (empty($ouser)) {
                $result['ret_num'] = 1000;
                $result['ret_msg'] = '被邀请用户不存在';
                echo json_encode($result);
                die ();
            }
            $applyid = $memberid;
        }

        $guser = EnterpriseMember::model()->find("contact_id = {$enterpriseid} and member_id = {$applyid}");
        if (empty($guser)) {
            $result['ret_num'] = 5237;
            $result['ret_msg'] = '已退出该通讯录';
            echo json_encode($result);
            die ();
        }
        //清空用户enterprise_display_member_log表信息，enterprise_display_member表信息

        //直接删除会引发bug，暂时注释，修复之后取消注释
        // $display_log = EnterpriseDisplayMemberLog::model()->find("member_id = {$applyid} and enterprise_id = {$enterpriseid}");
        // if($display_log){
        // 	$display_log->delete();
        // }

        $connection = Yii::app()->db;
        //删除取交集记录
        $dsqllog = "delete from enterprise_display_member_log where member_id=" . $applyid . " and enterprise_id=" . $enterpriseid;
        $commandlog = $connection->createCommand($dsqllog);
        $info = $commandlog->execute();
        //从别人通讯录里删除
        $dsql0 = "delete from enterprise_display_member where member_id = " . $guser->id . " and enterprise_id = " . $enterpriseid;
        $command0 = $connection->createCommand($dsql0);
        $info = $command0->execute();
        //删除自己的通讯录
        $dsql = "delete from enterprise_display_member where user_id = " . $applyid . " and enterprise_id = " . $enterpriseid;
        $command = $connection->createCommand($dsql);
        $info = $command->execute();
        if ($guser->delete()) {
            //$enterprise->member_id = 0;
            $sort_info = EnterpriseMember::model()->findAll("member_id = {$applyid} order by sort asc");
            foreach ($sort_info as $kk => $vv) {
                EnterpriseMember::model()->updateAll(array("sort" => ($kk + 1)), "id={$vv['id']}");
            }
            $huser = EnterpriseMember::model()->count("contact_id = {$enterpriseid}");
            if (empty($huser)) {
                $enterprise->delete();
            } else {
                $enterprise->number = $enterprise->number - 1;
                $enterprise->update();
            }
            $result['ret_num'] = 0;
            $result['ret_msg'] = '操作成功';
            $enterprise->out_num=$enterprise->out_num+1;
            $enterprise->update();
        } else {
            $result['ret_num'] = 5238;
            $result['ret_msg'] = '退出通讯录失败';
        }
        echo json_encode($result);

    }

    /**
     * 添加常用联系人
     */
    public function actionCommon()
    {
        $this->check_key();
        $enterpriseid = Frame::getIntFromRequest('enterprise_id');
        $ememberid = Frame::getStringFromRequest('emember_id');
        if (empty($ememberid)) {
            $result['ret_num'] = 1825;
            $result['ret_msg'] = '联系人不能为空';
            echo json_encode($result);
            die ();
        }
        $user = $this->check_user();

        $enterprise = Enterprise::model()->findByPk($enterpriseid);
        if (empty($enterprise)) {
            $result['ret_num'] = 1005;
            $result['ret_msg'] = '该政企通讯录不存在';
            echo json_encode($result);
            die ();
        }

        $allMember = explode(",", $ememberid);
        if (count($allMember) > 0) {
            $count1 = count($allMember);
            $count = EnterpriseDisplayMember::model()->count("enterprise_id = {$enterpriseid} and user_id = {$user->id} and is_common=1");
            if (!$count) $count = 0;
            if ($count + $count1 > 50) {
                $result['ret_num'] = 2806;
                $result['ret_msg'] = '常用联系人人数超过50人';
                echo json_encode($result);
                die ();
            }
            $displayList = EnterpriseDisplayMember::model()->findAll(array('select' => 'member_id,is_common,id', 'condition' => "enterprise_id = {$enterpriseid} and user_id = {$user->id} and member_id in (" . implode(',', $allMember) . ")"));
            if (count($displayList) > 0) {
                foreach($displayList as $kd=>$vd){
                    if($vd['is_common']==1){
                        EnterpriseDisplayMember::model()->updateAll(array("is_common"=>0),"id={$vd['id']}");
                        $result['ret_num'] = 0;
                        $result['ret_msg'] = '操作成功';
                    }else{
                        $result['ret_num'] = 1008;
                        $result['ret_msg'] = '已经为常用联系人';
                    }
                }
            } else {
                $insertArray = array();
                foreach ($allMember as $each) {
                    $item_values = '(' . $user->id . ', ' . $each . ', ' . $enterpriseid . ', 0)';
                    if (!in_array($item_values, $insertArray)) {
                        $insertArray[] = $item_values;
                    }
                }
                $insertSql = "insert into enterprise_display_member(user_id, member_id, enterprise_id, group_id) values" . implode(',', $insertArray);
                $connection = Yii::app()->db;
                $command = $connection->createCommand($insertSql);
                $result1 = $command->execute();

                $enterpriseDisplayMemberLog = new EnterpriseDisplayMemberLog();
                $enterpriseDisplayMemberLog->member_id = $user->id;
                $enterpriseDisplayMemberLog->enterprise_id = $enterpriseid;
                $enterpriseDisplayMemberLog->created_time = time();
                $enterpriseDisplayMemberLog->save();

                $result['ret_num'] = 0;
                $result['ret_msg'] = '操作成功';
            }

        } else {
            $result['ret_num'] = 1009;
            $result['ret_msg'] = '联系人ID为空';
        }
        echo json_encode($result);
    }

    /**
     * 取消常用联系人
     */
    public function actionCancelcommon()
    {
        //$this->check_key();
        $enterpriseid = Frame::getIntFromRequest('enterprise_id');
        $ememberid = Frame::getIntFromRequest('emember_id');
        // 		$name = Frame::getStringFromRequest('name');
        // 		$phone = Frame::getStringFromRequest('phone');
        $user = $this->check_user();

        $enterprise = Enterprise::model()->findByPk($enterpriseid);
        if (empty($enterprise)) {
            $result['ret_num'] = 1005;
            $result['ret_msg'] = '该政企通讯录不存在';
            echo json_encode($result);
            die ();
        }
        if ($ememberid) {
            $member = EnterpriseMember::model()->findByPk($ememberid);
            if (empty($member)) {
                $result['ret_num'] = 1007;
                $result['ret_msg'] = '待添加的联系人不存在';
                echo json_encode($result);
                die ();
            }

            $egm = EnterpriseDisplayMember::model()->find("member_id = {$member->id} and user_id = {$user->id} and enterprise_id = {$enterpriseid}");
            if ($egm) {
                $egm->is_common = 1;
                if ($egm->update()) {
                    $result['ret_num'] = 0;
                    $result['ret_msg'] = '操作成功';
                } else {
                    $result['ret_num'] = 1010;
                    $result['ret_msg'] = '取消失败';
                }
            }
        } else {
            $result['ret_num'] = 1009;
            $result['ret_msg'] = '联系人ID为空';
        }
        echo json_encode($result);
    }

    /**
     * 新建分组
     */
    public function actionAddgroup()
    {
        $this->check_key();
        $user = $this->check_user();
        $enterpriseid = Frame::getIntFromRequest('enterpriseid');
        $name = Frame::getStringFromRequest('name');
        $wfz = trim($name);
        if ($wfz == '未分组') {
            $result['ret_num'] = 5239;
            $result['ret_msg'] = '分组名重复';
            echo json_encode($result);
            die();
        }
        if (empty($enterpriseid)) {
            $result['ret_num'] = 503;
            $result['ret_msg'] = '通讯录ID为空';
            echo json_encode($result);
            die();
        }
        if (empty($name)) {
            $result['ret_num'] = 506;
            $result['ret_msg'] = '分组名为空';
            echo json_encode($result);
            die();
        }
        $my = EnterpriseMember::model()->find("contact_id = {$enterpriseid} and member_id = {$user->id}");
        if ($my) {
            //查询分组名是否重复
            $re = EnterpriseGroup::model()->find("member_id = {$my['id']} and enterprise_id = {$enterpriseid} and groupname = '{$name}'");
            if ($re) {
                $result['ret_num'] = 5239;
                $result['ret_msg'] = '分组名重复';
                echo json_encode($result);
                die();
            }
            $num = EnterpriseGroup::model()->count("member_id = {$my['id']} and enterprise_id = {$enterpriseid}");
            $group = new EnterpriseGroup();
            $group->member_id = $my->id;
            $group->enterprise_id = $enterpriseid;
            $group->groupname = $name;
            $group->sort = $num + 1;
            $group->created_time = time();
            if ($group->save()) {
                $result['ret_num'] = 0;
                $result['ret_msg'] = '操作成功';
                $result['group_id'] = $group->id;
            } else {
                $result['ret_num'] = 507;
                $result['ret_msg'] = '新建分组失败';
            }
        } else {
            $result['ret_num'] = 507;
            $result['ret_msg'] = '新建分组失败';
        }
        echo json_encode($result);

    }

    /**
     * 编辑分组
     */
    public function actionEditgroup()
    {
        $this->check_key();
        $user = $this->check_user();
        $groupid = Frame::getIntFromRequest('groupid');
        $name = Frame::getStringFromRequest('name');
        $wfz = trim($name);
        if ($wfz == '未分组') {
            $result['ret_num'] = 5239;
            $result['ret_msg'] = '分组名重复';
            echo json_encode($result);
            die();
        }
        if (empty($groupid)) {
            $result['ret_num'] = 508;
            $result['ret_msg'] = '分组ID为空';
            echo json_encode($result);
            die();
        }
        if (empty($name)) {
            $result['ret_num'] = 506;
            $result['ret_msg'] = '分组名不能为空';
            echo json_encode($result);
            die();
        }
        $group = EnterpriseGroup::model()->findByPk($groupid);
        if (empty($group)) {
            $result['ret_num'] = 509;
            $result['ret_msg'] = '分组不存在';
            echo json_encode($result);
            die();
        }
        $my = EnterpriseMember::model()->find("contact_id = {$group['enterprise_id']} and member_id = {$user->id}");
        if (empty($my)) {
            $result['ret_num'] = 507;
            $result['ret_msg'] = '还未加入通讯录';
            echo json_encode($result);
            die();
        }
        //查询分组名是否重复
        $re = EnterpriseGroup::model()->find("member_id={$my['id']} and enterprise_id = {$group->enterprise_id} and groupname = '{$name}' and id<>$groupid");
        if ($re) {
            $result['ret_num'] = 5239;
            $result['ret_msg'] = '分组名重复';
            echo json_encode($result);
            die();
        }
        $group->groupname = $name;
        if ($group->update()) {
            $result['ret_num'] = 0;
            $result['ret_msg'] = '操作成功';
            $result['group_name'] = $group->groupname;
        } else {
            $result['ret_num'] = 510;
            $result['ret_msg'] = '编辑分组失败';
        }
        echo json_encode($result);

    }

    /**
     * 删除分组
     */
    public function actionDeletegroup()
    {
        $this->check_key();
        $user = $this->check_user();
        $target = Frame::getIntFromRequest('target');
        $groupid = Frame::getIntFromRequest('groupid');
        $enterpriseid = Frame::getIntFromRequest('enterprise_id');
        $connection = Yii::app()->db;
        if ($groupid > 0) {
            $group = EnterpriseGroup::model()->find("id = {$groupid}");
            if (empty($group)) {
                $result['ret_num'] = 509;
                $result['ret_msg'] = '分组ID不存在';
                echo json_encode($result);
                die();
            }
            if (!$target) {
                $target = 0; //删除分组，并且删除分组下的常用联系人
                $sql = "delete from enterprise_display_member where group_id = {$groupid} and enterprise_id = {$group['enterprise_id']}";
            }
            if ($target == -1) { //把分组用户移动到未分组
                $sql = "update enterprise_display_member set group_id = 0 where group_id = {$groupid} and enterprise_id = {$group['enterprise_id']}";
            }
            if ($target > 0) { //移动到其他分组
                $sql = "update enterprise_display_member set group_id = {$target} where group_id = {$groupid} and enterprise_id = {$group['enterprise_id']}";
            }

        } else {
            //未分组
            if ($target > 0) { //移动到其他分组
                $sql = "update enterprise_display_member set group_id = {$target} where group_id=0 and enterprise_id = {$enterpriseid} and user_id={$user->id}";
            } else {
                $sql = "delete from  enterprise_display_member  where group_id = 0 and enterprise_id = {$enterpriseid} and user_id={$user->id}";
            }
        }

        $command = $connection->createCommand($sql);
        $result0 = $command->execute();
        $result = array();
        if ($groupid > 0 && $group) {
            $phoneid = $group['member_id'];
            $group->delete();
            $info = EnterpriseGroup::model()->findAll("member_id={$phoneid} order by sort asc");
            if (count($info)) {
                foreach ($info as $k => $v) {
                    EnterpriseGroup::model()->updateAll(array("sort" => ($k + 1)), "id={$v['id']}");
                }
            }
        }

        $result ['ret_num'] = 0;
        $result ['ret_msg'] = '操作成功';
        echo json_encode($result);
    }

    /**
     * 分组成员管理
     */
    public function actionEditmember()
    {
        $this->check_key();
        $user = $this->check_user();
        $userid = Frame::getStringFromRequest('user_id');
        $groupid = Frame::getIntFromRequest('group_id');
        $enterpriseid = Frame::getIntFromRequest('enterprise_id');
        if (empty ($userid)) {
            $result ['ret_num'] = 5210;
            $result ['ret_msg'] = '成员ID为空';
            echo json_encode($result);
            die ();
        }
        $connection = Yii::app()->db;
        if (empty ($groupid) || $groupid <= 0) {
            $sql = "update enterprise_display_member set group_id = 0  where enterprise_id = {$enterpriseid} and user_id = {$user->id} and member_id in ({$userid})";
            $command = $connection->createCommand($sql);
            $result0 = $command->execute();
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
            echo json_encode($result);
            die ();
        }
        $sql = "update enterprise_display_member set group_id = 0  where group_id = {$groupid}";
        $command = $connection->createCommand($sql);
        $result0 = $command->execute();
        $sql = "update enterprise_display_member set group_id = 0  where enterprise_id = {$enterpriseid} and user_id = {$user->id} and member_id in ({$userid})";
        $command = $connection->createCommand($sql);
        $result0 = $command->execute();
        $all_user = explode(",", $userid);

        if (count($all_user) > 0) {
            $sql = "update enterprise_display_member set group_id = {$groupid}  where enterprise_id = {$enterpriseid} and user_id = {$user->id} and member_id in ({$userid})";
            $command = $connection->createCommand($sql);
            $result0 = $command->execute();
        }
        if ($result0) {
            $result ['ret_num'] = 0;
            $result ['ret_msg'] = '操作成功';
        } else {
            $result ['ret_num'] = 106;
            $result ['ret_msg'] = '编辑分组成员失败';
        }
        echo json_encode($result);
    }

    /**
     * 邀请成员列表
     */
    public function actionInviteMember()
    {
        $connection = Yii::app()->db;
        $this->check_key();
        $user = $this->check_user();
        $enterpriseid = Frame::getIntFromRequest('enterprise_id');
        $member_id = $user->id;
        if (empty($enterpriseid)) {
            $result['ret_num'] = 1052;
            $result['ret_msg'] = '该通讯录不存在';
            echo json_encode($result);
            die ();
        }
        $sql = "select member_id from enterprise_member where contact_id = " . $enterpriseid;
        $command = $connection->createCommand($sql);
        $allMember = $command->queryAll();
        $allMemberInfo = array();
        foreach ($allMember as $va) {
            $allMemberInfo[] = $va['member_id'];
        }
        //获取分组
        $sql1 = "select id,group_name name from group_contact where member_id = {$user->id}";
        $command = $connection->createCommand($sql1);
        $result1 = $command->queryAll();
        $result_group = array();
        $groupId = array();
        if ($result1) {
            foreach ($result1 as $value) {
                $groupId[] = $value['id'];
                $result_group[$value['id']] = $value['name'];
            }
        }
        $sql1 = "select b.phone, a.group_id from group_contact_info as a left join group_contact_phone as b on a.id = b.contact_info_id where a.member_id=" . $user->id;
        $command = $connection->createCommand($sql1);
        $info = $command->queryAll();
        $searchPhone = array();
        $groupPhone = array();
        if ($info) {
            foreach ($info as $key => $value) {
                if ($value['phone']) {
                    $searchPhone[] = "'" . $value['phone'] . "'";
                    $groupPhone[$value['group_id']][] = $value['phone'];
                }
            }
        }
        $searchPhone = array_unique($searchPhone);
        //根据手机号查找犇犇用户
        $searchMemberInfo = array();
        if (count($searchPhone) > 0) {
            $sql1 = "select id, name, poster, nick_name, phone, benben_id  from member where phone in (" . implode(",", $searchPhone) . ")";
            $command = $connection->createCommand($sql1);
            $mInfo = $command->queryAll();
            if ($mInfo) {
                $PinYin = new PYInitials('utf8');
                foreach ($mInfo as $key => $value) {
                    if (!in_array($value['id'], $allMemberInfo)) {
                        $name = $value['name'] ? $value['name'] : $value['nick_name'];
                        $searchMemberInfo[$value['phone']] = array(
                            'id' => $value['id'],
                            'phone' => $value['phone'],
                            'is_benben' => $value['benben_id'],
                            'name' => $name,
                            'pinyin' => substr($PinYin->getInitials($name), 0, 1),
                            'poster' => $value['poster'] ? URL . $value['poster'] : ""
                        );
                    }

                }
            }
        }

        $member_list = array();
        foreach ($result_group as $key => $value) {
            $currentGroupPhone = array();
            $currentMember = array();
            if (isset($groupPhone[$key])) {
                $currentGroupPhone = $groupPhone[$key];
            }
            if (count($currentGroupPhone)) {
                foreach ($currentGroupPhone as $p) {
                    if (isset($searchMemberInfo[$p])) {
                        $currentMember[] = $searchMemberInfo[$p];
                    }
                }
            }
            $member_list[] = array('id' => $key, 'name' => $value . "(" . count($currentMember) . "人)", 'member' => $currentMember);
        }
        $result['ret_num'] = 0;
        $result['ret_msg'] = '操作成功';
        $result['member_list'] = $member_list;
        echo json_encode($result);

    }

    /**
     * 获取邀请成员列表
     */
    public function actionInviteList()
    {
        $connection = Yii::app()->db;
        $this->check_key();
        $user = $this->check_user();
        $enterpriseid = Frame::getIntFromRequest('enterprise_id');
        $enterprise = Enterprise::model()->findByPk($enterpriseid);
        if (empty($enterprise)) {
            $result['ret_num'] = 504;
            $result['ret_msg'] = '通讯录ID不存在';
            echo json_encode($result);
            die ();
        }
        $enterpriseType = $enterprise['type'];
        $sql = "select * from enterprise_member where contact_id = " . $enterpriseid . " and invite_id = " . $user->id . " order by name desc,remark_name desc";
        $command = $connection->createCommand($sql);
        $member_list = $command->queryAll();
        $memberInfo = array();
        if ($member_list) {
            foreach ($member_list as $each) {
                $memberInfo[] = array('id' => $each['id'], 'member_id' => $each['member_id'], 'short_phone' => $this->eraseNull($each['short_phone']), 'phone' => ($enterpriseType == 2) ? "" : $this->eraseNull($each['phone']), 'name' => $each['name'], 'remark_name' => $this->eraseNull($each['remark_name']), 'contact_id' => $each['contact_id'], 'created_time' => $each['created_time']);
            }
        }
        $result['ret_num'] = 0;
        $result['ret_msg'] = '操作成功';
        $result['member_list'] = $memberInfo;
        echo json_encode($result);
    }

    /**
     * 删除我邀请的成员列表
     */
    public function actionInviteDelete()
    {
        $connection = Yii::app()->db;
        $this->check_key();
        $user = $this->check_user();
        $enterpriseid = Frame::getIntFromRequest('enterprise_id');
        $id = Frame::getIntFromRequest('id');
        if ($id && $enterpriseid) {
            $enterpriseidInfo = Enterprise::model()->findByPk($enterpriseid);
            if ($enterpriseidInfo) {
                $enterpriseidInfo->number = max($enterpriseidInfo->number - 1, 1);
                $enterpriseidInfo->out_num=$enterpriseidInfo->out_num+1;
                $enterpriseidInfo->update();
            }
            // $sql = "select id from  enterprise_member where id = ".$id." and contact_id = ".$enterpriseid;
            $sql = "select id, member_id from  enterprise_member where id = " . $id;
            $command = $connection->createCommand($sql);
            $queryInfo = $command->queryAll();
            if ($queryInfo) {
                $memberid = $queryInfo[0]['member_id'];
                if ($memberid) {
                    //删除取交集记录
                    $dsqllog = "delete from enterprise_display_member_log where member_id=" . $memberid . " and enterprise_id=" . $enterpriseid;
                    $commandlog = $connection->createCommand($dsqllog);
                    $info = $commandlog->execute();

                    //删除自己的通讯录
                    $dsql = "delete from enterprise_display_member where user_id = " . $memberid . " and enterprise_id = " . $enterpriseid;
                    $command = $connection->createCommand($dsql);
                    $info = $command->execute();
                }
                //从别人通讯录里删除
                $dsql = "delete from enterprise_display_member where member_id = " . $queryInfo[0]['id'] . " and enterprise_id = " . $enterpriseid;
                $command = $connection->createCommand($dsql);
                $info = $command->execute();
                // $sql = "delete from enterprise_member where id = ".$id." and contact_id = ".$enterpriseid;
                $sql = "delete from enterprise_member where id = " . $id;
                $command = $connection->createCommand($sql);
                $info2 = $command->execute();
            }

        }

        $result['ret_num'] = 0;
        $result['ret_msg'] = '操作成功';

        // if ($info) {
        // 	$result['ret_num'] = 0;
        // 	$result['ret_msg'] = '操作成功';
        // }else{
        // 	$result['ret_num'] = 1011;
        // 	$result['ret_msg'] = '删除失败';
        // }
        echo json_encode($result);
    }

    /*
     * 政企通讯录电话数统计
     * 涉及enterprise_display_member/enterprise_member/enterprise
     */
    public function actionAddTelNum(){
        $this->check_key();
        $user = $this->check_user();
        $enterprise_member_id = Frame::getIntFromRequest('enterprise_member_id');
        if(empty($enterprise_member_id)){
            $result['ret_num'] = 2016;
            $result['ret_msg'] = '缺少参数';
            echo json_encode($result);
            die ();
        }
        $info=EnterpriseDisplayMember::model()->find("user_id={$user['id']} and member_id={$enterprise_member_id}");
        if($info){
            $transaction = Yii::app()->db->beginTransaction();
            try {
                $dial_log = new EnterpriseDialLog();
                $dial_log->user_id = $user['id'];
                $dial_log->display_id = $info['id'];
                $dial_log->enterprise_id = $info['enterprise_id'];
                $dial_log->dial_time = time();
                $dial_log->save();
                $info->tel_num = $info['tel_num'] + 1;
                $info->update();
                $transaction->commit(); //提交事务会真正的执行数据库操作
            }catch (Exception $e) {
                $transaction->rollback(); //如果操作失败, 数据回滚
            }
        }
        $result['ret_num'] = 0;
        $result['ret_msg'] = '操作成功';
        echo json_encode($result);
    }

}