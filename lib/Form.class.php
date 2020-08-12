<?php

	if (!session_id() && !PHP_CLI_CGI)
	{
		session_start();
	}

	class Form
	{
		const REQUIRED_VAL = 'Ce champ est obligatoire !';
		const WRONG_VAL = 'Cette valeur est incorrecte !';
		const BOOLEAN_REQUIRED = 'Il faut saisir 0 ou 1 !';
		const NUMBER_REQUIRED = 'Il faut saisir un nombre !';
		const NEGATIVE_NUMBER = 'Nombre négatif !';
		const NUMBER_OUT_OF_RANGE_MIN = 'Nombre trop petit !';
		const NUMBER_OUT_OF_RANGE_MAX = 'Nombre trop grand !';
		const WRONG_EMAIL = 'Valeur incorrecte !';
		const EMAIL_OUT_OF_RANGE = 'Adresse trop grande !';
		const NO_SPECIAL_CHARS = 'Pas de caractères spéciaux !';
		const VALUE_OUT_OF_RANGE = 'Valeur trop grande ou trop petite !';
		const CHARS_MIN = 'caractères minimum pour ce champ !';
		const CHARS_MAX = 'caractères maximum pour ce champ !';
                const EMPTY_STRING = '';

		/**
		 * Type boolean. The value can be a PHP native boolean (true, false), an integer (0, 1) or a string
		 * ('true', 'TRUE', 'yes', 'YES', 'y', 'Y', 'oui', 'OUI', 'o', 'O',
		 * 'false', 'FALSE', 'no', 'NO', 'n', 'N', 'non', 'NON', '0', '1')
		 */
		const TYPE_BOOLEAN = 1;
		/** Type integer. The value can be a positive or negative integer, as a native PHP integer or a string. */
		const TYPE_INT = 2;
		/** Type numeric. The value can be a native PHP integer, float or double or a string. @see PHP Documentation : is_numeric(). */
		const TYPE_NUMERIC = 3;
		/** Type digits. The value must contain only digits or characters between 0 and 9. */
		const TYPE_DIGITS = 4;
		/** Type email. The value must be a valide email. */
		const TYPE_EMAIL = 5;
		/** Type string. The value must be a scalar. @see PHP Documentation : is_scalar() */
		const TYPE_STRING = 6;
		/** Type login. The value must contains only alpha numeric and french accentued characters, "-" or "_" or "." */
		const TYPE_LOGIN = 7;

		/** Used to filter parameters that must be set */
		const OPTION_PARAM_REQUIRED = 0x01;
		/** Used to display errors to the user after filtering parameters */
		const OPTION_DISPLAY_ERRORS = 0x02;
		/** Used to filter parameters that must not be null */
		const OPTION_NOT_NULL = 0x04;

		const SIGNED_INT_8_MAX = 127;
		const UNSIGNED_INT_8_MAX = 255;
		const SIGNED_INT_16_MAX = 32767;
		const UNSIGNED_INT_16_MAX = 65535;
		const SIGNED_INT_24_MAX = 8388607;
		const UNSIGNED_INT_24_MAX = 16777215;
		const SIGNED_INT_32_MAX = 2147483647;
		const UNSIGNED_INT_32_MAX = 4294967295;

		private static $params = array();
		private static $errors = array();
		private static $confirms = array();
		private static $warnings = array();

		private static function filterString($paramName, $min, $max, $options)
		{
			$strlen = \function_exists('mb_strlen') ?
				\mb_strlen(self::$params[$paramName], \config\Configuration::TRADUCTION_ENCODING)
				: \strlen(self::$params[$paramName]);

			if ($strlen >= $min && $strlen <= $max)
			{
				return;
			}

			// Out of range
			if ($options & self::OPTION_DISPLAY_ERRORS)
			{
				self::$errors[$paramName] = self::VALUE_OUT_OF_RANGE;
				return;
			}

			throw new \Exception('Param "' . $paramName .'" is out of range. Length: ' . $strlen . ' Value: ' . self::$params[$paramName], \Error::VALUE_OUT_OF_RANGE);
		}

		private static function filterBoolean($paramName, $min, $max, $options)
		{
			if (in_array(self::$params[$paramName],
				array(1, '1', true, 'true', 'TRUE', 'yes', 'YES', 'y', 'Y', 'oui', 'OUI', 'o', 'O')))
			{
				self::$params[$paramName] = 1;
				return;
			}

			if (in_array(self::$params[$paramName],
				array(0, '0', false, 'false', 'FALSE', 'no', 'NO', 'n', 'N', 'non', 'NON')))
			{
				self::$params[$paramName] = 0;
				return;
			}

			if ($options & self::OPTION_DISPLAY_ERRORS)
			{
				self::$errors[$paramName] = self::BOOLEAN_REQUIRED;
				return;
			}

			throw new \Exception('$method["' . $paramName .'"] is not boolean: ' . self::$params[$paramName], \Error::WRONG_VARIABLE_VALUE);
		}

		private static function filterDigits($paramName, $min, $max, $options)
		{
			if (!\ctype_digit((String) self::$params[$paramName]))
			{
				if ($options & self::OPTION_DISPLAY_ERRORS)
				{
					self::$errors[$paramName] = self::WRONG_VAL;
					return;
				}

				throw new \Exception('$method["' . $paramName . '"] is not a string composed of digits: ' . self::$params[$paramName],
					\Error::WRONG_VARIABLE_VALUE);
			}

			self::filterString($paramName, $min, $max, $options); // Length test
		}

		private static function filterNumeric($paramName, $min, $max, $options)
		{
			self::$params[$paramName] = \str_replace(',', '.', self::$params[$paramName]);

			if (!\is_numeric(self::$params[$paramName]))
			{
				if ($options & self::OPTION_DISPLAY_ERRORS)
				{
					self::$errors[$paramName] = self::NUMBER_REQUIRED;
					return;
				}

				throw new \Exception('$method["' . $paramName . '"] is not a number : ' . self::$params[$paramName],
					\Error::WRONG_VARIABLE_VALUE);
			}

			if (self::$params[$paramName] >= $min && self::$params[$paramName] <= $max)
			{
				return;
			}

			// Value out of range
			if ($options & self::OPTION_DISPLAY_ERRORS && self::$params[$paramName] <= $min)
			{
				self::$errors[$paramName] = self::NUMBER_OUT_OF_RANGE_MIN;
				return;
			}
			// Value out of range
			if ($options & self::OPTION_DISPLAY_ERRORS  && self::$params[$paramName] >= $max)
			{
				self::$errors[$paramName] = self::NUMBER_OUT_OF_RANGE_MAX;
				return;
			}

			throw new \Exception('$method["' . $paramName .'"] is out of range :' . self::$params[$paramName], \Error::VALUE_OUT_OF_RANGE);
		}

		private static function filterInt($paramName, $min, $max, $options)
		{
			if (!\filter_var(self::$params[$paramName], FILTER_VALIDATE_INT) && self::$params[$paramName] !== 0 && self::$params[$paramName] !== '0')
			{
				if ($options & self::OPTION_DISPLAY_ERRORS)
				{
					self::$errors[$paramName] = self::NUMBER_REQUIRED;
					return;
				}

				throw new \Exception('$method["' . $paramName . '"] is not a number : ' . self::$params[$paramName],
					\Error::WRONG_VARIABLE_VALUE);
			}

			self::filterNumeric($paramName, $min, $max, $options);
			self::$params[$paramName] = (int) self::$params[$paramName];
		}

		private static function filterEmail($paramName, $min, $max, $options)
		{
			if (!\filter_var(self::$params[$paramName], FILTER_VALIDATE_EMAIL))
			{
				if ($options & self::OPTION_DISPLAY_ERRORS)
				{
					self::$errors[$paramName] = self::WRONG_EMAIL;
					return;
				}

				throw new \Exception('$method["' . $paramName .'"] = ' . self::$params[$paramName], \Error::WRONG_VARIABLE_VALUE);
			}

			self::filterString($paramName, $min, $max, $options);
		}

		private static function filterLogin($paramName, $min, $max, $options)
		{
			if (!\preg_match('/^[a-zA-Z0-9éèëàïôùç_\.\-]*$/' , self::$params[$paramName])) // Only alnum chars
			{
				self::$errors[$paramName] = self::NO_SPECIAL_CHARS;
				return;
			}

			self::$params[$paramName] = \ucfirst(\strtolower(self::$params[$paramName])); // Capitalize first letter
			self::filterString($paramName, $min, $max, $options);
		}

		/**
		 * Verify, format and add form parameters
		 * @param mixed   $paramsNames Parameter(s) name(s) : string or array of strings
		 * @param mixed   $values      The value to use, or the array in which we search the value with the keys in $paramsNames ($_POST for example)
		 * @param integer $type        The type of value to filter (see contants Form::TYPE_*)
		 * @param mixed   $min         The min inclusive value allowed for this parameter or array of min inclusive values for each given parameter (numeric or array)
		 * @param mixed   $max         The max inclusive value allowed for this parameter or array of max inclusive values for each given parameter (numerci or array)
		 * @param integer $options     The options to use to filter the parameters (see constants Form::OPTION_*)
		 */
		public static function addParams($paramsNames, $values, $type = self::TYPE_STRING,
			$min = 0, $max = 255, $options = 0)
		{
			if (!is_scalar($values) && !is_array($values) && $values !== null)
			{
				throw new \Exception('form parameters must be a scalar, an array or null');
			}

			if (!is_array($paramsNames))
			{
				$paramsNames = array($paramsNames);
			}

			$nbParams = count($paramsNames);

			if ((!is_numeric($min) && (!is_array($min) || count($min) != $nbParams))
					|| (!is_numeric($max) && (!is_array($max) || count($max) != $nbParams)))
			{
				throw new \Exception('Wrong format for $min or $max', \Error::WRONG_VARIABLE_VALUE);
			}

			switch ($type)
			{
				case self::TYPE_BOOLEAN:
					$filterByTypeCallback = 'self::filterBoolean';
					break;
				case self::TYPE_DIGITS:
					$filterByTypeCallback = 'self::filterDigits';
					break;
				case self::TYPE_INT:
					$filterByTypeCallback = 'self::filterInt';
					break;
				case self::TYPE_NUMERIC:
					$filterByTypeCallback = 'self::filterNumeric';
					break;
				case self::TYPE_EMAIL:
					$filterByTypeCallback = 'self::filterEmail';
					break;
				case self::TYPE_LOGIN:
					$filterByTypeCallback = 'self::filterLogin';
					break;
				default:
					$filterByTypeCallback = 'self::filterString';
			}

			for ($i = $nbParams - 1; $i >= 0; --$i)
			{
				if (!is_string($paramsNames[$i]) && !is_int($paramsNames[$i]))
				{
					throw new \Exception('Invalid type for a form param name: ' . \print_r($paramsNames[$i], true), \Error::WRONG_VARIABLE_VALUE);
				}

				// Value not set
				if(is_array($values) && !array_key_exists($paramsNames[$i], $values))
				{
					if($options & self::OPTION_PARAM_REQUIRED)
					{
						if($options & self::OPTION_DISPLAY_ERRORS)
						{
							self::$errors[$paramsNames[$i]] = self::REQUIRED_VAL;
						}
						else
						{
							throw new \Exception('Missing required $method["' . $paramsNames[$i] .'"]', \Error::MISSING_VALUE);
						}
					}

					self::$params[$paramsNames[$i]] = null;
					continue;
				}

				self::$params[$paramsNames[$i]] = is_array($values) ? $values[$paramsNames[$i]] : $values;

				if (!is_scalar(self::$params[$paramsNames[$i]]) && self::$params[$paramsNames[$i]] !== null)
				{
					if($options & self::OPTION_NOT_NULL)
					{
						self::$errors[$paramsNames[$i]] = self::WRONG_VAL;
						continue;
					}

					throw new \Exception('Invalid type for the value of param: ' . $paramsNames[$i] . ' value: '
							. \json_encode(self::$params[$paramsNames[$i]]), \Error::WRONG_VARIABLE_VALUE);
				}

				self::$params[$paramsNames[$i]] = trim(self::$params[$paramsNames[$i]]);

				// Null value
				if(self::$params[$paramsNames[$i]] === null
						|| self::$params[$paramsNames[$i]] === ''
						|| self::$params[$paramsNames[$i]] === 'null'
						|| self::$params[$paramsNames[$i]] === 'NULL')
				{
					if($options & self::OPTION_NOT_NULL)
					{
						if($options & self::OPTION_DISPLAY_ERRORS)
						{
							self::$errors[$paramsNames[$i]] = self::REQUIRED_VAL;
						}
						else
						{
							throw new \Exception('$method["' . $paramsNames[$i] .'"] is null !', \Error::NULL_VALUE);
						}
					}
					else
					{
						self::$params[$paramsNames[$i]] = null;
					}

					continue;
				}

				// Filter by type
				\call_user_func_array($filterByTypeCallback,
						array($paramsNames[$i], is_array($min) ? $min[$i] : $min, is_array($max) ? $max[$i] : $max, $options));
			}
		}

		/**
		 * Display error if $paramName has a wrong value.
		 * @param  string $paramName Parameter name.
		 * @return string            HTML code to display the error.
		 */
		public static function error($paramName, $tag = '')
		{
			if (!isset(self::$errors[$paramName]))
			{
				return null;
			}

			return $tag ?
				'<' . $tag . ' class="error">' . \Page::_(self::$errors[$paramName]) . '</' . $tag . '>'
				: \Page::_(self::$errors[$paramName]);
		}

		/**
		 * Return the first error encountered in the form
		 * @return string The first error encountered or null
		 */
		public static function firstError()
		{
			if(self::isValid())
			{
				return null;
			}

			return current(self::$errors);
		}

		/**
		 * Prepare a test to know if session cookies are enabled
		 */
		public static function prepareSessionTest()
		{
			$_SESSION['cookies_enabled'] = true;
		}

		/**
		 * True if the isn't any errors in the form, false otherwise.
		 */
		public static function isValid()
		{
			return empty(self::$errors);
		}

		/**
		 * Get the form parameter $paramName if it has been specified and saved.
		 * @param  string $paramName Parameter name.
		 * @return mixed             Parameter value.
		 */
		public static function param($paramName, $protectHtml = false)
		{
			if(isset(self::$params[$paramName]))
			{
				return ($protectHtml) ? htmlspecialchars(self::$params[$paramName]) : self::$params[$paramName];
			}

			return null;
		}

		public static function getParams()
		{
			return self::$params;
		}

		public static function getErrors()
		{
			return self::$errors;
		}

		public static function getWarnings()
		{
			return self::$warnings;
		}

		public static function getConfirms()
		{
			return self::$confirms;
		}

		/**
		 * Add an error on $paramName parameter if no error was saved on it
		 * @param  string $paramName Parameter name
		 * @param  string $errorMsg  Error message
		 * @return boolean           True if the error has been saved or false if there was already an error with $paramName
		 */
		public static function addError($paramName, $errorMsg)
		{
			if(!isset(self::$errors[$paramName]))
			{
				self::$errors[$paramName] = $errorMsg;
				return true;
			}

			return false;
		}

		public static function resetError($paramName)
		{
			if(isset(self::$errors[$paramName]))
			{
				unset(self::$errors[$paramName]);
				return true;
			}

			return false;
		}


		public static function addConfirmation($confirmMsg)
		{
				self::$confirms[] = $confirmMsg;
				return true;
		}

		public static function addWarning($warningMsg)
		{
				self::$warnings[] = $warningMsg;
				return true;
		}

		/**
		 * Redirects to a page to display the result of processing the form as recommended by the design pattern PRG
		 * @param string $page Absolute url to display the result
		 */
		public static function displayResult($page)
		{
			if (!empty($_SESSION))
			{
				$_SESSION['form'] = array('confirms' => self::$confirms);
				header('Location: ' . $page, true, 303);
				exit();
			}

		}

		public static function displayWarning($page)
		{
			if (!empty($_SESSION))
			{
				$_SESSION['form'] = array('warnings' => self::$warnings);
				header('Location: ' . $page, true, 303);
				exit();
			}

		}

		/**
		 * Redirects to a page to display the errors occured while processing the form (as recommended by the design pattern PRG).
		 * Save the form errors and parameters in session variables.
		 * @param string $page Absolute url to display the result
		 */
		public static function displayErrors($page)
		{
			if (!empty($_SESSION))
			{
				$_SESSION['form'] = array('errors' => self::$errors, 'params' => self::$params);
				header('Location: ' . $page, true, 303);
				exit();
			}
		}

		/**
		 * Retrieve form data saved in order to display errors
		 * @return boolean True on success or false if there was no data to retrieve
		 */
		public static function retrieveErrorsAndParams()
		{
			if(isset($_SESSION['form']['confirms']))
			{
				self::$confirms = $_SESSION['form']['confirms'];
				unset($_SESSION['form']);
				return false;
			}
			if(isset($_SESSION['form']['warnings']))
			{
				self::$warnings = $_SESSION['form']['warnings'];
				unset($_SESSION['form']);
				return false;
			}
			if(!isset($_SESSION['form'])
					|| !isset($_SESSION['form']['errors'])
					|| !isset($_SESSION['form']['params']))
			{
				return false;
			}

			self::$errors = $_SESSION['form']['errors'];
			self::$params = $_SESSION['form']['params'];

			unset($_SESSION['form']);

			return true;
		}

		public static function simplifiedString($txt)
		{
			$transliterationTable = array('á' => 'a', 'Á' => 'A', 'à' => 'a', 'À' => 'A', 'ă' => 'a', 'Ă' => 'A', 'â' => 'a', 'Â' => 'A', 'å' => 'a', 'Å' => 'A', 'ã' => 'a', 'Ã' => 'A', 'ą' => 'a', 'Ą' => 'A', 'ā' => 'a', 'Ā' => 'A', 'ä' => 'ae', 'Ä' => 'AE', 'æ' => 'ae', 'Æ' => 'AE', 'ḃ' => 'b', 'Ḃ' => 'B', 'ć' => 'c', 'Ć' => 'C', 'ĉ' => 'c', 'Ĉ' => 'C', 'č' => 'c', 'Č' => 'C', 'ċ' => 'c', 'Ċ' => 'C', 'ç' => 'c', 'Ç' => 'C', 'ď' => 'd', 'Ď' => 'D', 'ḋ' => 'd', 'Ḋ' => 'D', 'đ' => 'd', 'Đ' => 'D', 'ð' => 'dh', 'Ð' => 'Dh', 'é' => 'e', 'É' => 'E', 'è' => 'e', 'È' => 'E', 'ĕ' => 'e', 'Ĕ' => 'E', 'ê' => 'e', 'Ê' => 'E', 'ě' => 'e', 'Ě' => 'E', 'ë' => 'e', 'Ë' => 'E', 'ė' => 'e', 'Ė' => 'E', 'ę' => 'e', 'Ę' => 'E', 'ē' => 'e', 'Ē' => 'E', 'ḟ' => 'f', 'Ḟ' => 'F', 'ƒ' => 'f', 'Ƒ' => 'F', 'ğ' => 'g', 'Ğ' => 'G', 'ĝ' => 'g', 'Ĝ' => 'G', 'ġ' => 'g', 'Ġ' => 'G', 'ģ' => 'g', 'Ģ' => 'G', 'ĥ' => 'h', 'Ĥ' => 'H', 'ħ' => 'h', 'Ħ' => 'H', 'í' => 'i', 'Í' => 'I', 'ì' => 'i', 'Ì' => 'I', 'î' => 'i', 'Î' => 'I', 'ï' => 'i', 'Ï' => 'I', 'ĩ' => 'i', 'Ĩ' => 'I', 'į' => 'i', 'Į' => 'I', 'ī' => 'i', 'Ī' => 'I', 'ĵ' => 'j', 'Ĵ' => 'J', 'ķ' => 'k', 'Ķ' => 'K', 'ĺ' => 'l', 'Ĺ' => 'L', 'ľ' => 'l', 'Ľ' => 'L', 'ļ' => 'l', 'Ļ' => 'L', 'ł' => 'l', 'Ł' => 'L', 'ṁ' => 'm', 'Ṁ' => 'M', 'ń' => 'n', 'Ń' => 'N', 'ň' => 'n', 'Ň' => 'N', 'ñ' => 'n', 'Ñ' => 'N', 'ņ' => 'n', 'Ņ' => 'N', 'ó' => 'o', 'Ó' => 'O', 'ò' => 'o', 'Ò' => 'O', 'ô' => 'o', 'Ô' => 'O', 'ő' => 'o', 'Ő' => 'O', 'õ' => 'o', 'Õ' => 'O', 'ø' => 'oe', 'Ø' => 'OE', 'ō' => 'o', 'Ō' => 'O', 'ơ' => 'o', 'Ơ' => 'O', 'ö' => 'oe', 'Ö' => 'OE', 'ṗ' => 'p', 'Ṗ' => 'P', 'ŕ' => 'r', 'Ŕ' => 'R', 'ř' => 'r', 'Ř' => 'R', 'ŗ' => 'r', 'Ŗ' => 'R', 'ś' => 's', 'Ś' => 'S', 'ŝ' => 's', 'Ŝ' => 'S', 'š' => 's', 'Š' => 'S', 'ṡ' => 's', 'Ṡ' => 'S', 'ş' => 's', 'Ş' => 'S', 'ș' => 's', 'Ș' => 'S', 'ß' => 'SS', 'ť' => 't', 'Ť' => 'T', 'ṫ' => 't', 'Ṫ' => 'T', 'ţ' => 't', 'Ţ' => 'T', 'ț' => 't', 'Ț' => 'T', 'ŧ' => 't', 'Ŧ' => 'T', 'ú' => 'u', 'Ú' => 'U', 'ù' => 'u', 'Ù' => 'U', 'ŭ' => 'u', 'Ŭ' => 'U', 'û' => 'u', 'Û' => 'U', 'ů' => 'u', 'Ů' => 'U', 'ű' => 'u', 'Ű' => 'U', 'ũ' => 'u', 'Ũ' => 'U', 'ų' => 'u', 'Ų' => 'U', 'ū' => 'u', 'Ū' => 'U', 'ư' => 'u', 'Ư' => 'U', 'ü' => 'ue', 'Ü' => 'UE', 'ẃ' => 'w', 'Ẃ' => 'W', 'ẁ' => 'w', 'Ẁ' => 'W', 'ŵ' => 'w', 'Ŵ' => 'W', 'ẅ' => 'w', 'Ẅ' => 'W', 'ý' => 'y', 'Ý' => 'Y', 'ỳ' => 'y', 'Ỳ' => 'Y', 'ŷ' => 'y', 'Ŷ' => 'Y', 'ÿ' => 'y', 'Ÿ' => 'Y', 'ź' => 'z', 'Ź' => 'Z', 'ž' => 'z', 'Ž' => 'Z', 'ż' => 'z', 'Ż' => 'Z', 'þ' => 'th', 'Þ' => 'Th', 'µ' => 'u', 'а' => 'a', 'А' => 'a', 'б' => 'b', 'Б' => 'b', 'в' => 'v', 'В' => 'v', 'г' => 'g', 'Г' => 'g', 'д' => 'd', 'Д' => 'd', 'е' => 'e', 'Е' => 'E', 'ё' => 'e', 'Ё' => 'E', 'ж' => 'zh', 'Ж' => 'zh', 'з' => 'z', 'З' => 'z', 'и' => 'i', 'И' => 'i', 'й' => 'j', 'Й' => 'j', 'к' => 'k', 'К' => 'k', 'л' => 'l', 'Л' => 'l', 'м' => 'm', 'М' => 'm', 'н' => 'n', 'Н' => 'n', 'о' => 'o', 'О' => 'o', 'п' => 'p', 'П' => 'p', 'р' => 'r', 'Р' => 'r', 'с' => 's', 'С' => 's', 'т' => 't', 'Т' => 't', 'у' => 'u', 'У' => 'u', 'ф' => 'f', 'Ф' => 'f', 'х' => 'h', 'Х' => 'h', 'ц' => 'c', 'Ц' => 'c', 'ч' => 'ch', 'Ч' => 'ch', 'ш' => 'sh', 'Ш' => 'sh', 'щ' => 'sch', 'Щ' => 'sch', 'ъ' => '', 'Ъ' => '', 'ы' => 'y', 'Ы' => 'y', 'ь' => '', 'Ь' => '', 'э' => 'e', 'Э' => 'e', 'ю' => 'ju', 'Ю' => 'ju', 'я' => 'ja', 'Я' => 'ja');
			return str_replace(array_keys($transliterationTable), array_values($transliterationTable), $txt);
		}
	}

?>
