<?php

/**
 * Created by PhpStorm.
 * User: kacpe
 * Date: 16.07.2016
 * Time: 14:17
 */
class Session
{
    private static $instance;

    public static $member_id;
    public static $session_id = 0;
    public static $session_type;
    public static $ip_address;
    public static $location;
    public static $user_agent;
    public static $browser;
    public static $operating_system = 'unknown';
    public static $is_not_human = 0;

    static public function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    static public function init()
    {
        self::$ip_address = self::get_ip();
        self::$user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
        $browser = self::getBrowser(self::$user_agent);
        self::$browser = $browser['name'];
        self::$operating_system = $browser['platform'];

        if ($bot_uid = self::isBot(self::$user_agent)) {
            Core::DB()->update(Core::$settings['db_prefix'] . "bots", ["last_visit" => time()], "uid = " . $bot_uid);
        }

        if (Cookie::get('sid')) {
            self::$session_id = Core::DB()->secure(Cookie::get('sid'));

            Core::DB()->query("SELECT * FROM `" . Core::$settings['db_prefix'] . "sessions` WHERE `sid` = '" . self::$session_id . "' AND `ip` = '" . Core::DB()->secure(self::$ip_address) . "' LIMIT 1");
            $session = Core::DB()->fetch();
            if ($session['sid']) {
                self::$session_id = $session['sid'];
                self::$member_id = $session['member_id'];
            } else {
                self::$session_id = 0;
                self::$member_id = 0;
            }
        }

        if (Cookie::get('member_id') && self::$member_id == Cookie::get('member_id')) {
            self::$is_not_human = false;
            Member::load_user(self::$member_id);
            if (!empty(self::$session_id)) {
                self::update_session(self::$session_id, self::$member_id);
            } else {
                self::create_session(self::$member_id);
            }
        }

        if (!Member::getProperty("uid")) {
            self::$is_not_human = true;
            Member::load_guest();
        }

        if ((self::$session_id && Cookie::get('sid') && (Cookie::get('sid') != self::$session_id)) || self::$session_id && !Cookie::get('sid')) {
            Cookie::set("sid", self::$session_id, -1);
        }
    }

    static public function isBot(string $user_agent)
    {
        foreach (Core::$bots as $bot) {
            if (stripos($user_agent, $bot['name']) !== false)
                return $bot['uid'];
        }
        return false;
    }

    static public function get_ip($ip2long = false)
    {
        if (isset($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } else if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            if (isset($_SERVER['REMOTE_ADDR']))
                $ip = $_SERVER['REMOTE_ADDR'];
            else
                return false;
        }

        if ($ip2long) {
            $ip = ip2long($ip);
        }

        return $ip;
    }

    static public function getBrowser(string $u_agent): array
    {
        $bname = 'Unknown';
        $platform = 'Unknown';

        if (preg_match('/linux/i', $u_agent)) {
            $platform = 'Linux';
        } elseif (preg_match('/macintosh|mac os x/i', $u_agent)) {
            $platform = 'Mac';
        } elseif (preg_match('/windows|win32/i', $u_agent)) {
            $platform = 'Windows';
        }

        if (preg_match('/MSIE/i', $u_agent) && !preg_match('/Opera/i', $u_agent)) {
            $bname = 'IE';
            $ub = "MSIE";
        } elseif (preg_match('/Firefox/i', $u_agent)) {
            $bname = 'Mozilla';
            $ub = "Firefox";
        } elseif (preg_match('/Edge/i', $u_agent)) {
            $bname = 'Edge';
            $ub = "Edge";
        } elseif (preg_match('/Chrome/i', $u_agent)) {
            $bname = 'Chrome';
            $ub = "Chrome";
        } elseif (preg_match('/Safari/i', $u_agent)) {
            $bname = 'Safari';
            $ub = "Safari";
        } elseif (preg_match('/Opera/i', $u_agent)) {
            $bname = 'Opera';
            $ub = "Opera";
        } elseif (preg_match('/Netscape/i', $u_agent)) {
            $bname = 'Netscape';
            $ub = "Netscape";
        }

        $known = array('Version', $ub, 'other');
        $pattern = '#(?<browser>' . join('|', $known) .
            ')[/ ]+(?<version>[0-9.|a-zA-Z.]*)#';
        if (!preg_match_all($pattern, $u_agent, $matches)) {

        }

        $i = count($matches['browser']);
        if ($i != 1) {
            if (strripos($u_agent, "Version") < strripos($u_agent, $ub)) {
                $version = $matches['version'][0];
            } else {
                $version = $matches['version'][1];
            }
        } else {
            $version = $matches['version'][0];
        }

        if ($version == null || $version == "") {
            $version = "?";
        }

        return array(
            'userAgent' => $u_agent,
            'name' => $bname,
            'version' => $version,
            'platform' => $platform,
            'pattern' => $pattern
        );
    }

    static public function getSessionLocation(string $sid)
    {
        Core::DB()->query("SELECT `app`, `module`, `action`, `uid` FROM `" . Core::$settings['db_prefix'] . "sessions` WHERE `sid` = '" . $sid . "' LIMIT 1");
        $loc = Core::DB()->fetch();
        if (file_exists(CORE_DIR . 'apps/' . $loc['app'] . '/settings/location.php'))
            require_once(CORE_DIR . 'apps/' . $loc['app'] . '/settings/location.php');
        return $return;
    }

    static public function create_session(int $uid = 0)
    {
        $online_data = [];
        if ($uid > 0) {
            Core::DB()->delete("" . Core::$settings['db_prefix'] . "sessions", "member_id = '" . $uid . "' AND `user_agent` = '" . Core::DB()->secure(self::$user_agent) . "'");
            $online_data['member_id'] = $uid;
        } else {
            Core::DB()->delete("" . Core::$settings['db_prefix'] . "sessions", "`ip` = '" . Core::DB()->secure(self::$ip_address) . "' AND `user_agent` = '" . Core::DB()->secure(self::$user_agent) . "'");
            $online_data['member_id'] = 0;
        }

        $online_data['sid'] = md5(uniqid(microtime(true)));
        $online_data['time'] = time();
        $online_data['create_time'] = time();
        $online_data['ip'] = Core::DB()->secure(self::$ip_address);
        if (strpos(Core::$module, "ajax_") === false) {
            $online_data['location'] = Member::getLocation();
            $online_data['app'] = Core::$app;
            $online_data['module'] = Core::$module;
            $online_data['uid'] = Core::$request['uid'] ?? "";
            $online_data['action'] = Core::$request['action'] ?? "";
        }
        $online_data['user_agent'] = Core::DB()->secure(self::$user_agent);
        Core::DB()->replace_query(Core::$settings['db_prefix'] . "sessions", $online_data, "sid", false);

        self::$session_id = $online_data['sid'];
        self::$member_id = $online_data['member_id'];
    }

    static public function update_session(string $sid, int $uid = 0)
    {
        $online_data = [];
        if ($uid)
            $online_data['member_id'] = $uid;
        else
            $online_data['member_id'] = 0;

        $online_data['time'] = time();
        if (strpos(Core::$module, "ajax_") === false) {
            $online_data['location'] = Member::getLocation();
            $online_data['app'] = Core::$app;
            $online_data['module'] = Core::$module;
            $online_data['uid'] = Core::$request['uid'] ?? "";
            $online_data['action'] = Core::$request['action'] ?? "";
        }
        $online_data['user_agent'] = Core::DB()->secure(self::$user_agent);

        Core::DB()->update(Core::$settings['db_prefix'] . "sessions", $online_data, "sid='" . Core::DB()->secure($sid) . "'", 1);

    }
}