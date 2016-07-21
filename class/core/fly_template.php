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

    public static function get(string $group, string $title, $variables = []):array
    {
        $template = Member::getProperty("template");

        if (!file_exists(CORE_ROOT_PATH . 'cache/skin_' . $template . '/'))
            mkdir(CORE_ROOT_PATH . 'cache/skin_' . $template . '/', 0777);
        if (!file_exists(CORE_ROOT_PATH . 'cache/skin_' . $template . '/' . $group . '/'))
            mkdir(CORE_ROOT_PATH . 'cache/skin_' . $template . '/' . $group . '/', 0777);

        $filename = CORE_ROOT_PATH . 'cache/skin_' . $template . '/' . $group . '/' . $title . '.php';
        if (file_exists($filename)) {
            $output = file_get_contents($filename);
            if (DEBUG)
                $output = "<!-- Cached $group/$title, generated -->\n" . $output;
        } else {
            Core::DB()->query('SELECT `value` FROM `' . Core::$settings['db_prefix'] . 'set` WHERE `theme` = ' . $template . ' AND `group` = "' . $group . '" AND `name` = "' . $title . '"');
            $data = Core::DB()->fetch();
            $output = $data['value'];

            $file = fopen(CORE_ROOT_PATH . 'cache/skin_' . $template . '/' . $group . '/' . $title . '.php', 'w');
            fwrite($file, $output);
            fclose($file);
        }
        if (count($variables)) {
            if (!self::$Fly->getClass('smarty')->isCached("string: " . $output, 'skin_' . $template . '|' . $group . '|' . $title))
                foreach ($variables as $name => $value) {
                    self::$Fly->getClass('smarty')->assign($name, $value);
                }
        }
        return ['output' => $output, 'template' => $template, 'group' => $group, 'title' => $title];
    }
}