<!doctype html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="x-ua-compatible" content="ie=edge">
		<title><?php echo \config\Configuration::$vars['application']['name']; if (\Page::get('title')): ?> - <?php echo strip_tags(\Page::get('title')); endif ?></title>
		<meta name="viewport"  content="width=device-width, initial-scale=1">
                <link rel="icon" type="image/png" href="<?php echo \config\Configuration::$vars['application']['dirLib']; ?>/favicon.png">
		<link rel="stylesheet" type="text/css" href="<?php echo \config\Configuration::$vars['application']['dirLib']; ?>fontawesome-5.13.0/css/all.css">
		<link rel="stylesheet" type="text/css" href="<?php echo \config\Configuration::$vars['application']['dirLib']; ?>bootstrap-4.4.1/css/bootstrap.min.css">
		<link rel="stylesheet" type="text/css" href="<?php echo \config\Configuration::$vars['application']['dirLib']; ?>css/datatables.min.css">
		<link rel="stylesheet" type="text/css" href="<?php echo \config\Configuration::$vars['application']['dir']; ?>css/main.css">
                <?php if(LOGIN_INTERFACE) : ?>
                    <link rel="stylesheet" type="text/css" href="<?php echo \config\Configuration::$vars['application']['dir']; ?>css/login.css">
                <?php endif; ?>
		<?php
			if (\Page::get('style')):
		?>
		<style type="text/css"><?php echo \Page::get('style'); ?></style>
		<?php
			endif;
		?>
		</head>
	<body>
		

		<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
			<a class="navbar-brand" href="<?php echo \Application::getRoute('index', 'index'); ?>"><?php echo \config\Configuration::$vars['application']['name'] ?></a>
			<button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
			    <span class="navbar-toggler-icon"></span>
			</button>

			<div class="collapse navbar-collapse" id="navbarSupportedContent">
			    <ul class="navbar-nav mr-auto">
			      	<li class="nav-item active">
			       		<a class="nav-link" href="#">Home <span class="sr-only">(current)</span></a>
			      	</li>
			      	<li class="nav-item">
			        	<a class="nav-link" href="#">Link</a>
			      	</li>
			      	<li class="nav-item dropdown">
			        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
			          Dropdown
			        </a>
			        <div class="dropdown-menu" aria-labelledby="navbarDropdown">
			          <a class="dropdown-item" href="#">Action</a>
			          <a class="dropdown-item" href="#">Another action</a>
			          <div class="dropdown-divider"></div>
			          <a class="dropdown-item" href="#">Something else here</a>
			        </div>
			      	</li>
			      	<li class="nav-item">
			        	<a class="nav-link disabled" href="#" tabindex="-1" aria-disabled="true">Disabled</a>
			      	</li>
			    </ul>
			    
                            <?php if(LOGIN_INTERFACE) : ?>
                                <div class="form-inline my-2 my-lg-0">
                                     <a class="btn btn-outline-success my-2 my-sm-0" type="submit" href="<?php echo \Application::getRoute('login', 'delog'); ?>"><?php echo \User::getLogin() ? \User::getLogin() : 'Login'; ?> </a>
                                </div> 
                            <?php endif; ?>
			</div>
		</nav>


		<script src="<?php echo \config\Configuration::$vars['application']['dirLib']; ?>jquery/jquery-3.5.0.min.js"></script>
		<script src="<?php echo \config\Configuration::$vars['application']['dirLib']; ?>bootstrap-4.4.1/js/bootstrap.min.js"></script>
		<script type="text/javascript" src="<?php echo \config\Configuration::$vars['application']['dirLib']; ?>js/datatables.min.js"></script>
		<div id="contentPage">
			<?php require $template; ?>
		</div>
		<script>
			<?php echo \Page::get('bottomScript'); ?>
		</script>
	</body>
</html>
