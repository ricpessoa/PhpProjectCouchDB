<?php
/**
 * Class Message hold the message send from user 
 * and serialize message send back to user
 */
class Message
{
    public $username;
    public $color;
    public $message;
    public function __construct($username = '', $color = 'black', $message = '')
    {
        if (!$color) {
            $color = 'black';
        }
        $this->username = $username;
        $this->color = $color;
        $this->message = $message;
    }

    public function serialize()
    {
        return json_encode(array(
            'username' => $this->username, 
            'color' => $this->color,
            'message' => $this->message
        ));
    }

    public function unserialize($json_str)
    {
        $data = json_decode($json_str, true);
        $this->username = $data['username'];
        $this->color = $data['color'];
        $this->message = $data['message'];
        return $this;
    }
}
