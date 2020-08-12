<?php

    /**
     * Auto load project classes. Namespaces (except files in cgi-bin/lib) must match with directories
     * or a statement must be added in this function to define the path associated with the given namespace.
     * @param $classname The class to be loaded
     */
    function app_autoload($classname)
    {
        
        $classname = explode('\\', $classname);

        $size = sizeof($classname) - 1;

        $path = __DIR__ . (($size) ? '/../' : '/');

        for($i = 0; $i < $size; $i++)
        {
                $path .= $classname[$i] . '/';
        }

        $path .= $classname[$size] . '.class.php';

        return require $path;
    }

    define ('PHP_CLI_CGI', (($sapi = substr(php_sapi_name(), 0, 3)) && $sapi === 'cgi' || $sapi === 'cli') || !isset($_SERVER['SERVER_NAME']));

    spl_autoload_register('app_autoload');

    if (!PHP_CLI_CGI)
    {
        ob_start();
        session_start();
    }

    //Découpe le chemin et exrait les arguments fournis
    $path = filter_input(INPUT_SERVER, 'REQUEST_URI');
    $subPath = str_replace('/VinylLitle/' , '', $path);
    $args = explode('/', $subPath);
    \Application::setModule($args[0] !== '' ? array_shift($args) : 'index');
    \Application::setArguments($args);
    \Router::follow($subPath);

    if (!PHP_CLI_CGI)
    {
        ob_end_flush();
    }

?>
