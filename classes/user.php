<?php

class User extends Base {

    protected $name;
    protected $email;
    protected $full_name;
    protected $salt;
    protected $password_sha;
    protected $roles;
    protected $country;
    protected $mobile_phone;

    public function __construct() {
        parent::__construct('user');
    }

    public function signup($username, $password, $email) {
        $bones = new Bones();
        $bones->couch->setDatabase('_users');
        $bones->couch->login($bones->config->db_admin_user, $bones->config->db_admin_password);

        if (!$this->isValidEmail($email)) {
            $bones->set('error', 'A user with this email already exists.');
            $bones->set('ename', $this->full_name);
            $bones->set('email', $email);
            $bones->set('eusername', $username);

            $bones->render('user/signup');
            exit;
        }

        $this->roles = array();

        $this->name = preg_replace('/[^a-z0-9-]/', '', strtolower($username));
        $this->_id = 'org.couchdb.user:' . $this->name;
        $this->salt = $bones->couch->generateIDs(1)->body->uuids[0];
        $this->password_sha = sha1($password . $this->salt);

        try {
            $bones->couch->put($this->_id, $this->to_json());
        } catch (SagCouchException $e) {
            if ($e->getCode() == "409") {
                $bones->set('error', 'A user with this name already exists.');
                $bones->render('user/signup');
                exit;
            }
        }
        $this->creatDBForUser($username);
    }

    public function creatDBForUser($username) {
        $bones = new Bones();
        $bones->couch->setDatabase('_users');
        $bones->couch->login($bones->config->db_admin_user, $bones->config->db_admin_password);
        try {
            $bones->couch->createDatabase($this->name);
        } catch (SagCouchException $exc) {
            echo $exc->getTraceAsString();
            $bones->set('error', 'A user with this name already exists.');
            exit;
        }

        $bones->couch->setDatabase($username);
//create the views
        $doc_json = '{"_id": "_design/application",
   "language": "javascript",
   "views": {
       "getSafezones": {
           "map": "function(doc) {\n  if(doc.type == ' . "'" . safezone . "'" . ')  \n   emit(doc.device, doc);\n}",
           "reduce": "_count"
       },
       "getDevices": {
           "map": "function(doc) {\nif(doc.type == ' . "'" . device . "'" . ')\n  emit(doc._id, doc);\n}\n",
           "reduce": "_count"
       },
       "getSensors": {
           "map": "function(doc) {\nif(doc.sensors){\nfor(var i in doc.sensors)\n  emit(doc._id,doc.sensors[i]);\n}}",
           "reduce": "_count"
       },
       "getMonitoringSensor": {
           "map": "function(doc) {\nif(doc.type == ' . "'" . monitoring_sensor . "'" . '){ \n  emit([doc.mac_address,doc.subtype], doc);\n}\n}"
       }
   }
}';
        try {
            $bones->couch->post($doc_json);
        } catch (SagCouchException $exc) {
            echo $exc->getTraceAsString();
            $bones->set('error', 'Problem creating user');
        }

//Create a profile document         
//        $msjson = '{"_id": "profile","name": "'.$this->name.'","email": "'.$this->email.'","full_name": "'.$this->full_name.'","country": "'.$this->country.'","mobile_phone": "'.$this->mobile_phone.'","type":"profile"}';
//
//        try {
//            $bones->couch->post($msjson);
//        } catch (SagCouchException $exc) {
//            echo $exc->getTraceAsString();
//            $bones->set('error', 'Problem creating user');
//        }

