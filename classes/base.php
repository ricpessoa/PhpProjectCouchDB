<?php

abstract class Base {

    protected $_id;
    protected $_rev;
    protected $type;

    public function __construct($type) {
        $this->type = $type;
    }

    public function __get($property) {
        return $this->$property;
    }

    public function __set($property, $value) {
        $this->$property = $value;
    }

    public function to_json() {
        if (isset($this->_rev) === false) {
            unset($this->_rev);
        }
        return json_encode(get_object_vars($this));
    }
    
//TO DELETE THE OBJECTS LIKE DEVICE SAFEZONE CAN BE DELETED BY FATHER CLASS
    public function delete($username) {
        $bones = new Bones();
        $bones->couch->setDatabase($username);

        try {
            $bones->couch->delete($this->_id, $this->_rev);
        } catch (SagCouchException $e) {
            $bones->error500($e);
        }
    }

}
