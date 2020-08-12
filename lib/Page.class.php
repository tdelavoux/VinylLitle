<?php

	class Page
	{
		private static $values = array();

		public static function set($key, $val)
		{
			self::$values[$key] = $val;
			return self::$values[$key];
		}

		public static function get($value, $protectHtml = false)
		{
			if(isset(self::$values[$value]))
			{
				return ($protectHtml) ? htmlspecialchars(self::$values[$value]) : self::$values[$value];
			}

			return null;
		}

		public static function display($template = 'main.template.php', $path = null)
		{
			\header('Content-Type: text/html; charset=utf-8');

			if (!$path){$path = \Application::getModule() ;}

			$template = __DIR__ . '/../apps/http/' . $path . '/'. $template;
			require __DIR__ . '/../apps/http/layout.template.php';
		}

	}

?>
