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
    $app->set('message', 'Welcome Back!');
    $app->render('home');
});


get('/signup', function($app) {
    $app->render('user/signup');
});

post('/signup', function($app) {
    $user = new User();
    $user->full_name = $app->form('full_name');
    $user->email = $app->form('email');
    $user->signup($app->form('username'), $app->form('password'), $app->form('email'));

    $app->set('success', 'Thanks for Signing Up ' . $user->full_name . '!');
    $app->render('home');
});

get('/login', function($app) {
    $app->render('user/login');
});

post('/login', function($app) {
    $user = new User();
    $user->name = $app->form('username');
    $user->login($app->form('password'));

    $app->set('success', 'You are now logged in!');
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
//$app->set('success', 'User:' . $user->name . '  received to update ' . $user->country . " - " . $user->mobile_phone);
        $app->redirect('/user/' . User::current_user());
    } else {
        $app->set('error', 'You must be logged in to do that.');
        $app->render('user/login');
    }
});

delete('/post/delete/:id/:rev', function($app) {
    if (User::is_authenticated()) {
        $post = new Post();
        $post->_id = $app->request('id');
        $post->_rev = $app->request('rev');
        $post->delete(User::current_user());
    }
});

/* --------------- DEVICES --------------- */

get('devices/showdevices', function($app) {
    if (User::is_authenticated()) {
        $numberOfDevices = Device::getNumberOfDevices(User::current_user());
        $app->set('numberDevices', $numberOfDevices);

        if ($numberOfDevices != 0) {
            $app->set('devices', Device::getDevices(User::current_user()));
        }
        $app->render('/devices/showdevices');
    } else {
        $app->set('error', 'You must be logged in to do that.');
        $app->render('user/login');
    }
});

get('/devices/newdevice', function($app) {
    if (User::is_authenticated()) {
        $app->render('/devices/newdevice');
    } else {
        $app->set('error', 'You must be logged in to do that.');
        $app->render('user/login');
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
                $app->set('success', 'this is to edit the device ' . $deviceID);
            }
        }
        $app->render('/devices/newdevice');
    } else {
        $app->set('error', 'You must be logged in to do that.');
        $app->render('user/login');
    }
});


/* Create new device 
 * 
 *  */
post('/device', function($app) {
    if (User::is_authenticated()) {
        $mac_device = $app->form('mac_address');
        $name_device = $app->form('name_device');
        $isToEditDevice = $app->form('isEditDevice');
        if ($isToEditDevice == "1") { //edit device name
            $device = Device::getDevice(User::current_user(), $mac_device);
            if ($device != NULL) {
                if ($device->name_device != $name_device) {
                    $device->name_device = $name_device;
                    Device::updateSensor(User::current_user(), $device);
                }
                $app->redirect('/devices/showdevices/');
            } else {
                $app->redirect('/');
            }
        } else { // add new device
            //TODO: need get the device on Devices DB,
            // now if is available 
            //change document->owner to user
            // copy device/document to user DB
            //and update in db devices
            $device = Device::findTheDeviceOnDevicesDB($mac_device);
            if ($device != NULL) {
                if (trim($name_device) != '' && $name_device != $device->name_device) {
                    $device->name_device = $name_device;
                }
                $device->owner = User::current_user();
            }
            if (Device::updateTheOwnerDeviceOnDeviesDB($device)) {
                Device::saveDeviceInUserDB($device);
            };
            $app->redirect('/devices/showdevices');
        }
    } else {
        $app->set('error', 'You must be logged in to do that.');
        $app->render('user/login');
    }
});
/*
 * delete device Method POST and receive:
 * _id (mac address) of device
 * _rev of document of device  */
post('/deletedevice/:id/:rev', function($app) {
    if (User::is_authenticated()) {
        $device = new Device();
        $device->_id = $app->request('id');
        $device->_rev = $app->request('rev');
        $device->delete(User::current_user());
        User::registeDeviceInUser(User::current_user(), $device->_id, TRUE);
        $app->set('success', 'Delete Device successfull');
        $app->redirect('/devices/showdevices');
    } else {
        $app->set('error', 'You must be logged in to do that.');
        $app->render('user/login');
    }
});

/* END DEVICE */

/* ---------------START SENSOR --------------- */
/*
 * This method is to update the sensor temperature and receive the next information */

post('/configsensortemperature/:id/:rev', function($app) {
    if (User::is_authenticated()) {
        Temperature::updateTemperature(User::current_user(), $app->request('id'), $app->request('rev'), $app->form('max_temp_notification'), $app->form('min_temp_notification'));
        $app->set('success', 'Yes receive the id' . $app->request('id') . " and rev" . $app->request('rev') . " - max " . $app->form('max_temp_notification') . " min " . $app->form('min_temp_notification') . "<br>");
        $app->redirect('/devices/showdevices');
    } else {
        $app->set('error', 'You must be logged in to do that.');
        $app->render('user/login');
    }
});

post('/configsensorbattery/:id/:rev', function($app) {
    if (User::is_authenticated()) {
        Battery::updateBattery(User::current_user(), $app->request('id'), $app->request('rev'), $app->form('low_battery_notification'), $app->form('critical_battery_notification'));
        $app->redirect('/devices/showdevices');
    } else {
        $app->set('error', 'You must be logged in to do that.');
        $app->render('user/login');
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
        $app->set('error', 'You must be logged in to do that.');
        $app->render('user/login');
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
        $app->set('error', 'You must be logged in to do that.');
        $app->render('user/login');
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
        $app->set('error', 'You must be logged in to do that.');
        $app->render('user/login');
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
        $app->set('error', 'You must be logged in to do that.');
        $app->render('user/login');
    }
});

