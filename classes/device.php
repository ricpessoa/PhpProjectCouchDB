<?php

class Device extends Base {

    protected $name_device;
    protected $sensors;
    protected $timestamp;
    protected $owner;

    public function __construct() {
        parent::__construct('device');
    }

    /* Create new device in db of Devices by Admin */

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

    public function create() {
        $bones = new Bones();

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
//Base::insertOrUpdateObjectInDB($bones->config->db_database_devices, $arr, TRUE);
            $bones->couch->put($this->_id, $arr); // NEED REFACTURE
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

    public function getDevices($username) { /* all devices to show in lists */
        $devices = array();
        foreach (Base::getViewToIterateBasedInUrl($username, '_design/application/_view/getDevices?descending=false&reduce=false') as $_device) {
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
        foreach (Base::getViewToIterateBasedInUrl($username, '_design/application/_view/getDevices?key="' . $_id . '"&reduce=false') as $_device) {
            $device = new Device();
            $device->_id = $_device->value->_id;
            $device->_rev = $_device->value->_rev;
            $device->name_device = $_device->value->name_device;
            $device->timestamp = $_device->value->timestamp;
            $device->sensors = $_device->value->sensors;

            return $device;
        }
        return NULL;
    }

    public static function updateSensor($username, $device) {
        Base::insertOrUpdateObjectInDB($username, $device, FALSE);
    }

    /* GET ON USERDB the number of devices */

//TODO: delete=false
    public function getNumberOfDevices($usernameDB) {
        return Base::getViewReduceCountBasedInUrl($usernameDB, '_design/application/_view/getDevices?descending=true&reduce=true');
    }

//getRevision of documento must be generic -> pass to BASE Class
    public function getDeviceRevisionByID($username, $device) {
        $bones = new Bones();
        $bones->couch->setDatabase($username);
        foreach ($bones->couch->get('_design/application/_view/getDevices?key="' . $device . '"&reduce=false')->body->rows as $_device) {
            return $_device->value->_rev;
        }
        return NULL;
    }

    /* Based in mac address of device ask on db if device exist in DB */

    public function deviceExist($usernameDB, $device) {
        try {
            $valueReturn = Base::getViewReduceCountBasedInUrl($usernameDB, '_design/application/_view/getDevices?key="' . $device . '"&reduce=true');
            if ($valueReturn > 0) {
                return TRUE;
            }
        } catch (SagCouchException $e) {
            if ($e->getCode() == "401") {
                return FALSE;
            }
        }
        return FALSE;
    }

    public static function getAllDevicesInDBDevices() {
        $bones = new Bones(); // lazy 
        $devices = array();
        foreach (Base::getViewToIterateBasedInUrl($bones->config->db_database_devices, '_design/application/_view/getAllDevice?descending=false&reduce=false')as $_device) {
            $device = new Device();
            $device->_id = $_device->value->_id;
            $device->name_device = $_device->value->name_device;
            $device->timestamp = $_device->value->timestamp;
            $device->sensors = $_device->value->sensors;
            $device->owner = $_device->value->owner;
            array_push($devices, $device);
        }
        return $devices;
    }

    public static function findTheDeviceOnDevicesDB($macAddress) {
        $bones = new Bones();
        foreach (Base::getViewToIterateBasedInUrl($bones->config->db_database_devices, '_design/application/_view/getAllDevice?key=["' . $macAddress . '",null]&descending=false&reduce=false')as $_device) {
            $device = new Device();
            $device->_id = $_device->value->_id;
            $device->_rev = $_device->value->_rev;
            $device->name_device = $_device->value->name_device;
            $device->timestamp = $_device->value->timestamp;
            $device->sensors = $_device->value->sensors;
            $device->owner = $_device->value->owner;
            return $device; //only one device
        }
        return NULL;
    }

    public static function updateTheOwnerDeviceOnDeviesDB($device) {
        $bones = new Bones();
        try {
            Base::insertOrUpdateObjectInDB($bones->config->db_database_devices, $device, FALSE);
        } catch (SagCouchException $e) {
            $bones->error500($e);
            return FALSE;
        }
        return TRUE;
    }

    public static function saveDeviceInUserDB($device) {
        $bones = new Bones();
        //create new device to not send the previous revision of documment in devicesDB
        $newDevice = new Device();
        $newDevice->_id = $device->_id;
        $newDevice->name_device = $device->name_device;
        $newDevice->timestamp = $device->timestamp;
        $newDevice->sensors = $device->sensors;
        $newDevice->owner = $device->owner;

        try {
            Base::insertOrUpdateObjectInDB(User::current_user(), $newDevice, FALSE);
        } catch (SagCouchException $e) {
            $bones->error500($e);
            return FALSE;
        }
        return TRUE;
    }

    public static function getNumberOfAvailableDevices() {
        $bones = new Bones();
        return Base::getViewReduceCountBasedInUrl($bones->config->db_database_devices, '_design/application/_view/getAvailableDevices?reduce=true');
    }

    public static function getNumberOfDevicesInDBDevices() {
        $bones = new Bones();
        return Base::getViewReduceCountBasedInUrl($bones->config->db_database_devices, '_design/application/_view/getAllDevice?descending=true&reduce=true');
    }

}
