<?php

echo $this->renderPartial('_form', array('model'=>$model, 'member_name' => $member_name,'member_phone' => $member_phone, 'reason' => $reason,'status2' => $status2,'reason2' => $reason2, 'areas'=>$areas));