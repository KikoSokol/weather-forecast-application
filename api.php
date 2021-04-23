<?php

require_once "service/Service.php";
require_once "model/Host.php";
require_once "model/Location.php";
require_once "model/Access.php";
header('Content-Type: application/json');

session_start();

$service = new Service();

$operation = "";


if(isset($_GET["operation"]))
    $operation = $_GET["operation"];


switch ($operation)
{
    case "isAllowedAccess":
        if(isset($_SESSION["ipAddress"]) && $_SESSION["ipAddress"] === $_SERVER['REMOTE_ADDR'])
        {
            echo json_encode(getAllowedInfo(true));
        }
        else
        {
            echo json_encode(getAllowedInfo(false));
        }
        break;
    case "getWeather":
        $json = file_get_contents('php://input');
        $data = json_decode($json);

        if($data->allowed)
        {
            $_SESSION["ipAddress"] = $_SERVER['REMOTE_ADDR'];
        }
        echo json_encode(getWeather($service));
        break;
    case "getIpInfo":
        $json = file_get_contents('php://input');
        $data = json_decode($json);

        if($data->allowed)
        {
            $_SESSION["ipAddress"] = $_SERVER['REMOTE_ADDR'];
        }
        echo json_encode(getIpInfo($service));
        break;
//    default:
//        $_SESSION["ipAddress"] = $_SERVER['REMOTE_ADDR'];
//        echo json_encode(getWeather($service));

}




function getWeather(Service $service)
{
    $result = array();

    if(isset($_SESSION["ipAddress"]) && $_SESSION["ipAddress"] === $_SERVER['REMOTE_ADDR'])
    {
        $result["allowed"] = true;

        $ipInfomration = $service->createIpSession($_SESSION["ipAddress"]);

        if($ipInfomration == false)
        {
            $result["correct"] = false;
            $result["message"] = "Nepodarilo sa zistiť potrebné informácie";
            $result["weather"] = null;
        }
        else{
            $lat = $ipInfomration["location"]->latitude;
            $lon = $ipInfomration["location"]->longitude;
            $result["correct"] = true;
            $result["message"] = "Operácia úspešná";
            $result["weather"] = $service->getWeatherId($lat,$lon);
        }
    }
    else
    {
        $result["allowed"] = false;
        $result["correct"] = false;
        $result["message"] = "Nebolo povelené pristupovať ku IP adrese a GPS suradniciam preto nie je možné sprístupniť obsah";
        $result["weather"] = null;
    }

    return $result;

}





function getIpInfo(Service $service)
{
    $result = array();

    if(isset($_SESSION["ipAddress"]) && $_SESSION["ipAddress"] === $_SERVER['REMOTE_ADDR'])
    {
        $result["allowed"] = true;

        $ipInfomration = $service->createIpSession($_SESSION["ipAddress"]);

        if($ipInfomration == false)
        {
            $result["correct"] = false;
            $result["message"] = "Nepodarilo sa zistiť potrebné informácie";
            $result["weather"] = null;
        }
        else{
            $result["correct"] = true;
            $result["message"] = "Operácia úspešná";
            $result["ipInfo"] = $ipInfomration;
        }

    }
    else
    {
        $result["allowed"] = false;
        $result["correct"] = false;
        $result["message"] = "Nebolo povelené pristupovať ku IP adrese a GPS suradniciam preto nie je možné sprístupniť obsah";
        $result["weather"] = null;
    }

    return $result;
}







function getAllowedInfo($allowed)
{
    $result = array();

    if($allowed == true)
    {
        $result["allowed"] = true;
        $result["message"] = "Prístup ku dátam bol povolený.";
    }
    else
    {
        $result["allowed"] = false;
        $result["message"] = "Nebolo povelené pristupovať ku IP adrese a GPS suradniciam preto nie je možné sprístupniť obsah";
    }

    return $result;

}
