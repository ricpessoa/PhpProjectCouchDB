<?php

include 'lib/bones.php';
get('/', function($app) {
    if (User::is_authenticated()) {
        $devices = Device::getDevices(User::current_user());
        $app->set('devices', $devices);
        if (User::is_current_admin_authenticated()) {
            $app->set('userpermission', "admin");
            $app->redirect('/admin/manager_dashboard');
            return;
        }
    }
    $app->render('home');
});


get('/signup', function($app) {
    $app->render('user/signup');
});

post('/signup', function($app) {
    //parse_str(Bones::Unencrypter($app->form('jCryption')), $output); change output to app

    $user = new User();
    //$user->full_name = $app['full_name'];
    //$user->email = $app['email'];
    //$user->signup($app['username'], $app['password'], $app['email']);
    $user->full_name = $app->form('full_name');
    $user->email = $app->form('email');
    $user->signup($app->form('username'), $app->form('password'), $app->form('email'));
    Profile::createProfile($app->form('username'), $app->form('email'), $app->form('full_name'), "", "");


    $app->set('success', 'Thanks for Signing Up ' . $user->full_name . '!');
    $app->render('home');
});

get('/login', function($app) {
    $app->render('user/login');
});

post('/login', function($app) {
    //echo 'crypted: ' . $app->form('jCryption');
    //parse_str(Bones::Unencrypter($app->form('jCryption')), $output);change output to app
    $user = new User();
    //$user->name = $output['username'];
    //$user->login($output['password']);
    $user->name = $app->form('username');
    $user->login($app->form('password'));
    if (User::is_authenticated() && !User::is_current_admin_authenticated()) {
        $devices = Device::getDevices(User::current_user());
        $app->set('devices', $devices);
    }
    if (User::is_current_admin_authenticated()) {
        $app->redirect('/admin/manager_dashboard');
        return;
    }
    $app->render('home');
});

get('/logout', function($app) {
    User::logout();
    $app->redirect('/');
});

get('/user/', function($app) {
    $app->redirect('/');
});

get('/user/:username', function($app) {
    if ($app->request('username') == User::current_user()) {
        $app->set('user', User::get_by_username($app->request('username')));
        $app->set('numberDevices',  Device::getNumberOfDevicesOfUser($app->request('username')));
        $app->set('is_current_user', ($app->request('username') == User::current_user() ? true : false));
        $app->render('user/profile');
    } else {
//$app->redirect('/user/' . User::current_user());
        $app->redirect('/');
    }
});

post('/edituser', function($app) {
    if (User::is_authenticated()) {
        $user = new User();
        $user->name = User::current_user();
        $user->email = $app->form('email');
        $user->full_name = $app->form('full_name');
        $user->country = $app->form('country');
        $user->mobile_phone = $app->form('mobile_phone');
        User::updateUserProfile($user);
        $app->redirect('/user/' . User::current_user());
    } else {
        $app->redirect('/user/login');
    }
});

/* --------------- DEVICES --------------- */

get('devices/showdevices', function($app) {
    if (User::is_authenticated()) {
        $devicesUser = Device::getDevices(User::current_user());
        $app->set('devices', $devicesUser);
        $app->render('/devices/showdevices');
    } else {
        $app->redirect('/user/login');
    }
});

get('/devices/newdevice', function($app) {
    if (User::is_authenticated()) {
        $app->render('/devices/newdevice');
    } else {
        $app->redirect('/user/login');
    }
});

get('/devices/newdevice/:device', function($app) {
    $deviceID = $app->request('device');
    if (User::is_authenticated()) {
        if ($deviceID != "") {
            $device = Device::getDevice(User::current_user(), $deviceID);
            if ($device != NULL) {
                $app->set('editDevice', true);
                $app->set('deviceMacAddress', $device->_id);
                $app->set('deviceName', $device->name_device);
            }
        }
        $app->render('/devices/newdevice');
    } else {
        $app->redirect('/user/login');
    }
});

/* Create new device The user DON'T create device
 * but copy the document of device from devicesDB to your db
 *  */
