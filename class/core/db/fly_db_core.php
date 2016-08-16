<?php

/**
 * Created by PhpStorm.
 * User: kacpe
 * Date: 16.08.2016
 * Time: 11:05
 */
abstract class DB_core
{
    static protected $instance;
    static protected $last_result;
    static protected $connection;

    static public $error;
    static public $last_query;
    static public $count;

    static public function instance()
    {
        if (!self::$instance) {
            $class = get_called_class();
            self::$instance = new $class;
        }
        return self::$instance;
    }

    static public function getDB()
    {
        if (!self::$instance)
            return null;
        return self::$instance;
    }

    static public abstract function setDB();

    static public abstract function close();

    static public abstract function reconnect();

    static public abstract function query($sql);

    static public abstract function fetch($result = null, string $mode = '');

    static public abstract function getUID();

    static public abstract function num($result = NULL);

    static public abstract function secure(string $data) : string;

    static public abstract function delete(string $table, string $where = "", string $limit = "");

    static public abstract function update(string $table, array $array = [], string $where = "", string $limit = "", bool $no_quote = false);

    static public abstract function replace_query(string $table, array $replacements = array());

}