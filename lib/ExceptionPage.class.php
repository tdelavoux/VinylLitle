<?php

class ExceptionPage extends Exception
{
    // Redefine the exception so message isn't optional
    public function __construct($message, $code = 0) {
        // make sure everything is assigned properly
        parent::__construct($message, $code);
        die($this->message);
    }
}