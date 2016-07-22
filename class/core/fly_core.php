<?php

/**
 * Created by PhpStorm.
 * User: kacpe
 * Date: 16.07.2016
 * Time: 13:21
 */
class Core
{
    static public $version = "FlyCore 1.0.0";
    static public $f_version = "0.1";

    static private $instance;
    static private $initiated = FALSE;

    static protected $handles = [];
    static protected $classes = [];

    static public $request = [];
    static public $settings = [];
    static public $cookies = [];

    static public $bots = [];

    static public $ajax = false;

    static public $app = DEFAULT_APP;
    static public $module;
    static public $def_module;

    static public function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    static public function __myDestruct()
    {
        foreach (self::$handles as $name => $obj) {
            if (method_exists($obj, '__myDestruct')) {
                $obj->__myDestruct();
            }
        }
    }

    static public function init()
    {
        if (self::$initiated === true) {
            return FALSE;
        }
        self::$initiated = true;

        if (is_file(CORE_ROOT_PATH . 'fly_config.php')) {
            require(CORE_ROOT_PATH . 'fly_config.php');
            if (is_array($config))
                foreach ($config as $key => $val)
                    self::$settings[$key] = str_replace('&#092;', '\\', $val);
        }

        $pathInfo = pathinfo($_SERVER['PHP_SELF']);
        self::$settings['board_url'] = (strtolower(substr($_SERVER["SERVER_PROTOCOL"], 0, 5)) == 'https://' ? 'https' : 'http') . '://' . $_SERVER['HTTP_HOST'] . $pathInfo['dirname'] . "/";

        switch (self::$settings['db_type']) {
            case "mysqli":
                require(CORE_ROOT_PATH . 'class/core/mysql/fly_mysqli.php');
                break;
            case "mysql":
                require(CORE_ROOT_PATH . 'class/core/mysql/fly_mysql.php');
                break;
            case "postgre":
                require(CORE_ROOT_PATH . 'class/core/mysql/fly_postgre.php');
                break;
        }

        self::$handles['db'] = DB::instance();
        self::$handles['db']->setDB();

        self::$handles['db']->query('SELECT title, val FROM ' . self::$settings['db_prefix'] . 'config');
        while ($val = self::$handles['db']->fetch())
            self::$settings[$val['title']] = $val['val'];

        self::$handles['db']->query('SELECT uid, name FROM ' . self::$settings['db_prefix'] . 'bots');
        while ($val = self::$handles['db']->fetch())
            self::$bots[] = $val;

        self::$request['request_method'] = strtolower($_SERVER['REQUEST_METHOD']) ?? "";
        self::parse_incoming($_GET);
        self::parse_incoming($_POST);
        self::parse_incoming($_REQUEST);

        require(CORE_ROOT_PATH . 'class/core/fly_cookies.php');
        Cookie::parse();

        if (isset(self::$request['app']) && !empty(self::$request['app']))
            self::$app = self::$request['app'];

        if (file_exists(CORE_ROOT_PATH . 'apps/' . self::$app . '/modules/')) {
            if (file_exists(CORE_ROOT_PATH . 'apps/' . self::$app . '/modules/defaultSection.php')) {
                require_once(CORE_ROOT_PATH . 'apps/' . self::$app . '/modules/defaultSection.php');
                self::$def_module = $DEFAULT_SECTION;
            }

            if (isset(self::$request['module']) && !empty(self::$request['module']) && file_exists(CORE_ROOT_PATH . 'apps/' . self::$app . '/modules/' . self::$request['module'] . '.php'))
                self::$module = self::$request['module'];
            else if (!empty(self::$def_module))
                self::$module = self::$def_module;
            else {
                // TODO 404 page
            }
        }

        self::$request['seo'] = self::$request['url'] ?? '';
        foreach ($_GET as $value => $key) {
            self::$request['seo'] = $value;
            break;
        }

        require(CORE_ROOT_PATH . 'class/core/fly_session.php');
        self::$handles['session'] = Session::instance();

        require(CORE_ROOT_PATH . 'class/core/fly_member.php');
        self::$handles['member'] = Member::instance();

        require(CORE_ROOT_PATH . 'class/smarty/Smarty.class.php');
        self::instance()->setClass('smarty', new Smarty());

        require(CORE_ROOT_PATH . 'class/core/fly_output.php');
        self::instance()->setClass('output', new Output(self::instance()));

        require(CORE_ROOT_PATH . 'class/core/fly_template.php');
        self::instance()->setClass('template', new Template(self::instance()));

        self::$handles['session']->init();
        self::getClass('template')->init();

        self::getClass('smarty')->caching = true;
        self::getClass('smarty')->setCacheDir(CORE_ROOT_PATH . 'cache/skin_' . (Member::getProperty("template")) . '/cache/');
        self::getClass('smarty')->setTemplateDir(CORE_ROOT_PATH . 'cache/skin_' . (Member::getProperty("template")) . '/');
        self::getClass('smarty')->setCompileDir(CORE_ROOT_PATH . 'cache/skin_' . (Member::getProperty("template")) . '/cache/compile/');

        if (isset(self::$request['ajax'])) {
            if (self::$request['ajax'] == Session::$session_id)
                self::$ajax = true;
            else
                exit();
        }

        return true;
    }

    static protected function checkForInit()
    {
        if (self::$initiated !== true) {
            throw new Exception('Core has not been initiated. Do so by calling Core::init()');
        }
        return true;
    }

    static public function parse_incoming($array)
    {
        if (!is_array($array)) {
            return;
        }

        foreach ($array as $key => $val) {
            self::$request[$key] = $val;
        }
    }

    static public function dbFunctions()
    {
        self::checkForInit();
        return self::$handles['db'];
    }

    static public function DB($key = '')
    {
        self::checkForInit();
        return self::$handles['db']->getDB($key);
    }

    static public function settings()
    {
        self::checkForInit();
        return self::$settings;
    }

    static public function &fetchSettings()
    {
        return self::$settings;
    }

    static public function request()
    {
        self::checkForInit();
        return self::$request;
    }

    static public function &fetchRequest()
    {
        return self::$request;
    }

    static public function getClass($key)
    {
        if (!isset(self::$classes[$key])) {
            throw new Exception('Invalid class');
        } else
            return self::$classes[$key];
    }

    public function __get($key)
    {
        self::checkForInit();

        $_class = self::getClass($key);

        if (is_object($_class)) {
            return $_class;
        }
        return false;
    }

    static public function setClass($key = '', $value = '')
    {
        self::checkForInit();

        if (!$key OR !$value) {
            throw new Exception("Missing a key or value");
        } else if (!is_object($value)) {
            throw new Exception("$value is not an object");
        }

        self::$classes[$key] = $value;
    }

}