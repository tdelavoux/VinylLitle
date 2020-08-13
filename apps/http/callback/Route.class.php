<?php

	namespace apps\http\callback;

	class Route extends \Router
	{
            public static $routes = array(
                array('name' => '', 'pattern' => '', 'action' => 'MainAction::execute'),
                array('name' => 'ex2', 'pattern' => 'test', 'action' => 'MainAction::execute2'),
                array('name' => 'ex3', 'pattern' => 'test2/{id}', 'action' => 'MainAction::execute3'),
                array('name' => 'ex3', 'pattern' => 'test2/{id}/test/{id2}', 'action' => 'MainAction::execute4')
            );
	}

?>
