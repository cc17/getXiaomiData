<?php
class WeatherNew
{
	private $_tblName;
	private $_conn;
	private $_urlPrefixArr = array(
		"weatherInfo" => "http://weatherapi.market.xiaomi.com/wtr/data/weather2?city_id=",
	     "sk" => "http://weatherapi.market.xiaomi.com/wtr-v2/temp/realtime?cityId=",
	     "yb" => "http://weatherapi.market.xiaomi.com/wtr-v2/temp/forecast?cityId="
	);
	public function __construct(){
		//$this->_tblName = "weather_data";
		//$this->_conn = Yii::app()->liebao_grid_admin;
		$this->_tblName = "weather_dxm";
		$this->_conn = Yii::app()->liebao_grid_admin;
	}


	public function insert($data){

	}

	public function getCityData($id){
		$sql = "SELECT city_data FROM `" .$this->_tblName. "` WHERE `city_id`='$id' and status = 0";
        $command = $this->_conn->createCommand($sql);
		$result = $command->queryAll();
		return $result;
	}

	public function getXiaomiData($id){
		
		$content = array();
		foreach($this->_urlPrefixArr as $key=>$urlPrefex){
			
			$url = $urlPrefex.$id;
			$curl = curl_init();
			curl_setopt($curl, CURLOPT_URL, $url);
			//curl_setopt($curl, CURLOPT_HEADER, 1);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

			// Ïʾ»ñÄýr_dump($data);
			$json = curl_exec($curl);
			curl_close($curl);
			$content[$key] = json_decode($json,true);	
			
			/*$url = $urlPrexfex.$id;
			$ch = curl_init($url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			curl_setopt($ch, CURLOPT_HEADER, 0);
			$json = curl_exec($ch);
			var_dump($json);
			die();
			$content[$key] = curl_exec($ch);
			curl_close($ch);*/
		}


		//$url = "http://weatherapi.market.xiaomi.com/wtr/data/weather2?city_id=".$id."&callback=getXiaomiData";
		//$ch = curl_init($url);
		//curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_HEADER, 0);
		//$content = curl_exec($ch);
		//curl_close($ch);
		return $content;
	}

}
