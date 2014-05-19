<?php

class MSTemperature extends Base {

    protected $subtype;
    protected $value;
    protected $timestamp;
    protected $mac_address;
    //protected $arrayValues;
    //protected $arrayTimes;

    public function __construct($type) {
        parent::__construct($type);
    }

    public function getMonitoringSensorByKeys($username, $macAddress, $subtype) {
        $bones = new Bones();
        $bones->couch->setDatabase($username);
        $sensorsTime = array();
        $sensorsValues = array();
        try {//\[
//foreach ($bones->couch->get('_design/application/_view/getMonitoringSensor?key=\["' . $macAddress . '","' . $subtype . '"\]')->body->rows as $_monitoring) {
            $monitoringSensor = new MSTemperature();

            foreach ($bones->couch->get('_design/application/_view/getMonitoringSensor?key=["' . $macAddress . '","' . $subtype . '"]&limit=24&descending=false')->body->rows as $_monitoring) {
//$monitoringSensor = new MonitoringSensor();
//$monitoringSensor->_id = $_monitoring->id;
//$monitoringSensor->_rev = $_monitoring->value->_rev;
//$monitoringSensor->type = $_monitoring->value->type;
//$monitoringSensor->subtype = $_monitoring->value->date_created;
//$monitoringSensor->value = $_monitoringgetArrayValues();
//$monitoringSensor->timestamp = $_monitoring->value->timestamp;
//$monitoringSensor->mac_address = $_monitoring->value->mac_address;
//$monitoringSensor
                array_push($sensorsTime, date('d/m H:i:s', $_monitoring->value->timestamp));
                array_push($sensorsValues, $_monitoring->value->value);

//array_push($sensorsMonitoring, $monitoringSensor->value);
            }
            $monitoringSensor->arrayTimes = $sensorsTime;
            $monitoringSensor->arrayValues = $sensorsValues;
            if (sizeof(arrayValues) > 0)
                return $monitoringSensor;
        } catch (Exception $exc) {
            return NULL;
        }
        return NULL;
    }

    public function getArrayValues() {
        return json_encode($this->arrayValues);
    }

    public function getArrayTimes() {
        return json_encode($this->arrayTimes);
    }

    public function saveMonitoringSensorTemperature($username, $macaddress, $temperature) {
        $monitoringSensorTemperature = new MSTemperature();
        $timestamp = time();
        $monitoringSensorTemperature->_id = $macaddress . "_ms_tmp_" . $timestamp;
        $monitoringSensorTemperature->type = "monitoring_sensor";
        $monitoringSensorTemperature->subtype = "temperature";
        $monitoringSensorTemperature->value = (float)$temperature;
        $monitoringSensorTemperature->timestamp = $timestamp;
        $monitoringSensorTemperature->mac_address = $macaddress;

        $bones = new Bones();
        $bones->couch->setDatabase($username);
        try {
            $bones->couch->put($monitoringSensorTemperature->_id, $monitoringSensorTemperature->to_json());
        } catch (SagCouchException $e) {
            return "some error in save monitoring gps";
        }
        return " - see in couchdb value TEMPERATURE:" . $temperature;
    }

}
