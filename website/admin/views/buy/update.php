<?php

echo $this->renderPartial('_form', array('model'=>$model, 
										'reason' => $reason, 
										'member' => $member,
										'memberreason' => $memberreason,
										'areaInfo'=>$areaInfo,
										'memberInfo'=>$memberInfo
										));