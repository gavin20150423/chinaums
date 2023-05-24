<?php


namespace Command;

use think\console\Input;
use think\console\Output;
use think\facade\App;

class SecretCommand extends \think\console\Command
{
    public function configure()
    {
        $this->setName('ums:create')
            ->setDescription('create china ums config file');
    }

    public function execute(Input $input, Output $output)
    {
        $this->createConfig($output);
    }

    public function createConfig($output)
    {
        $configFilePath = app()->getAppPath().'..'.DIRECTORY_SEPARATOR.'config'
            .DIRECTORY_SEPARATOR.'ums.php';

        if (is_file($configFilePath)) {
            $output->writeln('Config file is exist');

            return;
        }
        $res = copy(__DIR__.DIRECTORY_SEPARATOR.'..'.DIRECTORY_SEPARATOR.'..'
            .DIRECTORY_SEPARATOR.'config'.DIRECTORY_SEPARATOR
            .'config.php', $configFilePath);
        if (strpos(\think\App::VERSION, '6.0') !== false) {
            $config = file_get_contents($configFilePath);
            $config = str_replace('Tp5', 'Tp6', $config);
            file_put_contents($configFilePath, $config);
        }
        if ($res) {
            $output->writeln('Create config file success:'.$configFilePath);
        } else {
            $output->writeln('Create config file error');
        }
    }
}
