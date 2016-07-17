<?php

/**
 * Created by PhpStorm.
 * User: kacpe
 * Date: 16.07.2016
 * Time: 16:29
 */
class class_admin_global_page
{
    public function doExecute(Core $Fly, $body, $head)
    {
        // Temp
        $Fly->getClass('output')->addJS("public/js/jquery.min.js");
        $Fly->getClass('output')->addJS("public/js/fly.js");

        $Fly->getClass('smarty')->assign('title', $Fly->getClass('output')->makeTitle());
        $Fly->getClass('smarty')->assign('body', $body);
        $Fly->getClass('smarty')->assign('head', $head);
        $template = $Fly->getClass('template')->get("admin", "global");
        $Fly->getClass('smarty')->display("string: " . $template);
    }
}