<?php

class User extends Base {

    protected $name;
    protected $email;
    protected $full_name;
    protected $salt;
    protected $password_sha;
    protected $roles;

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
            //$bones->couch->send("PUT", "/".$this->name); 
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
        $doc_json = '{
   "_id": "_design/application",
   "language": "javascript",
   "views": {
       "getSafezones": {
           "map": "function(doc) {\n  if(doc.type == ' . "'" . safezone . "'" . ')  \n   emit(doc.type, doc);\n}",
           "reduce": "_count"
       },
       "getDevices": {
           "map": "function(doc) {\nif(doc.type == ' . "'" . device . "'" . ')\n  emit(doc.type, doc);\n}\n",
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

    public static function get_by_username($username = null) {
        $bones = new Bones();
        $bones->couch->setDatabase('_users');
        $bones->couch->login($bones->config->db_admin_user, $bones->config->db_admin_password);
        $user = new User();

        try {
            $document = $bones->couch->get('org.couchdb.user:' . $username)->body;
            $user->_id = $document->_id;
            $user->name = $document->name;
            $user->email = $document->email;
            $user->full_name = $document->full_name;

            return $user;
        } catch (SagCouchException $e) {
            if ($e->getCode() == "404") {
                $bones->error404();
            } else {
                $bones->error500();
            }
        }
    }

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

    public function createFakeData($username) {
        $bones = new Bones();
        $bones->couch->setDatabase($username);

        $devicejson = '{
   "_id": "az",
   "name_device": "Fake Device",
   "sensors": {
       "3": {
           "name_sensor": "Panic Button",
           "type": "panic_button"
       },
       "4": {
           "name_sensor": "Sensor GPS",
           "type": "GPS"
       },
       "5": {
           "min_temperature": "23",
           "max_temperatrue": "27",
           "name_sensor": "Sensor Temperature",
           "type": "temperature"
       }
   },
   "timestamp": 1397147978,
   "type": "device"
}';
        $msjson = '{"_id": "az_1396963000", "type": "monitoring_sensor", "subtype": "temperature", "value": 29, "timestamp": "1396963000", "mac_address": "az"}';
        $bones->couch->post($msjson);
        $msjson = '{"_id": "az_1396960000",  "type": "monitoring_sensor", "subtype": "temperature", "value": 27, "timestamp": "1396960000", "mac_address": "az"}';
        $bones->couch->post($msjson);
        $msjson = '{"_id": "az_1396959100",  "type": "monitoring_sensor", "subtype": "temperature", "value": 26, "timestamp": "1396959100", "mac_address": "az"}';
        $bones->couch->post($msjson);
        $msjson = '{"_id": "az_1396959000",  "type": "monitoring_sensor", "subtype": "temperature", "value": 25, "timestamp": "1396959000", "mac_address": "az"}';
        $bones->couch->post($msjson);
        $msjson = '{"_id": "az_1396963001",  "type": "monitoring_sensor", "subtype": "panic_button", "pressed": true, "timestamp": "1396963000", "mac_address": "az"}';
        $bones->couch->post($msjson);
        $msjson = '{"_id": "az_1396963005", "type": "monitoring_sensor", "subtype": "GPS", "latitude": 41.411981, "longitude": -8.509985, "timestamp": "1396963000", "mac_address": "az"}';
        $bones->couch->post($msjson);
        $msjson = '{"_id": "az_1396963004", "type": "monitoring_sensor", "subtype": "GPS", "latitude": 41.106466, "longitude": -8.626827, "timestamp": "1396963000", "mac_address": "az"}';
        $bones->couch->post($msjson);
        $msjson = '{"_id": "az_1396963003", "type": "monitoring_sensor", "subtype": "GPS", "latitude": 41.11082, "longitude": -8.629301, "timestamp": "1396963000", "mac_address": "az"}';
        $bones->couch->post($msjson);
        $msjson = '{"_id": "az_1396963002", "type": "monitoring_sensor", "subtype": "GPS", "latitude": 41.112544, "longitude": -8.629665, "timestamp": "1396963000", "mac_address": "az"}';
        $bones->couch->post($msjson);
        try {
            $bones->couch->post($devicejson);
        } catch (SagCouchException $exc) {
            echo $exc->getTraceAsString();
            $bones->set('error', 'Problem creating user');
        }
    }

}
