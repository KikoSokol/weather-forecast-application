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


    function addAccess($sessionInfo,$site)
    {
        $this->repository->addVisit($site);
        $access = $this->repository->getTodayAccessByHostId($sessionInfo["host"]->id);

        if($access != false)
            return false;

        $newAccessId = $this->repository->addNewAccess($sessionInfo["location"]->id,$site);

        return $newAccessId;
    }

    function getStats()
    {
        $result = array();

        $result["countryStats"] = $this->repository->getCountryStats();

        $result["cords"] = $this->repository->getAllPlacesCords();


        $timesA["part"] = "00:00:00 - 05:59:59";
        $timesA["count"] = $this->repository->getA();

        $timesB["part"] = "06:00:00 - 14:59:59";
        $timesB["count"] = $this->repository->getB();

        $timesC["part"] = "15:00:00 - 20:59:59";
        $timesC["count"] = $this->repository->getC();

        $timesD["part"] = "21:00:00 - 23:59:59";
        $timesD["count"] = $this->repository->getD();


        $result["time"][0] = $timesA;
        $result["time"][1] = $timesB;
        $result["time"][2] = $timesC;
        $result["time"][3] = $timesD;

        $result["visits"] = $this->repository->getSiteVisit();

        $result["bestSite"] = $this->repository->getBestSite();

        return $result;
    }

    function getCityVisites($country)
    {
        return $this->repository->getVisitInState($country);
    }


}