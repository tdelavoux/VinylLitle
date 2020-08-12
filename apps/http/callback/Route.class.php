<?php

	namespace apps\http\callback;

	class Route extends \Router
	{
            protected static $routes = array(
                '' => 'MainAction::execute',
                'test' => 'MainAction::execute2',
                'test2/{id}' => 'MainAction::execute3',
                'test2/{id}/test/{id2}' => 'MainAction::execute4'
            );
	}

?>
