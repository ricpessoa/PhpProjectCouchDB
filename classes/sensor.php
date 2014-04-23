<?php

class Sensor extends Base {

    protected $name_sensor;

    public function __construct($type) {
        parent::__construct($type);
    }

    public function to_json() {
        return array(
            "name_sensor" => $this->name_sensor,
            "type" => $this->type
        );
    }

    public function getSensors($username, $device) {
        $bones = new Bones();
        $bones->couch->setDatabase($username);

        $sensors = array();

        foreach ($bones->couch->get('_design/application/_view/getSensors?key="' . $device . '"&descending=true&reduce=false')->body->rows as $_sensor) {
            if ($_sensor->value->type == "GPS") {
                array_push($sensors, $_sensor->value->type);
            } else if ($_sensor->value->type == "panic_button") {
                array_push($sensors, $_sensor->value->type);
            } else if ($_sensor->value->type == "temperature") {
                $sTemperature = new Temperature();
                $sTemperature->name_sensor = $_sensor->value->name_sensor;
                $sTemperature->min_temperature = $_sensor->value->min_temperature;
                $sTemperature->max_temperature = $_sensor->value->max_temperature;
                $sTemperature->type = $_sensor->value->type;
                array_push($sensors, $sTemperature);
            }
        }
        return $sensors;
    }

}
