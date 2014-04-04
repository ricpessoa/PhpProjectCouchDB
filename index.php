<?php

include 'lib/bones.php';

get('/', function($app) {
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

/* DEVICES */

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

post('/device', function($app) {
    if (User::is_authenticated()) {
        $device = new Device();
        $device->_id = $app->form('mac_address');
        $name_device = $app->form('name_device');
        if (trim($name_device) != '') {
            $device->name_device = $name_device;
        }
        $myArray = array();
        if ($app->form('check_temperature_send') == "1") {
            $temperature = new Temperature();
            $temperature->min_temperature = $app->form('min_temp_notification');
            $temperature->max_temperatrue = $app->form('max_temp_notification');
            $myArray[] = $temperature;
        }
        if ($app->form('check_gps_send') == "1") {
            $sensorGPS = new Sensor("GPS");
            $sensorGPS->name_sensor = "Sensor GPS";
            $myArray[] = $sensorGPS;
        }
        if ($app->form('check_panic_bt_send') == "1") {
            $sensorPanic = new Sensor("panic_button");
            $sensorPanic->name_sensor = "Panic Button";
            $myArray[] = $sensorPanic;
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

/* SAFEZONE */

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

post('/safezone', function($app) {
    if (User::is_authenticated()) {
        $safezone_data = $app->form('safezone');
        $json_safezone = json_decode($safezone_data, TRUE);

        $safezone = new Safezone();
        $safezone->_id = $json_safezone["_id"];
        $safezone->address = $json_safezone["address"];
        $safezone->name = $json_safezone["name"];
        $safezone->latitude = $json_safezone["latitude"];
        $safezone->longitude = $json_safezone["longitude"];
        $safezone->radius = $json_safezone["radius"];
        $safezone->notification = $json_safezone["notification"];
        //$safezone->timestamp= $json_safezone["timestamp"]; //timestamp generate by server
        //$safezone->shared = $json_safezone["shared"]; //safezone sared by other devices not yet implemented
        $safezone->create();


        $numSafezones = Safezone::get_safezones_count_by_user(User::current_user());
        $app->set('numberSafezones', $numSafezones);
        if ($numSafezones != 0) {
            $app->set('safezones', Safezone::get_safezones_by_user(User::current_user()));
        }
        $app->set('success', 'Yes safezone saved');
        $app->render('safezone/showsafezones');
    } else {
        $app->set('error', 'You must be logged in to do that.');
        $app->render('user/login');
    }
});


get('/safezone/newsafezone', function($app) {
    if (User::is_authenticated()) {
        $app->render('/safezone/newsafezone');
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