post('/devices/newdevice', function($app) {
    if (User::is_authenticated()) {
        $mac_device = $app->form('mac_address');
        $name_device = $app->form('name_device');
        $isToEditDevice = $app->form('isEditDevice');
        $result = Device::insertOrEditDevice(User::current_user(), $mac_device, $name_device, $isToEditDevice);
        if ($result == TRUE) {
            $app->redirect('/devices/showdevices');
        } else {
            if ($isToEditDevice == "1") {
                $app->set('deviceMacAddress', $mac_device);
                $app->set('deviceName', $name_device);
            }
            $app->set('error', "Device not Founded");
            $app->render('/devices/newdevice');
        }
    } else {
        $app->redirect('/user/login');
    }
});
/*
 * delete device Method POST and receive:
 * _id (mac address) of device
 * _rev of document of device  */
post('/deletedevice/:id/:rev', function($app) {
    if (User::is_authenticated()) {
        $mac_device = $app->request('id');
        $device = Device::getDevice(User::current_user(), $mac_device);
        if ($device != NULL) {
            $device->deleted = true;
            Base::insertOrUpdateObjectInDB(User::current_user(), $device, FALSE);
        }
        $app->set('success', 'Delete Device successfull');
        $app->redirect('/devices/showdevices');
    } else {
        $app->redirect('/user/login');
    }
});

get('/devices/monitoringdevice/:device', function($app) {
    $deviceID = $app->request('device');
    if (User::is_authenticated()) {
        if ($deviceID != "") {
            $device = Device::getDevice(User::current_user(), $deviceID);
            if ($device != NULL) {
                $app->set('deviceMacAddress', $device->_id);
                $app->set('userName', User::current_user());
            }
        }
        $app->render('/devices/monitoringdevice');
    } else {
        $app->redirect('/user/login');
    }
});

//get('/devices/client', function($app) {
//    $deviceID = $app->request('device');
//    if (User::is_authenticated()) {
//
//        $app->render('/devices/client');
//    } else {
//        $app->redirect('/user/login');
//    }
//});

/* END DEVICE */

/* ---------------START SENSOR --------------- */
/*
 * This method is to update the sensor temperature and receive the next information */

post('/configsensortemperature/:id/:rev', function($app) {
    if (User::is_authenticated()) {
        Temperature::updateTemperature(User::current_user(), $app->request('id'), $app->request('rev'), $app->form('max_temp_notification'), $app->form('min_temp_notification'));
        //$app->set('success', 'Yes receive the id' . $app->request('id') . " and rev" . $app->request('rev') . " - max " . $app->form('max_temp_notification') . " min " . $app->form('min_temp_notification') . "<br>");
        $app->redirect('/devices/showdevices');
    } else {
        $app->redirect('/user/login');
    }
});

post('/configsensorbattery/:id/:rev', function($app) {
    if (User::is_authenticated()) {
        Battery::updateBattery(User::current_user(), $app->request('id'), $app->request('rev'), $app->form('low_battery_notification'), $app->form('critical_battery_notification'));
        $app->redirect('/devices/showdevices');
    } else {
        $app->redirect('/user/login');
    }
});

/* THIS METHOD CAN BE REFACTURE TO OPTIMEZE */
get('/sensors/editsensor/:device/:sensor', function($app) {
    $deviceID = $app->request('device');
    $sensorType = $app->request('sensor');

    if (User::is_authenticated()) {
        if (Device::deviceExist(User::current_user(), $deviceID)) {
            $arraySensors = Sensor::getSensorByType(User::current_user(), $deviceID, $sensorType);
            $app->set('arraySensors', $arraySensors); //need get the safezones of device

            $device = new Device();
            $device->_id = $deviceID;
            $device->_rev = Device::getDeviceRevisionByID(User::current_user(), $deviceID);

            $safezones = Safezone::getSafezonesByUserAndDevice(User::current_user(), $device->_id);

            $app->set('deviceID', $device->_id);
            $app->set('deviceREV', $device->_rev);
            $app->set('numberSafezones', sizeof($safezones)); //need get the safezones of device
            $app->set('jsonSafezones', Safezone::getArrayOfSafezonesToJson($safezones)); //need get the safzones objects

            $app->render('/sensors/editsensor');
        } else {
            $app->render('error/404');
        }
    } else {
        $app->redirect('/user/login');
    }
});

