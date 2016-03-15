<?php
class ApplyFixWidge extends CWidget{
	public $apply_id;
	public function init() {
		
	}
	public function run() {
		// 此方法会被 CController::endWidget() 调用
		$modelFix=ApplyFixEnterprise::model()->findByAttributes(array('apply_id'=>$this->apply_id));
		if(!empty($modelFix)&&$modelFix->apply_status==0){
			echo "<a  class='btn btn-warning btn-sm' href='/admin.php/applyRegister/ReviewFix/".$this->apply_id."'>审核修改</a>";
		}
	}
}

