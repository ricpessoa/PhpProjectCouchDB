<?php

class Device extends Base {

    protected $name_device;
    protected $sensors;
    protected $timestamp;

    public function __construct() {
        parent::__construct('device');
    }

    public function create() { /*     * Need test the creation of Device */
        $bones = new Bones();
        $bones->couch->setDatabase($_SESSION['username']);

        //$this->_id = $bones->couch->generateIDs(1)->body->uuids[0];

        $this->timestamp = time();

        try {
            $bones->couch->put($this->_id, $this->to_json());
        } catch (SagCouchException $e) {
            $bones->error500($e);
        }
    }

   
}
