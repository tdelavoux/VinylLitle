<?php

	namespace apps\frontend\index;

	class MainAction
	{
            public static function execute()
            {
                \Form::retrieveErrorsAndParams();
                \Page::set('title', 'Index');
                
//                $users = \Application::getDb(\config\Configuration::get('magic_dsn', 'databases'))
//                        ->data('magic_handler\\user')->getAll();
               

                \Page::display();
            }
	}

?>
