<?php

class MSTemperature extends Base {

    protected $subtype;
    protected $value;
    protected $timestamp;
    protected $mac_address;
    protected $notification; //{"LOW", "RANGE","HIGH"};
    protected $seen = FALSE;

    public function __construct($type) {
        parent::__construct($type);
    }

    public function getMonitoringSensorByKeys($usernameDB, $macAddress, $subtype) {
        $sensorsTime = array();
        $sensorsValues = array();
        try {
            $monitoringSensor = new MSTemperature();

            foreach (Base::getViewToIterateBasedInUrl($usernameDB, '_design/application/_view/getMonitoringSensor?key=["' . $macAddress . '","' . $subtype . '"]&limit=20&descending=true')as $_monitoring) {
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
        return json_encode(array_reverse($this->arrayValues));
    }

    public function getArrayTimes() {
        return json_encode(array_reverse($this->arrayTimes));
    }

    public function saveMonitoringSensorTemperature($usernameDB, $macaddress, $temperature, $notification, $timestamsOfDevice) {
        $monitoringSensorTemperature = new MSTemperature();
        $monitoringSensorTemperature->_id = "ms_" . $timestamsOfDevice . "_" . $macaddress . "_temperature";
        //$monitoringSensorTemperature->_id = $macaddress . "_ms_tmp_" . $timestamsOfDevice;
        $monitoringSensorTemperature->type = "monitoring_sensor";
        $monitoringSensorTemperature->subtype = "temperature";
        $monitoringSensorTemperature->value = (float) $temperature;
        $monitoringSensorTemperature->timestamp = $timestamsOfDevice;
        $monitoringSensorTemperature->mac_address = $macaddress;
        $monitoringSensorTemperature->notification = $notification;
        $monitoringSensorTemperature->seen = FALSE;

        try {
            Base::insertOrUpdateObjectInDB($usernameDB, $monitoringSensorTemperature, FALSE);
        } catch (SagCouchException $e) {
           // return "some error in save monitoring temperature";
            return " T ERROR ";
        }
        //return " - see in couchdb value TEMPERATURE:" . $temperature;
        return " T OK ";
    }

    public function calcIfLowOrRangeOrHighTemperature($usernamedb, $macaddress, $numTemperature, $timestamsOfDevice) {
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
        return MSTemperature::saveMonitoringSensorTemperature($usernamedb, $macaddress, $numTemperature, $notification, $timestamsOfDevice);
    }

}
