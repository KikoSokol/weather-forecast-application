<?php
require_once "database/Database.php";
require_once "model/Host.php";
require_once "model/Location.php";
require_once "model/Access.php";

class Repository
{
    private $conn;

    public function __construct()
    {
        $database = new Database();
        $this->conn = $database->getConn();
    }


    function addNewHost($ipAddress)
    {
        try {
            $sql = "INSERT INTO host(ip_address) VALUES (:ipAddress);";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("ipAddress",$ipAddress,PDO::PARAM_STR);
            $result = $stmt->execute();
            if($result)
                return $this->conn->lastInsertId();
        }
        catch (PDOException $e)
        {
            return -1;
        }
    }

    function addNewLocation($hostId, $state, $city, $capitalCity, $code, $latitude, $longitude)
    {
        try {
            $sql = "INSERT INTO location(host_id, state, city, capital_city, code, latitude, longitude) VALUES (:hostId,:state,:city,:capitalCity,:code,:latitude,:longitude);";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("hostId",$hostId,PDO::PARAM_INT);
            $stmt->bindParam("state",$state,PDO::PARAM_STR);
            $stmt->bindParam("city",$city,PDO::PARAM_STR);
            $stmt->bindParam("capitalCity",$capitalCity,PDO::PARAM_STR);
            $stmt->bindParam("code",$code,PDO::PARAM_STR);
            $stmt->bindParam("latitude",$latitude,PDO::PARAM_STR);
            $stmt->bindParam("longitude",$longitude,PDO::PARAM_STR);
            $result = $stmt->execute();
            if($result)
                return $this->conn->lastInsertId();
        }
        catch (PDOException $e)
        {
            return -1;
        }
    }

    function addNewAccess($locationId, $timestamp, $site)
    {
        try {
            $sql = "INSERT INTO access(location_id, timestamp, site) VALUES (:locationId, :timestamp, :site);";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("locationId",$locationId,PDO::PARAM_STR);
            $stmt->bindParam("timestamp",$timestamp,PDO::PARAM_STR);
            $stmt->bindParam("site",$site,PDO::PARAM_STR);
            $result = $stmt->execute();
            if($result)
                return $this->conn->lastInsertId();
        }
        catch (PDOException $e)
        {
            return -1;
        }
    }

    function getHostByIpAddress($ipAddress)
    {
        $sql = "SELECT host.id as id, host.ip_address as ipAddress FROM host where host.ip_address = :ipAddress;";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("ipAddress",$ipAddress,PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS,"Host");
        $stmt->execute();
        return $stmt->fetch();
    }


    function getHostById($idHost)
    {
        $sql = "SELECT host.id as id, host.ip_address as ipAddress FROM host where host.id = :id;";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("id",$idHost,PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS,"Host");
        $stmt->execute();
        return $stmt->fetch();
    }



    function getLocationByHostId($hostId)
    {
        $sql = "SELECT id as id, host_id as hostId, state as state, city as city, capital_city as capitalCity, 
                                code as code, latitude as latitude, longitude as longitude 
                                        FROM location WHERE location.host_id = :hostId;";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("hostId",$hostId,PDO::PARAM_STR);
        $stmt->setFetchMode(PDO::FETCH_CLASS,"Location");
        $stmt->execute();
        return $stmt->fetch();
    }

}