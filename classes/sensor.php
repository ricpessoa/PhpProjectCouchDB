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
}
