<?php

class Application
{

    /**
     * Module en cours d'utilisation
     */
    private static $module;

    /**
     * Arguments données à l'url
     */
    private static $arguments;

    /**
     * route acctuelle 
     */
    private static $currentRoute;

    /**
     * Nom de la route acctuelle
     */
    private static $currentRouteName;

    /* #########################################################################
                            GETTERS AND SETTERS
    ############################################################################*/
    public static function getModule(){return self::$module;}
    public static function setModule($module){self::$module = $module;}

    public static function getArguments(){return self::$arguments;}
    public static function setArguments($arguments){self::$arguments = $arguments;}

    public static function getCurrentRoute(){return self::$currentRoute;}
    public static function setCurrentRoute($currentRoute){self::$currentRoute = $currentRoute;}

    public static function getCurrentRouteName(){return self::$currentRouteName;}
    public static function setCurrentRouteName($currentRouteName){self::$currentRouteName = $currentRouteName;}

    /**
     * Initialise les données de l'application en provenance du fichier d'environnement
     */
    public static function init(){
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
        $dotenv->load();
    }

    /**
     * Retourne la valeur d'une donnée d'env
     */
    public static function getEnv($index){
        return isset($_ENV[$index]) ?  $_ENV[$index] : null;
    }

    /**
     * Retourne la route d'un controller selon son nom
     */
    public static function getRoute($module = 'index', $name = ''){
        $routeClass = \Router::LOCATE . $module. '\\Route';
        foreach($routeClass::$routes as $route){
            if($route['name'] === $name) 
                return self::getEnv('DIR') . $module. '/'. $route['pattern'];
        }
        return self::getEnv('DIR');
    }


}