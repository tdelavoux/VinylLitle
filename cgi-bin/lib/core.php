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
                $path .= strtolower($classname[$i]) . '/';
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

    try
    {
        \Application::configure();
        \Application::load();
    }
    catch(\Exception $e)
    {
        die(var_dump( $e));
        if (\in_array($e->getCode(), array(\Error::PAGE_NOT_FOUND, \Error::UNAUTHORIZED)))
        {
            \config\Configuration::$vars['error']['observers'] = \Log::STDOUT;
        }

        \Log::e($e);
    }

    if (!PHP_CLI_CGI)
    {
        ob_end_flush();
        \Application::executeBackendTriggers();
    }

?>
