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

    function addNewAccess($locationId,$site)
    {
        try {
            $sql = "INSERT INTO access(location_id, site) VALUES (:locationId, :site);";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam("locationId",$locationId,PDO::PARAM_INT);
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


    function addVisit($site)
    {

        try
        {
            $sql = "INSERT INTO visit(site) VALUES (:site);";
            $stmt = $this->conn->prepare($sql);
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


    function getTodayAccessByHostId($hostId)
    {
        $sql = "SELECT a.id as id, location_id as locationId, timestamp as timestamp, site as site
                        FROM access a INNER JOIN location l on a.location_id = l.id INNER JOIN host h on l.host_id = h.id
                        WHERE l.host_id = :hostId AND DATE(a.timestamp) = DATE(NOW());";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("hostId",$hostId,PDO::PARAM_INT);
        $stmt->setFetchMode(PDO::FETCH_CLASS,"Access");
        $stmt->execute();
        return $stmt->fetch();
    }


    function getCountryStats()
    {
        $sql = "SELECT l.state as state, l.code, COUNT(l.state) as count FROM access a
                    INNER JOIN location l on a.location_id = l.id GROUP BY l.state, l.code;";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    function getSiteVisit()
    {
        $sql = "SELECT CASE WHEN visit.site = 'A' then 'Predpoveď počasia'
                    WHEN visit.site = 'B' then 'Informácie o IP adrese'
                    WHEN visit.site = 'C' then 'Štatistika' END as site, COUNT(*) as count
                        FROM visit group by visit.site;";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    function getBestSite()
    {
        $sql = "SELECT CASE WHEN visit.site = 'A' then 'Predpoveď počasia'
                    WHEN visit.site = 'B' then 'Informácie o IP adrese'
                    WHEN visit.site = 'C' then 'Štatistika' END as site, COUNT(*) as count
                    FROM visit group by visit.site ORDER BY count DESC LIMIT 1;";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);

    }

    function getAllPlacesCords()
    {
        $sql = "SELECT distinct latitude as lat, longitude as lon FROM location;";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    function getA()
    {
        $sql = "SELECT COUNT(access.timestamp) as count FROM access where TIME(access.timestamp) BETWEEN TIME('00:00:00') AND TIME('05:59:59');";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]["count"];
    }

    function getB()
    {
        $sql = "SELECT COUNT(access.timestamp) as count FROM access where TIME(access.timestamp) BETWEEN TIME('06:00:00') AND TIME('14:59:59');";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]["count"];
    }

    function getC()
    {
        $sql = "SELECT COUNT(access.timestamp) as count FROM access where TIME(access.timestamp) BETWEEN TIME('15:00:00') AND TIME('20:59:59');";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]["count"];
    }

    function getD()
    {
        $sql = "SELECT COUNT(access.timestamp) as count FROM access where TIME(access.timestamp) BETWEEN TIME('21:00:00') AND TIME('23:59:59');";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC)[0]["count"];
    }


    function getVisitInState($state)
    {
        $sql = "SELECT l.city as city, COUNT(l.city) as count FROM access a INNER JOIN location l on a.location_id = l.id WHERE l.state = :state GROUP BY l.city;";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam("state",$state,PDO::PARAM_STR);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);

    }



}