<?php

class WinningListController extends BaseController
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	public $layout='//layouts/admin';
	
	/**
	 * @var int the define the index for the menu
	 */
	 
	 public $menuIndex = 122;
	/**
	 * Creates a new model.
	 * If creation is successful, the browser will be redirected to the 'view' page.
	 */
	
	
	/**
	 * Lists all models.
	 */
	public function actionIndex()
	{
		$this->insert_log(122);
		$is_send = intval($_GET['is_send']);

		$model = LotteryLog::model();
		$cri = new CDbCriteria();
		//参数判断
		if($is_send !=-1){
            $cri->addCondition('t.is_send = ' . $is_send, 'AND');
        }
		$cri->select = "t.*,member.phone as phone,member.name as name";
        $cri->join = "left join member on member.benben_id = t.benben_id";
        $cri->order = "lottery_time";
        $pages = new CPagination();
        $pages->itemCount = $model->count($cri);
        $pages->pageSize = 20;
        $pages->applyLimit($cri);
        $items = $model->findAll($cri);
		$this->render('index',array('items'=>$items,'pages'=> $pages));
	}
	
	public function actionSend(){
		 $id = Frame::getIntFromRequest('id');


		//访问数据库操作
        $ret = LotteryLog::model()->updateAll (array (
                                   'is_send' => 0,
                                ), "id={$id}");
        if($ret >=0 ){
            $result['status']=1;

            echo json_encode($result);
        }else {
            $result['status']=0;

            echo json_encode($result);;
        }

	}
}
