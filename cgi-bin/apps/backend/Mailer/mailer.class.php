<?php

    namespace apps\backend\Mailer;

    class mailer
    {
        
        public static function execute()
        {
            \Form::retrieveErrorsAndParams();
            \Page::set('title', 'Index');

            // Catched By mailtrap
            $mail = new \AppMail();
            $mail->setReceiver('test@gmail.com');
            $mail->setSubject('test');
            $mail->setTextMessage('test');
            $mail->send();

            \Page::display();
        }

    }
?>