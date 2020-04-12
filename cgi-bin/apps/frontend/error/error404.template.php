<h2><?php echo \Page::get('errorTitle') ?></h2>
<p>La page que vous avez demandé n'existe pas (ou plus).</p>
<p>Si vous pensez qu'il s'agit d'une erreur, veuillez nous contacter à l'adresse :
<a href="mailto:<?php echo \config\Configuration::$vars['email']['admin'] ?>"><?php echo \config\Configuration::$vars['email']['admin'] ?></a>.</p>
<p><a href="<?php echo Application::getPageUrl('index'); ?>">Retour à l'accueil</a></p>
