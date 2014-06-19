<?php

class MSBattery extends Base {

    protected $subtype;
    protected $value;
    protected $timestamp;
    protected $mac_address;
    protected $notification; //{"CRITICAL","LOW", "RANGE"};

    public function __construct($type) {
        parent::__construct($type);
    }

    public function getMonitoringSensorByKeys($username, $macAddress, $subtype) {
        $bones = new Bones();
        $bones->couch->setDatabase($username);
        $sensorsBattery = array();
        $sensorsValues = array();
        try {//\[
//foreach ($bones->couch->get('_design/application/_view/getMonitoringSensor?key=\["' . $macAddress . '","' . $subtype . '"\]')->body->rows as $_monitoring) {
            $monitoringSensor = new MSBattery();

            foreach ($bones->couch->get('_design/application/_view/getMonitoringSensor?key=["' . $macAddress . '","' . $subtype . '"]&limit=24&descending=false')->body->rows as $_monitoring) {
                array_push($sensorsBattery, date('d/m H:i:s', $_monitoring->value->timestamp));
                array_push($sensorsValues, $_monitoring->value->value);

//array_push($sensorsMonitoring, $monitoringSensor->value);
            }
            $monitoringSensor->arrayTimes = $sensorsBattery;
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

    public static function saveMonitoringSensorBattery($username, $macaddress, $level, $notification) {
        $monitoringSensorBattery = new MSBattery();
        $timestamp = time();
        $monitoringSensorBattery->_id = $macaddress . "_ms_batlvl_" . $timestamp;
        $monitoringSensorBattery->type = "monitoring_sensor";
        $monitoringSensorBattery->subtype = "battery";
        $monitoringSensorBattery->value = $level;
        $monitoringSensorBattery->timestamp = $timestamp;
        $monitoringSensorBattery->mac_address = $macaddress;
        $monitoringSensorBattery->notification = $notification;

        $bones = new Bones();
        $bones->couch->setDatabase($username);
        try {
            $bones->couch->put($monitoringSensorBattery->_id, $monitoringSensorBattery->to_json());
        } catch (SagCouchException $e) {
            return "some error in save monitoring battery";
        }
        return " - see in couchdb value battery:" . $level;
    }

    public static function calcIfCriticalLowOrRangeBatteryLevel($usernamedb, $macaddress, $level) {
        $battery = Sensor::getSensorBatteryByUserAndDevice($usernamedb, $macaddress);
        if ($battery == NULL) {
            return "Error the device " + $macaddress + " dont have sensor battery";
        }

        $low = $battery->low_battery;
        $critical = $battery->critical_battery;
        //return "received ".$minTemp ."and" .$maxTemp ."to compare".$numTemperature;
        $notification = "RANGE";
        if ($critical != null && $low != null) {
            if ($critical <= $level)
                $notification = "CRITICAL";
            else if ($low <= $level)
                $notification = "LOW";
        }
        //return $minTemp." - ".$numTemperature." - ".$maxTemp." pass ".$notification;
        return MSBattery::saveMonitoringSensorBattery($usernamedb, $macaddress, $level, $notification);
    }

}
