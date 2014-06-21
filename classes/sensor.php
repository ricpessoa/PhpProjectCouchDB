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

    public function getSensors($usernameDB, $device) {
        $sensors = array();

        foreach (Base::getViewToIterateBasedInUrl($usernameDB, '_design/application/_view/getSensors?key="' . $device . '"&descending=true&reduce=false')as $_sensor) {
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
            } else if ($_sensor->value->type == "battery") {
                $sBattery = new Battery();
                $sBattery->name_sensor = $_sensor->value->name_sensor;
                $sBattery->low_battery = $_sensor->value->low_battery;
                $sBattery->critical_battery = $_sensor->value->critical_battery;
                $sBattery->type = $_sensor->value->type;
                array_push($sensors, $sBattery);
            }
        }
        return $sensors;
    }

    public function getSensorByType($usernameDB, $device, $type) {
        $sensors = array();

        foreach (Base::getViewToIterateBasedInUrl($usernameDB, '_design/application/_view/getSensors?key="' . $device . '"&descending=true&reduce=false') as $_sensor) {
            if ($_sensor->value->type == "GPS") {
                $sensor = new Sensor("GPS");
                $sensor->enable = $_sensor->value->enable;
                array_push($sensors, $_sensor->value->type);
                return $sensors;
            } else if ($_sensor->value->type == "panic_button" && $type == "panic_button") {
                $sensor = new Sensor("panic_button");
                $sensor->enable = $_sensor->value->enable;
                array_push($sensors, $_sensor->value->type);
                return $sensors;
            } else if ($_sensor->value->type == "temperature" && $type == "temperature") {
                $sTemperature = new Temperature();
                $sTemperature->name_sensor = $_sensor->value->name_sensor;
                $sTemperature->min_temperature = $_sensor->value->min_temperature;
                $sTemperature->max_temperature = $_sensor->value->max_temperature;
                $sTemperature->type = $_sensor->value->type;
                array_push($sensors, $sTemperature);
                return $sensors;
            } else if ($_sensor->value->type == "battery" && $type == "battery") {
                $sBattery = new Battery();
                $sBattery->name_sensor = $_sensor->value->name_sensor;
                $sBattery->low_battery = $_sensor->value->low_battery;
                $sBattery->critical_battery = $_sensor->value->critical_battery;
                $sBattery->type = $_sensor->value->type;
                array_push($sensors, $sBattery);
                return $sensors;
            }
        }
        return $sensors;
    }

    public function getSensorTemperatureByUserAndDevice($usernameDB, $device) {
        foreach (Base::getViewToIterateBasedInUrl($usernameDB, '_design/application/_view/getSensors?key="' . $device . '"&descending=true&reduce=false') as $_sensor) {
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

    public function getSensorBatteryByUserAndDevice($usernameDB, $device) {
        foreach (Base::getViewToIterateBasedInUrl($usernameDB, '_design/application/_view/getSensors?key="' . $device . '"&descending=true&reduce=false') as $_sensor) {
            if ($_sensor->value->type == "battery") {
                $sBattery = new Battery();
                $sBattery->name_sensor = $_sensor->value->name_sensor;
                $sBattery->low_battery = $_sensor->value->low_battery;
                $sBattery->critical_battery = $_sensor->value->critical_battery;
                $sBattery->type = $_sensor->value->type;
            }
        }
        return $sBattery;
    }

    public function setEnableOfSensor($username, $deviceID, $sensorType, $enable) {
        $device = Device::getDevice($username, $deviceID);
        foreach ($device->sensors as $_sensor) {
            if ($_sensor->type == $sensorType) {
                $_sensor->enable = $enable;
                Base::insertOrUpdateObjectInDB($username, $device, FALSE); // attention this a rude form to do it inside foreach
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

}
