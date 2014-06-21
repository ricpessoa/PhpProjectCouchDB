<?php

class MSPanicButton extends Base {

    protected $subtype;
    protected $value;
    protected $timestamp;
    protected $mac_address;
    protected $pressed;

    public function __construct($type) {
        parent::__construct($type);
    }

    public function getMonitoringSensorByKeys($usernameDB, $macAddress, $subtype) {
        try {
            $monitoringSensor1 = new MSPanicButton();

            foreach (Base::getViewToIterateBasedInUrl($usernameDB, '_design/application/_view/getMonitoringSensor?key=["' . $macAddress . '","' . $subtype . '"]&limit=1&descending=true') as $_monitoring1) {
                $monitoringSensor1->timestamp = date('d/m/Y H:i:s', $_monitoring1->value->timestamp);
                $monitoringSensor1->pressed = $_monitoring1->value->pressed;
            }

            return $monitoringSensor1;
        } catch (Exception $exc) {
            return exc;
        }
        return NULL;
    }

    public static function saveMonitoringSensorPanicButton($usernameDB, $macaddress, $pressed) {
        $monitoringSensorTemperature = new MSTemperature();
        $timestamp = time();
        $monitoringSensorTemperature->_id = $macaddress . "_ms_pb_" . $timestamp;
        $monitoringSensorTemperature->type = "monitoring_sensor";
        $monitoringSensorTemperature->subtype = "panic_button";
        $monitoringSensorTemperature->pressed = $pressed;
        $monitoringSensorTemperature->timestamp = $timestamp;
        $monitoringSensorTemperature->mac_address = $macaddress;

        try {
            Base::insertOrUpdateObjectInDB($usernameDB, $monitoringSensorTemperature, FALSE);
        } catch (SagCouchException $e) {
            return "some error in save monitoring panic button";
        }
        return " - see in couchdb value PANIC BUTTON:" . $pressed;
    }

}
