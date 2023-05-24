<?php

try {
    include "../vendor/autoload.php";
    // 1. 手动加载入口文件
    include "../include.php";

    // 2. 准备配置参数
    $config = include "../config/config.php";

    //3. 创建接口实例
    $ums = \WePay\Pay::instance($config);

    // 取消订单
    $result = $ums->closeOrder("34TJ1684931062");

    echo '<pre>';
    echo "\n--- 取消订单 ---\n";
    var_export($result);

} catch (Exception $exception) {
    // 出错啦，处理下吧
    echo $exception->getMessage() . PHP_EOL;
}