<?php
class IndexController extends BaseController {
	public $layout = '//layouts/home';
	public function actionIndex() {
		$this->render ( 'index' );
	}
}