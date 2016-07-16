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
        // TODO Loading CSS form database
        // TODO automatic minify

        $Fly->getClass('smarty')->assign('title', $Fly->getClass('output')->makeTitle());
        $Fly->getClass('smarty')->assign('body', $body);
        $Fly->getClass('smarty')->assign('head', $head);
        $template = $Fly->getClass('template')->get("global", "global");
        $Fly->getClass('smarty')->display("string: " . $template);
    }
}