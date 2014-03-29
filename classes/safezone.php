<?php

class Safezone extends Base {

    protected $address;
    protected $name;
    protected $latitude;
    protected $longitude;
    protected $radius;
    protected $notification;
    protected $timestamp;

    public function __construct() {
        parent::__construct('safezone');
    }

    public function create() { /*     * Need test the creation of Safezone */
        $bones = new Bones();
        $bones->couch->setDatabase($_SESSION['username']);

        $this->_id = $bones->couch->generateIDs(1)->body->uuids[0];

        $this->timestamp = time();

        try {
            $bones->couch->put($this->_id, $this->to_json());
        } catch (SagCouchException $e) {
            $bones->error500($e);
        }
    }

    public function get_safezones_by_user($username) {
        $bones = new Bones();
        $bones->couch->setDatabase($username);

        $safezones = array();

        foreach ($bones->couch->get('_design/application/_view/getSafezones')->body->rows as $_safezone) {
            $safezone = new Safezone();
            $safezone->_id = $_safezone->id;
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

    public function to_jsonString() {
        /* {_id: "safezone_02ecaf49b81af1a242b668984a001e64", _rev: "3-e299c8e0c31ae0d21c47ca60f5a8002f", address: "Rua Santa Maria Madalena", latitude: 41.112841, longitude: -8.629528, radius: 500, type: "safezone", notification: "[in-out]", name: "Rua Santa Maria Madalena"} */
        return '{'
        .'"_id":'.'"'.$this->_id.'",'
        .'"_rev":'.'"'.$this->_rev.'",'
        .'"address":'. '"'.$this->address.'",'
        .'"name":'.'"'.$this->name.'",'
        .'"latitude":'.'"'. $this->latitude.'",'
        .'"longitude":'.'"'.$this->longitude.'",'
        .'"radius":'.'"'.$this->radius.'",'
        .'"notification":'.'"'.$this->notification.'",'
        .'"timestamp":'.'"'. $this->timestamp.'"'
        .'},';
    }

}
