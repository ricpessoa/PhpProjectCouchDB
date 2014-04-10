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
  } */
class MSGPS extends Base {

    protected $subtype;
    protected $timestamp;
    protected $mac_address;
    protected $latitude;
    protected $longitude;
    protected $arrayOfGPS;
    protected $arrayOfTimeGps;

    public function __construct($type) {
        parent::__construct($type);
    }

//    public function getMonitoringSensorByKeys($username, $macAddress, $subtype) {
//        $bones = new Bones();
//        $bones->couch->setDatabase($username);
//
//        $sensorsGps = array();
//        $sensorsGpsTime = array();
//        try {
//            foreach ($bones->couch->get('_design/application/_view/getMonitoringSensor?key=["' . $macAddress . '","' . $subtype . '"]&limit=5&descending=false')->body->rows as $_monitoringGPS) {
//                $monitoringSensorGPS = new MSGPS();
//
//                $monitoringSensorGPS->_id = $_monitoringGPS->id;
//                $monitoringSensorGPS->_rev = $_monitoringGPS->value->_rev;
//                $monitoringSensorGPS->type = $_monitoringGPS->value->type;
//                $monitoringSensorGPS->subtype = $_monitoringGPS->value->date_created;
//                $monitoringSensorGPS->latitude = $_monitoringGPS->value->latitude;
//                $monitoringSensorGPS->longitude = $_monitoringGPS->value->longitude;
//                $monitoringSensorGPS->timestamp = date('d/m H:i:s', $_monitoringGPS->value->timestamp);
//                $monitoringSensorGPS->mac_address = $_monitoringGPS->value->mac_address;
//
//                array_push($sensorsGps, $monitoringSensorGPS);
//
////array_push($sensorsGpsTime, date('d/m H:i:s', $_monitoringGPS->value->timestamp));
//                //array_push($sensorsGpsTime, $monitoringSensorGPS)
//                //array_push($arrayMSGPS, $monitoringSensorGPS);
//            }
//            /* if (sizeof($arrayMSGPS) > 0) {
//              return $arrayMSGPS;
//              } */
//            //$arrayOfGPS->arrayOfGPS = $arrayMSGPS;
//            //$monitoringSensorGPS->arrayOfTimeGps = $sensorsGpsTime;
//            return $sensorsGps;
//        } catch (Exception $exc) {
//            return $exc;
//        }
//        return NULL;
//    }

    public function getMonitoringSensorByKeys($username, $macAddress, $subtype) {
        $bones = new Bones();
        $bones->couch->setDatabase($username);
        //$arrayTimes = array();
        $sensorsGps = array();
        try {
            foreach ($bones->couch->get('_design/application/_view/getMonitoringSensor?key=["' . $macAddress . '","' . $subtype . '"]&limit=5&descending=false')->body->rows as $_monitoringGPS) {
                //array_push($arrayTimes, "ola");
                $monitoringSensorGPS = new MSGPS();

                $monitoringSensorGPS->_id = $_monitoringGPS->id;
                $monitoringSensorGPS->_rev = $_monitoringGPS->value->_rev;
                $monitoringSensorGPS->type = $_monitoringGPS->value->type;
                $monitoringSensorGPS->subtype = $_monitoringGPS->value->date_created;
                $monitoringSensorGPS->latitude = $_monitoringGPS->value->latitude;
                $monitoringSensorGPS->longitude = $_monitoringGPS->value->longitude;
                $monitoringSensorGPS->timestamp = date('d/m H:i:s', $_monitoringGPS->value->timestamp);
                $monitoringSensorGPS->mac_address = $_monitoringGPS->value->mac_address;

                array_push($sensorsGps, $monitoringSensorGPS);
            }
        } catch (Exception $exc) {
            return $exc;
        }
        if (sizeof($sensorsGps) > 0)
            return $sensorsGps;

        return NULL;
    }

    public function getArrayOfGPS() {
        return json_encode($this->arrayOfGPS);
    }

    public function getArrayOfGpsTime() {
        return json_encode($this->arrayOfTimeGps);
    }

}
