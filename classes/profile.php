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

}
