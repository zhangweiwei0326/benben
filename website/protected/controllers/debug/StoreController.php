 <?php
class StoreController extends PublicController
{
	public $layout = false;
	/**
	 * 号码直通车搜索
	 */
	public function actionSearch(){
		
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

		$keyword = Frame::getStringFromRequest('keyword');
		//$last_time = Frame::getIntFromRequest('last_time');
		$page = Frame::getIntFromRequest('page');
		$connection = Yii::app()->db;
		$asql = "";
		if($province){
			$asql .= "province = {$province} and ";
		}
		if($city){
			$asql .= "city = {$city} and ";
		}
		if($area){
			$asql .= "area = {$area} and ";
		}
		if($street){
			$asql .= "street = {$street} and ";
		}
		$asql .= "is_close = 0 and ";
		$asql = trim($asql);
		$limit = $page*100;
		$distance_sql = "";
		$distance_order = "";
		if($latitude&&$longitude){
			$distance_sql = ",round(6378.138*2*asin(sqrt(pow(sin( (lat*pi()/180-".$latitude."*pi()/180)/2),2)+cos(lat*pi()/180)*cos(".$latitude."*pi()/180)* pow(sin( (lng*pi()/180-".$longitude."*pi()/180)/2),2)))*1000) as distance";
			$distance_order = "distance asc,";
		}

		if($keyword){			 						
			$sql = "select id,name,short_name,poster,phone,telephone,tag,description,lat,lng,industry,istop,views,created_time {$distance_sql} 		 
			from number_train where {$asql} (short_name like '%{$keyword}%' or tag like '%{$keyword}%') 
			order by istop desc, {$distance_order} created_time desc limit {$limit},100";
		}else{
			if($asql){
				$asql = "where ".$asql;
				$asql =trim($asql,'and');
			}			
			$sql = "select id,name,short_name,poster,phone,telephone,tag,description,lat,lng,industry,istop,views,created_time {$distance_sql}              
			from number_train {$asql} order by istop desc,{$distance_order} created_time desc limit {$limit},100";
		}
		
		$command = $connection->createCommand($sql);
		$result0 = $command->queryAll();
		$num = count($result0);
		//计算距离
		$industry_arr = $this->Industry($result0);
		foreach ($result0 as $key => $value){
			$result0[$key]['industry'] = $industry_arr[$value['industry']] ? $industry_arr[$value['industry']]:"";
			$result0[$key]['poster'] = $value['poster'] ? URL.$value['poster']:"";
			$result0[$key]['description'] = $value['description'] ? $value['description']:"";
			if($latitude&&$longitude){
				//$distance = $this->getDistanceBetweenPointsNew($latitude, $longitude, $value['lat'], $value['lng']);
				$distance = round(($value['distance']/1000),1);
				if($distance < 1){
					$result0[$key]['distance_kilometers'] = $value['distance']."m";
				}else{
					$result0[$key]['distance_kilometers'] = $distance."km";
				}
				
			}			
		}
		
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
		$result ['num'] = $num;
		$result ['number_info'] = $result0;
		echo json_encode( $result );
	}
	
