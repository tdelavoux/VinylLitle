<?php

    class User
    {
        private static $session_login = '';                 //$_SESSION utilisé par l'application
        private static $session_privileges= '_privileges'; //$_SESSION utilisé par l'application

        /* TODO Rajouter les profiles dont vous avez besoin. Modifier les valeurs ci-besoin */
        const PRIVILEGE_NONE= 0x00;
        const PRIVILEGE_ADMIN= 0x01;
        const PRIVILEGE_TECHNIQUE= 0x02;

        /* TODO Peupler ce tableau avec les profils que vous avez renseigner dans l'application Habilitation */
        private static $profilPrivileges = array(
            'ADMIN' => self::PRIVILEGE_ADMIN,
            'TECHNIQUE' => self::PRIVILEGE_TECHNIQUE
        );

        /*TODO Rajouter les informations que vous voulez stocker au moment de la connexion*/
        private static $login = '';
        private static $userPrivileges= null;

        public static function init()
        {
            /* TODO implémenter une vérification des privilèges*/

            if (isset($_SESSION) && isset($_SESSION[self::$session_login]))
            {
                self::$userPrivileges = $_SESSION[self::$session_privileges];
                self::$login = $_SESSION[self::$session_login];
            }
        }

        public static function setSessionVariables($login, $privilegesUser/*, TODO voir s il y a d autre variable*/)
        {
            $_SESSION[self::$session_privileges] = $privilegesUser;
            $_SESSION[self::$session_login] = $login;

            self::init();
        }

        public static function getLogin()
        {
            return self::$login;
        }

        public static function hasPrivilege($requestedPrivilege)
        {
            return isset(self::$userPrivileges) ?
                    (self::$userPrivileges & $requestedPrivilege) : 0;
        }

        public static function logout()
        {
            if (!PHP_CLI_CGI)
            {
                if (session_id())
                {
                    session_destroy();
                }

                session_start();
            }

            self::$userPrivileges = null;
            self::$login = '';
        }

        public static function retrieveUser($login)
        {
            self::init();
            self::setSessionVariables($login, self::PRIVILEGE_NONE);
            return true;
        }

        public static function verifSession($loginName = null)
        {
           
            if (!self::$login)
            {
                if(LOGIN_INTERFACE){
                    header('Location:'. \Application::getRoute('login', 'delog'));
                }else{
                    $login = array('user' => 'guest');
                    self::retrieveUser($login['user']);
                }
            }
        }
    }

?>
