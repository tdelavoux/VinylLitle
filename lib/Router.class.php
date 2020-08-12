<?php

class Router
{
    const LOCATE = "\\apps\\controller\\";

    public static function follow($path){

        //Découpe le chemin et exrait les arguments fournis
        $subPath = str_replace('/VinylLitle/' , '', $path);
        $args = explode('/', $subPath);
        $target = array_shift($args);

        // Pour chaque pattern de route défini dans le dossier visé, 
        $routeClass = self::LOCATE . $target . '\\Route';
        foreach($routeClass::$routes as $routePattern => $controler){

            $values = array();
            $patternControl = explode('/', $routePattern); 
            end($args) === '' && count($args) > 1 && array_pop($args);
            if(count($patternControl) !== count($args)){continue;}

            $patternMattching = true;
            foreach($patternControl as $key => $val){ 
                if(!isset($args[$key]) || (preg_match('/{(.*)}/', $val) === 0 &&  $val !== $args[$key])){ $patternMattching = false;} 
                preg_match('/{(.*)}/', $val) && $values[] = $args[$key];
            }
            $patternMattching && \call_user_func_array( self::LOCATE  . $target . '\\'. $controler, $values);
        }
        throw new \ExceptionPage('The URL doesn\'t match any pattern!', 404);
    }
}

?>
