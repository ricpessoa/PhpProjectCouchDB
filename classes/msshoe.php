<?php

class MSShoe extends Base {

    protected $subtype;
    protected $timestamp;
    protected $mac_address;
    protected $pressed;
    protected $seen = FALSE;

    public function __construct($type) {
        parent::__construct($type);
    }

    public function getMonitoringSensorByKeys($usernameDB, $macAddress, $subtype) {
        try {
            $monitoringSensor1 = new MSShoe();

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

    public static function saveMonitoringSensorShoe($usernameDB, $macaddress, $removed, $timestamsOfDevice) {
        $monitoringSensorShoe = new MSShoe();
        $monitoringSensorShoe->_id = "ms_" . $timestamsOfDevice . "_" . $macaddress . "_panic_button";
        $monitoringSensorShoe->type = "monitoring_sensor";
        $monitoringSensorShoe->subtype = "shoe";
        $monitoringSensorShoe->pressed = $removed;
        $monitoringSensorShoe->timestamp = $timestamsOfDevice;
        $monitoringSensorShoe->mac_address = $macaddress;
        $monitoringSensorShoe->seen = FALSE;


        try {
            Base::insertOrUpdateObjectInDB($usernameDB, $monitoringSensorShoe, FALSE);
        } catch (SagCouchException $e) {
            return "some error in save monitoring shoe";
        }
        return " - see in couchdb value Shoe:" . $removed;
    }

}
