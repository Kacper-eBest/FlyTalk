<?php

/**
 * Created by PhpStorm.
 * User: kacpe
 * Date: 16.07.2016
 * Time: 14:17
 */
class Member
{
    private static $instance;

    static public $data_store = [
        "uid" => 0,
        "display_name" => 'Guest',
        "time_format" => 'j F Y, \o H:i'
    ];
    static public $member_id = 0;

    static public function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    static public function getProperty(string $key)
    {
        return self::$data_store[$key] ?? null;
    }

    static public function setProperty(string $key, $value)
    {
        self::$data_store[$key] = $value;
    }

    public function __get($key)
    {
        return self::getProperty($key);
    }

    public static function fetchMemberData(): array
    {
        return self::$data_store;
    }

    static public function getMemberData(int $member_id): array
    {
        if (!$member_id) return null;
        Core::DB()->query("SELECT * FROM `" . Core::$settings['db_prefix'] . "accounts` WHERE `uid` = '" . intval($member_id) . "' LIMIT 1");
        $r = Core::DB()->fetch();
        $r['seo_name'] = Output::seoUrl($r['login']);
        return $r;
    }

    static public function getLocation()
    {
        if (!empty($_SERVER['PATH_INFO'])) {
            $location = Output::htmlspecialchars_uni($_SERVER['PATH_INFO']);
        } elseif (!empty($_ENV['PATH_INFO'])) {
            $location = Output::htmlspecialchars_uni($_ENV['PATH_INFO']);
        } elseif (!empty($_ENV['PHP_SELF'])) {
            $location = Output::htmlspecialchars_uni($_ENV['PHP_SELF']);
        } else {
            $location = Output::htmlspecialchars_uni($_SERVER['PHP_SELF']);
        }
        if (isset($_SERVER['QUERY_STRING'])) {
            $location .= "?" . Output::htmlspecialchars_uni($_SERVER['QUERY_STRING']);
        } else if (isset($_ENV['QUERY_STRING'])) {
            $location .= "?" . Output::htmlspecialchars_uni($_ENV['QUERY_STRING']);
        }

        if ((isset($_SERVER['REQUEST_METHOD']) && $_SERVER['REQUEST_METHOD'] == "POST") || (isset($_ENV['REQUEST_METHOD']) && $_ENV['REQUEST_METHOD'] == "POST")) {
            $post_array = array('app', 'module', 'action', 'uid');

            foreach ($post_array as $var) {
                if (isset($_POST[$var])) {
                    $addloc[] = urlencode($var) . '=' . urlencode($_POST[$var]);
                }
            }

            if (isset($addloc) && is_array($addloc)) {
                if (strpos($location, "?") === false) {
                    $location .= "?";
                } else {
                    $location .= "&amp;";
                }
                $location .= implode("&amp;", $addloc);
            }
        }
        return $location;
    }

    static public function generateCompiledPasshash($salt, $md5_once_password)
    {
        return md5(md5($salt) . md5($md5_once_password));
    }

    static public function generatePasswordSalt($len = 5)
    {
        $set = array("a", "A", "b", "B", "c", "C", "d", "D", "e", "E", "f", "F", "g", "G", "h", "H", "i", "I", "j", "J", "k", "K", "l", "L", "m", "M", "n", "N", "o", "O", "p", "P", "q", "Q", "r", "R", "s", "S", "t", "T", "u", "U", "v", "V", "w", "W", "x", "X", "y", "Y", "z", "Z", "1", "2", "3", "4", "5", "6", "7", "8", "9");
        $str = '';

        for ($i = 1; $i <= $len; ++$i) {
            $ch = rand(0, count($set) - 1);
            $str .= $set[$ch];
        }
        return $str;
    }

    static public function load_guest()
    {
        $time = time();
        if (Cookie::get('lastactive')) {
            if (!Cookie::get('lastlogin')) {
                self::setProperty("lastactive", $time);
                Cookie::set('lastactive', self::getProperty("lastactive"));
            } else
                self::setProperty("lastlogin", intval(Cookie::get('lastlogin')));

            if ($time - Cookie::get('lastlogin') > 900) {
                Cookie::set('lastlogin', self::getProperty("lastlogin"));
                self::setProperty("lastlogin", self::getProperty("lastlogin"));
            } else
                self::setProperty("lastlogin", intval(Cookie::get('lastlogin')));
        } else {
            Cookie::set('lastlogin', $time);
            self::setProperty("lastactive", $time);
        }

        Cookie::set('lastactive', $time);

        if (!empty(Session::$session_id))
            Session::update_session(Session::$session_id);
        else
            Session::create_session();
    }

    static public function load_user(int $member_id)
    {
        Core::DB()->query("SELECT * FROM " . Core::$settings['db_prefix'] . "accounts WHERE uid = '" . intval($member_id) . "' LIMIT 1");
        self::$data_store = Core::DB()->fetch();
        self::$member_id = self::$data_store['uid'];

        $last_ip_add = "";
        if (self::$data_store['last_ip'] != Session::$ip_address && isset(self::$data_store['last_ip'])) {
            $last_ip_add .= ", last_ip='" . Core::DB()->secure(Session::$ip_address) . "'";
        }
        if (time() - self::$data_store['last_active'] > 900) {
            Core::DB()->query("UPDATE `" . Core::$settings['db_prefix'] . "accounts` SET `last_login` = '" . self::$data_store['last_active'] . "', `last_active` = '" . time() . "' " . $last_ip_add . " WHERE `uid` = '" . self::$member_id . "'");
            self::$data_store['last_login'] = self::$data_store['last_active'];
        } else {
            $time_spent = time() - self::$data_store['lastactive'];
            Core::DB()->query("UPDATE `" . Core::$settings['db_prefix'] . "accounts` SET `last_active` = '" . time() . "', `time_online` = `time_online` + " . $time_spent . " " . $last_ip_add . " WHERE `uid` = '" . self::$member_id . "'");
        }
    }
}