<?php

	namespace config;

	class Security
	{
		private static $defaultRequired;
		private static $rules;

		public static function init()
		{
			self::$defaultRequired = \User::PRIVILEGE_NONE;

			self::$rules = array(
				'index' => array(
					'required' => null
				),
				'error' => array(
					'required' => null
				),
				'login' => array(
					'required' => null
				)
				/* exemple module avec droit admin
				'adminxxxxxx' => array(
					'required' => \User::PRIVILEGE_ADMIN
				)*/
			);
		}

		public static function getRequiredPrivileges($module, $route = null)
		{
			if (!isset(self::$rules[$module])
				|| !\array_key_exists('required', self::$rules[$module]))
			{
				return self::$defaultRequired;
			}

			if ($route === null
				|| !isset(self::$rules[$module]['routes'])
				|| !\array_key_exists($route, self::$rules[$module]['routes']))
			{
				return self::$rules[$module]['required'];
			}

			return self::$rules[$module]['routes'][$route];
		}
	}
