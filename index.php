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

get('/user/:username', function($app) {
    if ($app->request('username') == User::current_user()) {
        $app->set('user', User::get_by_username($app->request('username')));
        $app->set('is_current_user', ($app->request('username') == User::current_user() ? true : false));
        $app->set('posts', Post::get_posts_by_user($app->request('username')));
        $app->set('post_count', Post::get_post_count_by_user($app->request('username')));
        $app->render('user/profile');
    } else {
        $app->redirect('/user/' . User::current_user());
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
    $post = new Post();
    $post->_id = $app->request('id');
    $post->_rev = $app->request('rev');
    $post->delete();
});

get('/safezone/showsafezones', function($app) {
    if (User::is_authenticated()) {
        $app->set('safezones', Safezone::get_safezones_by_user(User::current_user()));
        $app->render('/safezone/showsafezones');
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

get('devices/showdevices', function($app) {
    if (User::is_authenticated()) {
        $app->set('devices', Device::getDevices(User::current_user()));
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
        $myArray = array();
        if ($app->form('check_temperature_send') == "1") {
            $temperature = new Temperature();
            $temperature->min_temperature = $app->form('min_temp_notification');
            $temperature->max_temperatrue = $app->form('max_temp_notification');
            //$device->sensors [] = $temperature;
            //array_push($device->sensors, $temperature);
            $myArray[] = $temperature;
        }
        if ($app->form('check_gps_send') == "1") {
            $sensorGPS = new Sensor("GPS");
            $sensorGPS->name_sensor = "Sensor GPS";
            //array_push($device->sensors, $sensorGPS);
            $myArray[] = $sensorGPS;
        }
        if ($app->form('check_panic_bt_send') == "1") {
            $sensorPanic = new Sensor("panic_button");
            $sensorPanic->name_sensor = "Panic Button";
            //array_push($device->sensors, $sensorPanic);
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


post('/safezone', function($app) {
    if (User::is_authenticated()) {
        //$app->set('success', 'Yes we receive the action to insert');
        //$app->render('/safezone/show');
        $safezone = new Safezone();
        $safezone->address = "Rua Teste";
        $safezone->name = "Rua Teste";
        $safezone->latitude = 123;
        $safezone->longitude = 123456;
        $safezone->radius = 255;
        $safezone->notification = "[in-out]";
        //$safezone->timestamp = getTime();

        $safezone->create();
        $app->set('success', 'Yes we receive the action to insert');
        $app->redirect('/safezone/show');
    } else {
        $app->set('error', 'You must be logged in to do that.');
        $app->render('user/login');
    }
});
