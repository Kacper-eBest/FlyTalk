<?php
/**
 * Created by PhpStorm.
 * User: kacpe
 * Date: 16.07.2016
 * Time: 13:27
 */

if (!defined('CORE_DIR')) {
    define('CORE_DIR', dirname(dirname(__FILE__)) . "/");
}

if (!defined('CORE_ROOT_PATH')) {
    define('CORE_ROOT_PATH', str_replace("\\", "/", dirname(__FILE__)) . '/');
}

if (!defined('DEFAULT_PAGE')) {
    define('DEFAULT_APP', 'core');
}