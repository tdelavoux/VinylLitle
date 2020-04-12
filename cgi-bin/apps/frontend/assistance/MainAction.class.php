<?php

	namespace apps\frontend\assistance;

	class MainAction
	{
		public static function execute()
		{
			$matriculeDeveloppeur = self::getDeveloppeurIni();
			if(!$matriculeDeveloppeur)
			{
				$matriculeDeveloppeur = self::getDeveloppeurSafran();
				$developpeur = self::setDeveloppeurSafran($matriculeDeveloppeur);
			}else{
				$developpeur = self::setDeveloppeurIni($matriculeDeveloppeur);
			}
			if($matriculeDeveloppeur)
			{
				$clickToTelDeveloppeur = self::getClickToCall($developpeur['matricule'], $developpeur['telephone']);
				\Page::set('developpeur', $developpeur);
				\Page::set('clickToTelDeveloppeur', $clickToTelDeveloppeur);
			}

			$matriculeGestionnaire = self::getGestionnaireIni();
			if(!$matriculeGestionnaire)
			{
				$matriculeGestionnaire = self::getGestionnaireSafran();
				$gestionnaire = self::setGestionnaireSafran($matriculeGestionnaire);
			}else{
				$gestionnaire = self::setGestionnaireIni($matriculeGestionnaire);
			}

			if($matriculeGestionnaire)
			{
				$clickToTelGestionnaire = self::getClickToCall($gestionnaire['matricule'], $gestionnaire['telephone']);
				\Page::set('gestionnaire', $gestionnaire);
				\Page::set('clickToTelGestionnaire', $clickToTelGestionnaire);
			}

			$retrive = \Form::retrieveErrorsAndParams();
			\Page::display('contact.template.php');
		}

		private static function getGestionnaireSafran()
		{
			try {
				$dns = \config\Configuration::get('safran_dsn', 'databases');

				$dbSaf = \Application::getDb(\config\Configuration::get('safran_dsn', 'databases'));
				$gestionnaireSaf = $dbSaf->data('safran\\APPLI')->getGestionnaire();
				$gestionnaireSaf = explode("_", $gestionnaireSaf);
				return $gestionnaireSaf;
			} catch (\Exception $exc) {
				return null;
			}
		}

		private static function setGestionnaireSafran($gestionnaireSaf)
		{
			$dbigl = \Application::getDb(\config\Configuration::get('igl_dsn', 'databases'));

			$emailGestionnaire = $dbigl->data('igl\\Ginvuti')->getEmailByMatricule($gestionnaireSaf[2]);
			$serviceGestionnaire = $dbigl->data('igl\\infoEds')->getName(substr($gestionnaireSaf[0],-5));
			$telGestionnaire = $dbigl->data('igl\\Ginvuti')->getTelephoneByMatricule($gestionnaireSaf[2]);

			$gestionnaire = array('service' => $serviceGestionnaire, 'matricule' => $gestionnaireSaf[2], 'nom' => $gestionnaireSaf[3], 'email' => $emailGestionnaire, 'telephone' => $telGestionnaire);

			return $gestionnaire;
		}

		private static function getGestionnaireIni()
		{
			if(\config\Configuration::$vars['contact']['matriculeGestionnaire']){
				\Form::addParams('matriculeGestionnaire', \config\Configuration::$vars['contact']['matriculeGestionnaire'], \Form::TYPE_STRING, 7,
					7, \Form::OPTION_NOT_NULL | \Form::OPTION_PARAM_REQUIRED);

				if(\Form::isValid()){
					return \config\Configuration::$vars['contact']['matriculeGestionnaire'];
				}else{
					die("Merci de remplir le parametre [contact][matriculeGestionnaire] dans le fichier default.ini avec un matricule sous 7 caractères !");
				}
			}else{
				return null;
			}
		}

		private static function setGestionnaireIni($matriculeGestionnaire)
		{
			$dbigl = \Application::getDb(\config\Configuration::get('igl_dsn', 'databases'));

			$nomGestionnaire = $dbigl->data('igl\\Ginvuti')->getNameByMatricule($matriculeGestionnaire);
			$emailGestionnaire = $dbigl->data('igl\\Ginvuti')->getEmailByMatricule($matriculeGestionnaire);
			$telGestionnaire = $dbigl->data('igl\\Ginvuti')->getTelephoneByMatricule($matriculeGestionnaire);
			$edsGestionnaire = $dbigl->data('igl\\Ginvuti')->getEDSByMatricule($matriculeGestionnaire);
			$serviceGestionnaire = $dbigl->data('igl\\InfoEds')->getName($edsGestionnaire);

			$gestionnaire = array('service' => $serviceGestionnaire, 'matricule' => $matriculeGestionnaire, 'nom' => $nomGestionnaire,
									'email' => $emailGestionnaire, 'telephone' => $telGestionnaire);

			return $gestionnaire;
		}

		private static function getDeveloppeurSafran()
		{
			try {
				$dns = \config\Configuration::get('safran_dsn', 'databases');

				$dbSaf = \Application::getDb(\config\Configuration::get('safran_dsn', 'databases'));
				$devActu = $dbSaf->data('safran\\APPLI')->getDeveloppeurActuel();
				return $devActu;
			} catch (\Exception $exc) {
				return null;
			}
		}

		private static function setDeveloppeurSafran($devActu)
		{
			$dbigl = \Application::getDb(\config\Configuration::get('igl_dsn', 'databases'));

			$emailDeveloppeur = $dbigl->data('igl\\Ginvuti')->getEmailByMatricule($devActu['CleValeur']);
			$telDeveloppeur = $dbigl->data('igl\\Ginvuti')->getTelephoneByMatricule($devActu['CleValeur']);

			$developpeur = array('service' => 'PERI INFORMATIQUE', 'matricule' => $devActu['CleValeur'], 'nom' => $devActu['Libelle'], 'email' => $emailDeveloppeur, 'telephone' => $telDeveloppeur);

			return $developpeur;
		}

		private static function getDeveloppeurIni()
		{
			if(\config\Configuration::$vars['contact']['matriculeTechnique']){
				\Form::addParams('matriculeTechnique', \config\Configuration::$vars['contact']['matriculeTechnique'], \Form::TYPE_STRING, 7,
					7, \Form::OPTION_NOT_NULL | \Form::OPTION_PARAM_REQUIRED);

				if(\Form::isValid()){
					return \config\Configuration::$vars['contact']['matriculeTechnique'];
				}else{
					die("Merci de remplir le parametre [contact][matriculeTechnique] dans le fichier default.ini avec un matricule sous 7 caractères !");
				}
			}else{
				return null;
			}
		}

		private static function setDeveloppeurIni($matriculeDev)
		{
			$dbigl = \Application::getDb(\config\Configuration::get('igl_dsn', 'databases'));

			$nomDeveloppeur = $dbigl->data('igl\\Ginvuti')->getNameByMatricule($matriculeDev);
			$emailDeveloppeur = $dbigl->data('igl\\Ginvuti')->getEmailByMatricule($matriculeDev);
			$telDeveloppeur = $dbigl->data('igl\\Ginvuti')->getTelephoneByMatricule($matriculeDev);
			$edsDeveloppeur = $dbigl->data('igl\\Ginvuti')->getEDSByMatricule($matriculeDev);
			$serviceDeveloppeur = $dbigl->data('igl\\InfoEds')->getName($edsDeveloppeur);

			$developpeur = array('service' => $serviceDeveloppeur, 'matricule' => $matriculeDev, 'nom' => $nomDeveloppeur,
									'email' => $emailDeveloppeur, 'telephone' => $telDeveloppeur);

			return $developpeur;
		}

		public static function getClickToCall($matricule, $telephone)
		{
			return $lien = "https://crse.collab.ca-technologies.credit-agricole.fr/_layouts/15/CRSE.Common/ASHX/ClicToCallProxy.ashx?action=clickToCall&calleePhoneNumber=" . $telephone . "&dn=uid=CR%20825-" . $matricule . ",ou=People,o=CR%20825,o=credit%20agricole&_=" . time() . "000";
		}

		public static function verifMail()
		{
			\Form::addParams('objet', $_POST, \Form::TYPE_STRING, 1,
					500, \Form::OPTION_DISPLAY_ERRORS | \Form::OPTION_NOT_NULL | \Form::OPTION_PARAM_REQUIRED);
			\Form::addParams('message', $_POST, \Form::TYPE_STRING, 1,
					5000, \Form::OPTION_DISPLAY_ERRORS);
			\Form::addParams('demandeur', \User::getLogin(), \Form::TYPE_STRING, 7,
					7, \Form::OPTION_DISPLAY_ERRORS | \Form::OPTION_NOT_NULL | \Form::OPTION_PARAM_REQUIRED);
			\Form::addParams('fichierAdd', \basename($_FILES['fichier']['name']), \Form::TYPE_STRING, 0,
					500, \Form::OPTION_DISPLAY_ERRORS);

			if(\Form::param('message')==''){
				\Form::addError('message', 'Le champ message doit être completé !');
			}

			if(strlen(\Form::param('message'))<20){
				\Form::addError('message', 'Le champ message doit contenir au moins 20 caractères ! Expliquez mieux votre problème !');
			}

			$dir = $_SERVER['CONTEXT_DOCUMENT_ROOT'].\config\Configuration::get('dir', 'application') . 'upload/mail/' . \Form::param('fichierAdd');

			if (move_uploaded_file($_FILES['fichier']['tmp_name'], $dir)) {

				\Form::addParams('fichier', $dir, \Form::TYPE_STRING, 0,
					500, \Form::OPTION_DISPLAY_ERRORS);

				if(\Form::isValid())
				{
					self::envoiMail(\Form::param('objet'), \Form::param('message'), \Form::param('demandeur'), \Form::param('fichier'));
					\Form::addConfirmation('Le message a bien été envoyé !');
					\Form::displayResult(\Application::getRoute('assistance', 'index'));
				}else{
					\Form::displayErrors(\Application::getRoute('assistance', 'index'));
				}
			}else{
				\Form::addError('fichier', 'Le fichier n\'est pas valide !');
				\Form::displayErrors(\Application::getRoute('assistance', 'index'));
			}
		}

		public static function envoiMail($objetDemandeur, $messageDemandeur, $demandeur, $fichier)
		{
			$matriculeDeveloppeur = self::getDeveloppeurIni();
			if(!$matriculeDeveloppeur)
			{
				$matriculeDeveloppeur = self::getDeveloppeurSafran();
				$developpeur = self::setDeveloppeurSafran($matriculeDeveloppeur);
			}else{
				$developpeur = self::setDeveloppeurIni($matriculeDeveloppeur);
			}

			$matriculeGestionnaire = self::getGestionnaireIni();
			if(!$matriculeGestionnaire)
			{
				$matriculeGestionnaire = self::getGestionnaireSafran();
				$gestionnaire = self::setGestionnaireSafran($matriculeGestionnaire);
			}else{
				$gestionnaire = self::setGestionnaireIni($matriculeGestionnaire);
			}

			$mail = new \AppMail();
			$mail->setTransmitter(\config\Configuration::$vars['email']['noreply']);

			$dbigl = \Application::getDb(\config\Configuration::get('igl_dsn', 'databases'));
			$nomDemandeur = $dbigl->data('igl\\Ginvuti')->getNameByMatricule($demandeur);

			if($objetDemandeur=='Assistance'){
				$objet = \config\Configuration::$vars['application']['name'] . " : Demande d'assistance sur l'outil";
				$message = 'Bonjour,</br>'
				.	'Ceci est un mail automatique pour vous informer que l\'utilisateur ' . $nomDemandeur . ' (' . $demandeur . ') demande une assistance sur l\'outil <b>"' . \config\Configuration::$vars['application']['name'] . '"</b>.</br>'
				.	'Voici son message :</br></br>'
				.	$messageDemandeur
				.	'</br></br>'
				.	'Merci de prendre en compte sa demande et de lui indiquer la marche à suivre.';

				$mail->setReceiver($gestionnaire['email']);
			}elseif($objetDemandeur=='Technique'){
				$objet = \config\Configuration::$vars['application']['name'] . " : Demande d'assistance technique sur l'outil";
				$message = 'Bonjour,</br>'
				.	'Ceci est un mail automatique pour vous informer que l\'utilisateur ' . $nomDemandeur . ' (' . $demandeur . ') demande une assistance technique sur l\'outil <b>"' . \config\Configuration::$vars['application']['name'] . '"</b>.</br>'
				.	'Voici son message :</br></br>'
				.	$messageDemandeur
				.	'</br></br>'
				.	'Merci de prendre en compte sa demande et de lui indiquer la marche à suivre.';

				$mail->setReceiver($developpeur['email']);
			}elseif($objetDemandeur=='Amelioration'){
				$objet = \config\Configuration::$vars['application']['name'] . " : Proposition d'amélioration de l'outil";
				$message = 'Bonjour,</br>'
				.	'Ceci est un mail automatique pour vous informer que l\'utilisateur ' . $nomDemandeur . ' (' . $demandeur . ') souhaite proposer une amélioration sur l\'outil <b>"' . \config\Configuration::$vars['application']['name'] . '"</b>.</br>'
				.	'Voici son message :</br></br>'
				.	$messageDemandeur
				.	'</br></br>'
				.	'Merci de l\'informer de l\'étude et du résultat sur sa proposition.';

				$mail->setReceiver(\config\Configuration::$vars['email']['admin']);
			}

			$mail->addAttachment($fichier);
			$mail->setSubject($objet);
			$mail->setTextMessage($message);
			$mail->send();
		}
	}

?>
