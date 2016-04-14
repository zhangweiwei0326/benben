<?php
Yii::$enableIncludePath = false;
define('__ROOT__', dirname(dirname(__FILE__)));

class BaixingController extends BaseController
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
     * 查询所有百姓网
     */
    public function actionNewbx()
    {
        $this->insert_log(130);
        $this->menuIndex = 130;

        $model = Enterprise::model();
        $cri = new CDbCriteria();

        $province = $this->getProvince();

        $cri->addCondition('t.type = 3', 'AND');
        if (isset($_GET) && !empty($_GET)) {
            $result = array();

            if ($_GET['name']) {
                $cri->addSearchCondition('t.name', $_GET['name'], true, 'AND');
                $result['name'] = $_GET['name'];
                $result['goback'] = -2;
            }
            if ($_GET['description']) {
                $cri->addSearchCondition('description', $_GET['description'], true, 'AND');
                $result['description'] = $_GET['description'];
                $result['goback'] = -2;
            }


            if ($_GET['created_time1'] && $_GET['created_time2']) {
                $ct1 = strtotime($_GET['created_time1']);
                $ct2 = strtotime($_GET['created_time2']) + 86399;

                if ($ct1 >= $ct2) {
                    $msg = "申请日期第一个必须比第二个小!";
                } else {
                    $cri->addCondition('t.created_time >= ' . $ct1, 'AND');
                    $result['created_time1'] = $_GET['created_time1'];
                    $cri->addCondition('t.created_time <= ' . $ct2, 'AND');
                    $result['created_time2'] = $_GET['created_time2'];
                    $result['goback'] = -2;
                }
            } else {
                if ($_GET['created_time1']) {
                    $cri->addCondition('t.created_time >= ' . strtotime($_GET['created_time1']), 'AND');
                    $result['created_time1'] = $_GET['created_time1'];
                    $result['goback'] = -2;
                }
                if ($_GET['created_time2']) {
                    $cri->addCondition('t.created_time <= ' . strtotime($_GET['created_time2']) + 86399, 'AND');
                    $result['created_time2'] = $_GET['created_time2'];
                    $result['goback'] = -2;
                }
            }
            $cancel_time1 = $_GET['cancel_time1'];
            $cancel_time2 = $_GET['cancel_time2'];


            if (isset($_GET['status']) && $_GET['status'] != -1) {
                $cri->addCondition('t.status = ' . intval($_GET['status']), 'AND');
                $result['status'] = $_GET['status'];
                $result['goback'] = -2;
            } else {
                $result['status'] = -1;
            }

            if ($_GET['province'] && ($_GET['province'] != -1)) {
                $cri->addCondition('t.province = ' . $_GET['province'], 'AND');
                $result['province'] = $_GET['province'];
                $result['goback'] = -2;
                $res = $this->getCity($_GET['province']);
            }

            if ($_GET['city'] && ($_GET['city'] != -1)) {
                $cri->addCondition('t.city = ' . $_GET['city'], 'AND');
                $result['city'] = $_GET['city'];
                $res2 = $this->getArea($_GET['city']);
                $result['goback'] = -2;
            }

            if ($_GET['area'] && ($_GET['area'] != -1)) {
                $cri->addCondition('t.area = ' . $_GET['area'], 'AND');
                $result['area'] = $_GET['area'];
                $result['goback'] = -2;
            }
        }
        if (!isset($_GET['status'])) {
            $result['status'] = -1;
        }
        $cri->select = "t.*";
        $cri->order = "t.id desc";

        $pages = new CPagination();
        $pages->itemCount = $model->count($cri);
        $pages->pageSize = 50;
        $pages->applyLimit($cri);
        $items = $model->findAll($cri);

        $url = Yii::app()->request->getUrl();
        $cookie = new CHttpCookie('benben-neverland', $url);
        $cookie->expire = time() + 3600;
        Yii::app()->request->cookies['benben-neverland'] = $cookie;

        $this->render('newbx', array('items' => $items, 'pages' => $pages, 'result' => $result, 'msg' => $msg,
            'province' => $province, 'res' => $res, 'res2' => $res2));
    }

    /**
     * 编辑百姓网
     */
    public function actionChangeinfo($id)
    {
        $model = $this->loadBxModel($id);

        if ($model->province) {
            $province = $this->areas($model->province) ? $this->areas($model->province) : "未知";
        } else {
            $province = '未知';
        }
        if ($model->city) {
            $city = $this->areas($model->city) ? $this->areas($model->city) : "未知";
        } else {
            $city = '未知';
        }
        if ($model->area) {
            $area = $this->areas($model->area) ? $this->areas($model->area) : "未知";
        } else {
            $area = '未知';
        }
        if ($model->street) {
            $street = $this->areas($model->street) ? $this->areas($model->street) : "未知";
        } else {
            $street = '未知';
        }

        $areas = array();
        $areas = array("province" => $province, "city" => $city, "area" => $area, "street" => $street);

        if (isset($_POST['Enterprise'])) {
            if ($_POST['Enterprise']['name']) {
                $model->name = $_POST['Enterprise']['name'];
            }
            if ($_POST['Enterprise']['province']) {
                $model->province = $_POST['Enterprise']['province'];
            }
            if ($_POST['Enterprise']['city']) {
                $model->city = $_POST['Enterprise']['city'];
            }
            if ($_POST['Enterprise']['area']) {
                $model->area = $_POST['Enterprise']['area'];
            }
            if ($_POST['Enterprise']['street']) {
                $model->street = $_POST['Enterprise']['street'];
            }
            if ($_POST['Enterprise']['description']) {
                $model->description = $_POST['Enterprise']['description'];
            }
            if ($_POST['Enterprise']['status'] == 0 || $_POST['Enterprise']['status']) {
                $model->status = $_POST['Enterprise']['status'];
            }
            if ($model->save())
                $this->redirect($this->getBackListPageUrl());
        }
        $aprovince = array();
        $aprovince['province'] = $this->getProvince();
        if ($model->province) {
            $aprovince['city'] = $this->getCity($model->province);
        } else {
            $aprovince['city'] = array();
        };
        if ($model->city) {
            $aprovince['area'] = $this->getArea($model->city);
        } else {
            $aprovince['area'] = array();
        };
        if ($model->area) {
            $aprovince['street'] = $this->getStreet($model->area);
        } else {
            $aprovince['street'] = array();
        };
        $model->created_time = date('Y-m-d H:i:s', $model->created_time);
        $msg = "";
        $this->render('changeinfo', array(
            'model' => $model,
            'areas' => $areas,
            'province' => $aprovince,
            'msg' => $msg,
            'backUrl' => $this->getBackListPageUrl(),
        ));
    }

    /**
     * 新建百姓网
     */
    public function actionCreatebx()
    {
        $model = new Enterprise();
        $model->name = "";
        $model->description = "";
        $model->province = "";
        $model->city = "";
        $model->area = "";
        $model->street = "";
        $aprovince = array();
        $aprovince['province'] = $this->getProvince();
        $aprovince['city'] = array();
        $aprovince['area'] = array();
        $aprovince['street'] = array();
        $this->render('createbx', array(
            'model' => $model,
            'province' => $aprovince,
            'backUrl' => $this->getBackListPageUrl(),
        ));
    }

    /**
     * 新建百姓网保存数据
     */
    public function actionSetbx()
    {
        $model = new Enterprise();
        if (isset($_POST['Enterprise'])) {
            if ($_POST['Enterprise']['name']) {
                $model->name = $_POST['Enterprise']['name'];
            }
            if ($_POST['Enterprise']['province']) {
                $model->province = $_POST['Enterprise']['province'];
            }
            if ($_POST['Enterprise']['city']) {
                $model->city = $_POST['Enterprise']['city'];
            }
            if ($_POST['Enterprise']['area']) {
                $model->area = $_POST['Enterprise']['area'];
            }
            if ($_POST['Enterprise']['street']) {
                $model->street = $_POST['Enterprise']['street'];
            }
            if ($_POST['Enterprise']['description']) {
                $model->description = $_POST['Enterprise']['description'];
            }
            $model->status = 0;
            $model->max_num = 0;
            $model->origin = 2;
            $model->type = 3;
            $model->number = 0;
            $model->created_time = time();
            if ($model->save())
                $this->redirect('newbx');
        }
    }

    public function loadModel($id)
    {
        $model = Bxapply::model()->findByPk($id);
        if ($model === null)
            throw new CHttpException(404, 'The requested page does not exist.');
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