/* this method is to enable sensor of device and show the notifications or not */
post('/sensor/setsensorenable/:id/:sensortype/:enable', function($app) {
    if (User::is_authenticated()) {
        $deviceID = $app->request('id');
        $sensorType = $app->request('sensortype');
        $enable = ($app->request('enable') == 1 ? TRUE : FALSE); // returns true if enable == 1
        Sensor::setEnableOfSensor(User::current_user(), $deviceID, $sensorType, $enable);
    } else {
        $app->redirect('/user/login');
    }
});
/* END SENSOR */

/* --------------- SAFEZONE --------------- */

//add safezone method 
post('/safezone', function($app) {
    if (User::is_authenticated()) {
        $safezone_data = $app->form('safezone');
        $json_safezone = json_decode($safezone_data, TRUE);

        $safezone = new Safezone();
        $safezone->_id = $json_safezone["_id"];
        $safezone->_rev = $json_safezone["_rev"];
        $safezone->address = $json_safezone["address"];
        $safezone->name = $json_safezone["name"];
        $safezone->latitude = $json_safezone["latitude"];
        $safezone->longitude = $json_safezone["longitude"];
        $safezone->radius = $json_safezone["radius"];
        $safezone->notification = $json_safezone["notification"];
        $safezone->device = $json_safezone["device"];
        $safezone->create();

        $numSafezones = Safezone::get_safezones_count_by_user(User::current_user());
        $app->set('numberSafezones', $numSafezones);
        if ($numSafezones != 0) {
            $app->set('safezones', Safezone::get_safezones_by_user(User::current_user()));
        }
        $app->redirect('/sensors/editsensor/' . $safezone->device . '/GPS');
    } else {
        $app->redirect('/user/login');
    }
});

//the page to insert new safezone or edit
post('/safezone/newsafezone', function($app) {
    if (User::is_authenticated()) {
        $macAddress = $app->form('create_safezone');
        $editDevice = $app->form('edit_safezone');
        $safezone = NULL;
        if ($editDevice != "true") {
            $editDevice = "false";
        } else {
            $_idsafezone = $app->form('id_safezone_to_edit');
            $safezone = Safezone::getSafezoneByID(User::current_user(), $_idsafezone);
            $app->set("eSafezone", $_idsafezone);
            $app->set("editSafezone", Safezone::to_jsonString($safezone));
        }

        $app->set("macAddressOfDevice", $macAddress);
        $app->set("editDevice", $editDevice);

        $app->render('/safezone/newsafezone');
    } else {
        $app->redirect('/user/login');
    }
});

post('/deletesafezone/:id/:rev/:device', function($app) {
    if (User::is_authenticated()) {
        Base::deleteDocument(User::current_user(), $app->request('id'), $app->request('rev'));

        $app->set('success', 'The safezone was deleted');
        //$app->redirect('/safezone/showsafezones');
        $app->redirect('/sensors/editsensor/' . $app->request('device') . '/GPS');
    } else {
        $app->redirect('/user/login');
    }
});
/* END SAFEZONE */

/* METHODS TO MOBILE APPLICATIONS */

post('/applogin', function($app) {
    if (isset($_POST["username"]) && isset($_POST["password"])) {
        $username = $_POST["username"];
        $password = $_POST["password"];
        $response = array();


        $user = new User();
        $respLogin = $user->appLogin($username, $password);

        if ($username == $respLogin) {
            $response['error'] = false;
            $response['code'] = 1;
            $response['message'] = "Login Successfull";
        } else {
            $response['error'] = true;
            $response['ceode'] = -1; //'Incorrect login credentials.'
            $response['message'] = "An error occurred. Please try again";
        }
        echo json_encode($response);
    } else {
        echo "Error";
    }
});
/*
 *  */