	/**
	 * 号码直通车添加(修改)
	 */
	public function actionAdd(){
		$this->check_key();
		$name = Frame::getStringFromRequest('name');
		$short_name = Frame::getStringFromRequest('short_name');
		$poster = Frame::saveImage('poster');
		$phone = Frame::getStringFromRequest('phone');
		$telephone = Frame::getStringFromRequest('telephone');
		$industry = Frame::getIntFromRequest('industry');
		$province = Frame::getIntFromRequest('province');
		$city = Frame::getIntFromRequest('city');
		$area = Frame::getIntFromRequest('area');
		$street = Frame::getIntFromRequest('street');
// 		$lat = $_POST['lat'];
// 		$lng = $_POST['lng'];
		$lng = Frame::getStringFromRequest(lng);//经度
		$lat = Frame::getStringFromRequest(lat);//经度
		$tag = Frame::getStringFromRequest('tag');
		$address = Frame::getStringFromRequest('address');
		$description = Frame::getStringFromRequest('description');
		$tag_arr = explode(" ", $tag);
		foreach ($tag_arr as $value){
			if(mb_strlen($value,"utf-8")>6){
				$result['ret_num'] = 5295;
				$result['ret_msg'] = '每个服务项目不能超过6个字';
				echo json_encode( $result );
				die();
			}
		}
	
		$user = $this->check_user();
		if (($user->userinfo & 1)>0 || ($user->userinfo & 2)>0) {
			
		}else {
			$result['ret_num'] = 1616;
			$result['ret_msg'] = '请先完善个人资料';
			echo json_encode( $result );
			die ();
		}
		// if(!($user->userinfo & 1 > 0)){
		// 	$result['ret_num'] = 1616;
		// 	$result['ret_msg'] = '请先完善个人资料';
		// 	echo json_encode( $result );
		// 	die ();
		// }
		$re = NumberTrain::model()->find("member_id = {$user->id}");
		
		if($re){
			//修改
			if($name){$re->name = $name;}
			if($short_name){$re->short_name = $short_name;}
			if($poster){$re->poster = $poster;}
			if($phone){$re->phone = $phone;}
			if($telephone){$re->telephone = $telephone;}
			else {$re->telephone = '';}
			if($industry){$re->industry = $industry;}
			if($province){$re->province = $province;}
			if($city){$re->city = $city;}
			if($area){$re->area = $area;}
			if($street){$re->street = $street;}
			if($lng){$re->lng = $lng;}
			if($lat){$re->lat = $lat;}
			if($tag){$re->tag = $tag;}
			if($address){$re->address = $address;}
			if($description){$re->description = $description;}
			if($re->update()){
				$result['ret_num'] = 0;
				$result['ret_msg'] = '操作成功';
				$result['number_info'] = array(
						"NumberId"=>$re->id,
						"NumberName"=>$re->name,
						"NumberMemberid"=>$user->id,
						"NumberDescription"=>$re->description,
						"Numbertag"=>$re->tag,
						"NumberCreated_time"=>$re->created_time
				);
			}else{
				$result['ret_num'] = 1004;
				$result['ret_msg'] = '号码直通车信息修改失败';
			}
		}else{
			if (empty($poster)) {
				$result['ret_num'] = 5236;
				$result['ret_msg'] = '头像不能为空';
				echo json_encode( $result );
				die();
			}
			//新增
			$ename = NumberTrain::model()->find("name = '{$name}'");
			if($ename){
				$result['ret_num'] = 5235;
				$result['ret_msg'] = '号码直通车名称已存在';
				echo json_encode( $result );
				die();
			}
			$number_info = new NumberTrain();
			$number_info->name = $name;
			$number_info->short_name = $short_name;
			$number_info->member_id = $user->id;
			$number_info->poster = $poster;
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
			if($number_info->save()){
				$this->addIntegral($user->id, 2);
				$result['ret_num'] = 0;
				$result['ret_msg'] = '操作成功';
				$result['number_info'] = array(
						"NumberId"=>$number_info->id,
						"NumberName"=>$number_info->name,
						"NumberMemberid"=>$user->id,
						"NumberDescription"=>$number_info->description,
						"Numbertag"=>$number_info->tag,
						"NumberCreated_time"=>$number_info->created_time
				);
			}else{
				$result['ret_num'] = 121;
				$result['ret_msg'] = '新建号码直通车失败';
			}
		}
		
		echo json_encode( $result );
	
	}
	
