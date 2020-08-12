<!doctype html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<title> <?php echo strip_tags(\Page::get('title')); ?></title>
		<link rel="stylesheet" type="text/css" href="<?php echo \Application::getEnv('DIR'); ?>css/main.css">
	</head>
	<body>
		

			TEST DU BOUZIN
			
		<div id="contentPage">
			<?php require $template; ?>
		</div>

	</body>
</html>
