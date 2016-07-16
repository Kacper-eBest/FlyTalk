<?php

/**
 * Created by PhpStorm.
 * User: kacpe
 * Date: 16.07.2016
 * Time: 14:15
 */
class Output
{
    public static $Fly;
    public static $title = '';
    public static $output = '';

    public function __construct(Core $Fly)
    {
        self::$Fly = $Fly;
    }

    static public function makeTitle():string
    {
        if (empty(self::$title))
            return Core::$settings['sitename'];
        self::$title = self::$title . ' - ' . Core::$settings['sitename'];
        return self::$title;
    }

    public function setTitle(string $title)
    {
        self::$title = $title;
    }

    public static function getTitle(): string
    {
        if (empty(self::$title)) return self::$Fly->$settings['sitename'];
        return self::$title;
    }

    static public function addContent(string $body, string $head = "")
    {
        if (isset(self::$Fly->fetchRequest()['ajax'])) {
            if (Core::$ajax)
                self::$Fly->getClass('smarty')->display("string: " . $body);
            else
                self::$output = "Permission denied"; // TODO 404
        } else {
            if (file_exists(CORE_ROOT_PATH . 'apps/' . Core::$app . '/global.php')) {
                require_once(CORE_ROOT_PATH . 'apps/' . Core::$app . '/global.php');
                if (class_exists("class_" . Core::$app . "_global_page")) {
                    $global_class = 'class_' . Core::$app . '_global_page';
                    $text = new $global_class();
                    self::$output = $text->doExecute(Core::instance(), $body, $head);
                }
            } else if (file_exists(CORE_ROOT_PATH . 'apps/global.php')) {
                require_once(CORE_ROOT_PATH . 'apps/global.php');
                if (class_exists("class_global_global_page")) {
                    $global_class = 'class_global_global_page';
                    $text = new $global_class();
                    self::$output = $text->doExecute(Core::instance(), $body, $head);
                }
            }
        }
    }

    static public function sendOutput()
    {
        self::$Fly->getClass('smarty')->display("string: " . self::$output);
    }
}