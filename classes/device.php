<?php

class Device extends Base {

    protected $name_device;
    protected $sensors;
    protected $timestamp;

    public function __construct() {
        parent::__construct('device');
    }

    public function create() {
        $bones = new Bones();
        $bones->couch->setDatabase($_SESSION['username']);

        $this->timestamp = time();

        $str = $this->to_json();
        $arr = json_decode($str, true);

        foreach ($this->sensors as $sensor) {
            if ($sensor != null)
                array_push($arr['sensors'], $sensor->to_json());
        }
        try {
            $bones->couch->put($this->_id, $arr);
        } catch (SagCouchException $e) {
            if ($e->getCode() == "409") {
                $bones->set('error', 'A device with this mac address already exists.');
                $bones->render('/devices/newdevice');
                exit;
            }
        }
       /* try {
            $bones->couch->put($this->_id, $arr);
        } catch (SagCouchException $e) {
            $bones->error500($e);
        }*/
    }

}
