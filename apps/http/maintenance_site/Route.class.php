<?php

	namespace apps\frontend\maintenance_site;

	class Route extends \Route
	{
            protected static $routes = array(
                'maintenance' => array(
                        'pattern' => '',
                        'controller' => 'MainAction::execute'
                )
            );
	}

?>
