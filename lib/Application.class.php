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


    /* #########################################################################
                            GETTERS AND SETTERS
    ############################################################################*/
    public static function getModule(){return self::$module;}
    public static function setModule($module){self::$module = $module;}

    public static function getArguments(){return self::$arguments;}
    public static function setArguments($arguments){self::$arguments = $arguments;}

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


}