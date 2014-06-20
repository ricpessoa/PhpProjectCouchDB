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
        $bones->couch->setDatabase($bones->config->db_database_devices);

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
            //User::registeDeviceInUser(User::current_user(), $this->_id, FALSE);
        } catch (SagCouchException $e) {
            if ($e->getCode() == "409") {
                //return FALSE;
                $bones->set('error', 'A device with this mac address already exists.');
                $bones->render('/admin/manager_newdevice');
                exit();
            }
        }
        //return TRUE;
    }

    public static function createDeviceFromManagerDevice($app) {
        $mac_device = $app->form('mac_address');
        $name_device = $app->form('name_device');
        $device = new Device();
        $device->_id = $mac_device;
        if (trim($name_device) != '') {
            $device->name_device = $name_device;
        }
        $myArray = array();
        if ($app->form('check_panic_bt_send') == "1") {
            $sensorPanic = new Sensor("panic_button");
            $sensorPanic->name_sensor = "Panic Button";
            $myArray[] = $sensorPanic;
        }
        if ($app->form('check_gps_send') == "1") {
            $sensorGPS = new Sensor("GPS");
            $sensorGPS->name_sensor = "Sensor GPS";
            $myArray[] = $sensorGPS;
        }
        if ($app->form('check_temperature_send') == "1") {
            $temperature = new Temperature();
            $temperature->min_temperature = $app->form('min_temp_notification');
            $temperature->max_temperature = $app->form('max_temp_notification');
            $myArray[] = $temperature;
        }
        if ($app->form('check_battery_lvl_send') == "1") {
            $battery = new Battery();
            $battery->low_battery = $app->form('low_battery_notification');
            $battery->critical_battery = $app->form('critical_battery_notification');
            $myArray[] = $battery;
        }
        $device->sensors = $myArray;

        $device->create();
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

    public static function getAvailableDevices() {
        $bones = new Bones();
        $bones->couch->setDatabase('devices');
        $bones->couch->login($bones->config->db_admin_user, $bones->config->db_admin_password);
        $rows = $bones->couch->get('_design/application/_view/getAvailableDevices?reduce=true')->body->rows;
        if ($rows) {
            return $rows[0]->value;
        } else {
            return 0;
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

    
    public static function getNumberAllDevices() {
        $bones = new Bones();
        $bones->couch->setDatabase($bones->config->db_database_devices);
        $rows = $bones->couch->get('_design/application/_view/getAllDevice?reduce=true')->body->rows;
        if($rows){
            return $rows->value;
        }
        return NULL;
    }
    
    
    /*public static function getNumberAvailableDevices() {
        $bones = new Bones();
        $bones->couch->setDatabase($bones->config->db_database_devices);
        $rows = $bones->couch->get('_design/application/_view/getAvailableDevices?reduce=true')->body->rows;
        if($rows){
            return $rows->value;
        }
        return NULL;
    }*/

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
            $device->owner = $_device->value->owner;

            array_push($devices, $device);
        }

        return $devices;
    }

}
