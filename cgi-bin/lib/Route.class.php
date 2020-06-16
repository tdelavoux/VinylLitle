<?php

    class Route
    {
        protected static $routes = array(
            'index' => array(
                'pattern' => '',
                'controller' => 'MainAction::execute'
            )
        );

        public static function follow($namespace)
    {
            if (!isset($_GET['params']))
            {
                global $argv;

                if (isset($argv[3]))
                {
                    $_GET['params'] = $argv[3];
                }
                else
                {
                    $_GET['params'] = '';
                }
                }

                $routeClass = $namespace . 'Route';

                foreach ($routeClass::$routes as $routeName => $route)
            {
                if (
                    \preg_match(
                        '/^'
                        . \str_replace(
                            array('{', '}', '/'),
                            array('(?P<', '>.*)', '\/'),
                            $route['pattern']
                        )
                        . '$/',
                        $_GET['params'],
                        $matches
                    ) === 1)
                {
                    $args = array();

                    foreach ($matches as $key => $match)
                    {
                        if (\is_int($key) && $key > 0)
                        {
                            $args[] = $match;
                        }
                    }

                    \Application::setCurrentRoute($routeName);
                    if(!\in_array(\Application::getModule(), array('login','error', 'maintenance_site')))
                    {
                        \User::verifSession(\config\Security::getRequiredPrivileges(\Application::getModule(), \Application::getCurrentRoute()));
                    }
                    \call_user_func_array($namespace . $route['controller'], $args);

                    return;
                }
            }

            throw new \Exception('The URL doesn\'t match any pattern!', \Error::PAGE_NOT_FOUND);
        }

        public static function getRoute($namespace, $routeName, $args)
        {
            $routeClass = $namespace . 'Route';

            if (!isset($routeClass::$routes[$routeName]['pattern']))
            {
                throw new \Exception('Route not found:' . $routeName);
            }

            if (empty($args))
            {
                return $routeClass::$routes[$routeName]['pattern'];
            }

            return \preg_replace(\array_fill(0, count($args), '/({\w+})/'),
                $args,
                $routeClass::$routes[$routeName]['pattern'],
                1);
        }
    }

?>
