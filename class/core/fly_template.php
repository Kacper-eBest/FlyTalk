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

    public static $template_id;
    public static $template_data = [];

    public function __construct(Core $Fly)
    {
        self::$Fly = $Fly;
    }

    public function init()
    {
        self::$template_id = Member::getProperty("template");

        Core::DB()->query('SELECT * FROM `' . Core::$settings['db_prefix'] . 'themes` WHERE `uid` = ' . self::$template_id);
        self::$template_data = Core::DB()->fetch();
    }

    public static function get(string $group, string $title, $variables = []):array
    {
        if (!file_exists(CORE_ROOT_PATH . 'cache/skin_' . self::$template_id . '/'))
            mkdir(CORE_ROOT_PATH . 'cache/skin_' . self::$template_id . '/', 0777);
        if (!file_exists(CORE_ROOT_PATH . 'cache/skin_' . self::$template_id . '/' . $group . '/'))
            mkdir(CORE_ROOT_PATH . 'cache/skin_' . self::$template_id . '/' . $group . '/', 0777);

        $filename = CORE_ROOT_PATH . 'cache/skin_' . self::$template_id . '/' . $group . '/' . $title . '.php';
        if (file_exists($filename)) {
            $output = file_get_contents($filename);
            if (DEBUG) {
                $temp_output = Core::DB()->secure($output);
                Core::DB()->query('SELECT `uid`, `value` FROM `' . Core::$settings['db_prefix'] . 'set` WHERE `theme` = ' . self::$template_id . ' AND `group` = "' . $group . '" AND `name` = "' . $title . '"');
                if (!Core::DB()->num()) {
                    Core::DB()->query('INSERT INTO `' . Core::$settings['db_prefix'] . 'set` (theme, group, name, value) VALUES (' . self::$template_id . ', "' . $group . '", "' . $title . '", "' . $temp_output . '")');
                } else {
                    $fetch = Core::DB()->fetch();
                    if ($fetch['value'] != $temp_output)
                        Core::DB()->update('`' . Core::$settings['db_prefix'] . 'set`', ["value" => $temp_output, "edit_time" => time()], '`theme` = ' . self::$template_id . ' AND `group` = "' . $group . '" AND `name` = "' . $title . '"');
                }
                $output = "<!-- From file $group/$title, generated -->\n" . $output;
            }
        } else {
            if (!DEBUG) {
                Core::DB()->query('SELECT `value` FROM `' . Core::$settings['db_prefix'] . 'set` WHERE `theme` = ' . self::$template_id . ' AND `group` = "' . $group . '" AND `name` = "' . $title . '"');
                $data = Core::DB()->fetch();
                $output = $data['value'];
            } else
                $output = "";

            $file = fopen(CORE_ROOT_PATH . 'cache/skin_' . self::$template_id . '/' . $group . '/' . $title . '.php', 'w');
            fwrite($file, $output);
            fclose($file);
        }
        if (count($variables)) {
            //if (!self::$Fly->getClass('smarty')->isCached("string: " . $output, 'skin_' . self::$template_id . '|' . $group . '|' . $title))
                foreach ($variables as $name => $value) {
                    self::$Fly->getClass('smarty')->assign($name, $value);
                }
        }
        return ['output' => $output, 'template' => self::$template_id, 'group' => $group, 'title' => $title];
    }
}