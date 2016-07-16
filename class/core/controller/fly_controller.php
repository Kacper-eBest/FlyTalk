<?php

/**
 * Created by PhpStorm.
 * User: kacpe
 * Date: 16.07.2016
 * Time: 13:42
 */
class Controller
{
    protected $Core;

    private function __construct()
    {
    }

    static public function run()
    {
        $instance = new self();
        $instance->init();
        if (file_exists(CORE_ROOT_PATH . 'apps/' . Core::$app . '/modules/')) {
            if (file_exists(CORE_ROOT_PATH . 'apps/' . Core::$app . '/modules/' . Core::$module . '.php')) {
                require_once(CORE_ROOT_PATH . 'class/core/controller/fly_command.php');
                require_once(CORE_ROOT_PATH . 'apps/' . Core::$app . '/modules/' . Core::$module . '.php');
                if (class_exists("class_" . Core::$app . "_" . Core::$module . "_page")) {
                    $classname = 'class_' . Core::$app . '_' . Core::$module . '_page';
                    $output = new $classname();
                    $output->execute($instance->Core);
                } else echo "1";
            } else echo Core::$module;
        } else echo Core::$app;
    }

    private function init()
    {
        $this->Core = Core::instance();
        $this->Core->init();
    }
}