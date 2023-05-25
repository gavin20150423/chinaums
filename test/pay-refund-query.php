<?php

try {
    include "../vendor/autoload.php";
    // 1. 手动加载入口文件
    include "../include.php";

    // 2. 准备配置参数
    $config = include "../config/config.php";

    //3. 创建接口实例
    $ums = \Gavin\Ums\WePay\Pay::instance($config);

    //4. 组装参数，可以参考官方文档
    $options = [
        'merOrderId' => '10002305256815419165061306', // 需要使用退款接口中的 refundOrderId 字段
    ];
    // 查询订单结果
    $result = $ums->queryRefund($options);

    echo '<pre>';
    echo "\n--- 查询退款结果 ---\n";
    var_export($result);

} catch (Exception $exception) {
    // 出错啦，处理下吧
    echo $exception->getMessage() . PHP_EOL;
}