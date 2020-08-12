<?php

	namespace apps\frontend\login;

	class MainAction
	{

            /* TODO PRévoir un plug-in d'usurpation d'identité */

            public static function execute()
            {        
                    \User::logout();
                    \Page::set('title', 'Identification');
                    \Page::display();
            }

            public static function login()
            {
                
                \Form::addParams('login', $_POST, \Form::TYPE_STRING, 0, 50);
                if(\Form::isValid()){
                    \User::retrieveUser(\Form::param('login'));
                    header('location:'.\Application::getRoute('index', 'index'));
                }          
            }

	}

?>
