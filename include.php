<?php
spl_autoload_register(function ($classname) {
    $classname = str_replace('Gavin\Ums\\', '', $classname);
    $pathname = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR;
    $filename = str_replace('\\', DIRECTORY_SEPARATOR, $classname) . '.php';
    if (file_exists($pathname . $filename)) {
        foreach (['AliPay', 'Contracts', 'UacPay', 'WePay', 'Ums', 'Exceptions'] as $prefix) {
            if (stripos($classname, $prefix) === 0) {
                include $pathname . $filename;
                return true;
            }
        }
    }
    return false;
});