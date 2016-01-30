<?php

/**
 * 大喇叭管理
 * 
 * */
class EnterpriseBroadcastController extends EnterpriseBaseController
{

    public function actionIndex()
    {
        $model = EnterpriseBroadcast::model();
        $cri = new CDbCriteria();
        
        // 检索
        $result = array();
        $created_time1 = $_GET['created_time1'];
        $created_time2 = $_GET['created_time2'];
        $accessLevel = Frame::getIntFromRequest('access_level');
        
        // 起止时间
        if ($created_time1 && $created_time2) {
            $ct1 = strtotime($created_time1);
            $ct2 = strtotime($created_time2) + 86399;
            
            // if ($ct1 >= $ct2) {
            // $result['msg'] = "日期第一个必须比第二个小!";
            // } else {
            $cri->addCondition('t.created_time >= ' . $ct1, 'AND');
            $result['created_time1'] = $created_time1;
            $cri->addCondition('t.created_time <= ' . $ct2, 'AND');
            $result['created_time2'] = $created_time2;
            // }
        } else {
            if ($created_time1) {
                $cri->addCondition('t.created_time >= ' . strtotime($created_time1), 'AND');
                $result['created_time1'] = $created_time1;
            }
            if ($created_time2) {
                $cri->addCondition('t.created_time <= ' . strtotime($created_time2) + 86399, 'AND');
                $result['created_time2'] = $created_time2;
            }
        }
        
        // var_dump($this->administrator);die();
        if (empty($this->administrator_id)) {
            $user = "supper";
            $modelRole = EnterpriseRole::model();
            $level1 = $modelRole->findByAttributes(array(
                'enterprise_id' => $this->enterprise_id
            ));
        } else {
            $user = "admin";
            $modelManage = EnterpriseMemberManage::model();
            $level = $modelManage->findByAttributes(array(
                'member_id' => $this->administrator->id
            ));
            $modelRole = EnterpriseRole::model();
            $level1 = $modelRole->findByAttributes(array(
                'enterprise_id' => $this->enterprise_id
            ));
        }
        
        $access_level = $level->access_level;
        $this->administrator->id;
        // $cri->addCondition('');
        
        if (empty($this->administrator_id)) {
            $cri->addCondition('enterprise_id = ' . $this->enterprise_id);
        } else {
            if (isset($accessLevel) && $accessLevel != - 1 && $accessLevel != 0) {
                $cri->select = "t.*, n.name as nname,n.remark_name as nremark_name,m.name as mname,m.remark_name as mremark_name,(select group_concat(a.access_level) from benben_test.enterprise_member_manage a left join
              benben_test.enterprise_member b on a.member_id = b.id where b.member_id = t.member_id  and b.contact_id = t.enterprise_id and a.access_level = $accessLevel) as level";
                
                $cri->addCondition('t.enterprise_id= ' . $this->enterprise_id, 'AND');
                $cri->addCondition("(select group_concat(a.access_level) from benben_test.enterprise_member_manage a left join
                  benben_test.enterprise_member b on a.member_id = b.id where b.member_id = t.member_id  and b.contact_id = t.enterprise_id and a.access_level = $accessLevel) <>" . "''", 'AND');
                $cri->addCondition('apply_id is null', 'AND');
            } else {
                $cri->select = "t.*, n.name as nname,n.remark_name as nremark_name,m.name as mname,m.remark_name as mremark_name,(select group_concat(access_level) from benben_test.enterprise_member_manage a left join
                                            benben_test.enterprise_member b on a.member_id = b.id where b.member_id = t.member_id  and b.contact_id = t.enterprise_id) as level";
                $cri->addCondition('enterprise_id= ' . $this->enterprise_id, 'AND');
                
                $cri->addCondition("(select group_concat(access_level) from benben_test.enterprise_member_manage a left join
                benben_test.enterprise_member b on a.member_id = b.id where b.member_id = t.member_id  and b.contact_id = t.enterprise_id) <>" . "''", 'AND');
                
                $cri->addCondition("t.apply_id is null", 'AND');
                // $cri->condition = 'enterprise_id = ' . $this->enterprise_id .' and (select group_concat(access_level) from benben_test.enterprise_member_manage a left join
                // benben_test.enterprise_member b on a.member_id = b.id where b.member_id = t.member_id and b.contact_id = t.enterprise_id) <> ""';
            }
            
