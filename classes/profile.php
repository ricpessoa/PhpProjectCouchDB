<?php

class Profile extends Base {

    protected $name;
    protected $email;
    protected $full_name;
    protected $country;
    protected $mobile_phone;

    public function __construct() {
        parent::__construct('profile');
    }

    public static function createProfile($usernameDB, $name, $email, $full_name, $country, $mobile_phone) {
        $profile = new Profile();
        //$monitoringSensorTemperature->_id = $macaddress . "_ms_pb_" . $timestamsOfDevice;
        $profile->_id = "profile";
        $profile->name = $name;
        $profile->email = $email;
        $profile->full_name = $full_name;
        $profile->country = $country;
        $profile->mobile_phone = $mobile_phone;

        try {
            Base::insertOrUpdateObjectInDB($usernameDB, $profile, FALSE);
        } catch (SagCouchException $e) {
            return "some error creating profile";
        }
    }

    public static function getProfileByUsername($username) {
        $bones = new Bones();
        $bones->couch->setDatabase($username);
        $_profile = $bones->couch->get("profile")->body;
        $profile = new Profile();
        if ($_profile) {
            $profile->name = $_profile->name;
            $profile->email = $_profile->email;
            $profile->full_name = $_profile->full_name;
            $profile->country = $_profile->country;
            $profile->mobile_phone = $_profile->mobile_phone;
            return $profile;
        }
        return NULL;
    }

    public function updateUserProfile($profile) {
        $bones = new Bones();
        $bones->couch->setDatabase($profile->name);

        $document = $bones->couch->get('profile')->body;
        $document->email = $profile->email;
        $document->full_name = $profile->full_name;
        $document->country = $profile->country;
        $document->mobile_phone = $profile->mobile_phone;

        try {
            $bones->couch->put($document->_id, $document);
        } catch (SagCouchException $exc) {
            echo $exc->getTraceAsString();
            return NULL;
        }
    }
    
    public function gravatar($size = '50') {
        return 'http://www.gravatar.com/avatar/?gravatar_id=' . md5(strtolower($this->email)) . '&size=' . $size;
    }

}
