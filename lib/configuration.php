<?php

class Configuration {

    private $db_server = '127.0.0.1';
    //private $db_server = 'https://rpessoa.cloudant.com';
    private $db_port = '5984';
    private $db_database_admins = '_users';
    private $db_database_devices = 'devices';
    private $db_admin_user = 'admin';
    private $db_admin_password = 'admin';
   
    public function __get($property) {
        if (getenv($property)) {
            return getenv($property);
        } else {
            return $this->$property;
        }
    }

}
