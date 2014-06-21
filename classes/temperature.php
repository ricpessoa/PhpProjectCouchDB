<?php

class Temperature extends Sensor {

    protected $min_temperature = 20;
    protected $max_temperature = 35;

    public function __construct() {
        parent::__construct('temperature');
        $this->name_sensor = "Sensor Temperature";
    }

    public function updateTemperature($username, $_id, $_rev, $max, $min) {
        $sTemp = Device::getDevice($username, $_id);
        $sTemp->_rev = $_rev;

        foreach ($sTemp->sensors as $_sensor) {
            if ($_sensor->type === "temperature") {
                $_sensor->min_temperature = $min;
                $_sensor->max_temperature = $max;
            }
        }
        Device::updateSensor($username, $sTemp);
    }

    public function to_json() {
        return array(
            "min_temperature" => $this->min_temperature,
            "max_temperature" => $this->max_temperature,
            "name_sensor" => $this->name_sensor,
            "enable" => $this->enable,
            "type" => $this->type
        );
    }

}
