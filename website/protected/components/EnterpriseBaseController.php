<?php
class EnterpriseBaseController extends Controller{
	
	public $layout='//layouts/enterpriseLayout';

	public $enterprise_id;		//政企通讯录id
	public $apply_id;		//申请注册id
	public $apply_status;		//申请审核状态；0：待审核，1：审核通过，2：审核拒绝
	public $apply_type;			//申请类型：1个人 2 企业/组织 3 学校
	public $enterprise_access_level_set;		//政企通讯录查阅权限最大等级，0表示1级，1表示10级
	public $enterprise_group_level;		//政企通讯录最大分组级数	
	public $enterprise_type;			//政企通讯录类型：1 企业政企 2 虚拟网政企
	
	public $administrator_id;		//当是普通管理员登录时，将给其赋予会员id值
	public $administrator;		//存储普通管理员信息
	
	public function beforeAction($action){
		//判断是否登录
		if(Yii::app ()->user->getState ( "Enterprise_memberInfo" )){
			$this->apply_id=Yii::app()->user->getState("Enterprise_memberInfo")->id;
			$apply=ApplyRegister::model()->findByPk($this->apply_id);
			$this->enterprise_id=$apply->enterprise_id;
			$this->apply_status=$apply->status;
			$this->apply_type=$apply->apply_type;
			$this->enterprise_type=$apply->enterprise_type;
			$role=EnterpriseRole::model()->find( "(enterprise_id='".$this->enterprise_id."')" );
			$this->enterprise_access_level_set=$role->access_level_set;
			$this->enterprise_group_level=$role->group_level?$role->group_level:1;
			//若是普通管理员登录
			if(Yii::app ()->user->getState ( "Enterprise_administrator" )){
				$this->administrator_id=Yii::app ()->user->getState ( "Enterprise_administrator" )->id;
				$enterpriseMember=EnterpriseMemberNew::model();
				$cri2 = new CDbCriteria ();
				$cri2->join="left join enterprise_member_manage a on t.id=a.member_id";
				$cri2->condition="(t.member_id='".$this->administrator_id."')and(t.contact_id='".$this->enterprise_id."')";
				$cri2->select="t.*";
				$this->administrator=$enterpriseMember->find($cri2);
			}
			return true;
		}else{
			// 			Yii::error("非法操作！！！请先登录",Yii::app()->createUrl("site/login"));
			$this->redirect(Yii::app()->createUrl("enterpriseSite/login"));die();
		}
	
	}
	/**
	 * 验证政企通讯录是否审核通过
	 */
	protected function verify(){
		if($this->apply_status != 1){		//待审核或审核拒绝
			$this->redirect(array("/enterpriseIndex/index"));die();
		}
	}
	//分页
	function textPage($total,$page,$dolink){
// 		Yii::app()->clientScript->registerCssFile(Yii::app()->request->baseUrl."/themes/enterprise/independent/css/pager.css");
		$line = 8;
		$totalpage = $total;
		$dolink = '?';
		parse_str($_SERVER['QUERY_STRING'], $params);
		unset($params['p']);
	
		foreach ($params as $k => $v) {
			$dolink .= "&" .$k . "=" . urlencode($v);
		}
		$dolink .= '&';
		if($totalpage==1)
		{
			return '';
		}
		$pages = $totalpage;
	
	//$page是当前页数
		$line = $line - 1;
		$page = $page <= 0 ? 1 : $page;
		$page = $page > $pages ? $pages : $page;
		$prev = '';
		$next = '';
		if (($line + 1) > $pages) {		//总页数小于规定的长度
			for ($i = 1; $i <= $pages; $i++) {
				$apclass = $i == $page ? "g-fy-now" :'';		//给当前页添加类'g-fy-now'
				$tmp = ($i-1)==1 ?'page=1': 'page='.($i-1);
				$href = $dolink.'page='.$i;
				if($i == 1){		//当前页是第一页
					$prev ='<li><font><a href="javascript:;"> &lt;&lt;首页 </a></font></li><li><font><a href="javascript:;"> &lt;上一页 </a></font></li>';
					$next ='<li><span><a href="'.$dolink.'page='.($i+1).'">下一页&gt;</a></span></li><li><span><a href="'.$dolink.'page='.$pages.'">尾页&gt;&gt;</a></span></li>';
				}elseif($i == $pages and $i==$page){		//当前页是最后一页
					$prev ='<li><span><a href="'.$dolink.'page=1"> &lt;&lt;首页 </a></span></li><li><span><a href="'.$dolink.$tmp.'"> &lt;上一页 </a></span></li>';
					$next ='<li><font><a href="javascript:;">下一页&gt;</a></font></li><li><font><a href="javascript:;">尾页&gt;&gt;</a></font></li>';
				}elseif($i==$page){		//当前页既不是第一页也不是最后一页
					$prev ='<li><span><a href="'.$dolink.'page=1"> &lt;&lt;首页 </a></span></li><li><span><a href="'.$dolink.$tmp.'"> &lt;上一页 </a></span></li>';
					$next ='<li><span><a href="'.$dolink.'page='.($i+1).'">下一页&gt;</a></span></li><li><span><a href="'.$dolink.'page='.$pages.'">尾页&gt;&gt;</a></span></li>';
				}
				$conpage .= "<li class='$apclass'><a href='$href'><p>$i</p></a></li>";
			}
		} else {
			$unit = ceil($line / 2);
			$s_show = $page - $unit;
			$e_show = $page + $unit;
	
			$s_show = $s_show <= 0 ? 1 : $s_show;
			$e_show = $e_show < ($line + 1) ? ($line + 1) : $e_show;
	
			if ($e_show > $pages) {
				$s_show = $pages - $line;
				$e_show = $pages;
			}
	
// 			if ($s_show > 1)
// 				$conpage .= '<li class="page"><a href="'.$dolink.'page=1">1</a></li><li class="page"><a style="padding:0">...</a></li>';
	
			for ($i = 1; $i <= $pages; $i++) {
				if ($i >= $s_show and $i <= $e_show) {
					$apclass = $i == $page ? "g-fy-now" :'';
					$tmp = ($i-1)==1 ?'page=1': 'page='.($i-1);
	
					$href = $dolink.'page='.$i;
					if($i == 1){
						$prev ='<li><font><a href="javascript:;"> &lt;&lt;首页 </a></font></li><li><font><a href="javascript:;"> &lt;上一页 </a></font></li>';
						$next ='<li><span><a href="'.$dolink.'page='.($i+1).'">下一页&gt;</a></span></li><li><span><a href="'.$dolink.'page='.$pages.'">尾页&gt;&gt;</a></span></li>';
					}elseif($i == $pages and $i==$page){
						$prev ='<li><span><a href="'.$dolink.'page=1"> &lt;&lt;首页 </a></span></li><li><span><a href="'.$dolink.$tmp.'"> &lt;上一页 </a></span></li>';
						$next ='<li><font><a href="javascript:;">下一页&gt;</a></font></li><li><font><a href="javascript:;">尾页&gt;&gt;</a></font></li>';
					}elseif($i==$page){
						$prev ='<li><span><a href="'.$dolink.'page=1"> &lt;&lt;首页 </a></span></li><li><span><a href="'.$dolink.$tmp.'"> &lt;上一页 </a></span></li>';
						$next ='<li><span><a href="'.$dolink.'page='.($i+1).'">下一页&gt;</a></span></li><li><span><a href="'.$dolink.'page='.$pages.'">尾页&gt;&gt;</a></span></li>';
					}
					$conpage .= "<li class='$apclass'><a href='$href'><p>$i</p></a></li>";
				}
			}
// 			if ($e_show < $pages){
// 				$conpage .= '<li class="page"><a style="padding:0">...</a></li><li class="page"><a href="'.$dolink.'page='.$totalpage.'">'.$totalpage.'</a></li>';
// 			}
		}
		$returnstr = $prev.$conpage.$next;
		return $returnstr;
	}


	
	
	
}