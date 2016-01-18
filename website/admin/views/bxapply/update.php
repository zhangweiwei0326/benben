<?php

echo $this->renderPartial('_form', array('model'=>$model, 'id_card'=>$id_card, 'reason'=>$reason, 
																		'poster1' => $poster1, 'poster2' => $poster2,'member_phone'=>$member_phone,
																		'member_name' => $member_name, 'areas'=>$areas, 'msg'=>$msg));