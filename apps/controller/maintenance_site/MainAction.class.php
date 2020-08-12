<?php

    namespace apps\frontend\maintenance_site;

    class MainAction
    {
        public static function execute()
        {        
            \Page::set('title', 'Maintenance en Cours');
            \Page::display();
        }

    }

?>
