<?php

class MSTemperature extends Base {

    protected $subtype;
    protected $value;
    protected $timestamp;
    protected $mac_address;
    protected $notification; //{"LOW", "RANGE","HIGH"};

    public function __construct($type) {
        parent::__construct($type);
    }

    public function getMonitoringSensorByKeys($usernameDB, $macAddress, $subtype) {
        $sensorsTime = array();
        $sensorsValues = array();
        try {
            $monitoringSensor = new MSTemperature();

            foreach (Base::getViewToIterateBasedInUrl($usernameDB, '_design/application/_view/getMonitoringSensor?key=["' . $macAddress . '","' . $subtype . '"]&limit=24&descending=false')as $_monitoring) {
                array_push($sensorsTime, date('d/m H:i:s', $_monitoring->value->timestamp));
                array_push($sensorsValues, $_monitoring->value->value);
            }
            $monitoringSensor->arrayTimes = $sensorsTime;
            $monitoringSensor->arrayValues = $sensorsValues;
            if (sizeof(arrayValues) > 0)
                return $monitoringSensor;
        } catch (Exception $exc) {
            return NULL;
        }
        return NULL;
    }

    public function getArrayValues() {
        return json_encode($this->arrayValues);
    }

    public function getArrayTimes() {
        return json_encode($this->arrayTimes);
    }

    public function saveMonitoringSensorTemperature($usernameDB, $macaddress, $temperature, $notification) {
        $monitoringSensorTemperature = new MSTemperature();
        $timestamp = time();
        $monitoringSensorTemperature->_id = $macaddress . "_ms_tmp_" . $timestamp;
        $monitoringSensorTemperature->type = "monitoring_sensor";
        $monitoringSensorTemperature->subtype = "temperature";
        $monitoringSensorTemperature->value = (float) $temperature;
        $monitoringSensorTemperature->timestamp = $timestamp;
        $monitoringSensorTemperature->mac_address = $macaddress;
        $monitoringSensorTemperature->notification = $notification;

        try {
            Base::insertOrUpdateObjectInDB($usernameDB, $monitoringSensorTemperature, FALSE);
        } catch (SagCouchException $e) {
            return "some error in save monitoring temperature";
        }
        return " - see in couchdb value TEMPERATURE:" . $temperature;
    }

    public function calcIfLowOrRangeOrHighTemperature($usernamedb, $macaddress, $numTemperature) {
        $temperature = Sensor::getSensorTemperatureByUserAndDevice($usernamedb, $macaddress);
        if ($temperature == NULL) {
            return "Error the device " + $macaddress + " dont have sensor temperature";
        }

        $minTemp = $temperature->min_temperature;
        $maxTemp = $temperature->max_temperature;
        //return "received ".$minTemp ."and" .$maxTemp ."to compare".$numTemperature;
        $notification = "RANGE";
        if ($minTemp != null && $maxTemp != null) {
            if ($minTemp != $maxTemp) {
                if ($minTemp > $numTemperature)
                    $notification = "LOW";
                else if ($maxTemp < $numTemperature)
                    $notification = "HIGH";
            }
        }
        //return $minTemp." - ".$numTemperature." - ".$maxTemp." pass ".$notification;
        return MSTemperature::saveMonitoringSensorTemperature($usernamedb, $macaddress, $numTemperature, $notification);
    }

}
