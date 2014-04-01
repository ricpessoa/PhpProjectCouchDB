<?php

class Temperature extends Sensor {

    protected $min_temperature;
    protected $max_temperatrue;

    public function __construct() {
        parent::__construct('temperature');
        $this->name_sensor = "Sensor Temperature";
    }

//"min_temperature":"23","max_temperatrue":"26","_id":null,"type":"temperature"
    public function create() { /*     * Need test the creation of Device */
        $bones = new Bones();
        $bones->couch->setDatabase($_SESSION['username']);
        $this->_id = "$bones->couch->generateIDs(1)->body->uuids[0]";

        try {
            $bones->couch->put($this->_id, $this->to_json());
        } catch (SagCouchException $e) {
            $bones->error500($e);
        }
    }

    public function to_json() {
        return array(
            "min_temperature" => $this->min_temperature,
            "max_temperatrue" => $this->max_temperatrue,
            "name_sensor" => $this->name_sensor,
            "type" => $this->type
        );
    }
}
