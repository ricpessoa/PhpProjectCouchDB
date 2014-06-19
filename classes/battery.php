<?php

class Battery extends Sensor {

    protected $low_battery = 25;
    protected $critical_battery = 15;

    public function __construct() {
        parent::__construct('battery');
        $this->name_sensor = "Battery Level";
    }

    public function create() {
        $bones = new Bones();
        $bones->couch->setDatabase($_SESSION['username']);
        $this->_id = "$bones->couch->generateIDs(1)->body->uuids[0]";

        try {
            $bones->couch->put($this->_id, $this->to_json());
        } catch (SagCouchException $e) {
            $bones->error500($e);
        }
    }

    public static function updateBattery($username, $_id, $_rev, $low, $critical) {
        $sBat = Device::getDevice($username, $_id);
        $sBat->_rev = $_rev;

        foreach ($sBat->sensors as $_sensor) {
            if ($_sensor->type === "battery") {
                $_sensor->critical_battery = $critical;
                $_sensor->low_battery = $low;
            }
        }
        Device::updateSensor($username, $sBat);
    }

    public function to_json() {
        return array(
            "critical_battery" => $this->critical_battery,
            "low_battery" => $this->low_battery,
            "name_sensor" => $this->name_sensor,
            "enable" => $this->enable,
            "type" => $this->type
        );
    }

}
