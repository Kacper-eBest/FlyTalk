<?php

/**
 * Created by PhpStorm.
 * User: kacpe
 * Date: 16.07.2016
 * Time: 13:11
 */
class DB
{
    static private $instance;
    static private $last_result;
    static private $connection;

    static public $error;
    static public $last_query;
    static public $count;

    static public function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    static public function getDB()
    {
        if (!self::$instance)
            return null;
        return self::$instance;
    }

    static public function setDB()
    {
        if (self::$connection)
            return self::$connection;
        self::$connection = new mysqli(Core::$settings['db_host'], Core::$settings['db_user'], Core::$settings['db_pass'], Core::$settings['db_db']);
        if (self::$connection) {
            return self::$connection;
        } else {
            throw new Exception(self::$error);
        }
    }

    static public function close()
    {
        if (!self::$connection)
            return false;
        $close = mysqli_close(self::$connection);
        if ($close) {
            self::$connection = null;
            return true;
        } else {
            self::$error = mysqli_error(self::$connection);
            return false;
        }
    }

    static public function reconnect()
    {
        self::close();
        self::setDB();
    }

    static public function query($sql)
    {
        if (!self::$connection) return false;
        if (!mysqli_ping(self::$connection))
            self::reconnect();
        if (isset($sql) && $sql != '') {
            mysqli_set_charset(self::$connection, Core::$settings['db_charset']);
            $result = mysqli_query(self::$connection, $sql);
            if ($result) {
                self::$last_result = $result;
                self::$last_query = $sql;
                self::$count++;
                return $result;
            } else {
                self::$last_result = null;
                self::$error = mysqli_error(self::$connection);
                $mysql_error = fopen(CORE_ROOT_PATH . 'cache/mysql_errors.txt', "a");
                $body = "Date: " . date("H:m:s j F, Y") . PHP_EOL . "Query: " . $sql . PHP_EOL . "Error: " . self::$error . PHP_EOL . PHP_EOL;
                fwrite($mysql_error, $body);
                fclose($mysql_error);
                Core::getClass('output')->showError("db_error", self::$error);
                return false;
            }
        }
        return false;
    }

    static public function fetch($result = null, string $mode = '')
    {
        if (!self::$connection) return false;
        if ($result == null) $result = self::$last_result;
        if ($result == null) return false;

        switch ($mode) {
            case 'ROW':
                $back = mysqli_fetch_row($result);
                break;

            case 'ARRAY':
            case 'BOTH':
                $back = mysqli_fetch_array($result);
                break;

            case 'OBJECT':
            case 'OBJ':
                $back = mysqli_fetch_object($result);
                break;

            case 'ASSOC':
            default:
                $back = mysqli_fetch_assoc($result);
                break;
        }
        return $back;
    }

    static public function getUID()
    {
        if (!self::$connection) return false;
        return mysqli_insert_id(self::$connection);
    }

    static public function num($result = NULL)
    {
        if (!self::$connection) return false;
        if ($result == NULL) $result = self::$last_result;
        if ($result == null) return false;
        return mysqli_num_rows($result);
    }

    static public function secure(string $data)
    {
        if (!self::$connection) return false;
        return mysqli_escape_string(self::$connection, $data);
    }

    static public function delete(string $table, string $where = "", string $limit = "")
    {
        if (!self::$connection) return false;
        $query = "";
        if (!empty($where))
            $query .= " WHERE $where";

        if (!empty($limit))
            $query .= " LIMIT $limit";

        return self::query('DELETE FROM ' . $table . ' ' . $query);
    }

    static public function update(string $table, array $array = [], string $where = "", string $limit = "", bool $no_quote = false)
    {
        if (!self::$connection) return false;
        if (!is_array($array))
            return false;

        $comma = "";
        $query = "";
        $quote = "'";

        if ($no_quote)
            $quote = "";

        foreach ($array as $field => $value) {
            $query .= $comma . "`" . $field . "`={$quote}{$value}{$quote}";
            $comma = ', ';
        }

        if (!empty($where))
            $query .= " WHERE " . $where;
        if (!empty($limit))
            $query .= " LIMIT " . $limit;

        return self::query('UPDATE ' . $table . ' SET ' . $query);
    }

    static public function replace_query(string $table, array $replacements = array())
    {
        if (!self::$connection) return false;
        $values = '';
        $comma = '';
        foreach ($replacements as $column => $value) {
            $values .= $comma . "`" . $column . "`='" . $value . "'";
            $comma = ',';
        }

        if (empty($replacements))
            return false;

        return self::query('REPLACE INTO ' . $table . ' SET ' . $values);
    }

}