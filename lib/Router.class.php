<?php

class Router
{
    const LOCATE = "\\apps\\http\\";

    public static function follow($path){
        
        $args = \Application::getArguments();
        // Pour chaque pattern de route défini dans le dossier visé, 
        $routeClass = self::LOCATE . \Application::getModule() . '\\Route';
        if(self::routeExist($routeClass)){
            foreach($routeClass::$routes as $route){
               
                $values = array();
                $patternControl = explode('/', $route['pattern']); 
                end($args) === '' && count($args) > 1 && array_pop($args);
                if(count($args) !== 0 && count($patternControl) !== count($args)){continue;}
                
                $patternMattching = true;
                foreach($patternControl as $key => $val){ 
                    
                    if(!isset($args[$key]) || (preg_match('/{(.*)}/', $val) === 0 &&  $val !== $args[$key])){ $patternMattching = false;} 
                    preg_match('/{(.*)}/', $val) && $values[] = $args[$key];
                }
                
                if($patternMattching){
                    \Application::setCurrentRouteName($route['name']);
                    \call_user_func_array( self::LOCATE  . \Application::getModule() . '\\'. $route['action'], $values);
                    exit();
                } 
                
            }
        }
        try{
            // Aucune route n'a été trouvé, on lance une exception pour renvoyer sur la page d'erreur 
            throw new \ExceptionPage('The URL doesn\'t match any pattern!', 404);
        }catch(Exception $e){
            // TODO Ajouter des traces
        }
    }

    /**
     * Vérifie qu'un chemin existe en vérifiant l'existance de son fichier de route
     */
    private static function routeExist($routeClass){
        return file_exists(__DIR__ . '/..'. $routeClass . '.class.php');
    }
}

?>
