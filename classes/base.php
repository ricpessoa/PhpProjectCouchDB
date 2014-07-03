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

    public static function insertOrUpdateObjectInDB($db, $object, $onJson) /* need throw Exception */ {
        $bones = new Bones();
        $bones->couch->setDatabase($db);
        try {
            if ($onJson) {
                $bones->couch->put($object->_id, $object);
            } else {
                $bones->couch->put($object->_id, $object->to_json());
            }
        } catch (SagException $exc) {
            echo $exc->getTraceAsString();
            return FALSE;
        }
        return TRUE;
    }

    public static function getViewToIterateBasedInUrl($db, $urlOfRequest) {
        $bones = new Bones();
        $bones->couch->setDatabase($db);

        return $bones->couch->get($urlOfRequest)->body->rows;
    }

    public static function getViewReduceCountBasedInUrl($db, $urlOfRequest) {
        $bones = new Bones();
        $bones->couch->setDatabase($db);

        $rows = $bones->couch->get($urlOfRequest)->body->rows;
        if ($rows) {
            return $rows[0]->value;
        } else {
            return 0;
        }
    }

    public static function deleteDocument($db, $id, $rev) {
        $bones = new Bones();
        $bones->couch->setDatabase($db);
        try {
            $bones->couch->delete($id, $rev);
        } catch (SagCouchException $e) {
            $bones->error500($e);
            return FALSE;
        }
        return TRUE;
    }

}
