<?php

class Battery extends Sensor {

    protected static $low_battery = 25;
    protected static $critical_battery = 15;

    public function __construct() {
        parent::__construct('battery');
        $this->name_sensor = "Battery Level";
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
