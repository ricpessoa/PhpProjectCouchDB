<?php
/**
 * Demo Websocket Lesson 2 - SERVER CODE
 * -----------------------------------------
 * @Topic  : Online ChatRoom
 * @Author : ANHVNSE02067 <anhvnse@gmail.com>
 * @Website: www.nhatanh.net
 */

require "PHP-Websockets/websockets.php";
require "Message.php";

class Server extends WebSocketServer
{
    private $_serverMessage;

    public function __construct($addr, $port)
    {
        parent::__construct($addr, $port);
        $this->_serverMessage = new Message('!', 'red', '');
    }

    // @override
    protected function connected($user)
    {
        // Send welcome message to user
        $this->_serverMessage->message = 'Successful connection.<br> Waiting for notification of the devices.';
        $this->send($user, $this->_serverMessage->serialize());
    }    

    // @override
    protected function process($user, $message)
    {
        // Send back message to everyone
        $this->sendAll(
            (new Message())
            ->unserialize($message)
        );
    }

    // @override
    protected function closed($user)
    {
        // Alert on server
        echo "User $user->id  closed connection".PHP_EOL;
    }

    /**
     * Send Message to ALl Users
     */
    protected function sendAll(Message $message)
    {
        if (!empty($this->users)) {
            foreach ($this->users as $user) {
                $this->send($user, $message->serialize());
            }
        }
    }

    public function __destruct()
    {
        echo "Server destroyed!".PHP_EOL;
    }
}


$addr = '192.168.0.104';
$port = '2207';

$server = new Server($addr, $port);
$server->run();
