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
    public static $title;
    public static $output;
    public static $css = [];
    public static $js = [];

    public function __construct(Core $Fly)
    {
        self::$Fly = $Fly;
    }

    static public function addJS($file)
    {
        self::$js[] = $file;
    }

    static public function addCSS($file)
    {
        self::$css[] = $file;
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

    static public function addContent(array $body, string $head = "")
    {
        if (isset(self::$Fly->fetchRequest()['ajax'])) {
            if (Core::$ajax)
                self::$Fly->getClass('smarty')->display("string: " . $body['output']);
            else
                self::$output = "Permission denied"; // TODO 404
        } else {
            $content = self::$Fly->getClass('smarty')->fetch("string: " . $body['output'], 'skin_' . $body['template'] . '|' . $body['group'] . '|' . $body['title']);
            self::$Fly->getClass('smarty')->caching = false;
            if (file_exists(CORE_ROOT_PATH . 'apps/' . Core::$app . '/global.php')) {
                require_once(CORE_ROOT_PATH . 'apps/' . Core::$app . '/global.php');
                if (class_exists("class_" . Core::$app . "_global_page")) {
                    $global_class = 'class_' . Core::$app . '_global_page';
                    $text = new $global_class();
                    self::$output = $text->doExecute(Core::instance(), $content, $head);
                }
            } else if (file_exists(CORE_ROOT_PATH . 'apps/global.php')) {
                require_once(CORE_ROOT_PATH . 'apps/global.php');
                if (class_exists("class_global_global_page")) {
                    $global_class = 'class_global_global_page';
                    $text = new $global_class();
                    self::$output = $text->doExecute(Core::instance(), $content, $head);
                }
            }
        }
    }

    static public function sendOutput()
    {
        self::$Fly->getClass('smarty')->caching = false;
        self::$Fly->getClass('smarty')->display("string: " . self::$output);
        exit;
    }

    static public function htmlspecialchars_uni(string $message):string
    {
        $message = preg_replace("#&(?!\#[0-9]+;)#si", "&amp;", $message); // Fix & but allow unicode
        $message = str_replace("<", "&lt;", $message);
        $message = str_replace(">", "&gt;", $message);
        $message = str_replace("\"", "&quot;", $message);
        return $message;
    }

    // Konwersja UTF-8 -> ISO-8859-2
    static public function Utf8ToIso(string $str):string
    {
        return iconv("utf-8", "iso-8859-2", $str);
    }

    // Konwersja ISO-8859-2 -> UTF-8
    static public function IsoToUtf8(string $str):string
    {
        return iconv("iso-8859-2", "utf-8", $str);
    }

    static public function dli(int $x, string $a, string $b, string $c):string
    {
        if ($x == 1) return $a;
        if ($x % 10 > 1 && $x % 10 < 5 && !($x % 100 >= 10 && $x % 100 <= 21)) return $b;
        return $c;
    }

    static public function seoUrl(string $string):string
    {
        $string = strtolower($string);
        $string = self::_no_pl($string);
        $string = preg_replace("/[^a-z0-9_\s-]/", "", $string);
        $string = preg_replace("/[\s-]+/", " ", $string);
        $string = preg_replace("/[\s_]/", "-", $string);
        return $string;
    }

    static public function _no_pl(string $input):string
    {
        $replace = [
            //WIN
            "\xb9" => "a", "\xa5" => "A", "\xe6" => "c", "\xc6" => "C",
            "\xea" => "e", "\xca" => "E", "\xb3" => "l", "\xa3" => "L",
            "\xf3" => "o", "\xd3" => "O", "\x9c" => "s", "\x8c" => "S",
            "\x9f" => "z", "\xaf" => "Z", "\xbf" => "z", "\xac" => "Z",
            "\xf1" => "n", "\xd1" => "N",
            //UTF
            "\xc4\x85" => "a", "\xc4\x84" => "A", "\xc4\x87" => "c", "\xc4\x86" => "C",
            "\xc4\x99" => "e", "\xc4\x98" => "E", "\xc5\x82" => "l", "\xc5\x81" => "L",
            "\xc3\xb3" => "o", "\xc3\x93" => "O", "\xc5\x9b" => "s", "\xc5\x9a" => "S",
            "\xc5\xbc" => "z", "\xc5\xbb" => "Z", "\xc5\xba" => "z", "\xc5\xb9" => "Z",
            "\xc5\x84" => "n", "\xc5\x83" => "N",
            //ISO
            "\xb1" => "a", "\xa1" => "A", "\xe6" => "c", "\xc6" => "C",
            "\xea" => "e", "\xca" => "E", "\xb3" => "l", "\xa3" => "L",
            "\xf3" => "o", "\xd3" => "O", "\xb6" => "s", "\xa6" => "S",
            "\xbc" => "z", "\xac" => "Z", "\xbf" => "z", "\xaf" => "Z",
            "\xf1" => "n", "\xd1" => "N"];

        return strtr($input, $replace);
    }

    public static function showError(string $title, string $additional_info = ""):string
    {
        self::$Fly->getClass('smarty')->assign("error", $additional_info);
        $template = self::$Fly->getClass('template')->get("global", $title);
        self::$Fly->getClass('output')->setTitle("Error");
        self::addContent($template);
        self::sendOutput();
    }

    public static function redirect(string $url)
    {
        header("Location: " . $url);
        exit();
    }
}