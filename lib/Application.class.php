<?php

class Application
{
    /**
     * Module en cours d'utilisation
     */
    private static $module;

    private static $arguments;

    /* #########################################################################
                            GETTERS AND SETTERS
    ############################################################################*/
    public static function getModule(){return self::$module;}
    public static function setModule($module){self::$module = $module;}

    public static function getArguments(){return self::$arguments;}
    public static function setArguments($arguments){self::$arguments = $arguments;}


}