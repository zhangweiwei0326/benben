<?php
class CreationController extends PublicController
{
	public $layout = false;
	
	/**
	 * 我的微创作及搜索
	 */
	public function actionMylist(){
		$this->check_key();
		$last_time = Frame::getIntFromRequest('last_time');
		$keyword = Frame::getStringFromRequest('keyword');
		$user = $this->check_user();
		$connection = Yii::app()->db;
		if($keyword){
			// $sql = "select b.id MemberId,b.benben_id,b.nick_name Name,b.poster Poster,a.id Id,a.description Description,a.type Type,a.views Views,a.created_time CreatedTime from creation a inner join member b on a.member_id = b.id where a.description like '%{$keyword}%' and a.status = 0 and b.id = {$user->id} order by a.created_time desc limit 10";
			$sql = "select b.id MemberId,b.benben_id,b.nick_name Name,b.poster Poster,a.id Id,a.description Description,a.type Type,a.views Views,a.created_time CreatedTime,a.status from creation a inner join member b on a.member_id = b.id where a.is_delete = 0 and a.description like '%{$keyword}%' and b.id = {$user->id} order by a.created_time desc limit 10";
			if($last_time){
				$sql = "select b.id MemberId,b.benben_id,b.nick_name Name,b.poster Poster,a.id Id,a.description Description,a.type Type,a.views Views,a.created_time CreatedTime,a.status from creation a inner join member b on a.member_id = b.id where a.is_delete = 0 and a.description like '%{$keyword}%' and a.created_time < {$last_time} and b.id = {$user->id} order by a.created_time desc limit 10";
			}
		}else{
			$sql = "select b.id MemberId,b.benben_id,b.nick_name Name,b.poster Poster,a.id Id,a.description Description,a.type Type,a.views Views,a.created_time CreatedTime,a.status from creation a inner join member b on a.member_id = b.id where a.is_delete = 0 and b.id = {$user->id} order by a.created_time desc limit 10";
			if($last_time){
				$sql = "select b.id MemberId,b.benben_id,b.nick_name Name,b.poster Poster,a.id Id,a.description Description,a.type Type,a.views Views,a.created_time CreatedTime,a.status from creation a inner join member b on a.member_id = b.id where a.is_delete = 0 and a.created_time < {$last_time} and b.id = {$user->id} order by a.created_time desc limit 10";
			}
		}
		$command = $connection->createCommand($sql);
		$result0 = $command->queryAll();
		
	    if($result0){
	    	//查出自己通讯录里的犇犇用户
	    	$rename = array();
// 	    	$benbenid = array();
// 	    	foreach ($result0 as $key=>$value){
// 	    		$benbenid[] = $value['benben_id'];
// 	    	}
	    	if(1){
	    		//$benbenid = implode(",", $benbenid);
	    		$sqla = "select a.is_benben,b.name from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id
	    		where b.member_id = {$user->id} and a.is_benben > 0";
	    		$command = $connection->createCommand($sqla);
	    		$resul = $command->queryAll();
	    		foreach ($resul as $v1){
	    		$rename[$v1['is_benben']] = $v1['name'];
	    		}
	    	}
	    		
			$creationid = "";
			foreach ($result0 as $val){
				$creationid .= $val['Id'].',';
			}
			$creationid = trim($creationid);
			$creationid =trim($creationid,',');
			if($creationid){
				$sql1 = "select creation_id,attachment from creation_attachment where creation_id in ({$creationid})";
				$command = $connection->createCommand($sql1);
				$result1 = $command->queryAll();
				//查询评论
				$sql2 = "select a.creation_id,a.member_id,a.review,a.created_time,b.nick_name,b.name rname,b.benben_id from creation_comment a inner join member b on a.member_id = b.id where a.creation_id in ({$creationid}) order by a.created_time asc";
				$command = $connection->createCommand($sql2);
				$result2 = $command->queryAll();
				$comment = array();
				foreach($result2 as $key => $va){
					//if(count($comment[$va['creation_id']])>2) continue;
					$comment[$va['creation_id']][] = array(
							"creation_id"=>$va['creation_id'],
							"member_id"=>$va['member_id'],
							"nick_name"=>$rename[$va['benben_id']] ? $rename[$va['benben_id']] : $va['nick_name'],
							"review"=>$va['review'],
							"created_time"=>$va['created_time']
					);
				}
				//查询是否点赞
				/* $laud_status = array();
				$sql = "select creation_id from creation_like where creation_id in ({$creationid}) and member_id = {$user->id}";
				$command = $connection->createCommand($sql);
				$laud = $command->queryAll();
				foreach ($laud as $valu){
					$laud_status[$valu['creation_id']] = $user->id;
				} */
				$laud_status = array();
				$sql = "select a.creation_id, b.nick_name as name, b.id,b.benben_id from creation_like as a left join member as b on a.member_id = b.id where a.creation_id in ({$creationid})";
				$command = $connection->createCommand($sql);
				$laud = $command->queryAll();
				foreach ($laud as $valu){
					$laud_status[$valu['creation_id']][] = $valu['id'];
					$laud_status_name[$valu['creation_id']][] = $rename[$valu['benben_id']] ? $rename[$valu['benben_id']] :  $valu['name'];
				}
			}
			//添加图片信息
			$thumb = "";
			foreach ($result0 as $key=>$value){
				if($rename[$value['benben_id']]){
					$result0[$key]['Name'] = $rename[$value['benben_id']];
				}
				$currentLaud = 0;
				if (isset($laud_status[$value['Id']]) && in_array($user->id, $laud_status[$value['Id']])) {
					$currentLaud = 1;
				}
				$laudString = '';
				if (isset($laud_status_name[$value['Id']])){
					if (count($laud_status_name[$value['Id']]) < 5) {
						$laudString = implode("、", $laud_status_name[$value['Id']]);
					}else{
						$laudString = $laud_status_name[$value['Id']][0]."、".$laud_status_name[$value['Id']][1]."、".$laud_status_name[$value['Id']][2]."、".$laud_status_name[$value['Id']][3];
					}
						
					$laudString .= '等人点赞';
				}
				
				$result0[$key]['Laud'] = $currentLaud;
				$result0[$key]['laud_list'] = $laudString;
				
				//$result0[$key]['Laud'] = ($laud_status[$value['Id']] == $user->id) ? "1":"0";
				$result0[$key]['Poster'] = $value['Poster'] ? URL.$value['Poster']:"";
				$result0[$key]['Images'] = array();
				$img = "";
				$thumb_img ="";
				$targetImage = null;
				foreach ($result1 as $v){
					if($value['Id'] == $v['creation_id']){
						$thumb = explode("/", $v['attachment']);
						$thumb[4] = 'small'.$thumb[4];
						$img .= URL.$v['attachment'].",";
						$thumb_img .= URL.implode("/",$thumb).",";
						if (!$targetImage) $targetImage = Yii::getPathOfAlias('webroot').implode("/",$thumb);
					}
				}
				if(file_exists($targetImage)){
					if(substr($targetImage, -3) != "amr"){
						$info = getimagesize($targetImage);
						$result0[$key]['Width'] = $info[0];
						$result0[$key]['Height'] = $info[1];
					}
				}
				$img = trim($img);
				$img =trim($img,',');
				$thumb_img = trim($thumb_img);
				$thumb_img =trim($thumb_img,',');
			    if($img){
	            	$img = explode(',',$img);
	            }
	            if($thumb_img){
	            	$thumb_img = explode(',',$thumb_img);
	            }
				$result0[$key]['Images'] = $img ? $img : array();
				$result0[$key]['Thumb'] = $thumb_img ? $thumb_img : array();
// 				if(count($result0[$key]['Thumb']) == 1){
// 					if($thumb_img[0]){
// 						if(file_exists($thumb_img[0])){
// 							if(substr($thumb_img[0], -3) != "amr"){
// 								$info = getimagesize($thumb_img[0]);
// 								$result0[$key]['Width'] = $info[0];
// 								$result0[$key]['Height'] = $info[1];
// 							}
// 						}					
// 					}
// 				}
				$result0[$key]['Comment'] =$comment[$value['Id']] ? $comment[$value['Id']] : array();
			}
		} 
		if($result0){
			$sql0 = "update creation set views=views+1 where id in ({$creationid})";
			$command = $connection->createCommand($sql0);
			$re = $command->execute();
		}
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
		$result ['number_info'] = $result0;
		echo json_encode( $result );
	}
	
