<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of msgps
 *
 * @author rpessoa
 */
/* {
  "_id": "ms_MACADDRESS_TIMESTAMP",
  "_rev": "rev",
  "type": "monitoring_sensor",
  "subtype": "GPS",
  "latitude": 0.91561,
  "longitude": 0.91561,
  "timestamp": "TIMESTAMP",
  "mac_address": "MACADDRESS"
  "address":"Rua"
  "notification": "Check-in ! Check-out"
 *  } */
class MSGPS extends Base {

    protected $subtype;
    protected $timestamp;
    protected $mac_address;
    protected $latitude;
    protected $longitude;
    protected $address;
    protected $notification;

    public function __construct($type) {
        parent::__construct($type);
    }

    public function getMonitoringSensorByKeys($username, $macAddress, $subtype) {
        $bones = new Bones();
        $bones->couch->setDatabase($username);
//$arrayTimes = array();
        $sensorsGps = array();
        try {
            foreach ($bones->couch->get('_design/application/_view/getMonitoringSensor?key=["' . $macAddress . '","' . $subtype . '"]&limit=5&descending=true')->body->rows as $_monitoringGPS) {
//array_push($arrayTimes, "ola");
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
//return json_encode($array);
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

    public function calcIfCheckInOrCheckOut($username, $macaddress, $lat, $lng) {
        $safezonesArray = Safezone::getSafezonesByUserAndDevice($username, $macaddress);
        $str = "";
        $smalldistance = INF;
        $inside = false;
        $bestSafezone = NULL;
        $typeNotification = "";

        foreach ($safezonesArray as $_safezone) {
            $distanceFromSafezoneToCoordinatorReceived = MSGPS::haversineGreatCircleDistance($_safezone->latitude, $_safezone->longitude, $lat, $lng);
            $str.=$_safezone->name;

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
            $str.="small distance:" . $smalldistance;
            if ($bestSafezone->notification === "ALL") {
                $saveMonitoringSensorGPS = TRUE;
            } else if ($bestSafezone->notification === "CHECK_INS_ONLY" && $inside == TRUE) {
                $saveMonitoringSensorGPS = TRUE;
            } else if ($bestSafezone->notification === "CHECK_OUTS_ONLY" && $inside == FALSE) {
                $saveMonitoringSensorGPS = TRUE;
            }

            if ($saveMonitoringSensorGPS) {
                $str.= MSGPS::saveMonitoringSensorGPS($username, $macaddress, $lat, $lng, $typeNotification);
            } else {
                $str.="not necessary to save!!!";
            }
        }
        return $str;
    }

    public function saveMonitoringSensorGPS($username, $macaddress, $lat, $lng, $typeNotification) {
        $monitoringSensorGPS = new MSGPS();
        $timestamp = time();

        $monitoringSensorGPS->_id = $macaddress . "_ms_gps_" . $timestamp;
        $monitoringSensorGPS->type = "monitoring_sensor";
        $monitoringSensorGPS->subtype = "GPS";
        $monitoringSensorGPS->latitude = $lat;
        $monitoringSensorGPS->longitude = $lng;
        $monitoringSensorGPS->timestamp = $timestamp;
        $monitoringSensorGPS->mac_address = $macaddress;
        $monitoringSensorGPS->address = "get mtf address from google maps!";
        $monitoringSensorGPS->notification = $typeNotification;

        $bones = new Bones();
        $bones->couch->setDatabase($username);
        try {
            $bones->couch->put($monitoringSensorGPS->_id, $monitoringSensorGPS->to_json());
        } catch (SagCouchException $e) {
            return "some error in save monitoring gps";
        }
        return " - see in couchdb " . $username . "," . $macaddress . ", " . $lat . "," . $lng . "," . $typeNotification;
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

}
