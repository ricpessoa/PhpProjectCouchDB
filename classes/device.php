<?php

class Device extends Base {

    protected $name_device;
    protected $sensors;
    protected $timestamp;
    protected $owner;
    protected $deleted = FALSE;
    protected $monitoring = TRUE;

    public function __construct() {
        parent::__construct('device');
    }

    /* Create new device in db of Devices by Admin */

    public static function createDeviceFromManagerDevice($app) {
        $mac_device = $app->form('mac_address');
        $name_device = $app->form('name_device');
        $isToEdit = $app->form('isEditDevice');
        if ($isToEdit == "1") {
            $device = Device::findTheDeviceOnDevicesDB($mac_device);
        } else {
            $device = new Device();
        }

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
            $bones->couch->put($this->_id, $arr); // NEED REFACTURE
        } catch (SagCouchException $e) {
            if ($e->getCode() == "409") {
                $bones->set('error', 'A device with this mac address already exists.');
                $bones->render('/admin/manager_newdevice');
                exit();
            }
        }
    }

    public function getDevices($usernameDB) { /* all devices to show in lists */
        $devices = array();
        foreach (Base::getViewToIterateBasedInUrl($usernameDB, '_design/application/_view/getDevices?descending=false&reduce=false') as $_device) {
            if ($_device->value->deleted == FALSE) {
                $device = new Device();
                $device->_id = $_device->id;
                $device->_rev = $_device->value->_rev;
                $device->name_device = $_device->value->name_device;
                $device->timestamp = $_device->value->timestamp;
                $device->sensors = $_device->value->sensors;
                $device->owner = $_device->value->owner;
                $device->deleted = $_device->value->deleted;
                $device->monitoring = $_device->value->monitoring;
                array_push($devices, $device);
            }
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
            $device->owner = $_device->value->owner;
            $device->deleted = $_device->value->deleted;
            $device->monitoring = $_device->value->monitoring;
            return $device;
        }
        return NULL;
    }

    public static function updateSensor($username, $device) {
        Base::insertOrUpdateObjectInDB($username, $device, FALSE);
    }

    public static function insertOrEditDevice($usernameDB, $mac_device, $name_device, $isToEditDevice) {
        $deviceOfUser = Device::getDevice($usernameDB, $mac_device);
        if ($isToEditDevice == "1" && $deviceOfUser != NULL) {
//edit device name
            if ($deviceOfUser->name_device != $name_device && trim($name_device) != '') {
                $deviceOfUser->name_device = $name_device;
                return Base::insertOrUpdateObjectInDB($usernameDB, $deviceOfUser, FALSE);
            }
        } else if ($isToEditDevice == "0" && $deviceOfUser != NULL) {
//revert delete is to insert but $deviceUser was founded
            $deviceOfUser->deleted = false;
            $deviceOfUser->monitoring = true;
            return Base::insertOrUpdateObjectInDB($usernameDB, $deviceOfUser, FALSE);
        } else if ($isToEditDevice == "0" && $deviceOfUser == NULL) {
            //insert for first time -> deviceUser not founded
            $deviceOfDevices = Device::findTheDeviceOnDevicesDB($mac_device);
            if ($deviceOfDevices != NULL) {
                if ($deviceOfDevices->name_device != $name_device && trim($name_device) != '') {
                    $deviceOfDevices->name_device = $name_device;
                }
                $deviceOfDevices->owner = $usernameDB;
                if (Device::updateTheOwnerDeviceOnDeviesDB($deviceOfDevices)) {
                    return Device::saveDeviceInUserDB($usernameDB, $deviceOfDevices);
                }
            }
        }
        return FALSE;
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
            $device->_rev = $_device->value->_rev;
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

    public static function saveDeviceInUserDB($userdb, $device) {
        $bones = new Bones();
//create new device to not send the previous revision of documment in devicesDB
        $newDevice = new Device();
        $newDevice->_id = $device->_id;
        $newDevice->name_device = $device->name_device;
        $newDevice->timestamp = $device->timestamp;
        $newDevice->sensors = $device->sensors;
        $newDevice->owner = $device->owner;
        $newDevice->deleted = FALSE;
        $newDevice->monitoring = TRUE;

        try {
            Base::insertOrUpdateObjectInDB($userdb, $newDevice, FALSE);
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

    public static function findUserOfDevice($macaddress) {
        $bones = new Bones();
        foreach (Base::getViewToIterateBasedInUrl($bones->config->db_database_devices, '_design/application/_view/getUserOfDevice?key="' . $macaddress . '"') as $_mac_address) {
            return $_mac_address->value;
        }
        return NULL;
    }

    public function getDeviceRevisionByID($username, $device) {
        $bones = new Bones();
        $bones->couch->setDatabase($username);
        foreach ($bones->couch->get('_design/application/_view/getDevices?key="' . $device . '"&reduce=false')->body->rows as $_device) {
            return $_device->value->_rev;
        }
        return NULL;
    }

    /* NEED REFACTURING ON APP WHEN ADD NEW DEVICE
     * public function registeDeviceInUser($username, $device, $delete) {
      $bones = new Bones();
      $bones->couch->setDatabase('_users');
      $bones->couch->login($bones->config->db_admin_user, $bones->config->db_admin_password);

      $document = $bones->couch->get('org.couchdb.user:' . $username)->body;
      $devices = $document->devices;
      $str = "add ->";
      $alreadyHaveThisDevice = FALSE;
      $i = 0;
      foreach ($devices as $_device) {
      if ($_device == $device) {
      $str.=" " . $_device . "  ";
      $alreadyHaveThisDevice = true;
      if ($delete == TRUE) {
      //array_pop($devices);
      unset($devices[$i]);
      //$devices[$i] = "";
      //$devices = array_diff($devices, array($_device));
      $str.="delete = " . $_device . "!";
      }
      break;
      }
      $i++;
      }
      if ($alreadyHaveThisDevice == FALSE) {
      array_push($devices, $device);
      }
      $document->devices = array_values($devices);

      $str .= "show ->";

      foreach ($devices as $_device) {
      $str.=" - " . $_device . " - ";
      }

      try {
      $bones->couch->put($document->_id, $document);
      } catch (SagCouchException $exc) {
      echo $exc->getTraceAsString();
      return NULL;
      }


      return $str;
      } */
}
