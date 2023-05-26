<?php

use Command\SecretCommand;
use Command\PermissionCommand;

class Service extends \think\Service
{
    public function boot()
    {
        $this->commands(SecretCommand::class);
        $this->commands(PermissionCommand::class);
    }
}