            // $cri->with = "member";
            $cri->join = "left join enterprise_member as m on m.contact_id=t.enterprise_id  and m.member_id=t.member_id
        		              left join enterprise_member as n on n.contact_id=t.enterprise_id  and n.member_id=t.receiver ";
            $cri->with = "receiver_member";
            $cri->order = "t.id desc";
        }
        if (empty($this->administrator_id)) {
            // $modelBroadcast=EnterpriseBroadcast::model();
            // $addcri='';
            // $sql="select t.*,r.name as rname,n.name as nname,n.remark_name as nremark_name
            // from benben_test.enterprise_broadcast t
            // left join benben_test.apply_register r on t.apply_id=r.id
            // left join benben_test.enterprise_member n on n.contact_id=t.enterprise_id and n.member_id=t.receiver
            // where t.enterprise_id=".$this->enterprise_id." and apply_id != ''".$addcri;
            
            // $items2=$modelBroadcast->findAllBySql($sql);
            // var_dump($items2);die();
        }
        
        // $cri1->join='left join EnterpriseMember as m on t.member_id =m.member_id';
        $pages = new CPagination();
        $pages->itemCount = $model->count($cri);
        $pages->pageSize = 18;
        $pages->applyLimit($cri);
        $items = $model->findAll($cri);
        
        // 获取发送者
        $receiver = EnterpriseMember::model()->with('member')->findAllByAttributes(array(
            'contact_id' => $this->enterprise_id
        ), array(
            'order' => 't.created_time desc'
        ));
        $this->render('index', array(
            'items' => $items,
            'pages' => $pages,
            'result' => $result,
            'user' => $user,
            'access_level' => $access_level,
            'receiver' => $receiver,
            'level' => $level,
            'level1' => $level1
        ));
    }
    
    // 创建大喇叭
    public function actionCreateBroadCast()
    {
        if (Yii::app()->request->isAjaxRequest) {
            // if (! empty($this->administrator_id)) {
            $receiver = Frame::getIntFromRequest('receiver');
            $content = Frame::getStringFromRequest("content");
            $images = Frame::getStringFromRequest("images");
            
            if (! $receiver || ! $content) {
                echo '400'; // 非法操作;
                Yii::app()->end();
            }
            
            $enterpriseBroadCast = new EnterpriseBroadcast();
            $enterpriseBroadCast->receiver = $receiver;
            $enterpriseBroadCast->content = $content;
            $enterpriseBroadCast->attachment = $images;
            $enterpriseBroadCast->enterprise_id = $this->enterprise_id;
            if (! empty($this->administrator_id)) {
                $enterpriseBroadCast->member_id = $this->administrator->member_id;
            } else {
                $enterpriseBroadCast->apply_id = $this->apply_id;
            }
            $enterpriseBroadCast->created_time = time();
            
            // if (empty($this->administrator_id)) {
            // $user = "supper";
            // $modelRole = EnterpriseRole::model();
            // $level1=$modelRole->findByAttributes(array(
            // 'enterprise_id'=>$this->enterprise_id
            // ));
            // } else {
            // $user = "admin";
            
            // $modelManage = EnterpriseMemberManage::model();
            // $level = $modelManage->findByAttributes(array(
            // 'member_id' => $this->administrator->id
            // ));
            // }
            if (! empty($this->administrator_id)) {
                $modelManage = EnterpriseMemberManage::model();
                $level = $modelManage->findByAttributes(array(
                    'member_id' => $this->administrator->id
                ));
                if ($level->broadcast_per_month > 0) {
                    $level->broadcast_per_month = $level->broadcast_per_month - 1;
                    $level->broadcast_available_month = $level->broadcast_available_month + 1;
                    $level->update();
                } else {
                    echo 401;
                    Yii::app()->end();
                }
                
                $modelRole = EnterpriseRole::model();
                
                $level1 = $modelRole->findByAttributes(array(
                    'enterprise_id' => $this->enterprise_id
                ));
                
                if ($level1->broadcast_available > 0) {
                    $level1->broadcast_available = $level1->broadcast_available - 1;
                    $level1->update();
                } else {
                    echo 401;
                    Yii::app()->end();
                }
            } else {
                $modelRole = EnterpriseRole::model();
                $level1 = $modelRole->findByAttributes(array(
                    'enterprise_id' => $this->enterprise_id
                ));
                if ($level1->broadcast_available > 0) {
                    $level1->broadcast_available = $level1->broadcast_available - 1;
                    $level1->update();
                } else {
                    echo 401;
                    Yii::app()->end();
                }
            }
            if ($enterpriseBroadCast->save()) {
                echo 200;
            } else {
                
                echo 401;
            }
            // }
        }
    }
    
    // 上传图片
    public function actionUploadImage()
    {
        if (Yii::app()->request->isAjaxRequest) {
            $base64 = $_POST['formFile'];
            $img = base64_decode($base64);
            $str = date('Y-m-d', time()) . '/';
            $tempFolder = Yii::getPathOfAlias('webroot') . '/uploads/broadcast/' . $str;
            
            if (! is_dir($tempFolder)) {
                mkdir($tempFolder, 0777, TRUE);
            }
            
            if ($_POST['name']) {
                $exe = explode('.', $_POST['name']);
                $fn = $this->generateNonceStrLength(16) . '.' . $exe[1];
            } else {
                $fn = false;
            }
            
            $file = Yii::getPathOfAlias('webroot') . '/uploads/broadcast/' . $str . $fn;
            
            if (file_put_contents($file, $img)) {
                echo '/uploads/broadcast/' . $str . $fn;
            } else {
                echo - 1;
            }
        }
    }

    private function generateNonceStrLength($length)
    {
        // 密码字符集，可任意添加你需要的字符
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i ++) {
            $str .= $chars[mt_rand(0, strlen($chars) - 1)];
        }
        return $str;
    }
}
