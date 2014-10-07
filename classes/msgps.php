<?php

class MSGPS extends Base {

    protected $subtype;
    protected $timestamp;
    protected $mac_address;
    protected $latitude;
    protected $longitude;
    protected $address;
    protected $notification;
    protected $seen = FALSE;

    public function __construct($type) {
        parent::__construct($type);
    }

    public function getMonitoringSensorByKeys($usernameDB, $macAddress, $subtype) {
        $sensorsGps = array();
        try {
            foreach (Base::getViewToIterateBasedInUrl($usernameDB, '_design/application/_view/getMonitoringSensor?key=["' . $macAddress . '","' . $subtype . '"]&limit=5&descending=true')as $_monitoringGPS) {
                $monitoringSensorGPS = new MSGPS();

                $monitoringSensorGPS->_id = $_monitoringGPS->id;
                $monitoringSensorGPS->_rev = $_monitoringGPS->value->_rev;
                $monitoringSensorGPS->type = $_monitoringGPS->value->type;
                $monitoringSensorGPS->subtype = $_monitoringGPS->value->subtype;
                $monitoringSensorGPS->latitude = $_monitoringGPS->value->latitude;
                $monitoringSensorGPS->longitude = $_monitoringGPS->value->longitude;
                $monitoringSensorGPS->timestamp = date('d/m H:i:s', $_monitoringGPS->value->timestamp);
                $monitoringSensorGPS->mac_address = $_monitoringGPS->value->mac_address;
                $monitoringSensorGPS->address = $_monitoringGPS->value->address;
                $monitoringSensorGPS->notification = $_monitoringGPS->value->notification;

                array_push($sensorsGps, $monitoringSensorGPS);
            }
        } catch (Exception $exc) {
            return $exc;
        }
        if (sizeof($sensorsGps) > 0)
            return $sensorsGps;

        return NULL;
    }

    public function getArrayOfGPSToJson($array) {
        $jsonReturn = "";
        foreach ($array as $_row) {
            $jsonReturn.='{'
                    . '"_id":' . '"' . $_row->_id . '",'
                    . '"address":' . '"' . $_row->address . '",'
                    . '"latitude":' . '"' . $_row->latitude . '",'
                    . '"longitude":' . '"' . $_row->longitude . '",'
                    . '"timestamp":' . '"' . $_row->timestamp . '",'
                    . '"notification":' . '"' . $_row->notification . '"'
                    . '},';
        }
        return "[" . substr($jsonReturn, 0, -1) . "]";
    }

    public function calcIfCheckInOrCheckOut($username, $macaddress, $lat, $lng, $timestamsOfDevice) {
        $safezonesArray = Safezone::getSafezonesByUserAndDevice($username, $macaddress);
        $str = "";
        $smalldistance = INF;
        $inside = false;
        $bestSafezone = NULL;
        $typeNotification = "";

        foreach ($safezonesArray as $_safezone) {
            $distanceFromSafezoneToCoordinatorReceived = MSGPS::haversineGreatCircleDistance($_safezone->latitude, $_safezone->longitude, $lat, $lng);
            //$str.=$_safezone->name;

            if ($distanceFromSafezoneToCoordinatorReceived <= $_safezone->radius) {
                $smalldistance = $distanceFromSafezoneToCoordinatorReceived;
                $inside = TRUE;
                $bestSafezone = $_safezone;
                $typeNotification = "CHECK-IN";
                break; //if point inside of Safezone stop locking
            }
            if ($distanceFromSafezoneToCoordinatorReceived < $smalldistance && $distanceFromSafezoneToCoordinatorReceived > $_safezone->radius) {
                $smalldistance = $distanceFromSafezoneToCoordinatorReceived;
                $inside = FALSE;
                $typeNotification = "CHECK-OUT";
                $bestSafezone = $_safezone;
                // find the safezone closer of point
            }
        }

        $saveMonitoringSensorGPS = false;

        if ($smalldistance < INF && $bestSafezone != NULL) {
            //$str.="small distance:" . $smalldistance;
            if ($bestSafezone->notification === "ALL") {
                $saveMonitoringSensorGPS = TRUE;
            } else if ($bestSafezone->notification === "CHECK_INS_ONLY" && $inside == TRUE) {
                $saveMonitoringSensorGPS = TRUE;
            } else if ($bestSafezone->notification === "CHECK_OUTS_ONLY" && $inside == FALSE) {
                $saveMonitoringSensorGPS = TRUE;
            }

            if ($saveMonitoringSensorGPS) {
                $str = MSGPS::saveMonitoringSensorGPS($username, $macaddress, $lat, $lng, $typeNotification, $timestamsOfDevice);
            } else {
                $str = "error";
            }
        } else {
            //$str.= "save location: ".MSGPS::saveMonitoringSensorGPS($username, $macaddress, $lat, $lng, "LOCATION", $timestamsOfDevice);
            $str = MSGPS::saveMonitoringSensorGPS($username, $macaddress, $lat, $lng, "LOCATION", $timestamsOfDevice);
        }
        return $str;
    }

