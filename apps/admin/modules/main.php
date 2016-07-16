<?php

/**
 * Created by PhpStorm.
 * User: kacpe
 * Date: 16.07.2016
 * Time: 14:30
 */
class class_admin_main_page extends Command
{
    public function doExecute(Core $Fly)
    {
        $body = "dupa";
        //$this->Fly->getClass('smarty')->assign('test', $body);

        //$template = $this->Fly->getClass('template')->get("main", "global");
        $this->Fly->getClass('output')->setTitle("Testuje");
        $this->Fly->getClass('output')->addContent($body);
        $this->Fly->getClass('output')->sendOutput();
    }
}