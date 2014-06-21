<?php

class Safezone extends Base {

    protected $address;
    protected $name;
    protected $latitude;
    protected $longitude;
    protected $radius;
    protected $notification; //[CHECK-IN CHECK-OUT OR BOTH]
    protected $timestamp;
    protected $device;

    public function __construct() {
        parent::__construct('safezone');
    }
    
    public function create() { /*     * Need test the creation of Safezone */
        $bones = new Bones();

        $this->timestamp = time();

        try {
            Base::insertOrUpdateObjectInDB(User::current_user(), $this, FALSE);
        } catch (SagCouchException $e) {
            $bones->error500($e);
            $bones->set('error', 'Error try save the safezone');
            $bones->render('/safezone/showsafezones');
            exit;
        }
    }

    public function get_safezones_by_user($usernameDB) {
        $safezones = array();

        foreach (Base::getViewToIterateBasedInUrl($usernameDB, '_design/application/_view/getSafezones?reduce=false') as $_safezone) {
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

    public function getSafezonesByUserAndDevice($usernameDB, $mac_address) {
        $safezones = array();
        foreach (Base::getViewToIterateBasedInUrl($usernameDB, '_design/application/_view/getSafezones?key="' . $mac_address . '"&reduce=false') as $_safezone) {
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

    public function getSafezoneByID($username, $_idsafezone) {
        $bones = new Bones();
        $bones->couch->setDatabase($username);
        $_safezone = $bones->couch->get($_idsafezone)->body;
        $safezone = new Safezone();
        if ($_safezone) {
            $safezone->_id = $_safezone->_id;
            $safezone->_rev = $_safezone->_rev;
            $safezone->address = $_safezone->address;
            $safezone->name = $_safezone->name;
            $safezone->latitude = $_safezone->latitude;
            $safezone->longitude = $_safezone->longitude;
            $safezone->radius = $_safezone->radius;
            $safezone->notification = $_safezone->notification;
            $safezone->timestamp = $_safezone->timestamp;
            $safezone->device = $_safezone->device;
            return $safezone;
        }
        return $_idsafezone . " doc? " . $doc->_id;
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

    public function to_jsonString($safezone) {
        $jsonReturnOne = '{'
                . '"_id":' . '"' . $safezone->_id . '",'
                . '"_rev":' . '"' . $safezone->_rev . '",'
                . '"address":' . '"' . $safezone->address . '",'
                . '"name":' . '"' . $safezone->name . '",'
                . '"latitude":' . '"' . $safezone->latitude . '",'
                . '"longitude":' . '"' . $safezone->longitude . '",'
                . '"radius":' . '"' . $safezone->radius . '",'
                . '"notification":' . '"' . $safezone->notification . '",'
                . '"timestamp":' . '"' . $safezone->timestamp . '",'
                . '"device":' . '"' . $safezone->device . '"'
                . '}';
        return "[" . $jsonReturnOne . "]";
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
