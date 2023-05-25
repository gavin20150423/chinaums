ChinaUms for PHP
----

* ChinaUms基于银联商务官方接口文档开发，优化并完善
* 运行最底要求 PHP 版本 7.0 , 建议在 PHP7.2 以上运行以获取最佳性能；
* ChinaUms 针对 access_token 失效增加了自动刷新机制；
* 获取认证的部分接口需要缓存数据在本地，因此对目录需要有写权限；
* 鼓励大家使用 composer 来管理您的第三方库，方便后期更新操作；

功能描述
----
目前已实现功能：
* 微信支付（小程序）
* *  创建订单、订单查询、订单关闭、申请退款、退款查询

未来将实现：
* 支付宝支付
* 银联云闪付
* 企业网银支付

安装使用
----
1.1 通过 Composer 来管理安装

```shell
# 首次安装 线上版本（稳定）
composer require gavin/chinaums

# 首次安装 开发版本（开发）
composer require gavin/chinaums dev-master

# 更新 chinaums
composer update gavin/chinaums
```

1.2 如果不使用 Composer， 可以下载 ChinaUms 并解压到项目中

```php
# 在项目中加载初始化文件
include "您的目录/ChinaUms/include.php";
```

1.3 如果使用的是thinkphp6，发布配置文件到config
```shell
php think ums:create
```

微信支付
---

```php
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
        'subOpenId' => 'xxxxx',
        'attachedData' => '备注',
        'orderDesc' => '测试描述'
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
```

开源协议
----

* gavin/chinaums 基于`MIT`协议发布，任何人可以用在任何地方，不受约束
* gavin/chinaums 部分代码来自互联网，若有异议，可以联系作者进行删除