/* Comment because i dont now the parameter dev???
 * post('/deletesafezone/:id/:rev/:dev', function($app) {
  if (User::is_authenticated()) {
  $safezone = new Safezone();
  $safezone->_id = $app->request('id');
  $safezone->_rev = $app->request('rev');
  $safezone->delete(User::current_user());

  $deviceadd = $app->request('dev');
  if ($deviceadd == NULL || $deviceadd === "") {
  $app->set('success', 'The safezone was deleted');
  $app->redirect('/safezone/showsafezones');
  } else {
  $app->redirect('/devices/editdevice/' . $app->request('dev'));
  }
  } else {
  $app->set('error', 'You must be logged in to do that.');
  $app->render('user/login');
  }
  }); */

post('/deletesafezone/:id/:rev', function($app) {
    if (User::is_authenticated()) {
        $safezone = new Safezone();
        $safezone->_id = $app->request('id');
        $safezone->_rev = $app->request('rev');
        $safezone->delete(User::current_user());

        $app->set('success', 'The safezone was deleted');
        $app->redirect('/safezone/showsafezones');
    } else {
        $app->set('error', 'You must be logged in to do that.');
        $app->render('user/login');
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

post('/deviceAddOrDelete', function($app) {
    if (isset($_POST["name"]) && isset($_POST["username"]) && isset($_POST["email"]) && isset($_POST["password"])) {

        $userDB = $_POST["user"];
        $macaddress = $_POST["mac"];
        $delete = $_POST["delete"];

        $deleteDevice = $delete === 'true' ? true : false;
        $response = array();

        $deviceResponse = User::registeDeviceInUser($userDB, $macaddress, $deleteDevice);
        if ($deviceResponse != NULL) {
            $response['error'] = false;
            $response['message'] = "Add device in " . $userDB . "Save successful ";
        } else {
            $response['error'] = true;
            $response['message'] = "Error try to save device in " . $userDB;
        }
    } else {
        $response['error'] = true;
        $response['message'] = "Error receiving data";
    }
});

post('/devicepost', function($app) {
    /* from device */
    $macaddress = $_POST["mac"];
    $battery = $_POST["batt"];
    /* from gps */
    $latfrom = $_POST["latfrom"];
    $lonfrom = $_POST["lngfrom"];
    /* from temperature */
    $temperature = $_POST["temp"];
    /* from panic button */
    $pressed = $_POST["press"];
    $response = array();

    $usernamedb = User::findUsernameByMACAddress($macaddress);

    if ($usernamedb == NULL) {
        /* test only for when user add new device */
//$devices = User::registeDeviceInUser("rpessoa", $macaddress, FALSE);
        $response['error'] = true;
        $response['message'] = "Device not found to user: " . $macaddress . " - " . $devices;
    } else {
        /* test only for when user delete device */
//$devices = User::registeDeviceInUser("rpessoa", $macaddress, TRUE);
        $str = "";
        if ($latfrom != NULL && $lonfrom != NULL) {
            $str.= MSGPS::calcIfCheckInOrCheckOut($usernamedb, $macaddress, $latfrom, $lonfrom);
        } else {
            $str.="|| _GPS null";
        }
        if ($temperature != NULL) {
            $str.= MSTemperature::calcIfLowOrRangeOrHighTemperature($usernamedb, $macaddress, $temperature);
            // $str.= MSTemperature::saveMonitoringSensorTemperature($usernamedb, $macaddress, $temperature);
        } else {
            $str.="|| _Temperature null";
        }

        if ($pressed != NULL) {
            $boolPressed = $pressed === 'true' ? true : false;
            if ($boolPressed) {
                $str.= MSPanicButton::saveMonitoringSensorPanicButton($usernamedb, $macaddress, $boolPressed);
            }
        } else {
            $str.="|| _Panic Button null or false";
        }
        $response['error'] = false;
        $response['message'] = $usernamedb . " found " . $str;

//echo ''.$usernamedb;
    }
    echo json_encode($response);
});


/* ---------- ADMIN  ---------- */
/* Create new device  */

post('/manager_newdevice', function($app) {
    if (User::is_authenticated() && User::is_current_admin_authenticated()) {
        Device::createDeviceFromManagerDevice($app);

        //$app->set('success', 'Yes device saved');
        $app->redirect('/admin/manager_showdevices');
    } else {
        $app->set('error', 'You must be logged in to do that.');
        $app->render('user/login');
    }
});

get('/admin/manager_dashboard', function($app) {
    if (User::is_authenticated() && User::is_current_admin_authenticated()) {
        $app->set("numbAvailableDevices", Device::getNumberOfAvailableDevices());
        $app->set("numbAllDevices", Device::getNumberOfDevicesInDBDevices());
        $app->render('/admin/manager_dashboard');
    } else {
        $app->redirect('/');
    }
});

get('/admin/manager_showdevices', function($app) {
    if (User::is_authenticated() && User::is_current_admin_authenticated()) {
        $numberOfDevices = Device::getNumberOfDevicesInDBDevices();
        $app->set('numberDevices', $numberOfDevices);

        if ($numberOfDevices != 0) {
            $app->set('devices', Device::getAllDevicesInDBDevices());
        }
        $app->render('/admin/manager_showdevices');
    } else {
        $app->redirect('/');
    }
});

get('/admin/manager_newdevice', function($app) {
    if (User::is_authenticated() && User::is_current_admin_authenticated()) {
        $app->render('/admin/manager_newdevice');
    } else {
        $app->redirect('/');
    }
});



resolve(); //if the route not exist page not found
