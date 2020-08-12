<?php

class ExceptionPage extends Exception
{
    public function __construct($message, $code = 0) {
        
        // TODO appeler le contenu de la page d'erreur
        \call_user_func_array('\\apps\\http\\error\\MainAction::execute', array($code));
    }
}