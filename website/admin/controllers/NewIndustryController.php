<?php

class NewIndustryController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin';
	
	/**
	 * @var int the define the index for the menu
	 */
	 
	 public $menuIndex = 94;
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	
	
	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$this->insert_log(94);
		$model = NewIndustry::model();
		// $cri = new CDbCriteria();
		// $cri->select = "t.*";
		// $cri->addCondition('t.level='."1",'AND');
		// $firstIndustry = $model->findAll($cri);

		//获取参数
		//参数处理
		//查找一级行业

		$sql = "SELECT * FROM industry WHERE level != 0";
		$firstIndustry = $model->findAllBySql($sql);
		$list = Tree::create($firstIndustry);
		//一级行业
		$sql1="SELECT * FROM industry WHERE level =1";
		$firstIndustry = $model->findAllBySql($sql1);

		$sql2="SELECT * FROM industry WHERE level =2";
		$secondIndustry = $model->findAllBySql($sql2);

		$sql3="SELECT * FROM industry WHERE level =3";
		$thirdIndustry = $model->findAllBySql($sql3);
		// var_dump($list);
		$this->render('index',array('list' =>$list,'firstIndustry' => $firstIndustry,'secondIndustry'=>$secondIndustry,'thirdIndustry'=> $thirdIndustry));
		//$this->render('index',array('$firstIndustry'=>$firstIndustry,'$sql'=>$sql));
	}


	public function actionCreate()
	{
		$this->render('create');
	}

	public function actionSave(){
		//获取参数
		$name = Frame::getStringFromRequest('name');
		$parent_id = Frame::getIntFromRequest('parent_id');
		$level = Frame::getIntFromRequest('level');
		//访问数据库
		// $model=NewIndustry::model();
		$industry =new NewIndustry();
		$industry->name =$name;
		$industry->parent_id =$parent_id;
		$industry->created_time =Time();
		$industry->last =0;
		if($level == 0){
			$industry->level =1;
		}else{
			$industry->level =$level+1;
		}
		if($industry->save() >0){
			$result['status']=1;
			$result['parent_id']=$parent_id;
			echo json_encode($result);
		}else{
			$result['status']=0;
			echo json_encode($result);
		}

		//返回结果		
	}

	public function actionEdit(){
		$this->render('edit');
	}
	public function actionUpdate(){
		//获取参数
		$name = Frame::getStringFromRequest('name');
		$id = Frame::getIntFromRequest('id');
		//访问数据库
		// $model=NewIndustry::model();
		$industry =new NewIndustry();
		$ret = $industry->updateAll (array (
                                        'name' => $name,
                                ), "id={$id}");
		if($ret >0){
			$result['status']=1;
			echo json_encode($result);
		}else{
			$result['status']=0;
			echo json_encode($result);
		}
	}
	public function actionDelete(){
		//获取参数
		$id = Frame::getIntFromRequest('id');
		$level = Frame::getIntFromRequest('level');
		//访问数据库
		$industry =new NewIndustry();

		if($industry->deleteByPk($id)>0){
			$result['status']=1;
			echo json_encode($result);
		}else{
			$result['status']=0;
			echo json_encode($result);
		} 
	}


	
	
}
