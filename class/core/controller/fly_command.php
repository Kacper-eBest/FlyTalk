<?php

/**
 * Created by PhpStorm.
 * User: kacpe
 * Date: 16.07.2016
 * Time: 14:10
 */
abstract class Command
{
    protected $Fly;
    protected $DB;
    protected $settings;
    protected $request;

    final public function __construct()
    {
    }

    public function makeRegistryShortcuts(Core $Fly)
    {
        $this->Fly = $Fly;
        $this->DB = $this->Fly->DB();
        $this->settings =& $this->Fly->fetchSettings();
        $this->request =& $this->Fly->fetchRequest();
    }

    public function execute(Core $Fly)
    {
        $this->makeRegistryShortcuts($Fly);
        $this->doExecute($Fly);
    }

    protected abstract function doExecute(Core $Fly);
}