<?php
date_default_timezone_set("PRC");
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
        'merOrderId' => '34TJ202305251559394112',
        'refundAmount' => '1',
        'targetOrderId' => '34TJ202305241559394112',
        'refundDesc' => '取消订单'
    ];
    // 生成预支付码
    $result = $ums->createRefund($options);

    echo '<pre>';
    echo "\n--- 申请退款 ---\n";
    var_export($result);

} catch (Exception $exception) {
    // 出错啦，处理下吧
    echo $exception->getMessage() . PHP_EOL;
}