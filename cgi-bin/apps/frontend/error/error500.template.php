<h2><?php echo \Page::get('errorTitle') ?></h2>
<p>Nous vous prions de bien vouloir nous excuser pour ce désagrément.</p>
<p>Un administrateur a été prévenu de ce problème et le corrigera dans les plus brefs délais.</p>
<p>Toutefois, si le problème persiste, veuillez nous contacter à l'adresse :
<a href="mailto:<?php echo \config\Configuration::$vars['email']['admin'] ?>"><?php echo \config\Configuration::$vars['email']['admin'] ?></a>.</p>
<p><a href="<?php echo Application::getPageUrl('index'); ?>">Retour à l'accueil</a></p>
