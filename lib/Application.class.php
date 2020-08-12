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
     * Tableau des variables de l'application
     */
    private static $vars = array();


    /* #########################################################################
                            GETTERS AND SETTERS
    ############################################################################*/
    public static function getModule(){return self::$module;}
    public static function setModule($module){self::$module = $module;}

    public static function getArguments(){return self::$arguments;}
    public static function setArguments($arguments){self::$arguments = $arguments;}

    /**
     * Initialise les données de l'application en provenance des fichiers init
     */
    public static function init(){
        self::$vars = \array_replace_recursive(self::$vars, \parse_ini_file(__DIR__ . '/../config/default.ini', true));

        if (!PRODUCTION)
        {
            self::$vars = \array_replace_recursive(self::$vars, \parse_ini_file(__DIR__ . '/../config/dev.ini', true));
        }
    }

    /**
     * Retourne la valeur d'une variable de configuration 
     */
    public static function getConf($index, $name){
        return isset(self::$vars[$index][$name]) ? self::$vars[$index][$name] : null;
    }


}