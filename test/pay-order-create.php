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
        'merOrderId' => time(),
        'totalAmount' => '1',
        'subOpenId' => 'oiRjs4vIreNS5NEgSk087ylBXm-E',
        'attachedData' => '备注',
        'orderDesc' => '安师傅代驾小程序'
    ];
    // 生成预支付码
    $result = $ums->createOrder($options);

    echo '<pre>';
    echo "\n--- 创建预支付 ---\n";
    var_export($result);

} catch (Exception $exception) {
    // 出错啦，处理下吧
    echo $exception->getMessage() . PHP_EOL;
}