post('/appregister', function($app) {
    if (isset($_POST["name"]) && isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["password"])) {
        $name = $_POST["name"];
        $email = $_POST["email"];
        $username = $_POST["username"];
        $password = $_POST["password"];
        $response = array();

        $user = new User();
        $user->full_name = $name;
        $user->email = $email;
        $respRegister = $user->appRegister($name, $email, $username, $password);

        if ($respRegister == -1) {
            $response['error'] = true;
            $response['code'] = -1;
            $response['message'] = "This email already exists";
        } else if ($respRegister == -2) {
            $response['error'] = true;
            $response['code'] = -2;
            $response['message'] = "A user with this username already exists";
        } else {
            $response['error'] = false;
            $response['code'] = 1;
            $response['message'] = "Register successfull";
        }
        echo json_encode($response);
    } else {
        echo "Error";
    }
});

post('/appAddNewDevice', function($app) {

    if (isset($_POST["username"]) && isset($_POST["mac_address"])) {
        $userDB = $_POST["username"];
        $mac_device = $_POST["mac_address"];
        $name_device = $_POST["name_device"];

        $result = Device::insertOrEditDevice($userDB, $mac_device, $name_device, "0");
        if ($result == TRUE) {
            $response['error'] = false;
            $response['message'] = "Add device" . $mac_device . " with name " . $name_device . " in " . $userDB . " DB ";
            $response['code'] = 1;
        } else {
            $response['error'] = true;
            $response['message'] = "Device not found " . $mac_device . " " . $userDB;
            $response['code'] = -1;
        }
    } else {
        $response['error'] = true;
        $response['message'] = "No receive information ";
        $response['code'] = -3;
    }
    echo json_encode($response);
});

/* To teste monitoring of device */
post('/devicepost', function($app) {
    //m=ashoez&s=1&p=1&b=98&la=41.411&ln=-8.510&t=1
//m - mac address
//la - latitude
//ln - longitude
//t - temperature
//p - press button
//b - battery
//s - shoe status
//ts - timestamp
    /* from device */
    $macaddress = $_POST["m"];
    /* from gps */
    $latfrom = $_POST["la"];
    $lonfrom = $_POST["ln"];
    /* from temperature */
    $temperature = $_POST["t"];
    /* from panic button */
    $pressed = $_POST["p"];
    /* from battery */
    $battery = $_POST["b"];
    /* from shoes*/
    $shoe = $_POST["s"];
    
    $timestamsOfDevice = $_POST["ts"];
    $str = "";

    if ($timestamsOfDevice == NULL) {
        $str.= "TIMESTAMP SERVER ||";
        $timestamsOfDevice = time(); //if empty give timestamp of server
    }
    $response = array();
    $usernamedb = Device::findUserOfDevice($macaddress);

    if ($usernamedb == NULL) {
        /* test only for when user add new device */
//$devices = User::registeDeviceInUser("rpessoa", $macaddress, FALSE);
        $response['error'] = true;
        $response['message'] = "Device not found to user: " . $macaddress . " - " . $devices;
    } else {
        /* test only for when user delete device */
//$devices = User::registeDeviceInUser("rpessoa", $macaddress, TRUE);
        if ($latfrom != NULL && $lonfrom != NULL) {
            $str.= MSGPS::calcIfCheckInOrCheckOut($usernamedb, $macaddress, $latfrom, $lonfrom, $timestamsOfDevice);
        } else {
            $str.="|| _GPS null";
        }
        if ($temperature != NULL) {
            $str.= MSTemperature::calcIfLowOrRangeOrHighTemperature($usernamedb, $macaddress, $temperature, $timestamsOfDevice);
            // $str.= MSTemperature::saveMonitoringSensorTemperature($usernamedb, $macaddress, $temperature);
        }
        if ($battery != NULL) {
            $str.= MSBattery::calcIfCriticalLowOrRangeBatteryLevel($usernamedb, $macaddress, $battery, $timestamsOfDevice);
            //$str.="|| _Temperature null";
        }

        if ($pressed != NULL) {
            $boolPressed = $pressed === '1' ? true : false;
            if ($boolPressed) {
                $str.= MSPanicButton::saveMonitoringSensorPanicButton($usernamedb, $macaddress, $boolPressed, $timestamsOfDevice);
            }
        } else {
            $str.="|| _Panic Button null or false";
        }
        
        if($shoe !=NULL){
        $boolRemoved = $shoe === '1' ? true : false;
            if ($boolRemoved) {
                $str.= MSShoe::saveMonitoringSensorShoe($usernamedb, $macaddress, $boolRemoved, $timestamsOfDevice);
            }
        } else {
            $str.="|| _Shoe null or false";
        }

        /* send notification to user */

        require_once 'notification_server/client/lib/class.websocket_client.php';
        $client = new WebsocketClient;
        $client->connect('192.168.50.94', 8000, '/monitoring_devices', 'foo.lh');
        usleep(500);

        $jsonReturn = '{'
                . '"action":' . '"echo",'
                . '"data":' . '[{"username":"' . $usernamedb . '","mac_address":"' . $macaddress . '",'
                . '"lat":"' . $latfrom . '","log":"' . $lonfrom . '","tmp":"' . $temperature . '",'
                . '"bat":"' . $battery . '","press":"' . $pressed . '","remov":"' . $shoe . '","time":"' . date("H:i:s d/m/Y ") . '"}]'
                . '}';
        //"time":"' . date("d-m H:i:s") .
        $client->sendData($jsonReturn);
        usleep(500);

        $response['error'] = false;
        $response['message'] = $usernamedb . " found " . $str;
//echo ''.$usernamedb;
    }
    echo json_encode($response);
});


