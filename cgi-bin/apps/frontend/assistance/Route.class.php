<?php

	namespace apps\frontend\assistance;

	class Route extends \Route
	{
		protected static $routes = array(
			'envoi-message' => array(
				'pattern' => 'envoi-message',
				'controller' => 'MainAction::verifMail'
			),
			'index' => array(
				'pattern' => '',
				'controller' => 'MainAction::execute'
			)
		);
	}

?>
