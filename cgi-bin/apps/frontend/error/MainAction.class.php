<?php

	namespace apps\frontend\error;

	class MainAction
	{
		public static function execute()
		{
			\Page::set('errorTitle', 'Oups, un problème est survenu...');
			$header = 'HTTP/1.1 500 Internal Server Error';
			$template = 'error500.template.php';

			if (isset($_GET['params']) && $_GET['params'])
			{
				if (\strpos($_GET['params'], (string)\Error::UNAUTHORIZED) !== false)
				{
					$header = 'HTTP/1.1 401 Unauthorized';
					\Page::set('errorTitle', 'Accès refusé !');
					$template = 'error401.template.php';
				}
				elseif (\strpos($_GET['params'], (string)\Error::PAGE_NOT_FOUND) !== false)
				{
					$header = 'HTTP/1.1 404 Not Found';
					\Page::set('errorTitle', 'Page introuvable !');
					$template = 'error404.template.php';
				}
			}

			\header($header);
			\Page::set('title', $header);
			\Page::display($template);
		}
	}

?>
