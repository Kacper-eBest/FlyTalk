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
        $css_dir = '/cache/skin_' . Member::getProperty("template") . '/style/';
        if ($handle = opendir(CORE_ROOT_PATH . $css_dir)) {
            while (false !== ($file = readdir($handle))) {
                $ext = substr($file, strrpos($file, '.') + 1);
                if ($ext != "css") continue;
                if ($file != "." && $file != "..") {
                    $Fly->getClass('output')->addCSS($css_dir . $file);
                }
            }
            closedir($handle);
        }
        $Fly->getClass('output')->addJS("/public/js/jquery.min.js");
        $Fly->getClass('output')->addJS("/public/js/date.format.js");
        $Fly->getClass('output')->addJS("/public/js/fly.js");

        $template = $Fly->getClass('template')->get("global", "global", ["title" => $Fly->getClass('output')->makeTitle(), "body" => $body, "head" => $head]);
        $Fly->getClass('smarty')->display("string: " . $template['output']);
    }
}