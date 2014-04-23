<?php

class Temperature extends Sensor {

    protected $min_temperature;
    protected $max_temperature;

    public function __construct() {
        parent::__construct('temperature');
        $this->name_sensor = "Sensor Temperature";
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
            "type" => $this->type
        );
    }

}
