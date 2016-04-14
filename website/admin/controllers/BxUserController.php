<?php
Yii::$enableIncludePath = false;
define('__ROOT__', dirname(dirname(__FILE__)));

class BxUserController extends BaseController
{
    /**
     * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
     * using two-column layout. See 'protected/views/layouts/column2.php'.
     */
    public $layout = '//layouts/admin';

    /**
     * @var int the define the index for the menu
     */

    public $menuIndex = 130;
    public $dongyang = 136;
    public $ownbx = 0;

    public function __construct($id, $module)
    {
        parent::__construct($id, $module);
        $this->ownbx = Yii::app()->user->getState('userInfo')->enterprise_id;
    }

    /**
     * 查询所有百姓网的管理帐号
     */
    public function actionIndex()
    {
        $this->insert_log(131);
        $role = $this->getRole("donewbx");
        $model = User::model();
        $cri = new CDbCriteria();
        $cri->addCondition("t.enterprise_id!=".$this->dongyang,'AND');
        $cri->select = "t.*, role.role_name as rname";
        $cri->join = "left join role on role.id = t.role";
        $cri->order = "t.created_time desc";
        $pages = new CPagination();
        $pages->itemCount = $model->count($cri);
        $pages->pageSize = 12;
        $pages->applyLimit($cri);
        $items = $model->findAll($cri);

        //取所有百姓
        $enter=array();
        $allenter=Enterprise::model()->findAll("type=3");
        foreach ($allenter as $ka=>$va){
            $enter[$va['id']]=$va['name'];
        }
        foreach ($items as $ki=>$vi){
            $items[$ki]['enterprise_name']=$enter[$vi['enterprise_id']];
        }
        $this->render('index',array('items'=>$items,'pages'=> $pages, 'role' => $role));
    }


    /**
     * 新建百姓网管理员
     * Creates a new model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     */
    public function actionCreate()
    {
        $model=new User;

        $role = $this->getRole("donewbx");
        //查询所有百姓网
        $enterprise_id=Enterprise::model()->findAll("type=3 and id!=".$this->ownbx);

        $result = array();

        if(isset($_POST['User']))
        {
            $msg="";
            //判断百姓网是否有最高权限的角色
            $nowEnterprise=$_POST['enterprise_id'];
            $hasRole=array();
            if($nowEnterprise) {
                $hasRole=Role::model()->find("enterprise_id=" . $nowEnterprise." and dobaixing=63 and dosystem=15");
            }
            $roleId=0;
            if(count($hasRole)){
                $roleId=$hasRole->id;
            }else{
                $newRole=new Role();
                $newRole->enterprise_id=$nowEnterprise;
                $newRole->role_name='超级管理员';
                $newRole->created_time=time();
                $newRole->dobaixing=63;
                $newRole->dosystem=15;
                if($newRole->save()){
                    $roleId=$newRole->id;
                }else{
                    $msg="创建失败！";
                    $this->render('create',array(
                        'model'=>$model,
                        'enterprise_id' => $enterprise_id,
                        'role' => $role,
                        'result' => $result,
                        'msg' => $msg,
                        'backUrl' => $this->getBackListPageUrl(),
                    ));
                    exit;
                }
            }

            if($role & 1){
                $model->username=$_POST['User']['username'];
                $model->role = $roleId;
                $model->password = md5($_POST['User']['password']);
                $model->created_time = time();
                $model->enterprise_id = $_POST['enterprise_id'];
                if($model->save())
                    $this->redirect(['bxUser/index']);
            }else{
                echo '非法操作！';
                die;
            }
        }

        $this->render('create',array(
            'model'=>$model,
            'enterprise_id' => $enterprise_id,
            'role' => $role,
            'result' => $result,
            'backUrl' => $this->getBackListPageUrl(),
        ));
    }

    /**
     * 更新百姓网管理员
     * Updates a particular model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id the ID of the model to be updated
     */
    public function actionUpdate($id)
    {
        $model=$this->loadModel($id);

        //获取所有角色
        $role = new Role();
        $sql = "SELECT id, role_name FROM role WHERE enterprise_id!=".$this->dongyang." ORDER BY created_time DESC";
        $role = $role->findAllBySql($sql);
        $roles = $this->getRole("donewbx");

        //查询所有百姓网
        $enterprise_id=Enterprise::model()->findAll("type=3 and id!=".$this->ownbx);

        $result = array();
        foreach ($role as $value){
            $temp = array('id' => $value->id, 'role_name' => $value->role_name);
            $result[] = $temp;
        }

        if(isset($_POST['User']))
        {
            if($roles & 1){
                $password = $_POST['User']['password'];
                if($model->password != $password){
                    $model->password = md5($password);
                }
                if(isset($_POST['User']['disable'])){
                    $model->disable = $_POST['User']['disable'];
                }
                if($model->save())
                    $this->redirect(['bxUser/index']);
            }else{
                echo '非法操作！';
            }

        }
        $model->created_time = date('Y-m-d H:i:s', $model->created_time);
        $model->last_login = date('Y-m-d H:i:s', $model->last_login);
        $this->render('update',array(
            'model'=>$model,
            'enterprise_id' => $enterprise_id,
            'result' => $result,
            'role' => $roles,
            'backUrl' => $this->getBackListPageUrl(),
        ));
    }

    /**
     * 删除百姓网
     * @param $id
     */
    public function actionDelete($id)
    {
        $role = $this->getRole("donewbx");
        if(!($role & 1)){
            echo '非法操作！';
            die;
        }
        $id = Frame::getIntFromRequest('id');
        if ($id > 0) {
            $model = User::model ()->findByPk ( $id );
        }
        if ($model) {
            $model->delete();
        }
        $this->redirect ( Yii::app()->createUrl('bxUser/index',array('page'=>intval($_REQUEST['page']))));
    }

    /**
     * 禁用百姓网
     * @param $id
     */
    public function actionDisable($id)
    {
        $role = $this->getRole("donewbx");
        if(!($role & 1)){
            echo '非法操作！';
            die;
        }
        $id = Frame::getIntFromRequest('id');
        if ($id > 0) {
            $model = User::model ()->findByPk ( $id );
        }
        if ($model) {
            $model->disable = 1;
            $model->update();
        }
        $this->redirect ( Yii::app()->createUrl('bxUser/index',array('page'=>intval($_REQUEST['page']))));
    }


    /**
     * Returns the data model based on the primary key given in the GET variable.
     * If the data model is not found, an HTTP exception will be raised.
     * @param integer $id the ID of the model to be loaded
     * @return User the loaded model
     * @throws CHttpException
     */
    public function loadModel($id)
    {
        $model=User::model()->findByPk($id);
        if($model===null)
            throw new CHttpException(404,'The requested page does not exist.');
        return $model;
    }

    public function loadBxModel($id)
    {
        $model = Enterprise::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
        return $model;
    }

    public function getBackListPageUrl()
    {
        $cookie = Yii::app()->request->getCookies();
        $returnUrl = $cookie['benben-neverland']->value;
        if ($returnUrl) {
            return $returnUrl;
        } else {
            return Yii::app()->createUrl("baixing/newbx", array('page' => $_REQUEST['page']));
        }
    }

    /**
     * Performs the AJAX validation.
     * @param Bxapply $model the model to be validated
     */
    protected function performAjaxValidation($model)
    {
        if (isset($_POST['ajax']) && $_POST['ajax'] === 'bxapply-form') {
            echo CActiveForm::validate($model);
            Yii::app()->end();
        }
    }

}
