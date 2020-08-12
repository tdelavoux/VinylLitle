<?php

	class Db
	{
		private $database;
		private $data = array();

		public function __construct($dsn = null, $user = null, $password = null, $setUTF8 = false)
		{
			if($user === null)
			{
				$user = \config\Configuration::get('user', 'databases');
			}

			if($password === null)
			{
				$password = \config\Configuration::get('password', 'databases');
			}

			try
			{

				$this->database = new \PDO($dsn, $user, $password);

		   }
		   catch(PDOException $e)
		   {
			   echo var_dump($e->getMessage()) . ' json : '.json_encode(array($dsn, $user, $password));
		   }

			$this->database->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

			if($setUTF8)
			{
				$this->database->exec('SET CHARACTER SET utf8');
			}
		}

		public function getDb()
		{
			return $this->database;
		}

		/**
		 * Autoload data classes and instanciate data objects. Then, get the required data object.
		 * @param	string		$dataname		Name of the data class
		 * @return								Instance of the required data class
		 */
		public function data($dataname)
		{
			$dataname = 'data\\' . $dataname;

			if(!array_key_exists($dataname, $this->data))
			{
				$this->data[$dataname] = new $dataname($this->database);
			}

			return $this->data[$dataname];
		}

		/**
		 * @see	PDO
		 * @return integer Total number of records found for the last query with CALC_FOUND_ROWS
		 */
		public function sql_num_rows()
		{
			$statement = $this->database->query('SELECT FOUND_ROWS() as nb');
			return $statement->fetchColumn();
		}

		/**
		 * @see PDO::beginTransaction()
		 * @return boolean True on success
		 */
		public function beginTransaction()
		{
			return $this->database->beginTransaction();
		}

		/**
		 * @see PDO::commit()
		 * @return boolean True on success
		 */
		public function commit()
		{
			return $this->database->commit();
		}

		/**
		 * @see PDO::rollBack()
		 * @return boolean True on success
		 */
		public function rollBack()
		{
			return $this->database->rollBack();
		}

		public static function encode($string)
		{
			if($string !== null)
			{
					return \iconv(\config\Configuration::TRADUCTION_ENCODING, \config\Configuration::get('encoding', 'databases') . '//TRANSLIT', $string);
			}
			return null;
		}

		public static function decode($string)
		{
			return \iconv(\config\Configuration::get('encoding', 'databases'), \config\Configuration::TRADUCTION_ENCODING . '//TRANSLIT', $string);
		}

		public static function decodeRecursive($variable)
		{
			if (\is_array($variable) && !empty($variable))
			{
				foreach ($variable as $key => $value)
				{
					$variable[$key] = self::decodeRecursive($value);
				}
			}
			elseif (\is_scalar($variable))
			{
				$variable = self::decode($variable);
			}

			return $variable;
		}

		public static function encodeRecursive($variable)
		{
			if (\is_array($variable) && !empty($variable))
			{
				foreach ($variable as $key => $value)
				{
					$variable[$key] = self::encodeRecursive($value);
				}
			}
			elseif (\is_scalar($variable))
			{
				$variable = self::encode($variable);
			}

			return $variable;
		}
	}

?>