        /* ONLY FOR TEST PROPOSE */
        $this->createFakeData($username);
    }

    public function login($password) {
        $bones = new Bones();
        $bones->couch->setDatabase('_users');

        try {
            $bones->couch->login($this->name, $password, Sag::$AUTH_COOKIE);
            session_start();
            $_SESSION['username'] = $bones->couch->getSession()->body->userCtx->name;
            session_write_close();
        } catch (sagCouchException $e) {
            if ($e->getCode() == "401") {
                $bones->set('error', 'Incorrect login credentials.');
                $bones->render('user/login');
                exit;
            }
        }
    }

    public function appLogin($username, $password) {
        $bones = new Bones();
        $bones->couch->setDatabase('_users');

        try {
            $bones->couch->login($username, $password, Sag::$AUTH_COOKIE);
            return $bones->couch->getSession()->body->userCtx->name;
        } catch (sagCouchException $e) {
            if ($e->getCode() == "401") {
                return -1;
            }
        }
    }

    public static function logout() {
        $bones = new Bones();
        $bones->couch->login(null, null);
        session_start();
        session_destroy();
    }

    public static function current_user() {
//session_start();
        return $_SESSION['username'];
        session_write_close();
    }

    public static function is_authenticated() {
        if (self::current_user()) {
            return true;
        } else {
            return false;
        }
    }

    public static function is_current_admin_authenticated() {
        if (self::current_user() == "admin") {
            return true;
        } else {
            return false;
        }
    }

//    public static function get_by_username($username = null) {
//        $bones = new Bones();
//        $bones->couch->setDatabase('_users');
//        $bones->couch->login($bones->config->db_admin_user, $bones->config->db_admin_password);
//        $user = new User();
//
//        try {
//            $document = $bones->couch->get('org.couchdb.user:' . $username)->body;
//            $user->_id = $document->_id;
//            $user->name = $document->name;
//            $user->email = $document->email;
//            $user->full_name = $document->full_name;
//            $user->mobile_phone = $document->mobile_phone;
//            $user->country = $document->country;
//            return $user;
//        } catch (SagCouchException $e) {
//            if ($e->getCode() == "404") {
//                $bones->error404();
//            } else {
//                $bones->error500();
//            }
//        }
//    }

