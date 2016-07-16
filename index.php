<?php
/**
 * Created by PhpStorm.
 * User: kacpe
 * Date: 16.07.2016
 * Time: 13:28
 */

define('IN_INDEX', 1);
define('DEBUG', true);

if (DEBUG) {
    ini_set('display_errors', 1);
    error_reporting(E_ALL);
}

require_once('fly_init.php');
require_once(CORE_ROOT_PATH . 'class/core/fly_core.php');
require_once(CORE_ROOT_PATH . 'class/core/controller/fly_controller.php');
Controller::run();
exit();
