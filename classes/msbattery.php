<?php

class MSBattery extends Base {

    protected $subtype;
    protected $value;
    protected $timestamp;
    protected $mac_address;
    protected $notification; //{"CRITICAL","LOW", "RANGE"};
    protected $seen = FALSE;

    public function __construct($type) {
        parent::__construct($type);
    }

    public function getMonitoringSensorByKeys($usernameDB, $macAddress, $subtype) {
        $sensorsBattery = array();
        $sensorsValues = array();
        try {
            $monitoringSensor = new MSBattery();
            foreach (Base::getViewToIterateBasedInUrl($usernameDB, '_design/application/_view/getMonitoringSensor?key=["' . $macAddress . '","' . $subtype . '"]&limit=20&descending=true') as $_monitoring) {
                array_push($sensorsBattery, date('d/m H:i:s', $_monitoring->value->timestamp));
                array_push($sensorsValues, $_monitoring->value->value);
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
        return json_encode(array_reverse($this->arrayValues));
    }

    public function getArrayTimes() {
        return json_encode(array_reverse($this->arrayTimes));
    }

    public static function saveMonitoringSensorBattery($usernameDB, $macaddress, $level, $notification, $timestamsOfDevice) {
        $monitoringSensorBattery = new MSBattery();
        $monitoringSensorBattery->_id = "ms_" . $timestamsOfDevice . "_" . $macaddress . "_battery";
        //$monitoringSensorBattery->_id = $macaddress . "_ms_batlvl_" . $timestamsOfDevice;
        $monitoringSensorBattery->type = "monitoring_sensor";
        $monitoringSensorBattery->subtype = "battery";
        $monitoringSensorBattery->value = (float) $level;
        $monitoringSensorBattery->timestamp = $timestamsOfDevice;
        $monitoringSensorBattery->mac_address = $macaddress;
        $monitoringSensorBattery->notification = $notification;
        $monitoringSensorBattery->seen = FALSE;

        try {
            Base::insertOrUpdateObjectInDB($usernameDB, $monitoringSensorBattery, FALSE);
        } catch (SagCouchException $e) {
            //return "some error in save monitoring battery";
            return " B ERROR ";
        }
        //return " - see in couchdb value battery:" . $level;
        return " B OK ";
    }

    public static function calcIfCriticalLowOrRangeBatteryLevel($usernamedb, $macaddress, $level, $timestamsOfDevice) {
        $battery = Sensor::getSensorBatteryByUserAndDevice($usernamedb, $macaddress);
        if ($battery == NULL) {
            return "Error the device " + $macaddress + " dont have sensor battery";
        }

        $low = $battery->low_battery;
        $critical = $battery->critical_battery;
        //return "received ".$minTemp ."and" .$maxTemp ."to compare".$numTemperature;
        $notification = "RANGE";
        if ($critical != null && $low != null) {
            if ($level <= $critical)
                $notification = "CRITICAL";
            else if ($level <= $low)
                $notification = "LOW";
        }
        //return $minTemp." - ".$numTemperature." - ".$maxTemp." pass ".$notification;
        return MSBattery::saveMonitoringSensorBattery($usernamedb, $macaddress, $level, $notification, $timestamsOfDevice);
    }

}
