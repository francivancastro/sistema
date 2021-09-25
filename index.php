<?php
session_start();

define('CONTROLLERS', 'app/controllers/');
define('VIEWS', 'app/views/');
define('MODELS', 'app/models/');
define('HELPERS', 'system/helpers/');
define('RELATORIO', 'system/relatorios/');
define('LAYOUT', 'app/views/layout');
define('LIB', 'lib/');

include 'system/pdf/mpdf.php';

require_once 'system/System.php';
require_once 'system/Controller.php';
require_once 'system/Model.php';
require_once 'lib/Banco.php';

function __autoload($file){
    
    if(file_exists( MODELS. $file . '.php')){
        require_once ( MODELS. $file . '.php');
    } elseif (file_exists( HELPERS. $file . '.php')) {
        require_once ( HELPERS. $file . '.php');
    } elseif (file_exists( LIB. $file . '.php')) {
        require_once ( LIB. $file . '.php');
    } elseif (file_exists( RELATORIO. $file . '.php')) {
        require_once ( RELATORIO. $file . '.php');
    } else {
        die("MODEL, HELPER ou LIB NÃO ENCONTRADO");
    }
    
}

$start = new system();
$start->run();

