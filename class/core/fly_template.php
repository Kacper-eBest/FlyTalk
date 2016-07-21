<?php

/**
 * Created by PhpStorm.
 * User: kacpe
 * Date: 16.07.2016
 * Time: 14:18
 */
class Template
{
    private static $Fly;

    public function __construct(Core $Fly)
    {
        self::$Fly = $Fly;
    }

    public static function get(string $group, string $title):string
    {
        $template = Member::getProperty("template") ?? Core::$settings['theme'];

        if (!file_exists(CORE_ROOT_PATH . 'cache/skin_' . $template . '/'))
            mkdir(CORE_ROOT_PATH . 'cache/skin_' . $template . '/', 0777);
        if (!file_exists(CORE_ROOT_PATH . 'cache/skin_' . $template . '/' . $group . '/'))
            mkdir(CORE_ROOT_PATH . 'cache/skin_' . $template . '/' . $group . '/', 0777);

        $filename = CORE_ROOT_PATH . 'cache/skin_' . $template . '/' . $group . '/' . $title . '.php';
        if (file_exists($filename)) {
            $output = "<!-- Cached $group/$title, generated -->\n" . file_get_contents($filename);
        } else {
            Core::DB()->query('SELECT `value` FROM `' . Core::$settings['db_prefix'] . 'set` WHERE `theme` = ' . $template . ' AND `group` = "' . $group . '" AND `name` = "' . $title . '"');
            $data = Core::DB()->fetch();
            $output = $data['value'];

            $file = fopen(CORE_ROOT_PATH . 'cache/skin_' . $template . '/' . $group . '/' . $title . '.php', 'w');
            fwrite($file, $output);
            fclose($file);
        }
        return $output;
    }
}