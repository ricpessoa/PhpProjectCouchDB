<?php

class Safezone extends Base {

    protected $address;
    protected $name;
    protected $latitude;
    protected $longitude;
    protected $radius;
    protected $notification;
    protected $timestamp;
    protected $device;

    public function __construct() {
        parent::__construct('safezone');
    }

    public function create() { /*     * Need test the creation of Safezone */
        $bones = new Bones();
        $bones->couch->setDatabase($_SESSION['username']);

        //$this->_id = $bones->couch->generateIDs(1)->body->uuids[0];

        $this->timestamp = time();

        try {
            $bones->couch->put($this->_id, $this->to_json());
        } catch (SagCouchException $e) {
            $bones->error500($e);
            $bones->set('error', 'Error try save the safezone');
            $bones->render('/safezone/showsafezones');
            exit;
        }
    }

    public function get_safezones_by_user($username) {
        $bones = new Bones();
        $bones->couch->setDatabase($username);

        $safezones = array();

        foreach ($bones->couch->get('_design/application/_view/getSafezones?reduce=false')->body->rows as $_safezone) {
            $safezone = new Safezone();
            $safezone->_id = $_safezone->value->_id;
            $safezone->_rev = $_safezone->value->_rev;
            $safezone->address = $_safezone->value->address;
            $safezone->name = $_safezone->value->name;
            $safezone->latitude = $_safezone->value->latitude;
            $safezone->longitude = $_safezone->value->longitude;
            $safezone->radius = $_safezone->value->radius;
            $safezone->notification = $_safezone->value->notification;
            $safezone->timestamp = $_safezone->value->timestamp;

            array_push($safezones, $safezone);
        }

        return $safezones;
    }

    public function getSafezonesByUserAndDevice($username, $mac_address) {
        $bones = new Bones();
        $bones->couch->setDatabase($username);

        $safezones = array();
        foreach ($bones->couch->get('_design/application/_view/getSafezones?key="' . $mac_address . '"&reduce=false')->body->rows as $_safezone) {
            $safezone = new Safezone();
            $safezone->_id = $_safezone->value->_id;
            $safezone->_rev = $_safezone->value->_rev;
            $safezone->address = $_safezone->value->address;
            $safezone->name = $_safezone->value->name;
            $safezone->latitude = $_safezone->value->latitude;
            $safezone->longitude = $_safezone->value->longitude;
            $safezone->radius = $_safezone->value->radius;
            $safezone->notification = $_safezone->value->notification;
            $safezone->timestamp = $_safezone->value->timestamp;
            $safezone->device = $_safezone->value->device;

            array_push($safezones, $safezone);
        }

        return $safezones;
    }

    public function get_safezones_count_by_user($username) {
        $bones = new Bones();
        $bones->couch->setDatabase($username);
        $rows = $bones->couch->get('_design/application/_view/getSafezones?reduce=true')->body->rows;

        if ($rows) {
            return $rows[0]->value;
        } else {
            return 0;
        }
    }

    public function to_jsonString() {
        return '{'
                . '"_id":' . '"' . $this->_id . '",'
                . '"_rev":' . '"' . $this->_rev . '",'
                . '"address":' . '"' . $this->address . '",'
                . '"name":' . '"' . $this->name . '",'
                . '"latitude":' . '"' . $this->latitude . '",'
                . '"longitude":' . '"' . $this->longitude . '",'
                . '"radius":' . '"' . $this->radius . '",'
                . '"notification":' . '"' . $this->notification . '",'
                . '"timestamp":' . '"' . $this->timestamp . '"'
                . '},';
    }

    public function getArrayOfSafezonesToJson($array) {
        //return json_encode($array);
        $jsonReturn = "";
        foreach ($array as $_row) {
            $jsonReturn.='{'
                    . '"_id":' . '"' . $_row->_id . '",'
                    . '"_rev":' . '"' . $_row->_rev . '",'
                    . '"address":' . '"' . $_row->address . '",'
                    . '"name":' . '"' . $_row->name . '",'
                    . '"latitude":' . '"' . $_row->latitude . '",'
                    . '"longitude":' . '"' . $_row->longitude . '",'
                    . '"radius":' . '"' . $_row->radius . '",'
                    . '"notification":' . '"' . $_row->notification . '",'
                    . '"timestamp":' . '"' . $_row->timestamp . '",'
                    . '"device":' . '"' . $_row->device . '"'
                    . '},';
        }
        return "[" . substr($jsonReturn, 0, -1) . "]";
    }

}
