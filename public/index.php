<?php
    header('Content-type: text/html; charset=UTF-8');
    const PRODUCTION = false;
    const LOGIN_INTERFACE = true; // Defini si l'application passe par une interface de Login
    const MAINTENANCE = false; // Défini un blocage de l'application si celle ci est en cours de maintenance
    const CGI_DIR = '../cgi-bin/';


    require __DIR__ . '/' . CGI_DIR . 'lib/core.php';
?>
