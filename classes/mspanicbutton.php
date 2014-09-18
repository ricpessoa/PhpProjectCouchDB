<?php

class MSPanicButton extends Base {

    protected $subtype;
    protected $value;
    protected $timestamp;
    protected $mac_address;
    protected $pressed;
    protected $seen;

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

    public static function saveMonitoringSensorPanicButton($usernameDB, $macaddress, $pressed, $timestamsOfDevice) {
        $monitoringSensorPanicButton = new MSPanicButton();
        //$monitoringSensorTemperature->_id = $macaddress . "_ms_pb_" . $timestamsOfDevice;
        $monitoringSensorPanicButton->_id = "ms_" . $timestamsOfDevice . "_" . $macaddress . "_panic_button";
        $monitoringSensorPanicButton->type = "monitoring_sensor";
        $monitoringSensorPanicButton->subtype = "panic_button";
        $monitoringSensorPanicButton->pressed = $pressed;
        $monitoringSensorPanicButton->timestamp = $timestamsOfDevice;
        $monitoringSensorPanicButton->mac_address = $macaddress;

        try {
            Base::insertOrUpdateObjectInDB($usernameDB, $monitoringSensorPanicButton, FALSE);
        } catch (SagCouchException $e) {
            return "some error in save monitoring panic button";
        }
        return " - see in couchdb value PANIC BUTTON:" . $pressed;
    }

}
