<?php

	require_once('./ressource/PHPMailer-5.2.1/class.phpmailer.php');
	class AppMail
	{
		const WRAP_LEN = 400;

		protected $phpMailer;
		private $citation;
		private $citationAuthor;
		private $citationDate;

		public function __construct()
		{
			$this->phpMailer = new PHPMailer(true);
			$this->phpMailer->CharSet = 'utf-8';
			$this->phpMailer->citation = null;
                        
                        // TODO Configure with SMTP Datas, else, catched by mailtrap
                        $this->phpMailer->Host = "smtp.mailtrap.io";
                        $this->phpMailer->SMTPAuth = true;
                        $this->phpMailer->Username = "84f688e5292056";
                        $this->phpMailer->Password = "6a24a6d189c503";
                        $this->phpMailer->SMTPSecure = 'tls';
                        $this->phpMailer->Port  = 2525;
                        $this->phpMailer->setFrom('info@mailtrap.io', 'Mailtrap');
                            
                            
			$this->phpMailer->citationAuthor = null;
			$this->phpMailer->citationDate = null;
			$this->phpMailer->IsSmtp();
			$this->phpMailer->IsHTML(true);
		}

		public function setTransmitter($transmitter, $transmitterName = '')
		{
			$this->phpMailer->SetFrom($transmitter, $transmitterName);
  			$this->phpMailer->AddReplyTo($transmitter, $transmitterName);
		}

		public function setReceiver($receiver, $receiverName = '')
		{
			$this->phpMailer->AddAddress($receiver, $receiverName);
		}

		public function setReceiverCC($receiver, $receiverName = '')
		{
			$this->phpMailer->AddCC($receiver, $receiverName);
		}

		public function setReceiverCCi($receiver, $receiverName = '')
		{
			$this->phpMailer->AddBCC($receiver, $receiverName);
		}
		
		public function setPriority($priority)
		{
			// 1 = High, 2 = Medium, 3 = Low
			$this->phpMailer->Priority = $priority;
		}

		public function clearReceivers()
		{
			$this->phpMailer->ClearAddresses();
		}

		public function clearReceiversCC()
		{
			$this->phpMailer->ClearCCs();
		}

		public function clearReceiversCCi()
		{
			$this->phpMailer->ClearBCCs();
		}

		/**
		 * Send email separately to each email adresses in the given list
		 * @param array $receiverList 	List of receivers, separate by a ";"
		 */
		public function sendSeparatelyToReceiverList($receiverList)
		{
			$receiverList = explode(',', $receiverList);

			foreach($receiverList as $to)
			{
				$this->phpMailer->ClearAddresses();
				$this->phpMailer->AddAddress(trim($to), '');
				\Log::i(trim($to));
				$this->phpMailer->Send();
			}
		}

		public function setSubject($subject)
		{
			$this->phpMailer->Subject = $subject;
		}

		public function setCitation($citation, $author, $date)
		{
			$this->citation = $citation;
			$this->citationAuthor = $author;
			$this->citationDate = $date;
		}

		public function citationToString()
		{
			if(!$this->citation || !$this->citationAuthor || !$this->citationDate)
			{
				return '';
			}

			return "\n\n"
				. 'Le '
				. $this->citationDate . ', ' . $this->citationAuthor . ' a écrit :'
				. \preg_replace("/\n/", "\n> ", "\n" . $this->citation);
		}

		public function setTextMessage($message)
		{
			$this->phpMailer->Body = \wordwrap(
				$message
				. $this->citationToString()
				. "\n\n",
				self::WRAP_LEN
			);
		}

		public function setAltMessage($altBody)
		{
			$this->phpMailer->AltBody = strip_tags($altBody);
		}

		public function setHtmlMessage($msgHtml)
		{
			$this->phpMailer->MsgHTML(
				'<html>'
					. '<head></head>'
					. '<body>'
						. '<table cellpadding="0" cellspacing="0" style="font-family:Arial,Geneva,sans-serif;font-size:14px;color:#2B343D;width:80%;min-width:500px;margin:20px auto;">'
							. '<tr><td style="padding:20px 40px;border:1px solid #ccc">' . nl2br($msgHtml) . '</td></tr>'
						. '</table>'
					. '</body>'
				. '</html>'
			);
		}

		public function send()
		{
			if(!PRODUCTION)
			{
				list($destinataire, $copie, $copieCache) = $this->phpMailer->getAllRecipients();
				if(!empty($destinataire))
				{
					$this->phpMailer->Body .=  '</br></br>Destinataire : '. \implode(',</br>  ', \array_map(function($dest){return $dest[0];},$destinataire));
				}
				if(!empty($copie))
				{
					$this->phpMailer->Body  .=  '</br></br>Destinataire copie : '. \implode(',</br>  ', \array_map(function($dest){return $dest[0];},$copie));
				}
				if(!empty($copieCache))
				{
					$this->phpMailer->Body  .=  '</br></br>Destinataire copie caché : '. \implode(',</br> ', \array_map(function($dest){return $dest[0];},$copieCache));
				}

				$this->phpMailer->ClearAllRecipients();
				$this->phpMailer->AddAddress(\config\Configuration::$vars['email']['admin'], '');

			}

			return $this->phpMailer->Send();
		}

		public function addAttachment($path, $name = '', $encoding = 'base64', $type = 'application/octet-stream')
		{
			$this->phpMailer->AddAttachment($path, $name, $encoding, $type);
		}

		public function addStringAttachment($string, $filename, $encoding = 'base64')
		{
			$this->phpMailer->AddStringAttachment($string, $filename, $encoding);
		}
	}

?>