	/**
	* 删除自己的微创作
	**/
	public function actionDeleteItem() {
		$this->check_key();
		$user = $this->check_user();
		$id = Frame::getIntFromRequest('id');
		$model = Creation::model()->findByPk($id);
		if (empty($model)) {
			$result ['ret_num'] = 100;
			$result ['ret_msg'] = '帖子不存在';
			echo json_encode( $result );
			die();
		}
		if ($model->member_id != $user->id) {
			$result ['ret_num'] = 101;
			$result ['ret_msg'] = '没有删除权限';
			echo json_encode( $result );
			die();
		}
		$model->is_delete = 1;
		if ($model->update()) {
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = '删除成功';
			echo json_encode( $result );
			die();
		}else {
			$result ['ret_num'] = 102;
			$result ['ret_msg'] = '删除失败';
			echo json_encode( $result );
			die();
		}
	}
	
	/**
	 * 微创作列表及搜索
	 */
	public function actionList(){		
		$this->check_key();
		$last_time = Frame::getIntFromRequest('last_time');
		$keyword = Frame::getStringFromRequest('keyword');
		$islike = Frame::getIntFromRequest('is_like');
		$user = $this->check_user();
		$connection = Yii::app()->db;
		//查询我关注的作者
		$sqla = "select creation_auth_id from creation_attention where member_id = {$user->id}";
		$command = $connection->createCommand($sqla);
		$resulta = $command->queryAll();
		$authid = "";
		$authid_arr = array();
		foreach ($resulta as $vl){
			$authid .= $vl['creation_auth_id'].",";
			$authid_arr[] = $vl['creation_auth_id'];
		}
		$authid = trim($authid);
		$authid =trim($authid,',');
		if($islike){
			if($authid){
				if($keyword){
					$sql = "select b.id MemberId,b.benben_id,b.nick_name Name,b.poster Poster,a.id Id,a.description Description,a.type Type,a.views Views,a.created_time CreatedTime from creation a inner join member b on a.member_id = b.id where a.member_id in ({$authid}) and a.description like '%{$keyword}%' and a.status = 0 and a.is_delete = 0 order by a.created_time desc limit 10";
					if($last_time){
						$sql = "select b.id MemberId,b.benben_id,b.nick_name Name,b.poster Poster,a.id Id,a.description Description,a.type Type,a.views Views,a.created_time CreatedTime from creation a inner join member b on a.member_id = b.id where a.member_id in ({$authid}) and a.description like '%{$keyword}%' and a.status = 0 and a.is_delete = 0 and a.created_time < {$last_time} order by a.created_time desc limit 10";
					}
				}else{
					$sql = "select b.id MemberId,b.benben_id,b.nick_name Name,b.poster Poster,a.id Id,a.description Description,a.type Type,a.views Views,a.created_time CreatedTime from creation a inner join member b on a.member_id = b.id where a.member_id in ({$authid}) and a.status = 0 and a.is_delete = 0 order by a.created_time desc limit 10";
					if($last_time){
						$sql = "select b.id MemberId,b.benben_id,b.nick_name Name,b.poster Poster,a.id Id,a.description Description,a.type Type,a.views Views,a.created_time CreatedTime from creation a inner join member b on a.member_id = b.id where a.member_id in ({$authid}) and a.status = 0 and a.is_delete = 0 and a.created_time < {$last_time} order by a.created_time desc limit 10";
					}
				}
				$command = $connection->createCommand($sql);
				$result0 = $command->queryAll();
			}else{
				$result0 = array();
			}
			
		}else{
			if($keyword){
				$sql = "select b.id MemberId,b.benben_id,b.nick_name Name,b.poster Poster,a.id Id,a.description Description,a.type Type,a.views Views,a.created_time CreatedTime from creation a inner join member b on a.member_id = b.id where a.description like '%{$keyword}%' and a.status = 0 and a.is_delete = 0 order by a.created_time desc limit 10";
				if($last_time){
					$sql = "select b.id MemberId,b.benben_id,b.nick_name Name,b.poster Poster,a.id Id,a.description Description,a.type Type,a.views Views,a.created_time CreatedTime from creation a inner join member b on a.member_id = b.id where a.description like '%{$keyword}%' and a.status = 0 and a.is_delete = 0 and a.created_time < {$last_time} order by a.created_time desc limit 10";
				}
			}else{
				$sql = "select b.id MemberId,b.benben_id,b.nick_name Name,b.poster Poster,a.id Id,a.description Description,a.type Type,a.views Views,a.created_time CreatedTime from creation a inner join member b on a.member_id = b.id where a.status = 0 and a.is_delete = 0 order by a.created_time desc limit 10";
				if($last_time){
					$sql = "select b.id MemberId,b.benben_id,b.nick_name Name,b.poster Poster,a.id Id,a.description Description,a.type Type,a.views Views,a.created_time CreatedTime from creation a inner join member b on a.member_id = b.id where a.status = 0 and a.is_delete = 0 and a.created_time < {$last_time} order by a.created_time desc limit 10";
				}
			}
			$command = $connection->createCommand($sql);
			$result0 = $command->queryAll();
		}
								
		if($result0){
			//查出自己通讯录里的犇犇用户
			$rename = array();
// 			$benbenid = array();
// 			foreach ($result0 as $key=>$value){
// 				$benbenid[] = $value['benben_id'];
// 			}
			if(1){
				//$benbenid = implode(",", $benbenid);
				$sqla = "select a.is_benben,b.name from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id
				where b.member_id = {$user->id} and a.is_benben >0";
				$command = $connection->createCommand($sqla);
				$resul = $command->queryAll();
				foreach ($resul as $v1){
					$rename[$v1['is_benben']] = $v1['name'];
				}
			}
				
			$creationid = "";
			foreach ($result0 as $val){
				$creationid .= $val['Id'].',';
				$creationid_tpl[$val['Id']]=$val['MemberId'] ;
			}
			$creationid = trim($creationid);
			$creationid =trim($creationid,',');
			if($creationid){
				$sql1 = "select creation_id,attachment from creation_attachment where creation_id in ({$creationid})";
				$command = $connection->createCommand($sql1);
				$result1 = $command->queryAll();
				//查询评论
				$sql2 = "select a.creation_id,a.member_id,a.review,a.replier,a.created_time,b.nick_name,b.name rname,b.benben_id from creation_comment a inner join member b on a.member_id = b.id where a.creation_id in ({$creationid}) order by a.created_time asc";
				$command = $connection->createCommand($sql2);
				$result2 = $command->queryAll();
				$comment = array();
				foreach($result2 as $key => $va){
					$owninfo_tpl=Member::model()->find("id={$va['replier']}");
					$benben=$this->getfriend($user['id'],2);
					//if(count($comment[$va['creation_id']])>2) continue;
					$comment[$va['creation_id']][] = array(
							"creation_id"=>$va['creation_id'],
							"member_id"=>$va['member_id'],
							"nick_name"=>$rename[$va['benben_id']] ? $rename[$va['benben_id']] :  $va['nick_name'],
							"review"=>$va['replier']==$creationid_tpl[$va['creation_id']]?$va['review']:
									($va['replier']==$user['id']?$va['review']."@".$user['nick_name']:
											($benben[$owninfo_tpl['benben_id']]?$va['review']."@".$benben[$owninfo_tpl['benben_id']]:
													($owninfo_tpl['nick_name']?$va['review']."@".$owninfo_tpl['nick_name']:$va['review']))),
							"created_time"=>$va['created_time']
					);
				}
				//查询是否点赞
				$laud_status = array();
				$sql = "select a.creation_id, b.nick_name as name, b.id,b.benben_id from creation_like as a left join member as b on a.member_id = b.id where a.creation_id in ({$creationid})";
				$command = $connection->createCommand($sql);
				$laud = $command->queryAll();
				foreach ($laud as $valu){
					$laud_status[$valu['creation_id']][] = $valu['id'];
					$laud_status_name[$valu['creation_id']][] = $rename[$valu['benben_id']] ? $rename[$valu['benben_id']] :  $valu['name'];
				}
			}
						
			//添加图片信息
			$thumb = "";
			foreach ($result0 as $key=>$value){
				if($rename[$value['benben_id']]){
					$result0[$key]['Name'] = $rename[$value['benben_id']];
				}
				
				$is_attention = 0;
				if(in_array($value['MemberId'], $authid_arr)){
					$is_attention = 1;
				}
				$currentLaud = 0;
				if (isset($laud_status[$value['Id']]) && in_array($user->id, $laud_status[$value['Id']])) {
					$currentLaud = 1;
				}
				$laudString = '';
				if (isset($laud_status_name[$value['Id']])){
					if (count($laud_status_name[$value['Id']]) < 5) {
						$laudString = implode("、", $laud_status_name[$value['Id']]);
					}else{
						$laudString = $laud_status_name[$value['Id']][0]."、".$laud_status_name[$value['Id']][1]."、".$laud_status_name[$value['Id']][2]."、".$laud_status_name[$value['Id']][3];
					}
					
					$laudString .= '等人点赞';
				}
				
				$result0[$key]['Laud'] = $currentLaud;
				$result0[$key]['laud_list'] = $laudString;
				$result0[$key]['Attention'] = $is_attention;
				$result0[$key]['Poster'] = $value['Poster'] ? URL.$value['Poster']:"";
				$result0[$key]['Images'] = array();
				$img = "";
				$thumb_img ="";
				$targetImage = null;
				foreach ($result1 as $v){
					if($value['Id'] == $v['creation_id']){
						$thumb = explode("/", $v['attachment']);
						$thumb[4] = 'small'.$thumb[4];
						$img .= URL.$v['attachment'].",";
						$thumb_img .= URL.implode("/",$thumb).",";
						if (!$targetImage) $targetImage = Yii::getPathOfAlias('webroot').implode("/",$thumb);
					}
				}
				if(file_exists($targetImage)){
					if(substr($targetImage, -3) != "amr"){
						$info = getimagesize($targetImage);
						$result0[$key]['Width'] = $info[0];
						$result0[$key]['Height'] = $info[1];
					}
				}
				$img = trim($img);
				$img =trim($img,',');
				$thumb_img = trim($thumb_img);
				$thumb_img =trim($thumb_img,',');
	//var_dump(json_encode(explode(',',$img)));
	            if($img){
	            	$img = explode(',',$img);
	            }
	            if($thumb_img){
	            	$thumb_img = explode(',',$thumb_img);
	            }
				$result0[$key]['Images'] = $img ? $img : array();
				$result0[$key]['Thumb'] = $thumb_img ? $thumb_img : array();
// 				if(count($result0[$key]['Thumb']) == 1){
// 					if($thumb_img[0]){
// 						if(file_exists($thumb_img[0])){
// 							if(substr($thumb_img[0], -3) != "amr"){
// 								$info = getimagesize($thumb_img[0]);
// 								$result0[$key]['Width'] = $info[0];
// 								$result0[$key]['Height'] = $info[1];
// 							}
// 						}											
// 					}					
// 				}
				$result0[$key]['Comment'] =$comment[$value['Id']] ? $comment[$value['Id']] : array();
			}
		}
		//var_dump(json_encode($result0));
		if($result0){
			$sql0 = "update creation set views=views+1 where id in ({$creationid})";
			$command = $connection->createCommand($sql0);
		    $re = $command->execute();
		}
			$result ['ret_num'] = 0;
			$result ['ret_msg'] = '操作成功';
			$result ['number_info'] = $result0;						
		    echo json_encode( $result );
	}

