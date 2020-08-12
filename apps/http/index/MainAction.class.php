<?php

    namespace apps\http\index;

    class MainAction
    {
        public static function execute()
        {
            die(\Application::getConf('application', 'name'));
        }
    }

?>
