<?php

	class Application
	{
		/** The application language for the current session (fr | en)*/
		private static $language;

		/** The locality for the traduction files (fr_FR | en_US) */
		private static $locality;

		/** The current app (frontend | backend) */
		private static $app;

		/** The current module (contact | account | ...) */
		private static $module;

		/** The current used route */
		private static $route;

		/** Object to query the default database of the application */
		private static $db = null;

		/** Object to call asynchronously backend modules */
		private static $backend_trigger_queue = array();

		/** Traductions on developpment website */
		public static $trad = array();

		/**
		 * Strip php / asp / javascript code in a string
		 * @param string $val The value to strip
		 */
		private static function stripScripts(&$val)
		{
			if (is_array($val))
			{
				foreach ($val as $subval)
				{
					self::stripScripts($subval);
				}
			}
			else
			{
				$val = preg_replace("@<(\%|\?|([[:space:]]*)script)@i", "&lt;\\1", $val);
			}
		}

		/**
		 * Strip PHP, ASP or JS script tags from $_GET and $_POST
		 */
		private static function stripAllScripts()
		{
			array_walk($_POST, 'self::stripScripts');
			array_walk($_GET, 'self::stripScripts');
		}

		/**
		 * Configure the application : initialize all configuration variables, set the current language, etc.
		 */
		public static function configure()
		{
			self::stripAllScripts();
			\config\Configuration::init();
			\date_default_timezone_set(\config\Configuration::$vars['locale']['default_timezone']);
			\User::init();
			\config\Security::init();
			self::setLanguage();
			self::setApp();
			self::setModule();

		}

		/**
		 * Load page or web service
		 */
		public static function load()
		{
                    $namespace = '\\' . \config\Configuration::APP_PATH . '\\'
                            . self::$app
                            . '\\'
                            . self::$module
                            . '\\';
                    
                    //Si le site est en maintenance, on affiche la page associée
                    if(MAINTENANCE && !\in_array(self::$module, array('maintenance_site'))){
                        header('Location:'. \Application::getRoute('maintenance_site', 'maintenance'));
                    }      

                    if (\file_exists(__DIR__ . '/..' . str_replace('\\', '/', $namespace) . 'Route.class.php'))
                    {
                            $action = $namespace . 'Route';
                            $action::follow($namespace);
                    }
                    else
                    {             
                        if(!\in_array(self::$module, array('login','error', 'maintenance_site'))&&!PHP_CLI_CGI)
                        {
                            \User::verifSession(\config\Security::getRequiredPrivileges(self::$module, self::$route));
                        }
                        $action = $namespace . 'MainAction';
                        $action::execute();
                    }
		}

		/**
		 * Set the application language
		 * @global $_GET['lang] Application language (facultative)
		 */
		private static function setLanguage()
		{
			if(!isset($_GET['lang']) || !$_GET['lang'] || !\config\Configuration::isAllowedLanguage($_GET['lang']))
			{
				self::$language = \config\Configuration::$vars['locale']['default_language'];
			}
			else
			{
				self::$language = $_GET['lang'];
			}

			self::$locality = \config\Configuration::getLocality(self::$language);
		}

		public static function getLanguage()
		{
			return self::$language;
		}

		public static function getLocality()
		{
			return self::$locality;
		}

		private static function setApp()
		{
			if (isset($_GET['app']))
			{
				self::$app = $_GET['app'];
			}
			else
			{
				if (PHP_CLI_CGI)
				{
					global $argc, $argv;

					if ($argc > 1)
					{
						self::$app = $argv[1];
					}
					else
					{
						self::$app = \config\Configuration::DEFAULT_CLI_CGI_APP;
					}
				}
				else
				{
					self::$app = \config\Configuration::DEFAULT_APP;
				}
			}
		}

		public static function getApp()
		{
			return self::$app;
		}

		private static function setModule()
		{
			if (isset($_GET['p']))
			{
				self::$module = $_GET['p'];
			}
			else
			{
				if (PHP_CLI_CGI)
				{
					global $argc, $argv;

					if ($argc > 2)
					{
						self::$module = $argv[2];
					}
					else
					{
						throw new \Exception('No module given', \Error::MISSING_VALUE);
					}
				}
				else
				{
					self::$module = '';
				}
			}

			self::$module = \config\Configuration::getModule(self::$module);

			if (isset($_GET['lang']) && self::$module === 'index' && (!isset($_GET['params']) || !($_GET['params'])))
			{
				header("Location: " . \config\Configuration::$vars['application']['dir'], true, 301);
				exit;
			}
		}

		public static function getModule()
		{
			return self::$module;
		}

		public static function setCurrentRoute($route)
		{
			self::$route = $route;
		}

		public static function getCurrentRoute()
		{
			return self::$route;
		}

		public static function getPageUrl($mod = '', $absolutePath = false, $params = '', $witoutRewrite = true, $language = null)
		{
			$appDir = ($absolutePath) ? \config\Configuration::getAbsUrl() : \config\Configuration::$vars['application']['dir'];

			if ($mod === 'index' && !$params)
			{
				return $appDir;
			}

			$mod = \config\Configuration::getModuleKey($mod, $language);

			if ($witoutRewrite)
			{
				return $appDir . '?p=' . $mod . '&params=';
			}

			return $appDir . $mod . (($mod) ? '/' : '');
		}

		public static function getRoute($module = '', $routeName = '', $args = array(), $absolutePath = false, $app = 'frontend', $withoutRewrite = true, $language = null)
		{
			$namespace = '\\' . \config\Configuration::APP_PATH . '\\' . $app . '\\' . $module . '\\';
			$routeClass = $namespace . 'Route';

			if ($withoutRewrite)
			{
				$args = \array_map(function ($item) {
					return \urlencode($item);
				}, $args);
			}

			$route = $routeClass::getRoute($namespace, $routeName, $args);

			return self::getPageUrl($module, $absolutePath, $route !== '', $withoutRewrite, $language) . $route;
		}

		/**
		 * Connect to the default database of the application, if the application wasn't yet connected
		 * @return boolean True if the operation succeeded or false if the application was connected
		 */
		private static function connectDb($dsn)
		{
			if (!isset(self::$db[$dsn]))
			{
				self::$db[$dsn] = new \Db($dsn);
				return true;
			}

			return false;
		}

		/**
		 * Disconnect the default database of the application, if the application was connected
		 * @return boolean True if the operation succeeded or false if the application wasn't connected to this database
		 */
		public static function disconnectDb()
		{
			if(self::$db instanceof Db)
			{
				self::$db = null;
				return true;
			}

			return false;
		}

		/**
		 * Get the default database of the application. Connect to the default database if no connection was openned.
		 * @return Db Db object to query the default database.
		 */
		public static function getDb($dsn)
		{
			self::connectDb($dsn);
			return self::$db[$dsn];
		}

		public static function getUserAgent()
		{
			return self::$userAgent;
		}

		public static function addBackendTrigger($module, $params = null)
		{
			\array_push(self::$backend_trigger_queue, array(\config\Configuration::getModuleKey($module),$params));
			return true;
		}

		public static function executeBackendTriggers()
		{

			if(empty (self::$backend_trigger_queue))
			{
				return false;
			}

			foreach (self::$backend_trigger_queue as $array)
			{
				if($array[1] === null)
				{
					\Network::requestAsync('https://' . \config\Configuration::$vars['server']['domain'] . \config\Configuration::$vars['application']['dir'],
							array('lang' => self::getLanguage(), 'app' => 'backend', 'p' => $array[0]), 'GET');
				}
				else
				{
					\Network::requestAsync('https://' . \config\Configuration::$vars['server']['domain'] . \config\Configuration::$vars['application']['dir'],
							array('lang' => self::getLanguage(), 'app' => 'backend', 'p' => $array[0],'params' => $array[1]), 'GET');
				}
			}
		}
	}

?>