	/*
	 * 被评论列表
	 * 涉及Creation，creation_comment，member
	 */
	public function actionCreationcommentlist(){
		$this->check_key();
		$user = $this->check_user();
		$creation_time = Frame::getIntFromRequest('creation_time');
		$listnum = Frame::getIntFromRequest('listnum');
		$connection = Yii::app()->db;

		if(empty($creation_time)||empty($listnum)){
			$result['ret_num'] = 100;
			$result['ret_msg'] = '缺少参数';
			echo json_encode( $result );
			die();
		}
		//查出我发表的被回复的内容数、查出被@的内容数
		$sqlp="select a.*,b.description,c.nick_name,c.poster,c.huanxin_username from creation_comment as a
		left join creation as b on a.creation_id=b.id left join member as c on c.id=a.member_id
		where (b.member_id={$user['id']} or a.replier={$user['id']}) and a.created_time<{$creation_time} and b.is_delete=0 and a.member_id!={$user->id} order by a.created_time desc limit 0,{$listnum}";
		$command=$connection->createCommand($sqlp);
		$resultp=$command->queryAll();

		$p_num=$resultp ? count($resultp) : 0;
		$nickname=$this->getContactIdName($user['id']);
		foreach($resultp as $k=>$v){
			$resultp[$k]['poster']=$v['poster'] ? URL.$v['poster'] : "";
			$resultp[$k]['nick_name']=$nickname[$v['member_id']] ? $nickname[$v['member_id']] : $v['nick_name'];
		}
		$result['comment']=$resultp;
		$result['num']=$p_num;
		$result['ret_num'] = 0;
		$result['ret_msg'] = '获取成功';
		echo json_encode( $result );
	}
	
