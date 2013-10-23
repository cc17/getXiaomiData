<?php
$ini = "/data/projects/config/mysql.ini";
$ini = parse_ini_file($ini , true);
//$db = $ini['liebao_grid_admin']; 线上的
$db = $ini['liebao_grid'];

$urlPrefixArr = array(
	"aqi" => "http://weatherapi.market.xiaomi.com/wtr/data/weather2?city_id=",
	"sk" => "http://weatherapi.market.xiaomi.com/wtr-v2/temp/realtime?cityId=",
	"yb" => "http://weatherapi.market.xiaomi.com/wtr-v2/temp/forecast?cityId="
);

$ch = curl_init();
curl_setopt($ch , CURLOPT_RETURNTRANSFER , true);
curl_setopt($ch , CURLOPT_TIMEOUT , 10);
$dsn = "mysql:host=".$db['host'].";port=".$db['port'].";dbname=".$db['database'];
$pdo = new PDO( $dsn ,$db['username'] , $db['password'] , array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8") );
$city = file_get_contents("city.json");
$city = json_decode($city , true);
$i = 0;
$data = array();


foreach($city as $row){
	foreach($row['sub'] as $sub){
		$data[] = getXiaomiData($ch,$sub);

		if($i % 10 == 0){
			data2db($data);
			$i = 0;
			$data = array();
		}
		$i++;
	}
}

function getXiaomiData($ch,$sub){
	//$data = array();
	$data = array();
	$data['city_id'] = $sub['id'];
	$data['city_name'] = $sub['name'];
	$data['city_data'] = array();
	global $urlPrefixArr;
	foreach($urlPrefixArr as $key=>$urlPrefix){
		curl_setopt($ch , CURLOPT_URL , $urlPrefix . $sub['id']);
		$json = curl_exec($ch);
		$httpinfo = curl_getinfo($ch);
		if(false === $json){
			echo $sub['name'] . "   " . $sub['id'] . "\n";
			echo "error:" . curl_error($ch) . "\n"; 
			echo "error NO:" . curl_errno($ch) . "\n"; 
		}
		elseif($httpinfo['http_code'] != '200') {
			echo $sub['name'] . "   " . $sub['id'] . "\n";
			print_r($httpinfo);
		}
		else{
			if(!checkJSON($json , $sub['id'])) {
				echo $sub['name'] . "   " . $sub['id'] . "\n";
				echo "invalid info:" . $json . "\n";
				//continue;
			}
			$data['city_data'][$key] = json_decode($json,true);
			//$data[$i]['fetch_time'] = time();
			//$i++;
		}
	}
	return $data;
};


data2db($data);

function data2db($data){
	global $pdo;
	$sql = "REPLACE INTO `weather_dxm` (city_id,city_name,city_data) VALUES ";
	foreach($data as $i => $row){
		$sql .= $i == 0 ? "" : ",";
		$sql .= "('".$row['city_id']."' , '".$row['city_name']."','".json_encode($row['city_data'])."')";
	}
	$pdo->exec($sql) or die(print_r($pdo->errorInfo(), true));
}

function checkJSON($json , $cityID){
	$data = json_decode($json , true);
	return $data['city_id'] == $cityID;
}
