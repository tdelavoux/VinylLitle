<?php

    namespace apps\http\callback;

    class MainAction
    {
        public static function execute()
        { 
            die('ex');
            \Page::display();
        }

        public static function execute2()
        {
            die('ex2');
            \Page::display();
        }

        public static function execute3($id)
        {
            die('ex3');
            \Page::display();
        }

        public static function execute4($id, $id2)
        {
            die('ex4');
            \Page::display();
        }
    }

?>
