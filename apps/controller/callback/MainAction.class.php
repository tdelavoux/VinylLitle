<?php

    namespace apps\controller\callback;

    class MainAction
    {
        public static function execute()
        {
            die('callback');
        }

        public static function execute2()
        {
            die('callback2');
        }

        public static function execute3($id)
        {
            die('callback3 ' . $id);
        }

        public static function execute4($id, $id2)
        {
            die('callback3 ' . $id . ' ' . $id2);
        }
    }

?>
