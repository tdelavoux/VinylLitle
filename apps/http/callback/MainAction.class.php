<?php

    namespace apps\http\callback;

    class MainAction
    {
        public static function execute()
        {
           \Page::display();
        }

        public static function execute2()
        {
            \Page::display();
        }

        public static function execute3($id)
        {
            \Page::display();
        }

        public static function execute4($id, $id2)
        {
            \Page::display();
        }
    }

?>
