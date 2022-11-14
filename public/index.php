<?php

require '..\vendor\autoload.php';

use App\Controller\TestAutoload;
use App\Exceptions\AppException;

/**
 * Description of index
 *
 * @author ayme.pignon
 */
//Declaration de constants d'application
define('DS', DIRECTORY_SEPARATOR);
define('RACINE', new DirectoryIterator(dirname(__FILE__)) . DS . ".." . DS);
include_once(RACINE . DS . 'config/conf.php');
include_once(PATH_VENDOR . "autoload.php");
include_once(RACINE . DS . 'includes/params.php');

try {
    if ((!array_key_exists('c', $_GET)) || (!array_key_exists('a', $_GET))) {
        throw new Exception("Erreur, cette page n'existe pas");
    }
    $BaseController = filter_input(INPUT_GET, 'c', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    $action = filter_input(INPUT_GET, 'a', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
    // TODO : contrôleur à écrire plus tard
    if (empty($BaseController)) {
        $BaseController = 'Identification';
        $action = 'login';
    }
    $controller = "App\\Controller\\" . $BaseController . "Controller";

    if (class_exists($controller, true)) {
        $c = new $controller();
        $params = array(array_slice($_REQUEST, 2));
        call_user_func_array(array($c, $action), $params);
    } else {
        throw new Error("Le contrôleur demandé n'existe pas");
    }
} catch (Error $ex) {
    $error['message'] = $ex->getMessage();
    $error['mode'] = MODE_DEV;
    MyTwig::afficheVue('errors/error.html.twig', $error);
} catch (AppException $ex) {
    $error['message'] = $ex->getMessage();
    $error['mode'] = MODE_DEV;
    MyTwig::afficheVue('errors/error.html.twig', $error);
} catch (Exception $ex) {
    $error['message'] = $ex->getMessage();
    $error['mode'] = MODE_DEV;
    MyTwig::afficheVue('errors/error.html.twig', $error);
} 