	/**
	 * 获取一条微创作
	 */
	public function actionListone(){
		$this->check_key();
		$creationid = Frame::getIntFromRequest('creationid');
		if(empty($creationid)){
			$result['ret_num'] = 129;
			$result['ret_msg'] = '微创作ID为空';
			echo json_encode( $result );
			die();
		}
		$user = $this->check_user();
		$sql = "select b.id MemberId,b.nick_name Name,b.poster Poster,a.id Id,a.description Description,a.type Type,a.views Views,a.created_time CreatedTime from creation a inner join member b on a.member_id = b.id where a.id = {$creationid} and a.status = 0 ";
		$connection = Yii::app()->db;
		$command = $connection->createCommand($sql);
		$result0 = $command->queryAll();
		if($result0){
			$creationid = "";
			foreach ($result0 as $val){
				$creationid .= $val['Id'].',';
			}
			$creationid = trim($creationid);
			$creationid =trim($creationid,',');
			if($creationid){
				$sql1 = "select creation_id,attachment from creation_attachment where creation_id in ({$creationid})";
				$command = $connection->createCommand($sql1);
				$result1 = $command->queryAll();
				//查询评论
				$sql2 = "select a.creation_id,a.member_id,a.review,a.created_time,b.nick_name from creation_comment a inner join member b on a.member_id = b.id where a.creation_id in ({$creationid}) order by a.created_time asc";
				$command = $connection->createCommand($sql2);
				$result2 = $command->queryAll();
				$comment = array();
				foreach($result2 as $key => $va){
					//if(count($comment[$va['creation_id']])>2) continue;
					$comment[$va['creation_id']][] = array(
							"creation_id"=>$va['creation_id'],
							"member_id"=>$va['member_id'],
							"nick_name"=>$va['nick_name'],
							"review"=>$va['review'],
							"created_time"=>$va['created_time']
					);
				}
				//查询是否点赞
				$laud_status = array();
				$sql = "select creation_id from creation_like where creation_id in ({$creationid}) and member_id = {$user->id}";
				$command = $connection->createCommand($sql);
				$laud = $command->queryAll();
				foreach ($laud as $valu){
					$laud_status[$valu['creation_id']] = $user->id;
				}
			}
			//添加图片信息
			$thumb = "";
			foreach ($result0 as $key=>$value){
				$result0[$key]['Laud'] = ($laud_status[$value['Id']] == $user->id) ? "1":"0";
				$result0[$key]['Poster'] = $value['Poster'] ? URL.$value['Poster']:"";
				$result0[$key]['Images'] = array();
				$img = "";
				$thumb_img ="";
				$targetImage = null;
				foreach ($result1 as $v){
					if($value['Id'] == $v['creation_id']){
						$thumb = explode("/", $v['attachment']);
						$thumb[4] = 'small'.$thumb[4];
						$img .= URL.$v['attachment'].",";
						$thumb_img .= URL.implode("/",$thumb).",";
						if (!$targetImage) $targetImage = Yii::getPathOfAlias('webroot').implode("/",$thumb);
					}
				}
				if(file_exists($targetImage)){
					if(substr($targetImage, -3) != "amr"){
						$info = getimagesize($targetImage);
						$result0[$key]['Width'] = $info[0];
						$result0[$key]['Height'] = $info[1];
					}
				}
				$img = trim($img);
				$img =trim($img,',');
				$thumb_img = trim($thumb_img);
				$thumb_img =trim($thumb_img,',');
				//var_dump(json_encode(explode(',',$img)));
				if($img){
					$img = explode(',',$img);
				}
				if($thumb_img){
					$thumb_img = explode(',',$thumb_img);
				}
				$result0[$key]['Images'] = $img ? $img : array();
				$result0[$key]['Thumb'] = $thumb_img ? $thumb_img : array();
// 				if(count($result0[$key]['Thumb']) == 1){
// 					if($thumb_img[0]){
// 						if(substr($thumb_img[0], -3) != "amr"){
// 							$info = getimagesize($thumb_img[0]);
// 							$result0[$key]['Width'] = $info[0];
// 							$result0[$key]['Height'] = $info[1];
// 						}
// 					}
// 				}
				$result0[$key]['Comment'] =$comment[$value['Id']] ? $comment[$value['Id']] : array();
			}
		}
		//var_dump(json_encode($result0));
		if($result0){
			$sql0 = "update creation set views=views+1 where id in ({$creationid})";
			$command = $connection->createCommand($sql0);
			$re = $command->execute();
		}
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
		$result ['number_info'] = $result0[0];
		echo json_encode( $result );
	}
	
