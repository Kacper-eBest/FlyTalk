<?php

/**
 * Created by PhpStorm.
 * User: kacpe
 * Date: 16.07.2016
 * Time: 14:18
 */
class Template
{
    private static $cache = [];

    private static $Fly;

    public function __construct(Core $Fly)
    {
        self::$Fly = $Fly;
    }

    public static function get(string $group, string $title):string
    {
        if (!isset(self::$cache[$title])) {
            $template = Member::getProperty("template") ?? Core::$settings['theme'];
            Core::DB()->query('SELECT `value` FROM `' . Core::$settings['db_prefix'] . 'set` WHERE `theme` = ' . $template . ' AND `group` = "' . $group . '" AND `name` = "' . $title . '"');
            $data = Core::DB()->fetch();
            if (!$data['value'])
                $data['value'] = "";
            self::$cache[$title] = $data['value'];
        }
        return self::$cache[$title];
    }
}