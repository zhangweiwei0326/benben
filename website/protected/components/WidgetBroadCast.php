<?php

class WidgetBroadCast extends CWidget
{

    public $id;

    public $enterprise_id;

    public $member_id;

    public $apply_id;

    public function init()
    {}

    public function run()
    {
        $model = EnterpriseBroadcast::model();
        if ($this->member_id != "" && $this->apply_id == "") {
            $cri = new CDbCriteria();
            $cri->select = "t.*, n.name as nname,n.remark_name as nremark_name,m.name as mname,m.remark_name as mremark_name,(select group_concat(access_level) from benben_test.enterprise_member_manage a left join
                                            benben_test.enterprise_member b on a.member_id = b.id where b.member_id = t.member_id  and b.contact_id = t.enterprise_id) as level";
            $cri->addCondition('enterprise_id= ' . $this->enterprise_id, 'AND');
            
            $cri->addCondition("(select group_concat(access_level) from benben_test.enterprise_member_manage a left join
                benben_test.enterprise_member b on a.member_id = b.id where b.member_id = t.member_id  and b.contact_id = t.enterprise_id) <>" . "''", 'AND');
//             $cri->addCondition('apply_id = ""', 'AND');
//             $cri->addCondition('t.member_id = '.$this->member_id, 'AND');
            $cri->addCondition('t.id = '.$this->id, 'AND');
            $cri->join = "left join enterprise_member as m on m.contact_id=t.enterprise_id  and m.member_id=t.member_id
        		              left join enterprise_member as n on n.contact_id=t.enterprise_id  and n.member_id=t.receiver ";
            $cri->with = "receiver_member";
            $cri->order = "t.id desc";
            $cri->limit = "1";
            
            $item = $model->find($cri);
            
            $html = "";
            if (isset($item)) {
            
                $html .= "<dl class=\"con_list com\">";
                if ($item->nname) {
                    $html .= "<dt class=\"con_list_dt\">" . $item->nname . "</dt>";
                } else {
                    $html .= "<dt class=\"con_list_dt\">" . $item->nremark_name . "</dt>";
                }
                if ($item->remarks) {
                    $html .= '<dd class="con_list_fdd">' . $item->remarks . '</dd>';
                } else {
                    $html .= '<dd class="con_list_fdd">' . '　　　' . '</dd>';
                }
                if (mb_strlen($item->content, 'utf8') > 20) {
                    $html .= '<dd class="con_list_sdd">' . mb_substr($item->content, 0, 20, 'utf8') . '...' . '</dd>';
                } else {
                    $html .= '<dd class="con_list_sdd">' . $item->content . '</dd>';
                }
            
                $html .= '<dd class="con_list_tdd">'.$item->level.'</dd>';
                $nname = $item->nname != "" ? $item->nname : $item->nremark_name;
                $attachment = $item->attachment == "" ? "" : $item->attachment;
                $html .= '<dd class="con_list_ldd col_bule"
		            					receiver="' . $nname . '"
		            					content="' . $item->content . '"
		            					attachment="' . $attachment . '">查看详情</dd>';
                $html .= "</dl>";
                echo $html;
            }
            
        } else {
            $sql = "select t.*,r.name as rname,n.name as nname,n.remark_name as nremark_name
            			from benben_test.enterprise_broadcast t
            			left join benben_test.apply_register r on t.apply_id=r.id
            			left join benben_test.enterprise_member n on n.contact_id=t.enterprise_id  and n.member_id=t.receiver
            			where t.enterprise_id=" . $this->enterprise_id . " and t.apply_id = $this->apply_id and t.id = $this->id limit 1";
            $item = $model->findBySql($sql);
            $html = "";
            if (isset($item)) {
                
                $html .= "<dl class=\"con_list com\">";
                if ($item->nname) {
                    $html .= "<dt class=\"con_list_dt\">" . $item->nname . "</dt>";
                } else {
                    $html .= "<dt class=\"con_list_dt\">" . $item->nremark_name . "</dt>";
                }
                if ($item->remarks) {
                    $html .= '<dd class="con_list_fdd">' . $item->remarks . '</dd>';
                } else {
                    $html .= '<dd class="con_list_fdd">' . '　　　' . '</dd>';
                }
                if (mb_strlen($item->content, 'utf8') > 20) {
                    $html .= '<dd class="con_list_sdd">' . mb_substr($item->content, 0, 20, 'utf8') . '...' . '</dd>';
                } else {
                    $html .= '<dd class="con_list_sdd">' . $item->content . '</dd>';
                }
                
                $html .= '<dd class="con_list_tdd">　　　</dd>';
                $nname = $item->nname != "" ? $item->nname : $item->nremark_name;
                $attachment = $item->attachment == "" ? "" : $item->attachment;
                $html .= '<dd class="con_list_ldd col_bule"
		            					receiver="' . $nname . '"
		            					content="' . $item->content . '"
		            					attachment="' . $attachment . '">查看详情</dd>';
                $html .= "</dl>";
                echo $html;
            }
        }
    }
}