	/**
	 * 微创作我的关注
	 */
	public function actionMyattention(){
		$this->check_key();
		$user = $this->check_user();
		$connection = Yii::app()->db;
		$nameArray = $this->getBenbenName($user->id);
		//查询我关注的作者
		$sqla = "select a.creation_auth_id,b.nick_name,b.poster,b.benben_id from creation_attention a inner join member b on a.creation_auth_id = b.id where a.member_id = {$user->id} order by a.created_time desc";
		$command = $connection->createCommand($sqla);
		$resulta = $command->queryAll();
		foreach ($resulta as $key => $value){
			$resulta[$key]['poster'] = $value['poster'] ? URL.$value['poster'] : "";
			if ($nameArray[$value['benben_id']]) {
				$resulta[$key]['nick_name'] = $nameArray[$value['benben_id']];
			}
		}
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
		$result ['number_info'] = $resulta;		
		echo json_encode( $result );
	}
	/**
	 * 微创作发布
	 */
	public function actionCreate(){
		$this->check_key();
		$img1 = Frame::saveImage('img1',1);	
		$img2 = Frame::saveImage('img2',1);
		$img3 = Frame::saveImage('img3',1);
		$img4 = Frame::saveImage('img4',1);
		$img5 = Frame::saveImage('img5',1);
		$img6 = Frame::saveImage('img6',1);
		$province = Frame::getIntFromRequest('province');
		$city = Frame::getIntFromRequest('city');
		$area = Frame::getIntFromRequest('area');
		$street = Frame::getIntFromRequest('street');
		$type = Frame::getIntFromRequest('type');
		$description = Frame::getStringFromRequest('description');
		$leagueid = Frame::getStringFromRequest('league_id');
		$region = Frame::getStringFromRequest('region');
		$audio = Frame::saveAudio('audio');
		if($type && empty($audio)){
			$result['ret_num'] = 1292;
			$result['ret_msg'] = '音频为空';
			echo json_encode( $result );
			die();
		}
		$user = $this->check_user();
		$connection = Yii::app()->db;
		
		$creation_info = new Creation();		
		$creation_info->member_id = $user->id;
		$creation_info->province = $province;
		$creation_info->city = $city;
		$creation_info->area = $area;
		$creation_info->street = $street;
		$creation_info->type = $type;
		$creation_info->description = $description;
		$creation_info->created_time = time();
		$creation_info->views = 0;
		$creation_info->goods = 0;
		if($creation_info->save()){
			$id = $creation_info->id;
			$v ="";
			if($img1){
				$v .= "({$id},'{$img1}'),";
			}
			if($img2){
				$v .= "({$id},'{$img2}'),";
			}
			if($img3){
				$v .= "({$id},'{$img3}'),";
			}
			if($img4){
				$v .= "({$id},'{$img4}'),";
			}
			if($img5){
				$v .= "({$id},'{$img5}'),";
			}
			if($img6){
				$v .= "({$id},'{$img6}'),";
			}
			if($audio){
				$v .= "({$id},'{$audio}'),";
			}
			$v = trim($v);
			$v =trim($v,',');
			if($v){
				$sql = "insert into creation_attachment (creation_id,attachment) values {$v}";
				$command = $connection->createCommand($sql);
				$result1 = $command->execute();
			}
			//查询联盟成员,发送消息
			if($leagueid){
				$sql = "select league_id, member_id, remark_content, type from league_member where status =1 and type > 0 and league_id in (".$leagueid.")";
				$command = $connection->createCommand($sql);
				$info = $command->queryAll();
				$memberId = array();
				$leagueId = array();
				$newsArray = array();
				if ($info) {
					foreach ($info as $key => $value) {
						if ($value['type'] == 1) {
							$sender = $user->id;
						}else{
							$sender = $value['remark_content'];
						}

						if (!in_array($value['league_id'], $leagueId)) {
							$leagueId[] = $value['league_id'];
						}
						if (!in_array($value['member_id'], $memberId)) {
							$memberId[] = $value['member_id'];
						}
						if (!in_array($sender, $memberId)) {
							$memberId[] = $sender;
						}
						$newsArray[] = array('league_id'=>$value['league_id'], 'sender'=>$sender, 'receive'=>$value['member_id']);	
					}
					$sql = "select id, name, nick_name from member where id in (".implode(",", $memberId).")";
					$command = $connection->createCommand($sql);
					$memberQuery = $command->queryAll();
					$memberInfo = array();
					if ($memberQuery) {
						foreach ($memberQuery as $key => $value) {
							$memberInfo[$value['id']] = $value['name']?$value['name']:$value['nick_name'];
						}
					}
					$sql = "select id, name from friend_league where id in (".implode(",", $leagueId).")";
					$command = $connection->createCommand($sql);
					$leagueQuery = $command->queryAll();
					$leagueInfo = array();
					foreach ($leagueQuery as $key => $value) {
						$leagueInfo[$value['id']] = $value['name'];
					}
					if (count($newsArray) > 0) {
						$insertNews = array();

						foreach ($newsArray as $key => $value) {
							$content= $memberInfo[$value['sender']]."向您分享了一条微创作";
							$insertNews[] = "(6, ".$value['sender'].", ".$value['receive'].", '".$content."', 0, ".time().", ".$id.", 1)";
						}
						$sql = "insert into news (type, sender, member_id, content, status, created_time, identity1, identity2) values ".implode(",", $insertNews);
						$command = $connection->createCommand($sql);
						$result1 = $command->execute();
					}

				}
			}
			$this->addIntegral($user->id, 3);					
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';							
		}else{
			$result['ret_num'] = 126;
			$result['ret_msg'] = '微创作发布失败';
		}
		echo json_encode( $result );
	}
	
