<?php

/*
  {
  "_id": "ms_MACADDRESS_TIMESTAMP",
  "_rev": "rev",
  "type": "monitoring_sensor",
  "subtype": "panic_button",
  "pressed": true,
  "timestamp": "TIMESTAMP",
  "mac_address": "MACADDRESS"
  }
 *  */

class MSPanicButton extends Base {

    protected $subtype;
    protected $value;
    protected $timestamp;
    protected $mac_address;
    protected $pressed;

    public function __construct($type) {
        parent::__construct($type);
    }

    public function getMonitoringSensorByKeys($username, $macAddress, $subtype) {
        $bones = new Bones();
        $bones->couch->setDatabase($username);
        try {
            $monitoringSensor1 = new MSPanicButton();

            foreach ($bones->couch->get('_design/application/_view/getMonitoringSensor?key=["' . $macAddress . '","' . $subtype . '"]&limit=1&descending=true')->body->rows as $_monitoring1) {

                $monitoringSensor1->timestamp = date('d/m/Y H:i:s', $_monitoring1->value->timestamp);
                $monitoringSensor1->pressed = $_monitoring1->value->pressed;
            }
//            $monitoringSensor1->pressed = TRUE;
//            $monitoringSensor1->timestamp = "123";
            /* if ($monitoringSensor1->pressed) {

              } */

            return $monitoringSensor1;
        } catch (Exception $exc) {
            return exc;
        }
        return NULL;
    }

}