    public function saveMonitoringSensorGPS($usernameDB, $macaddress, $lat, $lng, $typeNotification, $timestamsOfDevice) {
        $monitoringSensorGPS = new MSGPS();
        $monitoringSensorGPS->_id = "ms_" . $timestamsOfDevice . "_" . $macaddress . "_gps";
        //$monitoringSensorGPS->_id = $macaddress . "_ms_gps_" . $timestamsOfDevice;
        $monitoringSensorGPS->type = "monitoring_sensor";
        $monitoringSensorGPS->subtype = "GPS";
        $monitoringSensorGPS->latitude = $lat;
        $monitoringSensorGPS->longitude = $lng;
        $monitoringSensorGPS->timestamp = $timestamsOfDevice;
        $monitoringSensorGPS->mac_address = $macaddress;
        $monitoringSensorGPS->address = MSGPS::getAddress($lat, $lng);
        $monitoringSensorGPS->notification = $typeNotification;
        $monitoringSensorGPS->seen = FALSE;


        try {
            Base::insertOrUpdateObjectInDB($usernameDB, $monitoringSensorGPS, FALSE);
        } catch (SagCouchException $e) {
            //return "some error in save monitoring gps";
            return "error";
        }
        //return " - see in couchdb " . $usernameDB . "," . $macaddress . ", " . $lat . "," . $lng . "," . $typeNotification;
        return $monitoringSensorGPS->address;
    }

    public function haversineGreatCircleDistance($latitudeFrom, $longitudeFrom, $latitudeTo, $longitudeTo, $earthRadius = 6371000) {
        // convert from degrees to radians
        $latFrom = deg2rad($latitudeFrom);
        $lonFrom = deg2rad($longitudeFrom);
        $latTo = deg2rad($latitudeTo);
        $lonTo = deg2rad($longitudeTo);

        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;

        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) +
                                cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        return $angle * $earthRadius;
    }

    public static function getAddress($lat, $lon) {
        $url = "http://maps.googleapis.com/maps/api/geocode/json?latlng=" .
                $lat . "," . $lon . "&sensor=false";
        $json = @file_get_contents($url);
        $data = json_decode($json);
        $status = $data->status;
        $address = '';
        if ($status == "OK") {
            $address = $data->results[0]->formatted_address;
            if ($address === "") {
                $address = "Address not found: Lat:" . $lat . " Lng:" . $lon;
            }
        } else {
            $address = "Address not found: Lat:" . $lat . " Lng:" . $lon;
        }
        return $address;
    }

// Converts DMS ( Degrees / minutes / seconds ) 
// to decimal format longitude / latitude

    public static function receiveLatCoordinator($latString) {
        $array = MSGPS::parseCoordinator($latString);
        if ($array[0] != NULL && $array[1] != NULL && $array[2] != NULL && $array[3] != NULL) {
            $latfrom = MSGPS::DMStoDEC($array[1], $array[2], $array[3]);
            if ($latID == "S") {
                $latfrom = $latfrom * -1;
            }
            return $latfrom;
        }
        return NULL;
    }

    public static function receiveLngCoordinator($lngString) {
        $array = MSGPS::parseCoordinator($lngString);
        if ($array[0] != NULL && $array[1] != NULL && $array[2] != NULL && $array[3] != NULL) {
            $lonfrom = MSGPS::DMStoDEC($array[1], $array[2], $array[3]);
            if ($array[0] == "W") {
                $lonfrom = $lonfrom * -1;
            }
            return $lonfrom;
        }
        return NULL;
    }

    public static function parseCoordinator($strCoordinator) {
        $coor_id = $strCoordinator{0};
        $strToParse = substr($strCoordinator, 1);
        $arrayPart = explode(chr(176), $strToParse);
        $degree = $arrayPart[0];
        $strToParse = $arrayPart[1];
        $strToParse = substr($strToParse, 0, -1);
        $arrayPart = explode(".", $strToParse);
        $minutes = $arrayPart[0];
        $seconds = $arrayPart[1];
        return array($coor_id, $degree, $minutes, $seconds*0.006);
    }

    public static function DMStoDEC($deg, $min, $sec) {
        return $deg + ((($min * 60) + ($sec)) / 3600);
    }

}
