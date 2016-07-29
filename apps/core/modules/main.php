<?php

/**
 * Created by PhpStorm.
 * User: kacpe
 * Date: 16.07.2016
 * Time: 14:30
 */
class class_core_main_page extends Command
{
    public function doExecute(Core $Fly)
    {
        $body = '<a href="index.php?app=admin">ACP</a>';

        $template = $this->Fly->getClass('template')->get("forum", "main", ["test" => $body]);
        $this->Fly->getClass('output')->setTitle("Strona główna");
        $this->Fly->getClass('output')->addContent($template);
        $this->Fly->getClass('output')->sendOutput();
    }
}