<?php

    namespace apps\frontend\index;

    class MainAction
    {
        public static function execute()
        {
            \Form::retrieveErrorsAndParams();
            \Page::set('title', 'Index');

            die('index');
            \Page::display();
        }
    }

?>
