<?php

require_once "repository/Repository.php";
require_once "config.php";
require_once "model/Host.php";
require_once "model/Location.php";
require_once "model/Access.php";

class Service
{
    private Repository $repository;

    public function __construct()
    {
        $this->repository = new Repository();
    }


    public function createIpSession($ip)
    {
        $host = $this->repository->getHostByIpAddress($ip);

        if($host === false)
        {
            $ipInfo = $this->getIpInfo($ip);

            $newHostId = $this->repository->addNewHost($ip);

            if($newHostId == -1)
                return false;
            else
            {
                $newLocationId = $this->addNewLocation($newHostId,$ipInfo->country_name,$ipInfo->city,$ipInfo->location->capital,$ipInfo->country_code,$ipInfo->latitude,$ipInfo->longitude);

                if($newLocationId == false)
                    return false;
                else
                {
                    $allSesstionInformation = $this->getSessionInformation($newHostId);
                    if($allSesstionInformation == false)
                        return false;
                    else
                        return $allSesstionInformation;
                }
            }

        }
        else
        {
            $allSesstionInformation = $this->getSessionInformation($host->id);
            if($allSesstionInformation == false)
                return false;
            else
                return $allSesstionInformation;
        }

    }

    private function addNewLocation($newHostId,$country_name,$city,$capital,$country_code,$latitude,$longitude)
    {
        $newLocationIp = $this->repository->addNewLocation($newHostId,$country_name,$city,$capital,$country_code,$latitude,$longitude);

        if($newLocationIp == -1)
        {
            return false;
        }

        return $newLocationIp;
    }


    private function getIpInfo($ip)
    {
        $url = "http://api.ipstack.com/";
        $option = "?access_key=";
        $key = IP_KEY;

        $path = $url . $ip . $option . $key;

        $ch = curl_init($path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $json = curl_exec($ch);
        curl_close($ch);

        return json_decode($json);
    }


    private function getSessionInformation($hostId)
    {
        $information = array();

        $host = $this->repository->getHostById($hostId);
        if($host == false)
            return false;
        else
        {
            $information["host"] = $host;
        }

        $location = $this->repository->getLocationByHostId($host->id);

        if($location == false)
            return false;
        else
        {
            $information["location"] = $location;
        }

        return $information;
    }


    public function getWeatherId($lat,$lon)
    {
        $url = "http://api.openweathermap.org/data/2.5/weather?";
        $and = "&";
        $key = WEATHER_KEY;

        $lat = "lat=" . $lat;
        $lon = "lon=" . $lon;
        $appid = "appid=" . $key;


        $path = $url . $lat . $and .$lon .$and . $appid;

        $ch = curl_init($path);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $json = curl_exec($ch);
        curl_close($ch);

        $result = json_decode($json);

        return $result->id;
    }


}