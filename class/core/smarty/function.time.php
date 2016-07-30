<?php
/**
 * Created by PhpStorm.
 * User: kacpe
 * Date: 30.07.2016
 * Time: 23:03
 */

function smarty_function_time($params)
{
    $time = $params['time'] ?? time();

    $output = '<time data-unix="' . $time . '" title="' . date(Member::getProperty("time_format"), $time) . '" datetime="' . date(Member::getProperty("time_format"), $time) . '">' . date(Member::getProperty("time_format"), $time) . '</time>';

    return $output;
}
