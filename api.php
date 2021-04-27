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

//        if($data->allowed)
//        {
//            $_SESSION["ipAddress"] = $_SERVER['REMOTE_ADDR'];
//        }
        registerSession($data->allowed);
        echo json_encode(getWeather($service,$data->site));
        break;
    case "getIpInfo":
        $json = file_get_contents('php://input');
        $data = json_decode($json);

//        if($data->allowed)
//        {
//            $_SESSION["ipAddress"] = $_SERVER['REMOTE_ADDR'];
//        }
        registerSession($data->allowed);
        echo json_encode(getIpInfo($service,$data->site));
        break;
    case "getStats":
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        registerSession($data->allowed);
        echo json_encode(getStats($service,$data->site));
        break;
    case "getCityCounts":
        $json = file_get_contents('php://input');
        $data = json_decode($json);
        echo json_encode($service->getCityVisites($data->state));
        break;
    default:
//        $_SESSION["ipAddress"] = $_SERVER['REMOTE_ADDR'];
//        echo json_encode(getWeather($service));
//        registerSession(true);
//        echo json_encode(getWeather($service,"A"));
//        break;
}




function getWeather(Service $service,$site)
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
            $service->addAccess($ipInfomration,$site);
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





function getIpInfo(Service $service,$site)
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
            $result["ipInfo"] = null;
        }
        else{
            $result["correct"] = true;
            $result["message"] = "Operácia úspešná";
            $result["ipInfo"] = $ipInfomration;
            $service->addAccess($ipInfomration,$site);
        }

    }
    else
    {
        $result["allowed"] = false;
        $result["correct"] = false;
        $result["message"] = "Nebolo povelené pristupovať ku IP adrese a GPS suradniciam preto nie je možné sprístupniť obsah";
        $result["ipInfo"] = null;
    }

    return $result;
}


function getStats(Service $service,$site)
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
            $result["stats"] = null;
        }
        else{
            $service->addAccess($ipInfomration,$site);
            $result["correct"] = true;
            $result["message"] = "Operácia úspešná";
            $result["stats"] = $service->getStats();
        }

    }
    else
    {
        $result["allowed"] = false;
        $result["correct"] = false;
        $result["message"] = "Nebolo povelené pristupovať ku IP adrese a GPS suradniciam preto nie je možné sprístupniť obsah";
        $result["stats"] = null;
    }

    return $result;
}





function registerSession($allow)
{
    if($allow == true)
    {
        $_SESSION["ipAddress"] = $_SERVER['REMOTE_ADDR'];
    }
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
