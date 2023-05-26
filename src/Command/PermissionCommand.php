<?php


namespace Command;

use think\console\Input;
use think\console\input\Option;
use think\console\Output;
use think\facade\App;

class PermissionCommand extends \think\console\Command
{
    public function configure()
    {
        $this->setName('ums:cache')
            ->addOption('user', 'u', Option::VALUE_OPTIONAL, 'set cache user')
            ->addOption('dir', 'd', Option::VALUE_OPTIONAL, 'set cache permission')
            ->setDescription('set china ums cache');
    }

    public function execute(Input $input, Output $output)
    {
        $cacheDir = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR;
        $user = $input->getOption('user');
        $dir = $input->getOption('dir');
        if ($user) {
            $rs = chown($cacheDir, $user);
            if($rs){
                $output->writeln($cacheDir . ' set user success: ' . $user);
            }
        }

        if ($dir) {
            $rs = chmod($cacheDir, $dir);
            if($rs){
                $output->writeln($cacheDir . ' set chmod success: ' . $dir);
            }
        }
    }
}
