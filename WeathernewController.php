<?php
/*
 * Created on 2013-8-27
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

class WeathernewController extends Controller
{
	public function actionGetXiaomiData() {
		if(!empty($_GET['city_id'])) {
			$id = intval($_GET['city_id']);
			$model=new WeatherNew();
			$data = $model->getCityData($id);
			$result = $data[0]['city_data'];
			if(empty($result)){
				//$result = $model->getCityData($id);
				$data = $model->getXiaomiData($id);
				$result = "getXiaomiData(".json_encode($data).")";
				//var_dump($result);
				//die();
			}else{
				$result = "getXiaomiData(".$result.")";
			}
			echo $result;
			Yii::app()->end();
		}else{
			echo "没有数据";
			exit();
		}
	}

}
?>
