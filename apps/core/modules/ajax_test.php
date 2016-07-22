<?php

/**
 * Created by PhpStorm.
 * User: kacpe
 * Date: 22.07.2016
 * Time: 22:54
 */
class class_core_ajax_test_page extends Ajax
{
    public function doExecute(Core $Fly)
    {
        $array = ["test" => "test"];

        $this->returnJsonArray($array);
    }
}