//    public function updateUserProfile($user) {
//        $bones = new Bones();
//        $bones->couch->setDatabase('_users');
//        $bones->couch->login($bones->config->db_admin_user, $bones->config->db_admin_password);
//
//        $document = $bones->couch->get('org.couchdb.user:' . $user->name)->body;
//        $document->email = $user->email;
//        $document->full_name = $user->full_name;
//        $document->country = $user->country;
//        $document->mobile_phone = $user->mobile_phone;
//
//        try {
//            $bones->couch->put($document->_id, $document);
//        } catch (SagCouchException $exc) {
//            echo $exc->getTraceAsString();
//            return NULL;
//        }
//    }

    public function isValidEmail($email) {
        $bones = new Bones();
        $bones->couch->setDatabase('_users');
        $bones->couch->login($bones->config->db_admin_user, $bones->config->db_admin_password);

        try {
            $rows = $bones->couch->get('_design/application/_view/get_users_by_email?key="' . $email . '"')->body->rows;
        } catch (SagCouchException $e) {
            if ($e->getCode() == "401") {
                return FALSE;
            }
        }

        if ($rows) {
            return FALSE;
        } else {
            return TRUE;
        }
    }

    public function gravatar($size = '50') {
        return 'http://www.gravatar.com/avatar/?gravatar_id=' . md5(strtolower($this->email)) . '&size=' . $size;
    }

    /* this methods is to applivation */

    public function appRegister($name, $email, $username, $password) {
        $bones = new Bones();
        $bones->couch->setDatabase('_users');
        $bones->couch->login($bones->config->db_admin_user, $bones->config->db_admin_password);

        if (!$this->isValidEmail($email)) {
            return -1; //email already in use
        }

        $this->roles = array();
        $this->name = preg_replace('/[^a-z0-9-]/', '', strtolower($username));
        $this->_id = 'org.couchdb.user:' . $this->name;
        $this->salt = $bones->couch->generateIDs(1)->body->uuids[0];
        $this->password_sha = sha1($password . $this->salt);

        try {
            $bones->couch->put($this->_id, $this->to_json());
        } catch (SagCouchException $e) {
            if ($e->getCode() == "409") {
                return -2; //the username already exist
            }
        }
        $this->creatDBForUser($username);
    }

    /* this method is only to create data to user on DEBUG */

    public function createFakeData($username) {
        $bones = new Bones();
        $bones->couch->setDatabase($username);

        $devicejson = '{
   "_id": "az",
   "name_device": "az teste",
   "sensors": {
       "4": {
           "name_sensor": "Panic Button",
           "enable": true,
           "type": "panic_button"
       },
       "5": {
           "name_sensor": "Sensor GPS",
           "enable": true,
           "type": "GPS"
       },
       "6": {
           "min_temperature": "20",
           "max_temperature": "35",
           "name_sensor": "Sensor Temperature",
           "enable": true,
           "type": "temperature"
       },
       "7": {
           "critical_battery": "15",
           "low_battery": "25",
           "name_sensor": "Battery Level",
           "enable": true,
           "type": "battery"
       }
   },
   "timestamp": 1403214734,
   "owner": "rpessoa",
   "deleted": false,
   "monitoring": true,
   "type": "device"
}';
        //Safezone
        $msjson = '{"_id": "safezone_1411573598808","address": "Instituto Superior de Engenharia do Porto, Rua São Tomé, 4200-485 Porto, Portugal","name": "ISEP","latitude": 41.177863,"longitude": -8.608292,"radius": 500,"notification": "ALL","timestamp": 1411573596,"device": "az","type": "safezone"}';
        $bones->couch->post($msjson);
        //Battery
        $msjson = '{"_id": "ms_1411567684_az_battery","timestamp": "1411567684", "mac_address": "az","subtype": "battery","value": 5,"notification": "CRITICAL","seen": true,"type": "monitoring_sensor"}';
        $bones->couch->post($msjson);
        $msjson = '{"_id": "ms_1411574888_az_battery","timestamp": "1411574888","mac_address": "az","subtype": "battery","value": 15,"notification": "CRITICAL","seen": true,"type": "monitoring_sensor"}';
        $bones->couch->post($msjson);
        //GPS
        $msjson = '{"_id": "ms_1411567684_az_gps","timestamp": "1411567684","mac_address": "az","address": "Travessa Doutor Carlos Pires Felgueiras 47, 4470-157 Maia, Portugal","subtype": "GPS","notification": "CHECK-OUT","longitude": "-8.624164","latitude": "41.23206","seen": true,"type": "monitoring_sensor"}';
        $bones->couch->post($msjson);
        $msjson = '{"_id": "ms_1411635283_az_gps","timestamp": "1411574888","mac_address": "az","address": "Rua Doutor António Bernardino de Almeida 431, 4200-072 Porto, Portugal","subtype": "GPS","notification": "CHECK-IN","longitude": "-8.606329","latitude": "41.178501","seen": true,"type": "monitoring_sensor"}';
        $bones->couch->post($msjson);
        //Temperature
        $msjson = '{"_id": "ms_1411567684_az_temperature","timestamp": "1411567684","mac_address": "az","subtype": "temperature","value": 24,"notification": "RANGE","seen": true,"type": "monitoring_sensor"}';
        $bones->couch->post($msjson);
        $msjson = '{"_id": "ms_1411497762_az_temperature","timestamp": 1411574888,"mac_address": "az","subtype": "temperature","value": 1,"notification": "LOW","seen": true,"type": "monitoring_sensor"}';
        $bones->couch->post($msjson);
        //PanicButton
        $msjson = '{"_id": "ms_1411497620_az_panic_button","timestamp": 1411567684,"mac_address": "az","pressed": true,"subtype": "panic_button","value": null,"seen": true,"type": "monitoring_sensor"}';
        $bones->couch->post($msjson);
        try {
            $bones->couch->post($devicejson);
        } catch (SagCouchException $exc) {
            echo $exc->getTraceAsString();
            $bones->set('error', 'Problem creating user');
        }
    }

}