/* ---------- ADMIN  ---------- */
/* Create new device  */

post('/manager_device', function($app) {
    if (User::is_authenticated() && User::is_current_admin_authenticated()) {

        Device::createDeviceFromManagerDevice($app);
        //$app->set('success', 'Yes device saved');
        $app->redirect('/admin/manager_showdevices');
    } else {
        $app->redirect('/user/login');
    }
});

get('/admin/manager_dashboard', function($app) {
    if (User::is_authenticated() && User::is_current_admin_authenticated()) {
        $app->set("numbAvailableDevices", Device::getNumberOfAvailableDevices());
        $app->set("numbAllDevices", Device::getNumberOfDevicesInDBDevices());
        $app->render('/admin/manager_dashboard');
    } else {
        $app->redirect('/user/login');
    }
});

get('/admin/manager_showdevices', function($app) {
    if (User::is_authenticated() && User::is_current_admin_authenticated()) {
        $app->set('devices', Device::getAllDevicesInDBDevices());
        $app->render('/admin/manager_showdevices');
    } else {
        $app->redirect('/user/login');
    }
});

get('/admin/manager_device', function($app) {
    if (User::is_authenticated() && User::is_current_admin_authenticated()) {
        $app->render('/admin/manager_device');
    } else {
        $app->redirect('/user/login');
    }
});

get('/admin/manager_device/:id', function($app) {
    if (User::is_authenticated() && User::is_current_admin_authenticated()) {
        $id = $app->request('id');
        if ($id != "") {
            $deviceToEdit = Device::findTheDeviceOnDevicesDB($id);
            $app->set('deviceToEdit', Device::findTheDeviceOnDevicesDB($id));
        }
        $app->render('/admin/manager_device');
    } else {
        $app->redirect('/user/login');
    }
});

post('/admin/deletedevice/:id/:rev', function($app) {
    if (User::is_authenticated() && User::is_current_admin_authenticated()) {
        $bones = new Bones();
        Base::deleteDocument($bones->config->db_database_devices, $app->request('id'), $app->request('  rev'));
        $app->redirect('/admin/manager_showdevices');
    } else {
        $app->redirect('/user/login');
    }
});

resolve(); //if the route not exist page not found
