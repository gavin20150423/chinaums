<?php

use Command\SecretCommand;

class Service extends \think\Service
{
    public function boot()
    {
        $this->commands(SecretCommand::class);
    }
}
