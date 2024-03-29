<?php

header('Content-Type: text/html; charset=utf-8'); //add this line for special characters 
date_default_timezone_set("Europe/Lisbon"); // add time zone to Lisboan
define('ROOT', __DIR__ . '/..');

require_once ROOT . '/lib/bootstrap.php';
require_once ROOT . '/lib/sag/src/Sag.php';
require_once ROOT . '/lib/configuration.php';

function __autoload($classname) {
    include_once(ROOT . "/classes/" . strtolower($classname) . ".php");
}

function get($route, $callback) {
    Bones::register($route, $callback, 'GET');
}

function post($route, $callback) {
    Bones::register($route, $callback, 'POST');
}

function put($route, $callback) {
    Bones::register($route, $callback, 'PUT');
}

function delete($route, $callback) {
    Bones::register($route, $callback, 'DELETE');
}

function resolve() {
    Bones::resolve();
}

class Bones {

    private static $instance;
    public static $route_found = false;
    public static $rendered = false;
    public $route = '';
    public $method = '';
    public $content = '';
    public $vars = array();
    public $route_segments = array();
    public $route_variables = array();
    public $couch;
    public $config;

    public function __construct() {
        $this->route = $this->get_route();
        $this->route_segments = explode('/', trim($this->route, '/'));
        $this->method = $this->get_method();

        session_start();
        $this->config = new Configuration();
        $this->couch = new Sag($this->config->db_server, $this->config->db_port);
        $this->couch->setDatabase($this->config->db_database);
    }

    public static function get_instance() {
        if (!isset(self::$instance)) {
            self::$instance = new Bones();
        }
        return self::$instance;
    }

    public static function register($route, $callback, $method) {
        if (!static::$route_found) {
            $bones = static::get_instance();
            $url_parts = explode('/', trim($route, '/'));
            $matched = null;

            if (count($bones->route_segments) == count($url_parts)) {
                foreach ($url_parts as $key => $part) {
                    if (strpos($part, ":") !== false) {
                        // Contains a route variable
                        $bones->route_variables[substr($part, 1)] = $bones->route_segments[$key];
                    } else {
                        // Does not contain a route variable
                        if ($part == $bones->route_segments[$key]) {
                            if (!$matched) {
                                // Routes match
                                $matched = true;
                            }
                        } else {
                            // Routes don't match
                            $matched = false;
                        }
                    }
                }
            } else {
                // Routes are different lengths
                $matched = false;
            }


            if (!$matched || $bones->method != $method) {
                return false;
            } else {
                static::$route_found = true;
                echo $callback($bones);
            }
        }
    }

    protected function get_route() {
        parse_str($_SERVER['QUERY_STRING'], $route);
        if ($route) {
            return '/' . $route['request'];
        } else {
            return '/';
        }
    }

    public function make_route($path = '') {
        $url = explode("/", $_SERVER['PHP_SELF']);
        return '/' . $url[1] . $path;
    }

    protected function get_method() {
        return isset($_SERVER['REQUEST_METHOD']) ? $_SERVER['REQUEST_METHOD'] : 'GET';
    }

    public function set($index, $value) {
        $this->vars[$index] = $value;
    }

    public function form($key) {
        return $_POST[$key];
    }

    public function request($key) {
        return $this->route_variables[$key];
    }

    public function render($view, $layout = "layout") {
        $this->content = ROOT . '/views/' . $view . '.php';
        foreach ($this->vars as $key => $value) {
            $$key = $value;
        }

        if (!$layout) {
            include($this->content);
        } else {
            include(ROOT . '/views/' . $layout . '.php');
        }
    }

    public function display_alert($variable = 'error') {
        if (isset($this->vars[$variable])) {
            return "<div class='alert alert-" . $variable . "'><a class='close' data-dismiss='alert'>x</a>" . $this->vars[$variable] . "</div>";
        }
    }

    public function redirect($path = '') {
        header('Location: ' . $this->make_route($path));
    }

    public function error500($exception) {
        $this->set('exception', $exception);
        $this->render('error/500');
        exit;
    }

    public function error404() {
        $this->render('error/404');
        exit;
    }

    public static function resolve() {
        session_write_close();
        if (!static::$route_found) {
            $bones = static::get_instance();
            $bones->error404();
        }
    }

    public static function Unencrypter($valueEncrypted) {
        // Start the session so we can use sessions
        session_start();

        $descriptorspec = array(
            0 => array("pipe", "r"), // stdin is a pipe that the child will read from
            1 => array("pipe", "w")  // stdout is a pipe that the child will write to
        );

        if (isset($valueEncrypted)) {
            $key = $_SESSION["key"];

            // Decrypt the client's request and send it to the clients(uncrypted)
            $cmd = sprintf("openssl enc -aes-256-cbc -pass pass:" . escapeshellarg($key) . " -d");
            $process = proc_open($cmd, $descriptorspec, $pipes);
            if (is_resource($process)) {
                fwrite($pipes[0], base64_decode($valueEncrypted));
                fclose($pipes[0]);

                $data = stream_get_contents($pipes[1]);
                fclose($pipes[1]);
                proc_close($process);
            }

            return $data;
            //print_r($output);
        }
    }

}
