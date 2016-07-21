<?php

/**
 * Created by PhpStorm.
 * User: kacpe
 * Date: 16.07.2016
 * Time: 16:29
 */
class class_global_global_page
{
    public function doExecute(Core $Fly, $body, $head)
    {
        // TODO Loading CSS from DB
        // TODO automatic minify JS&CSS in one file per type

        // Temp
        $Fly->getClass('output')->addJS("public/js/jquery.min.js");
        $Fly->getClass('output')->addJS("public/js/fly.js");

        $template = $Fly->getClass('template')->get("global", "global", ["title" => $Fly->getClass('output')->makeTitle(), "body" => $body, "head" => $head]);
        $Fly->getClass('smarty')->display("string: " . $template['output']);
    }
}