	/**
	 * 微创作评论
	 */
	public function actionComment(){
		$this->check_key();
		$creationid = Frame::getIntFromRequest('creationid');
		$content = Frame::getStringFromRequest('content');
		$replier = Frame::getIntFromRequest('replier');
		if(empty($creationid)){
			$result['ret_num'] = 129;
			$result['ret_msg'] = '微创作ID为空';
			echo json_encode( $result );
			die();
		}
		if(empty($content)){
			$result['ret_num'] = 130;
			$result['ret_msg'] = '评论内容为空';
			echo json_encode( $result );
			die();
		}
		$user = $this->check_user();
		$creation = Creation::model()->findByPk($creationid);
		if(!$creation){
			$result['ret_num'] = 131;
			$result['ret_msg'] = '微创作ID不存在';
			echo json_encode( $result );
			die();
		}
		
		$comment_info = new CreationComment();
		$comment_info->member_id = $user->id;
		$comment_info->creation_id = $creationid;
		$comment_info->review = $content;
		$comment_info->created_time = time();
		$comment_info->replier = $replier;
		if($comment_info->save()){
			//发送给评论者
			if($replier!=$creation['member_id']) {
				$nickname = $this->getContactIdName($replier);
				$tpl_1 = array(
					"id" => $comment_info['id'],
					"creation_id" => $creationid,
					"member_id" => $user->id,
					"review" => $content,
					"created_time" => $comment_info->created_time,
					"replier" => $replier,
					"description" => $creation['description'],
					"nick_name" => $nickname[$user['id']] ? $nickname[$user['id']] : $user['nick_name'],
					"poster" => $user['poster'] ? URL . $user['poster'] : "",
					"huanxin_username" => $user['huanxin_username']
				);
				$tinfo = Member::model()->find("id={$replier}");
				$this->sendTCMessage('admin', array(0 => $tinfo['huanxin_username']), "action1", $tpl_1);
			}

			if($user->id!=$creation['member_id']) {
				//发送给作者
				$nickname1 = $this->getContactIdName($creation['member_id']);
				$tpl_2 = array(
						"id" => $comment_info['id'],
						"creation_id" => $creationid,
						"member_id" => $user->id,
						"review" => $content,
						"created_time" => $comment_info->created_time,
						"replier" => $replier,
						"description" => $creation['description'],
						"nick_name" => $nickname1[$user['id']] ? $nickname1[$user['id']] : $user['nick_name'],
						"poster" => $user['poster'] ? URL . $user['poster'] : "",
						"huanxin_username" => $user['huanxin_username']
				);
				$tinfo1 = Member::model()->find("id={$creation['member_id']}");
				$this->sendTCMessage('admin', array(0 => $tinfo1['huanxin_username']), "action1", $tpl_2);
			}

			//查出自己通讯录里的自己的名字
			$connection = Yii::app()->db;
			$sqla = "select a.is_benben,b.name from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id
			where b.member_id = {$user->id} and a.is_benben={$user->benben_id}";
			$command = $connection->createCommand($sqla);
			$resul = $command->queryAll();
			$rename = array();
			foreach ($resul as $v1){
				$rename = $v1['name'];
			}
		
			$result['display_name'] = empty($rename) ? $user->nick_name : $rename;
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';
		}else{
			$result['ret_num'] = 127;
			$result['ret_msg'] = '微创作评论发布失败';
		}
		echo json_encode( $result );
	}
	
