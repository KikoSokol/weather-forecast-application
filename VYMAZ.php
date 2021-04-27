<?php

require_once "service/Service.php";
require_once "repository/Repository.php";
header('Content-Type: application/json');

//$url = "http://api.ipstack.com/";
//$apiKey = "?access_key=a4f220eb5d6e34cdd9293168fcc777be";
//$final = $url . $_SERVER['REMOTE_ADDR'] . $apiKey;
//$ch = curl_init($final);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
//$json = curl_exec($ch);
//curl_close($ch);
//$obj = json_decode($json);
//echo $json;




$service = new Service();
$repository = new Repository();
//
//echo json_encode($service->createIpSession($_SERVER['REMOTE_ADDR']));


//$url = "http://api.openweathermap.org/data/2.5/weather?";
//$and = "&";
//$option = "?access_key=";
//$key = IP_KEY;
//
//$lat = "lat=" . "49.00170135498";
//$lon = "lon=" . "21.239999771118";
//$appid = "appid=" . "d18770c402ea2c0fa2cbb1e16d2162c8";
//
//
//$path = $url . $lat . $and .$lon .$and . $appid;
//
//$ch = curl_init($path);
//curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
//
//$json = curl_exec($ch);
//curl_close($ch);
//
//echo $json;


//echo json_encode($service->getWeatherId("49.00170135498","21.239999771118"));


//echo json_encode($repository->getTodayAccessByHostId(1));

//echo json_encode($repository->addNewAccess(1,"A"));
echo json_encode($repository->getTodayAccessByHostId(1));