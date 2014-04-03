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

        //remove all empty array objects :S
        foreach ($arr['sensors'] as $key => $values) {
            unset($arr['sensors'][$key]);
        }

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
    }

    public function getDevices($username) {
        $bones = new Bones();
        $bones->couch->setDatabase($username);

        $devices = array();
        /*
          protected $name_device;
          protected $sensors;
          protected $timestamp;
         *  */
        foreach ($bones->couch->get('_design/application/_view/getDevices?descending=true&reduce=false')->body->rows as $_device) {
            $device = new Device();
            $device->_id = $_device->id;
            $device->_rev = $_device->value->_rev;
            $device->name_device = $_device->value->name_device;
            $device->timestamp = $_device->value->timestamp;
            $device->sensors = $_device->value->sensors;

            array_push($devices, $device);
        }

        return $devices;
    }

    public function getNumberOfDevices($username) {
        $bones = new Bones();
        $bones->couch->setDatabase($username);


        $rows = $bones->couch->get('_design/application/_view/getDevices?descending=true&reduce=true')->body->rows;
        if ($rows) {
            return $rows[0]->value;
        } else {
            return 0;
        }
    }
}
