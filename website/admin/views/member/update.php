<?php

echo $this->renderPartial('_form', array('model'=>$model, 'sex' => $sex, 'reason'=>$reason, 'areas' => $areas,'status_info' => $status_info,'msg'=>$msg));