	/**
	 * 微创作点赞
	 */
	public function actionLaud(){
		$this->check_key();
		$creationid = Frame::getIntFromRequest('creationid');
		if(empty($creationid)){
			$result['ret_num'] = 129;
			$result['ret_msg'] = '微创作ID为空';
			echo json_encode( $result );
			die();
		}
		$user = $this->check_user();
		$creation = Creation::model()->findByPk($creationid);
		if(!$creation){
			$result['ret_num'] = 131;
			$result['ret_msg'] = '微创作ID不存在';
			echo json_encode( $result );
			die();
		}
		$laud_info = CreationLike::model()->find("member_id = {$user->id} and creation_id = {$creationid}");
		if($laud_info){							
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';			
		}else{
			$laud_info = new CreationLike();
			$laud_info->member_id = $user->id;
			$laud_info->creation_id = $creationid;
			$laud_info->created_time = time();
			if($laud_info->save()){
				$connection = Yii::app()->db;
				//查出自己通讯录里的犇犇用户
				$sqla = "select a.is_benben,b.name from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id
				where b.member_id = {$user->id} and a.is_benben>0";
				$command = $connection->createCommand($sqla);
				$resul = $command->queryAll();
				$rename = array();
				foreach ($resul as $v1){
					$rename[$v1['is_benben']] = $v1['name'];
				}
				
				$creation->goods = $creation->goods +1;
				$creation->update();
				$connection = Yii::app()->db;
				$sql = "select a.creation_id, b.nick_name as name, b.id, b.benben_id from creation_like as a left join member as b on a.member_id = b.id where a.creation_id={$creationid}";
				$command = $connection->createCommand($sql);
				$laud = $command->queryAll();
				$laud_status_name = array();
				foreach ($laud as $valu){
					$memberid = $valu['benben_id'];
					$laud_status_name[] = empty($rename[$memberid]) ? $valu['name'] : $rename[$memberid];
				}
				$msg = '';
				if (count($laud_status_name) > 0) {
					if (count($laud_status_name) < 5) {
						$msg = implode("、", $laud_status_name);
					}else{
						$msg = $laud_status_name[0]."、".$laud_status_name[1]."、".$laud_status_name[2]."、".$laud_status_name[3];
					}					
					$msg .= '等人点赞';
				}
				$result['ret_num'] = 0;
				$result['msg'] = $msg;
				$result['ret_msg'] = '操作成功';
			}else{
				$result['ret_num'] = 128;
				$result['ret_msg'] = '微创作点赞失败';
			}
		}		
		echo json_encode( $result );
	}
	
