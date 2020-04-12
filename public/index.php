<?php
if (strpos($_SERVER['HTTP_USER_AGENT'], 'Chrome') === FALSE &&!PHP_CLI_CGI)
{?>
<h1>Merci de patienter, nous allons lancer votre application dans Google Chrome</h1>
<?php
require_once('./lancement_chrome.php');
exit();
}?>
<?php
	header('Content-type: text/html; charset=UTF-8');
	const PRODUCTION = false;
	const CGI_DIR = '../cgi-bin/';


	require __DIR__ . '/' . CGI_DIR . 'lib/core.php';
?>
