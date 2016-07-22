<?php

/**
 * Created by PhpStorm.
 * User: kacpe
 * Date: 22.07.2016
 * Time: 22:50
 */
abstract class Ajax extends Command
{
    public function returnJsonError(string $name)
    {
        self::returnJsonArray(["error" => $name]);
    }

    public function returnJsonSuccess(string $name)
    {
        self::returnJsonArray(["success" => $name]);
    }

    public function returnJsonArray(array $array)
    {
        // TODO Some security functions :)
        echo json_encode($array);
        exit;
    }

    public function returnString(string $value)
    {
        // TODO Some security functions :)
        echo $value;
        exit;
    }
}