	/**
	 * 号码直通车信息完善
	 */
	public function actionInfo(){
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
		
		if($user->userinfo & 1 > 0){ 
			$apply_info = ApplyComplete::model()->find("member_id = {$user->id} and type = 2");
			if($apply_info){				
				if($name){$apply_info->name = $name;}
				if($id_card){$apply_info->id_card = $id_card;	}			
				if($poster1){$apply_info->poster1 = $poster1;}
				if($poster2){$apply_info->poster2 = $poster2;}
				if($phone){$apply_info->phone = $phone;}
				if($province){$apply_info->province = $province;}
				if($city){$apply_info->city = $city;}
				if($area){$apply_info->area = $area;}
				if($street){$apply_info->street = $street;}
				if($apply_info->update()){					
					$result['ret_num'] = 0;
					$result['ret_msg'] = '操作成功';
					$result['apply_info'] = array(
							"ApplyId"=>$apply_info->id,
							"ApplyName"=>$apply_info->name,
							"ApplyPhone"=>$apply_info->phone,
							"ApplyCreated_time"=>$apply_info->created_time
					);
				}else{
					$result['ret_num'] = 1003;
					$result['ret_msg'] = '号码直通车信息完善失败';
				}
			}
		}else{
			if (empty( $id_card )) {
				$result['ret_num'] = 1005;
				$result['ret_msg'] = '证件号为空';
				echo json_encode( $result );
				die ();
			}
			if (empty( $poster1 )) {
				$result['ret_num'] = 1006;
				$result['ret_msg'] = '身份证正面照没有上传';
				echo json_encode( $result );
				die ();
			}
			if (empty( $poster2 )) {
				$result['ret_num'] = 1007;
				$result['ret_msg'] = '身份证反面照没有上传';
				echo json_encode( $result );
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
			if($apply_info->save()){
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
								
				$user->userinfo = $user->userinfo + 1;
				$user->update();
				$result['ret_num'] = 0;
				$result['ret_msg'] = '操作成功';
				$result['apply_info'] = array(
						"ApplyId"=>$apply_info->id,
						"ApplyName"=>$apply_info->name,
						"ApplyPhone"=>$apply_info->phone,
						"ApplyCreated_time"=>$apply_info->created_time
				);
			}else{
				$result['ret_num'] = 1003;
				$result['ret_msg'] = '号码直通车信息完善失败';
			}
		}	
		echo json_encode( $result );
	
	}
	
	/**
	 * 号码直通车查看详情
	 */
	public function actionDetail(){
		$this->check_key();
		$id = Frame::getIntFromRequest('id');
		$longitude = Frame::getStringFromRequest('longitude');//经度
		$latitude = Frame::getStringFromRequest('latitude');//经度
		if (empty( $id )) {
			$result['ret_num'] = 122;
			$result['ret_msg'] = '号码直通车ID为空';
			echo json_encode( $result );
			die ();
		}
		$user = $this->check_user();
		
		$number_info = NumberTrain::model()->findByPk($id);
		if($number_info){
			$connection = Yii::app()->db;

			$distance = 0;
			$distance_kilometers = '';
			if($latitude&&$longitude){
				$distance_sql = "round(6378.138*2*asin(sqrt(pow(sin( (lat*pi()/180-".$latitude."*pi()/180)/2),2)+cos(lat*pi()/180)*cos(".$latitude."*pi()/180)* pow(sin( (lng*pi()/180-".$longitude."*pi()/180)/2),2)))*1000) as distance";
				$sql = "select {$distance_sql} from number_train where id={$id}";

				$command = $connection->createCommand($sql);
				$distanceReault = $command->queryAll();
				if ($distanceReault) {
					$distance = $distanceReault[0]['distance'];

					$kmDistance = round(($distance/1000), 1);
					if($kmDistance < 1){
						$distance_kilometers = $distance."m";
					}else{
						$distance_kilometers = $kmDistance."km";
					}
				}
			}
			//查询号码直通车的创建人环信ID
			if($number_info->member_id){
				$nuser = Member::model()->findByPk($number_info->member_id);
			}			
			$industry = "";
			//$industy_arr = $this->industryinfo();
			$industry = $this->getIndustryinfo($number_info->industry);
			//自己是否收藏
			$conllection = 0;
			$con = NumberTrainCollect::model()->find("number_train_id = {$id} and member_id = {$user->id}");
			if($con){
				$conllection = 1;
			}
			//收藏人数
			$sql = "select count(id) num from number_train_collect where number_train_id = {$id}";
			$command = $connection->createCommand($sql);
			$result0 = $command->queryAll();
			//增加浏览量
			$number_info->views = $number_info->views + 1;
			$number_info->update();
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';
			$result['number_info'] = array(
				    "NumberId"=>$number_info->id,
					"NumberName"=>$number_info->name,
					"NumberShortName"=>$number_info->short_name,
					"NumberMemberid"=>$user->id,
					"huanxin_username"=>$nuser ? $nuser->huanxin_username : "",
					"NumberPoster"=>$number_info->poster ? URL.$number_info->poster : "",
					"NumberPhone"=>$number_info->phone,
					"NumberTelephone"=>$number_info->telephone,
					"NumberCollection"=>$conllection,
					"NumberLat"=>$number_info->lat,
					"NumberLng"=>$number_info->lng,
					"NumberAddress"=>$number_info->address,
					"NumberInd"=>$number_info->industry ? $number_info->industry : "",
					"NumberIndustry"=>$industry[0]['name'] ? $industry[0]['name'] : "",
					"NumberDescription"=>$number_info->description,
					"Numbertag"=>$number_info->tag,
					"NumberViews"=>$number_info->views,
					"CollectionNumber"=>$result0[0]['num'],
					"NumberCreated_time"=>$number_info->created_time,
					'distance'=>$distance,
					'distance_kilometers'=>$distance_kilometers
			);
		}else{
			$result['ret_num'] = 123;
			$result['ret_msg'] = '号码直通车信息不存在';
		}
		echo json_encode( $result );
		
	}
	
	/**
	 * 我的号码直通车
	 */
	public function actionMydetail(){
		$this->check_key();
		$user = $this->check_user();
		//$pinfo = $this->pcinfo();
		$number_info = NumberTrain::model()->find("member_id = {$user->id}");
		if($number_info){
			$industry = "";
			//$industy_arr = $this->industryinfo();
			$industry = $this->getIndustryinfo($number_info->industry);
			//省市
			$pro = array("province"=>$number_info->province,"city"=>$number_info->city,
			                          "area"=>$number_info->area,"street"=>$number_info->street);
			$pro_arr = $this->ProCity(array($pro));
			//增加浏览量
			$number_info->views = $number_info->views + 1;
			$number_info->update();
			$connection = Yii::app()->db;
			$command = $connection->createCommand("select count(*) as c from broadcasting_log where member_id = {$user->id}  and type = 1");
			$authority = $command->queryAll();
			$haveRight = 2;
			if ($authority) {
				$haveRight = 2-$authority[0]['c'];
			}
			if (!$content) {
				$content = '我创建了直通车，来看看吧～';
			}
			$result['ret_num'] = 0;
			$result['ret_msg'] = '操作成功';
			$result['number_info'] = array(
					"NumberId"=>$number_info->id,
					"NumberName"=>$number_info->name,
					"NumberShort_name"=>$number_info->short_name,
					"NumberPhone"=>$number_info->phone,
					"NumberTel"=>$number_info->telephone,
					"NumberLat"=>$number_info->lat,
					"NumberLng"=>$number_info->lng,
					"NumberProvince"=>$number_info->province,
					"NumberCity"=>$number_info->city,
					"NumberArea"=>$number_info->area,
					"NumberStreet"=>$number_info->street,
					"NumberInd"=>$number_info->industry ? $number_info->industry : "",
					"NumberIndustry"=>$industry[0]['name'] ? $industry[0]['name'] : "",
					"NumberPoster"=>$number_info->poster ? URL.$number_info->poster : "",
					"NumberMemberid"=>$user->id,
					"NumberPro_city" => $pro_arr[$number_info->province]." ".$pro_arr[$number_info->city]." ".$pro_arr[$number_info->area]." ".$pro_arr[$number_info->street],
					"NumberDescription"=>$number_info->description,
					"NumberAddress"=>$number_info->address,
					"Numbertag"=>$number_info->tag,
					"NumberViews"=>$number_info->views,
					"NumberCreated_time"=>$number_info->created_time,
					"HaveRight"=>$haveRight,
					'is_close'=>$number_info->is_close
			);
		}else{
			$result['ret_num'] = 123;
			$result['ret_msg'] = '您还没有创建号码直通车！';
		}
		echo json_encode( $result );
	
	}
	
	/**
	 * 号码直通车收藏
	 */
	public function actionCollect(){
		$this->check_key();
		$id = Frame::getIntFromRequest('id');
		if (empty( $id )) {
			$result['ret_num'] = 122;
			$result['ret_msg'] = '号码直通车ID为空';
			echo json_encode( $result );
			die ();
		}
		$user = $this->check_user();
	
		$number_info = NumberTrain::model()->findByPk($id);
		if (empty( $number_info )) {
			$result['ret_num'] = 123;
			$result['ret_msg'] = '号码直通车信息不存在';
			echo json_encode( $result );
			die ();
		}
		if ($number_info->member_id == $user->id ) {
			$result['ret_num'] = 1298;
			$result['ret_msg'] = '不能收藏自己的号码直通车';
			echo json_encode( $result );
			die ();
		}
		$str = "";
		$re = NumberTrainCollect::model()->find("number_train_id = {$id} and member_id = {$user->id}");
		if(!$re){
			$numtc = new NumberTrainCollect();
			$numtc->number_train_id = $id;
			$numtc->member_id = $user->id;
			$numtc->created_time = time();
			if($numtc->save()){
				$this->addIntegral($user->id, 9);
				$this->addIntegral($number_info->member_id, 20);		
				$result['ret_num'] = 0;
				$result['ret_msg'] = "收藏成功！";
				$phone = array(
						"id"=> "",
						"contact_info_id"=> $number_info->id +1000000,
						"phone"=> $number_info->phone,				
						"is_benben"=> 0,
						"is_baixing"=> 0,
						"poster"=> $number_info->poster ? URL.$number_info->poster : "",
						"huanxin_username"=> ""
				);
				$short_phone = array(
						"id"=> "",
						"contact_info_id"=> $number_info->id +1000000,
						"phone"=> $number_info->telephone,						
						"is_benben"=> 0,
						"is_baixing"=> 0,
						"poster"=> $number_info->poster ? URL.$number_info->poster : "",
						"huanxin_username"=> ""
				);
				$collect = array(
						"id"=>$number_info->id +1000000,
						"group_id"=> 10000,
						"name"=> $number_info->short_name,
						"short_name"=> $number_info->name,
						"pinyin"=> "",
						"created_time"=> "",
						"is_benben"=> 0,
						"is_baixing"=> 0,
						"poster"=> $number_info->poster ? URL.$number_info->poster : "",
						"huanxin_username"=> "",
						"phone"=> $number_info->telephone?array($phone,$short_phone):array($phone)
				);
				$result['collect'] = $collect;
			} 
		}else{
			$result['ret_num'] = 5236;
			$result['ret_msg'] = "已经收藏该号码直通车";
		}			
		echo json_encode( $result );
	
	}
	
	/**
	 * 号码直通车取消收藏
	 */
	public function actionCancelcollect(){
		$this->check_key();
		$id = Frame::getIntFromRequest('id');
		if (empty( $id )) {
			$result['ret_num'] = 122;
			$result['ret_msg'] = '号码直通车ID为空';
			echo json_encode( $result );
			die ();
		}
		$user = $this->check_user();
	
		$number_info = NumberTrain::model()->findByPk($id);
		if (empty( $number_info )) {
			$result['ret_num'] = 123;
			$result['ret_msg'] = '号码直通车信息不存在';
			echo json_encode( $result );
			die ();
		}
		
		$re = NumberTrainCollect::model()->find("number_train_id = {$id} and member_id = {$user->id}");
		if($re){
			$re->delete();
			$result['ret_num'] = 0;
		    $result['ret_msg'] = "操作成功";
		}else{
			$result['ret_num'] = 5232;
			$result['ret_msg'] = "没有收藏该号码直通车";
		}		
		echo json_encode( $result );
	
	}
	
	/**
	 * 号码直通车收藏列表
	 */
	public function actionCollectlist(){
		$this->check_key();
		$longitude = Frame::getStringFromRequest('longitude');//经度
		$latitude = Frame::getStringFromRequest('latitude');//经度
		$page = Frame::getIntFromRequest('page');
		$user = $this->check_user();
	
		$connection = Yii::app()->db;
		$limit = $page*10;				
		$sql = "select a.id,a.name,a.poster,a.phone,a.tag,a.lat,a.lng,a.industry,a.istop,a.views,a.created_time from number_train a inner join number_train_collect b on a.id = b.number_train_id where b.member_id = {$user->id} order by a.istop desc,a.created_time desc,a.id desc limit {$limit},10";			
		$command = $connection->createCommand($sql);
		$result0 = $command->queryAll();
		//计算距离
		foreach ($result0 as $key => $value){
			$result0[$key]['poster'] = $value['poster'] ? URL.$value['poster']:"";
			if($latitude&&$longitude){
				$distance = $this->getDistanceBetweenPointsNew($latitude, $longitude, $value['lat'], $value['lng']);
				$result0[$key]['distance_kilometers'] = round($distance['kilometers'],1)."km";
				$result0[$key]['distance_meters'] = round($distance['meters'],1)."m";
			}			
		}
		
		$result ['ret_num'] = 0;
		$result ['ret_msg'] = '操作成功';
		$result ['number_info'] = $result0;
		echo json_encode( $result );
	
	}
	
	/**
	 * 号码直通车信息完善
	 */
	public function actionClose(){
		$this->check_key();
		$id = Frame::getIntFromRequest('id');
		if (empty( $id )) {
			$result['ret_num'] = 122;
			$result['ret_msg'] = '号码直通车ID为空';
			echo json_encode( $result );
			die ();
		}
		$user = $this->check_user();
		
		$number_info = NumberTrain::model()->find("id = {$id} and member_id = {$user->id}");
		if (empty( $number_info )) {
			$result['ret_num'] = 123;
			$result['ret_msg'] = '号码直通车信息不存在';
			echo json_encode( $result );
			die ();
		}
		if($number_info->is_close){
			$number_info->is_close = 0;
		}else{
			$number_info->is_close = 1;
		}

		if($number_info->update()){	
		
			if (!$number_info->is_close) {
				//打开直通车，返回通讯录直通车信息
				$phone = array(
					"id"=> "",
					"contact_info_id"=> $number_info['id']+1000000,
					"phone"=> $number_info['phone'],
					"is_benben"=> 0,
					"is_baixing"=> 0,
					"poster"=> $number_info['poster'] ? URL.$number_info['poster'] : "",
					"huanxin_username"=> ""
				);
				$short_phone = array(
					"id"=> "",
					"contact_info_id"=> $number_info['id']+1000000,
					"phone"=> $number_info['telephone'],
					"is_benben"=> 0,
					"is_baixing"=> 0,
					"poster"=> $number_info['poster'] ? URL.$number_info['poster'] : "",
					"huanxin_username"=> ""
				);
				$collect = array(
					"id"=>$number_info['id']+1000000,
					"group_id"=> 10000,
					"name"=> $number_info['short_name'],
					"short_name"=> $number_info['name'],
					"pinyin"=> "",
					"created_time"=> "",
					"is_benben"=> 0,
					"is_baixing"=> 0,
					"poster"=> $number_info['poster'] ? URL.$number_info['poster'] : "",
					"huanxin_username"=> "",
					"phone"=> $number_info['telephone']?array($phone,$short_phone):array($phone)
				);
				$result['collect'] = $collect;
			}		
			$result['ret_num'] = 0;	
			$result['is_close'] = $number_info->is_close;
			$result['ret_msg'] = "操作成功";
		}else{
			$result['ret_num'] = 5036;
			$result['ret_msg'] = "号码直通车打开或关闭失败";
		}
		echo json_encode( $result );
	}
	
	/**
	 * 计算距离
	 * @param $latitude1,$longitude1 第一点的经纬度
	 * @param $latitude2,$longitude2 第二点的经纬度
	 * @return Array
									(
									[kilometers] => kilometers
									[meters] => meters									
									)
	 */
	function getDistanceBetweenPointsNew($latitude1, $longitude1, $latitude2, $longitude2) {
		$theta = $longitude1 - $longitude2;
		$miles = (sin(deg2rad($latitude1)) * sin(deg2rad($latitude2))) + (cos(deg2rad($latitude1)) * cos(deg2rad($latitude2)) * cos(deg2rad($theta)));
		$miles = acos($miles);
		$miles = rad2deg($miles);
		$miles = $miles * 60 * 1.1515;
		$feet = $miles * 5280;
		$yards = $feet / 3;
		$kilometers = $miles * 1.609344;
		$meters = $kilometers * 1000;
		return compact('miles','feet','yards','kilometers','meters');	
	}
	
	
}