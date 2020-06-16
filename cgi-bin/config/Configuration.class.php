<?php

	namespace config;

	class Configuration
	{
		const APP_PATH = 'apps';
		const DEFAULT_APP = 'frontend';
		const DEFAULT_CLI_CGI_APP = 'backend';

		const TRADUCTION_ENCODING = 'UTF-8';

		const ERROR_MODULE = 'error';

		private static $init = false;
		private static $absUrl;

		private static $documentations = array();

		private static $modules = array(
			'fr' => array(
				'accueil' => 'index',
				'' => 'index',
				'error' => 'error',
				'maintenance_site' => 'maintenance_site',
				'assistance' => 'assistance',
				'login' => 'login'
			)
		);

		public static $vars = array(
			'application' => array('name' => '', 'dir' => '/', 'dirLib' => '/'),
			'error' => array('levels' => 0, 'stdout_format' => 0, 'observers' => 0),
			'server' => array('domain' => 'localhost'),
			'email' => array('admin' => '', 'contact' => '', 'noreply' => ''),
			//'database' => array('server' => '', 'user' => '', 'password' => '', 'name' => '', 'dsn' => ''),
			'locale' => array('default_timezone' => 'Europe/Paris', 'default_language' => 'fr', 'allowed_languages' => array('fr' => 'fr_FR'))
		);

		/**
		 * Initialize the application configuation "dynamic" variables.
		 */
		public static function init()
		{
			if (self::$init === false)
			{
				define('ASSERT', \Log::ASSERT);
				define('DEBUG', \Log::DEBUG);
				define('ERROR', \Log::ERROR);
				define('INFO', \Log::INFO);
				define('VERBOSE', \Log::VERBOSE);
				define('WARN', \Log::WARN);

				define('APACHE_LOG', \Log::APACHE_LOG_FILE);
				if(!defined('STDOUT'))
				{
					define('STDOUT', \Log::STDOUT);
				}
				define('MAIL', \Log::MAIL);

				define('ERROR_PAGE', \Log::STDOUT_FORMAT_ERROR_PAGE);
				define('HTML', \Log::STDOUT_FORMAT_HTML);
				define('JSON', \Log::STDOUT_FORMAT_JSON);

				self::$vars = \array_replace_recursive(self::$vars, \parse_ini_file(__DIR__ . '/default.ini', true));

				if (PHP_CLI_CGI)
				{
					self::$vars = \array_replace_recursive(self::$vars, \parse_ini_file(__DIR__ . '/cli.ini', true));
				}

				if (!PRODUCTION)
				{
					self::$vars = \array_replace_recursive(self::$vars, \parse_ini_file(__DIR__ . '/dev.ini', true));
				}

				self::$absUrl = 'https://' . ((isset($_SERVER['HTTP_HOST']) && PRODUCTION) ? $_SERVER['HTTP_HOST'] : self::$vars['server']['domain'])
					. self::$vars['application']['dir'];

				//liste les fichers dans le répertoire public\documentation
				if (!PHP_CLI_CGI)
				{
					self::$documentations = \array_values(array_diff(\scandir($_SERVER['CONTEXT_DOCUMENT_ROOT'].self::$vars['application']['dir'].'documentation/'), array('..', '.')));
				}

				self::$init = true;

				date_default_timezone_set(self::$vars['locale']['default_timezone']);
				setlocale(LC_TIME, 'fr', 'fr_FR', 'french', 'fra', 'fra_FRA', 'fr_FR.ISO_8859-1', 'fra_FRA.ISO_8859-1', 'fr_FR.utf8', 'fr_FR.utf-8', 'fra_FRA.utf8', 'fra_FRA.utf-8');
			}
		}

		public static function get($name, $section = null)
		{
			if ($section !== null)
			{
				if (isset(self::$vars[$section][$name]))
				{
					return self::$vars[$section][$name];
				}
			}

			if (isset(self::$vars[$name]))
			{
				return self::$vars[$name];
			}

			throw new \Exception('Variable not found in .ini file: ' . $name . ' (section: ' . $section . ')');
		}

		public static function getDocumentation()
		{
			return self::$documentations;
		}

		/**
		 * Check if $language is allowed in the Application configuration.
		 * @param  string  $language The language to check it's support.
		 * @return boolean           True if the specified language is supported
		 */
		public static function isAllowedLanguage($language)
		{
			return \array_key_exists($language, self::$vars['locale']['allowed_languages']);
		}

		/**
		 * Get the locality var associated with the given language
		 * @param  string $language Language to get the locality
		 * @return string           Locality for traductions
		 */
		public static function getLocality($language)
		{
			if (!isset(self::$vars['locale']['allowed_languages'][$language]))
			{
				throw new \Exception('Unknown language ' . $language . '. Fail to get it\'s locality.', \Error::WRONG_VARIABLE_VALUE);
			}

			return self::$vars['locale']['allowed_languages'][$language];
		}

		/**
		 * Get the absolute URL to the web application, like "http://www.my-app.com"
		 */
		public static function getAbsUrl()
		{
			return self::$absUrl;
		}

		public static function getModule($mod)
		{
			$lang = \Application::getLanguage();

			if(isset(self::$modules[$lang][$mod]))
			{
				return self::$modules[$lang][$mod];
			}

			throw new \Exception('Page not found', \Error::PAGE_NOT_FOUND);
		}

		/**
		 * Get the translated string attached with the specified module
		 * @param  string $mod Module name.
		 * @return string      Module key.
		 */
		public static function getModuleKey($mod, $language = null)
		{
			return \array_search($mod, self::$modules[$language ?: \Application::getLanguage()]);
		}
	}

?>
