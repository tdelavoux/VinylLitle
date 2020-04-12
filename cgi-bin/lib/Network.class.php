<?php

	class Network
	{

		private static $ip = null;

		public static function checkIP($ip)
		{
			if (empty($ip) || !ip2long($ip))
			{
				return false;
			}

			$private_ips = array(
				array('0.0.0.0', '2.255.255.255'),
				array('10.0.0.0', '10.255.255.255'),
				array('127.0.0.0', '127.255.255.255'),
				array('169.254.0.0', '169.254.255.255'),
				array('172.16.0.0', '172.31.255.255'),
				array('192.0.2.0', '192.0.2.255'),
				array('192.168.0.0', '192.168.255.255'),
				array('255.255.255.0', '255.255.255.255')
			);

			foreach ($private_ips as $r)
			{
				$min = ip2long($r[0]);
				$max = ip2long($r[1]);

				if ((ip2long($ip) >= $min) && (ip2long($ip) <= $max))
				{
					return false;
				}
			}

			return true;
		}

		public static function getClientIP()
		{
			if (PHP_CLI_CGI)
			{
				return 'cli/cgi';
			}

			if (self::$ip !== null)
			{
				return self::$ip;
			}

			if (isset($_SERVER['HTTP_CLIENT_IP']) && self::checkIP($_SERVER['HTTP_CLIENT_IP']))
			{
				self::$ip = $_SERVER['HTTP_CLIENT_IP'];
				return self::$ip;
			}

			if (isset($_SERVER['HTTP_X_FORWARDED_FOR']))
			{
				foreach (explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']) as $ip)
				{
					if (self::checkIP(trim($ip)))
					{
						self::$ip = $ip;
						return self::$ip;
					}
				}
			}

			if (isset($_SERVER['HTTP_X_FORWARDED']) && self::checkIP($_SERVER['HTTP_X_FORWARDED']))
			{
				self::$ip = $_SERVER['HTTP_X_FORWARDED'];
				return self::$ip;
			}
			elseif (isset($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']) && self::checkIP($_SERVER['HTTP_X_CLUSTER_CLIENT_IP']))
			{
				self::$ip = $_SERVER['HTTP_X_CLUSTER_CLIENT_IP'];
				return self::$ip;
			}
			elseif (isset($_SERVER['HTTP_FORWARDED_FOR']) && self::checkIP($_SERVER['HTTP_FORWARDED_FOR']))
			{
				self::$ip = $_SERVER['HTTP_FORWARDED_FOR'];
				return self::$ip;
			}
			elseif (isset($_SERVER['HTTP_FORWARDED']) && self::checkIP($_SERVER['HTTP_FORWARDED']))
			{
				self::$ip = $_SERVER['HTTP_FORWARDED'];
				return self::$ip;
			}
			else
			{
				self::$ip = $_SERVER['REMOTE_ADDR'];
				return self::$ip;
			}
		}

		public static function requestAsync($url, array $params, $type = 'POST')
		{
			$post_params = array();

			foreach ($params as $key => $val)
			{
				if (\is_array($val))
				{
					$val = \implode(',', $val);
				}

				$post_params[] = $key . '=' . \urlencode($val);
			}

			$post_string = \implode('&', $post_params);
			$parts = \parse_url($url);
			$parts['host'] = (isset($parts['host']) ? $parts['host'] : \config\Configuration::$vars['server']['domain']);

			// Data goes in the path for a GET request
			if ('GET' == $type)
			{
				$parts['path'] .= '?' . $post_string;
			}

			$out = "$type " . $parts['path'] . " HTTP/1.1\r\n";
			$out .= "Host: " . $parts['host'] . "\r\n";
			$out .= "Content-Type: application/x-www-form-urlencoded\r\n";
			$out .= "Content-Length: " . \strlen($post_string) . "\r\n";
			$out .= "Connection: Close\r\n\r\n";

			// Data goes in the request body for a POST request
			if ('POST' == $type && isset($post_string))
			{
				$out .= $post_string;
			}

			$fp = \fsockopen($parts['host'], isset($parts['port']) ? $parts['port'] : 80, $errno, $errstr, 30);

			if($fp === false)
			{
				throw new \Exception('Unable to open socket on host: ' . $parts['host'], \Error::UNKNOWN_ERROR);
			}

			\fwrite($fp, $out);
			\fclose($fp);

			return true;
		}

	}

?>
