<?php

	class Log
	{
		const ASSERT = 0x01;
		const DEBUG = 0x02;
		const ERROR = 0x04;
		const INFO = 0x08;
		const VERBOSE = 0x10;
		const WARN = 0x20;

		const APACHE_LOG_FILE = 0x01;
		const STDOUT = 0x02;
		const MAIL = 0x04;

		const STDOUT_FORMAT_ERROR_PAGE = 0x01;
		const STDOUT_FORMAT_HTML = 0x02;
		const STDOUT_FORMAT_JSON = 0x04;

		/**
		 * Get the message's code if the message is an Exception or an integer
		 * @param  mixed $message Log message
		 * @return mixed          Message code (positive integer) or null
		 */
		private static function getMessageCode($message)
		{
			if ($message instanceof \Exception)
			{
				return $message->getCode() > 0 ? $message->getCode() : -$message->getCode();
			}
			elseif (is_numeric($message))
			{
				return ($message = (int) $message) > 0 ? $message : -$message;
			}

			return null;
		}

		/**
		 * Send an HTTP Redirect response based on given log message
		 * @param mixed $message Log message
		 */
		private static function messageToRedirect($message)
		{
			$messageCode = self::getMessageCode($message);

			$_GET['p'] = \config\Configuration::getModuleKey(\config\Configuration::ERROR_MODULE);
			$_GET['params'] = $messageCode . '/';
			\Application::configure();
			\Application::load();

			exit();
		}

		/**
		 * Get a JSON String based on given log message
		 * @param  mixed   $message    Log message
		 * @param  integer $errorLevel The log message's level (ASSERT, DEBUG, ERROR, INFO, VERBOSE or WARN)
		 * @return string              The log message formated as a JSON String
		 */
		private static function messageToJSON($message, $errorLevel)
		{
			if ($message instanceof \Exception)
			{
				$message = array(
					'ErrorCode' => $message->getCode(),
					'Message' => \utf8_encode($message->getMessage()),
					'Stack' => $message->getTraceAsString(),
					'File' => $message->getFile(),
					'Line' => $message->getLine(),
				);
			}
			elseif (!is_array($message))
			{
				$message = array($message);
			}

			if ($errorLevel & (self::ERROR | self::WARN | self::VERBOSE))
			{
				$message += array(
					'ClientIp' => \Network::getClientIp(),
					'$_POST' => $_POST,
					'$_GET' => $_GET,
					'$_SESSION' => (isset($_SESSION)) ? $_SESSION : array()
				);
			}
			return \var_dump($message);
			return \json_encode($message, JSON_NUMERIC_CHECK);
		}

		/**
		 * Get an HTML code based on log message
		 * @param  mixed   $message    Log message
		 * @param  integer $errorLevel The log message's level (ASSERT, DEBUG, ERROR, INFO, VERBOSE or WARN)
		 * @return string              The log message formated as HTML code
		 */
		private static function messageToHTML($message, $errorLevel)
		{
			if ($message instanceof \Exception)
			{
				$message = '<tr><th>ErrorCode</th><td><strong>' . $message->getCode() . '</strong></td></tr>'
					. '<tr><th>Message</th><td><pre>' . $message->getMessage() . '</pre></td></tr>'
					. '<tr><th>Stack</th><td><pre>' . $message->getTraceAsString() . '</pre></td></tr>'
					. '<tr><th>File</th><td><pre>' . $message->getFile() . '</pre></td></tr>'
					. '<tr><th>Line</th><td><pre>' . $message->getLine() . '</pre></td></tr>';
			}
			else
			{
				$message = '<tr><th>Message</th><td><pre>' . \print_r($message, true) . '</pre></td></tr>';
			}

			if ($errorLevel & (self::ERROR | self::WARN | self::VERBOSE))
			{
				$message .= '<tr><th>ClientIp</th><td>' . \Network::getClientIp() . '</td></tr>'
					. '<tr><th>$_POST</th><td><pre>' . \print_r($_POST, true) . '</pre></td></tr>'
					. '<tr><th>$_GET</th><td><pre>' . \print_r($_GET, true) . '</pre></td></tr>'
					. '<tr><th>$_SESSION</th><td><pre>' . ((isset($_SESSION)) ? \print_r($_SESSION, true) : '') . '</pre></td></tr>';
			}

			return '<!doctype html><html><head><title>Erreur</title><meta http-equiv="Content-type" content="text/html; charset=UTF-8"/><style>th{padding:10px;background:#ddd}td{padding:10px;border:1px solid #000}</style></head><body><table>'
				. $message . '</table></body></html>';
		}

		/**
		 * Get a Text/Plain message based on given log message
		 * @param  mixed   $message    Log message
		 * @param  integer $errorLevel The log message's level (ASSERT, DEBUG, ERROR, INFO, VERBOSE or WARN)
		 * @return string              The log message formated as Text/Plain message
		 */
		private static function messageToTextPlain($message, $errorLevel)
		{
			if ($message instanceof \Exception)
			{
				$message = 'ErrorCode: ' . $message->getCode() . "\n\n"
					. 'Message: ' . $message->getMessage() . "\n\n"
					. 'Stack: ' . $message->getTraceAsString() . "\n\n"
					. 'File: ' . $message->getFile() . "\n\n"
					. 'Line:' . $message->getLine() . "\n\n";
			}
			else
			{
				$message = 'Message: ' . \print_r($message, true) . "\n\n";
			}

			if ($errorLevel & (self::ERROR | self::WARN | self::VERBOSE))
			{
				$message .= 'ClientIp: ' . \Network::getClientIp() . "\n\n"
					. '$_POST: ' . \print_r($_POST, true) . "\n\n"
					. '$_GET: ' . \print_r($_GET, true) . "\n\n"
					. '$_SESSION: ' . ((isset($_SESSION)) ? \print_r($_SESSION, true) : '') . "\n\n";
			}

			return $message;
		}

		/**
		 * Log the given message, with given log level
		 * @param  mixed   $message    Log message
		 * @param  integer $errorLevel The log message's level (ASSERT, DEBUG, ERROR, INFO, VERBOSE or WARN)
		 */
		private static function logMessage($message, $errorLevel)
		{
			if (!(\config\Configuration::$vars['error']['levels'] & $errorLevel))
			{
				return;
			}

			if (\config\Configuration::$vars['error']['observers'] & self::APACHE_LOG_FILE)
			{
				\trigger_error(self::messageToJSON($message, $errorLevel));
			}

			if (($errorLevel & self::ERROR) && (\config\Configuration::$vars['error']['observers'] & self::MAIL))
			{
				$text = self::messageToTextPlain($message, $errorLevel);
				$mail = new \AppMail();
				$mail->setTransmitter(\config\Configuration::$vars['email']['noreply']);
				$mail->setReceiver(\config\Configuration::$vars['email']['admin']);
				$mail->setSubject(\config\Configuration::$vars['application']['name'] . ' - Fatal error '
					. self::getMessageCode($message) . ': ' . \substr($text, 0, 50));
				$mail->setTextMessage($text);
				$mail->send();
			}

			if (($errorLevel & self::ERROR) && (\config\Configuration::$vars['error']['observers'] & self::STDOUT))
			{
				if (ob_get_level())
				{
					\ob_clean();
				}

				switch ($message->getCode())
				{
				case 401:
					\header('HTTP/1.1 401 Unauthorized');
					break;
				case 404:
					\header('HTTP/1.1 404 Not Found');
					break;
				default:
					\header('HTTP/1.1 500 Internal Server Error');
					break;
				}

				switch (\config\Configuration::$vars['error']['stdout_format'])
				{
					case self::STDOUT_FORMAT_ERROR_PAGE:
						self::messageToRedirect($message);
						break;
					case self::STDOUT_FORMAT_JSON:
						echo self::messageToJSON($message, $errorLevel);
						break;
					case self::STDOUT_FORMAT_HTML:
						echo self::messageToHTML($message, $errorLevel);
						break;
				}
			}
		}

		/**
		 * Log the given message with DEBUG log level
		 * @param mixed $message Log message
		 */
		public static function d($message)
		{
			self::logMessage($message, self::DEBUG);
		}

		/**
		 * Log the given message with ERROR log level
		 * @param mixed $message Log message
		 */
		public static function e($message)
		{
			self::logMessage($message, self::ERROR);
		}

		/**
		 * Log the given message with INFO log level
		 * @param mixed $message Log message
		 */
		public static function i($message)
		{
			self::logMessage($message, self::INFO);
		}

		/**
		 * Log the given message with VERBOSE log level
		 * @param mixed $message Log message
		 */
		public static function v($message)
		{
			self::logMessage($message, self::VERBOSE);
		}

		/**
		 * Log the given message with WARNING log level
		 * @param mixed $message Log message
		 */
		public static function w($message)
		{
			self::logMessage($message, self::WARN);
		}
	}

?>
