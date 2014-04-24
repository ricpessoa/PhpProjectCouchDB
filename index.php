<?php

include 'lib/bones.php';

get('/', function($app) {
    if (User::is_authenticated()) {
        $devices = Device::getDevices(User::current_user());
        $app->set('devices', $devices);
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
    if (User::is_authenticated()) {
        $devices = Device::getDevices(User::current_user());
        $app->set('devices', $devices);
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
        $app->set('posts', Post::get_posts_by_user($app->request('username')));
        $app->set('post_count', Post::get_post_count_by_user($app->request('username')));
        $app->render('user/profile');
    } else {
        //$app->redirect('/user/' . User::current_user());
        $app->redirect('/');
    }
});

post('/post', function($app) {
    if (User::is_authenticated()) {
        $post = new Post();
        $post->content = $app->form('content');
        $post->create();
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


get('/devices/editdevice/:device', function($app) {
    if (User::is_authenticated()) {
        if (Device::deviceExist(User::current_user(), $app->request('device'))) {
            $arraySensors = Sensor::getSensors(User::current_user(), $app->request('device'));
            $app->set('arraySensors', $arraySensors); //need get the safezones of device

            $device = new Device();
            $device->_id = $app->request('device');
            $device->_rev = Device::getDeviceRevisionByID(User::current_user(), $app->request('device'));

            $safezones = Safezone::getSafezonesByUserAndDevice(User::current_user(), $device->_id);

            $app->set('deviceID', $device->_id);
            $app->set('deviceREV', $device->_rev);
            $app->set('numberSafezones', sizeof($safezones)); //need get the safezones of device
            $app->set('jsonSafezones', Safezone::getArrayOfSafezonesToJson($safezones)); //need get the safzones objects

            $app->render('/devices/editdevice');
        } else {
            $app->render('error/404');
        }
    } else {
        $app->set('error', 'You must be logged in to do that.');
        $app->render('user/login');
    }
});

/* Create new device 
 * need refacturing because when create the new device isn't receive the max and min temperature
 *  */
post('/device', function($app) {
    if (User::is_authenticated()) {
        $device = new Device();
        $device->_id = $app->form('mac_address');
        $name_device = $app->form('name_device');
        if (trim($name_device) != '') {
            $device->name_device = $name_device;
        }
        $myArray = array();
        if ($app->form('check_panic_bt_send') == "1") {
            $sensorPanic = new Sensor("panic_button");
            $sensorPanic->name_sensor = "Panic Button";
            $myArray[] = $sensorPanic;
        }
        if ($app->form('check_gps_send') == "1") {
            $sensorGPS = new Sensor("GPS");
            $sensorGPS->name_sensor = "Sensor GPS";
            $myArray[] = $sensorGPS;
        }
        if ($app->form('check_temperature_send') == "1") {
            $temperature = new Temperature();
            $temperature->min_temperature = $app->form('min_temp_notification');
            $temperature->max_temperature = $app->form('max_temp_notification');
            $myArray[] = $temperature;
        }

        $device->sensors = $myArray;

        $device->create();

        $app->set('success', 'Yes device saved');
        $app->render('/devices/newdevice');
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
 * This method is to update the sensor temperature and receive the next information
 *  User::current_user()
 *  $app->request('id')
 *  $app->request('rev')
 *  $app->form('max_temp_notification')
 *  $app->form('min_temp_notification') */

post('/sensor/:id/:rev', function($app) {
    if (User::is_authenticated()) {
        Temperature::updateTemperature(User::current_user(), $app->request('id'), $app->request('rev'), $app->form('max_temp_notification'), $app->form('min_temp_notification'));
        //$app->set('success', 'Yes receive the id' . $app->request('id') . " and rev" . $app->request('rev') . " - max " . $app->form('max_temp_notification') . " min " . $app->form('min_temp_notification') . "<br>");
        $app->redirect('/device/editdevice/' . $app->request('id'));
    } else {
        $app->set('error', 'You must be logged in to do that.');
        $app->render('user/login');
    }
});
/* END SENSOR */

/* --------------- SAFEZONE --------------- */
/*
get('/safezone/showsafezones', function($app) {
    if (User::is_authenticated()) {
        $numSafezones = Safezone::get_safezones_count_by_user(User::current_user());
        $app->set('numberSafezones', $numSafezones);
        if ($numSafezones != 0) {
            $app->set('safezones', Safezone::get_safezones_by_user(User::current_user()));
        }
        $app->render('safezone/showsafezones');
    } else {
        $app->set('error', 'You must be logged in to do that.');
        $app->render('user/login');
    }
});
*/
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
        //$safezone->timestamp= $json_safezone["timestamp"]; //timestamp generate by server
        //$safezone->shared = $json_safezone["shared"]; //safezone sared by other devices not yet implemented
        $safezone->create();


        $numSafezones = Safezone::get_safezones_count_by_user(User::current_user());
        $app->set('numberSafezones', $numSafezones);
        if ($numSafezones != 0) {
            $app->set('safezones', Safezone::get_safezones_by_user(User::current_user()));
        }
        //$app->set('success', 'Yes safezone saved');
        $app->redirect('/devices/editdevice/' . $safezone->device);
    } else {
        $app->set('error', 'You must be logged in to do that.');
        $app->render('user/login');
    }
});


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

        $app->set('success', 'Yes receive the mac_address ' . $macAddress . " edit device?" . $editDevice . " - ");
        $app->render('/safezone/newsafezone');
    } else {
        $app->set('error', 'You must be logged in to do that.');
        $app->render('user/login');
    }
});

post('/deletesafezone/:id/:rev/:dev', function($app) {
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
});

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

resolve(); //if the route not exist page not found
