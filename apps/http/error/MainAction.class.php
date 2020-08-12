<?php

    namespace apps\http\error;

    class MainAction
    {
        public static function execute($code)
        {
            switch($code){
                case 401 : 
                    $header = 'HTTP/1.1 401 Unauthorized';
                    $template = 'error401.template.php';
                    break;
                case 404 : 
                    $header = 'HTTP/1.1 404 Not Found';
                    $template = 'error404.template.php';
                    break;
                default:
                    $header = 'HTTP/1.1 500 Internal Server Error';
                    $template = 'error500.template.php';
                    break;
            }

            \header($header);
            \Page::set('title', $header);
            \Page::display($template, 'error');
        }
    }

?>
