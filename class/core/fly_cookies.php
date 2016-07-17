<?php

/**
 * Created by PhpStorm.
 * User: kacpe
 * Date: 16.07.2016
 * Time: 13:53
 */
class Cookie
{
    public static $cookies = [];

    public static function parse()
    {
        if (!is_array($_COOKIE)) {
            return;
        }

        $prefix_length = strlen(Core::$settings['cookie_prefix']);

        foreach ($_COOKIE as $key => $val) {
            if ($prefix_length && substr($key, 0, $prefix_length) == Core::$settings['cookie_prefix']) {
                $key = substr($key, $prefix_length);
                if (isset(self::$cookies[$key])) {
                    unset(self::$cookies[$key]);
                }
            }

            if (!isset(self::$cookies[$key])) {
                self::$cookies[$key] = $val;
            }
        }
    }

    public static function get(string $name)
    {
        return self::$cookies[$name] ?? null;
    }

    public static function del(string $name)
    {
        self::set($name, "", -3600);
        unset($cookies[$name]);
    }

    public static function set(string $name, string $value, $expires = "", bool $httponly = false)
    {
        if (Session::get_ip() == "") {
            return false;
        }

        if (!Core::$settings['cookie_path']) {
            Core::$settings['cookie_path'] = "/";
        }

        if ($expires == -1) {
            $expires = 0;
        } elseif ($expires == "" || $expires == null) {
            $expires = time() + (60 * 60 * 24 * 365);
        } else {
            $expires = time() + intval($expires);
        }

        Core::$settings['cookie_path'] = str_replace(array("\n", "\r"), "", Core::$settings['cookie_path']);
        Core::$settings['cookie_domain'] = str_replace(array("\n", "\r"), "", Core::$settings['cookie_domain']);
        Core::$settings['cookie_prefix'] = str_replace(array("\n", "\r", " "), "", Core::$settings['cookie_prefix']);

        $cookie = "Set-Cookie: " . Core::$settings['cookie_prefix'] . "" . $name . "=" . urlencode($value);

        if ($expires > 0) {
            $cookie .= "; expires=" . @gmdate('D, d-M-Y H:i:s \\G\\M\\T', $expires);
        }

        if (!empty(Core::$settings['cookie_path'])) {
            $cookie .= "; path=" . Core::$settings['cookie_path'] . "";
        }

        if (!empty(Core::$settings['cookie_domain'])) {
            $cookie .= "; domain=" . Core::$settings['cookie_domain'] . "";
        }

        if ($httponly == true) {
            $cookie .= "; HttpOnly";
        }
        header($cookie, false);
        self::$cookies[$name] = $value;
        return true;
    }
}