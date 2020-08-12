<?php

	class Page
	{
		private static $values = array('style' => '', 'js' => '');

		public static function set($key, $val)
		{
			self::$values[$key] = $val;
			return self::$values[$key];
		}

		public static function addStyle($style)
		{
			self::$values['style'] .= $style;
		}

		public static function addScript($script)
		{
			self::$values['js'] .= $script;
		}

		public static function get($value, $protectHtml = false)
		{
			if(isset(self::$values[$value]))
			{
				return ($protectHtml) ? htmlspecialchars(self::$values[$value]) : self::$values[$value];
			}

			return null;
		}

		public static function display($template = 'main.template.php', $path = null, $filters = array())
		{
			\header('Content-Type: text/html; charset=utf-8');

			if (!$path)
			{
				$path = \Application::getModule() . '/';
			}

			$template = __DIR__ . '/../apps/frontend/' . $path . $template;

			require __DIR__ . '/../apps/frontend/layout.template.php';
		}

		/**
		 * Translate a string, if a translation was found. Otherwise, return the string without translation.
		 * @param  string  $string	   String to translate
		 * @param  boolean $recordTrad True if the program on dev website must record the string a string to translate if it was not found
		 * @return string			   Translated string
		 */
		public static function _($string, $recordTrad = true)
		{
			if (!$string)
			{
				return $string;
			}

			$string = \str_replace('"', '&quot;', $string); // To avoid syntax errors in the .po file, espace the double quotes

			if (!PRODUCTION)
			{
				if ($recordTrad)
				{
					//self::addTrad($string);
				}

				$string = isset(\Application::$trad[$string]) ? \Application::$trad[$string] : $string;

				return \str_replace('&quot;', '"', $string);
			}

			return \str_replace('&quot;', '"', _($string));
		}
	}

?>