	/**
	 * 微创作取消点赞
	 */
	public function actionCancellaud(){
		$this->check_key();
		$creationid = Frame::getIntFromRequest('creationid');
		if(empty($creationid)){
			$result['ret_num'] = 129;
			$result['ret_msg'] = '微创作ID为空';
			echo json_encode( $result );
			die();
		}
		$user = $this->check_user();
		$creation = Creation::model()->findByPk($creationid);
		if(!$creation){
			$result['ret_num'] = 131;
			$result['ret_msg'] = '微创作ID不存在';
			echo json_encode( $result );
			die();
		}
		$laud_info = CreationLike::model()->find("member_id = {$user->id} and creation_id = {$creationid}");
		if($laud_info){
			if($laud_info->delete()){
				$connection = Yii::app()->db;
				//查出自己通讯录里的犇犇用户
				$sqla = "select a.is_benben,b.name from group_contact_phone a left join group_contact_info b on a.contact_info_id = b.id
				where b.member_id = {$user->id} and a.is_benben>0";
				$command = $connection->createCommand($sqla);
				$resul = $command->queryAll();
				$rename = array();
				foreach ($resul as $v1){
					$rename[$v1['is_benben']] = $v1['name'];
				}
				
				$creation->goods = $creation->goods -1;
				$creation->update();
				$connection = Yii::app()->db;
				$sql = "select a.creation_id, b.nick_name as name, b.id, b.benben_id from creation_like as a left join member as b on a.member_id = b.id where a.creation_id={$creationid}";
				$command = $connection->createCommand($sql);
				$laud = $command->queryAll();
				$laud_status_name = array();
				foreach ($laud as $valu){
					$memberid = $valu['benben_id'];
					$laud_status_name[] = empty($rename[$memberid]) ? $valu['name'] : $rename[$memberid];
				}
				$msg = '';
				if (count($laud_status_name) > 0) {
					if (count($laud_status_name) < 5) {
						$msg = implode("、", $laud_status_name);
					}else{
						$msg = $laud_status_name[0]."、".$laud_status_name[1]."、".$laud_status_name[2]."、".$laud_status_name[3];
					}					
					$msg .= '等人点赞';
				}
				$result['msg'] = $msg;
				$result['ret_num'] = 0;
				$result['ret_msg'] = '操作成功';
			}else{
				$result['ret_num'] = 1028;
				$result['ret_msg'] = '微创作取消点赞失败';
			}
		}else{									
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';		
		}
		echo json_encode( $result );
	}
	
	/**
	 * 微创作关注
	 */
	public function actionAttention(){
		$this->check_key();
		$memberid = Frame::getIntFromRequest('memberid');
		if(empty($memberid)){
			$result['ret_num'] = 1029;
			$result['ret_msg'] = '微创作作者ID为空';
			echo json_encode( $result );
			die();
		}
		$user = $this->check_user();
		$member = Member::model()->findByPk($memberid);
		if(!$member){
			$result['ret_num'] = 1031;
			$result['ret_msg'] = '微创作作者ID不存在';
			echo json_encode( $result );
			die();
		}
		$laud_info = CreationAttention::model()->find("member_id = {$user->id} and creation_auth_id = {$memberid}");
		if($laud_info){			
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';			
		}else{
			$laud_info = new CreationAttention();
			$laud_info->member_id = $user->id;
			$laud_info->creation_auth_id = $memberid;
			$laud_info->created_time = time();
			if($laud_info->save()){
				$result['ret_num'] = 0;
				$result['ret_msg'] = '操作成功';
			}else{
				$result['ret_num'] = 1280;
				$result['ret_msg'] = '微创作关注失败';
			}
		}
		echo json_encode( $result );
	}
	
	/**
	 * 微创作取消关注
	 */
	public function actionCancelattention(){
		$this->check_key();
		$memberid = Frame::getIntFromRequest('memberid');
		if(empty($memberid)){
			$result['ret_num'] = 1029;
			$result['ret_msg'] = '微创作作者ID为空';
			echo json_encode( $result );
			die();
		}
		$user = $this->check_user();
		$member = Member::model()->findByPk($memberid);
		if(!$member){
			$result['ret_num'] = 1031;
			$result['ret_msg'] = '微创作作者ID不存在';
			echo json_encode( $result );
			die();
		}
		$laud_info = CreationAttention::model()->find("member_id = {$user->id} and creation_auth_id = {$memberid}");
		if($laud_info){
			if($laud_info->delete()){
				$result['ret_num'] = 0;
				$result['ret_msg'] = '操作成功';
			}else{
				$result['ret_num'] = 1128;
				$result['ret_msg'] = '微创作取消关注失败';
			}
		}else{						
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';			
		}
		echo json_encode( $result );
	}
	
	/**
	 * 热门搜索
	 */
	public function actionHot(){
		
	}
	
}