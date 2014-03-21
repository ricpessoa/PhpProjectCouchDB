<?php

/* 
 * base class from which all other classes will inherit properties
 *$type which will store the classification of the document such as user 
 *function to_json that uses get_object_vars, along with json_encode, to represent our object in a JSON string.

 */

abstract class Base
{
    protected $type;
    
    public function __construct($type)
    {
        $this->type = $type;
        
    }
    
    public function __get($property) {
        return $this->$property;
    }
    
    public function __set($property, $value) {
        $this->$property = $value;
    }
    public function to_json() {
        return json_encode(get_object_vars($this));       
    }

}
