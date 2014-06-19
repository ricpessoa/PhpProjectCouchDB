<?php

class Device extends Base {

    protected $name_device;
    protected $sensors;
    protected $timestamp;
    protected $owner;

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
            User::registeDeviceInUser(User::current_user(), $this->_id, FALSE);
        } catch (SagCouchException $e) {
            if ($e->getCode() == "409") {
                $bones->set('error', 'A device with this mac address already exists.');
                $bones->render('/devices/newdevice');
                exit;
            }
        }
    }

    public function getDevices($username) { /* all devices to show in lists */
        $bones = new Bones();
        $bones->couch->setDatabase($username);

        $devices = array();

        foreach ($bones->couch->get('_design/application/_view/getDevices?descending=false&reduce=false')->body->rows as $_device) {
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

    public static function getDevice($username, $_id) { /* one device to update */
        $bones = new Bones();
        $bones->couch->setDatabase($username);

        foreach ($bones->couch->get('_design/application/_view/getDevices?key="' . $_id . '"&reduce=false')->body->rows as $_device) {
            $device = new Device();
            $device->_id = $_device->id;
            $device->_rev = $_device->value->_rev;
            $device->name_device = $_device->value->name_device;
            $device->timestamp = $_device->value->timestamp;
            $device->sensors = $_device->value->sensors;

            return $device;
        }
        return NULL;
    }

    public static function updateSensor($username, $device) {
        $bones = new Bones();
        $bones->couch->setDatabase($username);
        try {
            $bones->couch->put($device->_id, $device->to_json());
        } catch (SagCouchException $e) {
            $bones->error500($e);
        }
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

    public function getDeviceRevisionByID($username, $device) {
        $bones = new Bones();
        $bones->couch->setDatabase($username);
        foreach ($bones->couch->get('_design/application/_view/getDevices?key="' . $device . '"&reduce=false')->body->rows as $_device) {
            return $_device->value->_rev;
        }
        return NULL;
    }

    public function deviceExist($username, $device) {
        $bones = new Bones();
        $bones->couch->setDatabase($username);

        try {
            $rows = $bones->couch->get('_design/application/_view/getDevices?key="' . $device . '"')->body->rows;
        } catch (SagCouchException $e) {
            if ($e->getCode() == "401") {
                return FALSE;
            }
        }

        if ($rows) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    //
    public static function getNumberOfDevicesInDBDevices() {
        $bones = new Bones();
        $bones->couch->setDatabase('devices');
        $bones->couch->login($bones->config->db_admin_user, $bones->config->db_admin_password);
        $rows = $bones->couch->get('_design/application/_view/getAllDevice?descending=true&reduce=true')->body->rows;
        if ($rows) {
            return $rows[0]->value;
        } else {
            return 0;
        }
    }

    public static function getAllDevicesInDBDevices() {
        $bones = new Bones();
        $bones->couch->setDatabase('devices');
        $bones->couch->login($bones->config->db_admin_user, $bones->config->db_admin_password);
        $devices = array();

        foreach ($bones->couch->get('_design/application/_view/getAllDevice?descending=false&reduce=false')->body->rows as $_device) {
            $device = new Device();
            $device->_id = $_device->id;
            $device->name_device = $_device->value->name_device;
            $device->timestamp = $_device->value->timestamp;
            $device->sensors = $_device->value->sensors;
            $device->owner = $_device->value->sensors;

            array_push($devices, $device);
        }

        return $devices;
    }

}
