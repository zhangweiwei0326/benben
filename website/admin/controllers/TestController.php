<?php
class TestController extends Controller
{
	public $layout = false;
	/**
	 * 通讯录匹配
	 */
	public function actionIndex(){
		$connection = Yii::app()->db;
/*
15325922260---浙江恒城硬质合金有限公司
15325793385---紫金财产保险股份有限公司东阳支公司
18957976755---浙江东阳中国木雕城有限公司2
18006519056---浙江省东阳市祥丰实业有限公司
15372902066---东阳市金彤房地产开发有限公司
18094796692---金华市横源农业科技有限公司
18969363070---东阳市新艺文教卫生用品有限公司【张旭】
15355361345---浙江省电信有限公司东阳市分公司灵通公话
18069981626---东阳市林商塑料衣架有限公司
18066220258---东阳市紫竹莲工艺品有限公司
15325900381---紫金财产保险股份有限公司东阳支公司
18066220298---东阳市紫竹莲工艺品有限公司
18072332065---浙江广厦文化旅游开发有限公司
13735774476---东阳市紫竹莲工艺品有限公司
15325796850---紫金财产保险股份有限公司东阳支公司
15397512710---东阳市艾美特针织有限公司

15097125146---user
*/

		$sql = "select phone,member_id from  enterprise_member where contact_id = 136";
		$command = $connection->createCommand($sql);
		$result2 = $command->queryAll();
		foreach($result2 as $e){
			$havePhone[] = $e['phone'];
		}



		$sql = "select phone, name from bxapply where status = 3";
		$command = $connection->createCommand($sql);
		$result = $command->queryAll();
		foreach($result as $e){
			$havePhone2[] = $e['phone'];
		}
		var_dump(count($havePhone2));
		echo "<br />";
		var_dump(array_count_values($havePhone2));
		echo "<br />";

		var_dump(array_diff($havePhone2, $havePhone));
		
	}
}
