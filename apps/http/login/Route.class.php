<?php

	namespace apps\frontend\login;

	class Route extends \Route
	{
            protected static $routes = array(
                'delog' => array(
                        'pattern' => '',
                        'controller' => 'MainAction::execute'
                ),
                'login' => array(
                        'pattern' => 'login',
                        'controller' => 'MainAction::login'
                )
            );
	}

?>
