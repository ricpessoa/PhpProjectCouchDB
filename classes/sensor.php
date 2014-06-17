<?php

class Sensor extends Base {

    protected $name_sensor;
    protected $enable = true;

    public function __construct($type) {
        parent::__construct($type);
    }

     public function to_json() {
      return array(
      "name_sensor" => $this->name_sensor,
      "enable" => $this->enable,
      "type" => $this->type
      );
      } 

    public function getSensors($username, $device) {
        $bones = new Bones();
        $bones->couch->setDatabase($username);

        $sensors = array();

        foreach ($bones->couch->get('_design/application/_view/getSensors?key="' . $device . '"&descending=true&reduce=false')->body->rows as $_sensor) {
            if ($_sensor->value->type == "GPS") {
                $sensor = new Sensor("GPS");
                $sensor->enable = $_sensor->value->enable;
                array_push($sensors, $_sensor->value->type);
            } else if ($_sensor->value->type == "panic_button") {
                $sensor = new Sensor("panic_button");
                $sensor->enable = $_sensor->value->enable;
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
    
    public function getSensorByType($username, $device,$type) {
        $bones = new Bones();
        $bones->couch->setDatabase($username);

        $sensors = array();

        foreach ($bones->couch->get('_design/application/_view/getSensors?key="' . $device . '"&descending=true&reduce=false')->body->rows as $_sensor) {
            if ($_sensor->value->type == "GPS" && "GPS" == $type) {
                $sensor = new Sensor("GPS");
                $sensor->enable = $_sensor->value->enable;
                array_push($sensors, $_sensor->value->type);
            } else if ($_sensor->value->type == "panic_button" && "panic_button" == $type) {
                $sensor = new Sensor("panic_button");
                $sensor->enable = $_sensor->value->enable;
                array_push($sensors, $_sensor->value->type);
            } else if ($_sensor->value->type == "temperature" && "temperature" == $type) {
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

    public function getSensorTemperatureByUserAndDevice($username, $device) {
        $bones = new Bones();
        $bones->couch->setDatabase($username);

        foreach ($bones->couch->get('_design/application/_view/getSensors?key="' . $device . '"&descending=true&reduce=false')->body->rows as $_sensor) {
            if ($_sensor->value->type == "temperature") {
                $sTemperature = new Temperature();
                $sTemperature->name_sensor = $_sensor->value->name_sensor;
                $sTemperature->min_temperature = $_sensor->value->min_temperature;
                $sTemperature->max_temperature = $_sensor->value->max_temperature;
                $sTemperature->type = $_sensor->value->type;
            }
        }
        return $sTemperature;
    }

    /* public function getSensorByMacAddressandType($username, $device, $type) {
      $bones = new Bones();
      $bones->couch->setDatabase($username);

      foreach ($bones->couch->get('_design/application/_view/getSensors?key="' . $device . '"&descending=true&reduce=false')->body->rows as $_sensor) {
      if ($_sensor->value->type == $type) {
      $sensor = new Sensor($type);
      $sensor->_id = $_sensor->id;
      $sensor->_rev = $_sensor->value->_rev;
      $sensor->name_sensor = $sensor->value->name_sensor;
      $sensor->enable = $_sensor->value->enable;
      return $sensor;
      }
      }
      return NULL;
      }
     */

    public function setEnableOfSensor($username, $deviceID, $sensorType, $enable) {
        $device = Device::getDevice($username, $deviceID);
        foreach ($device->sensors as $_sensor) {
            if ($_sensor->type == $sensorType) {
                $_sensor->enable = $enable;
                Device::updateSensor($username, $device);
            }
        }
    }

    public function changeEnable($enable) {
        if ($enable == TRUE) {
            $enable = FALSE;
        } else {
            $enable = TRUE;
        }
        return $enable;
    }

    public function saveInDB($username) {
        $bones = new Bones();
        $bones->couch->setDatabase($username);
        try {
            $bones->couch->put($this->_id, $this->to_json());
        } catch (SagCouchException $e) {
            $bones->error500($e);
        }
    }

    /*
      public function updateSensor($username, $device) {
      $bones = new Bones();
      $bones->couch->setDatabase($username);
      try {
      $bones->couch->put($device->_id, $device->to_json());
      } catch (SagCouchException $e) {
      $bones->error500($e);
      }
      }

     *      */
}
