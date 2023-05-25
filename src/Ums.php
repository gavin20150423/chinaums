<?php

use Contracts\DataArray;
use Exceptions\InvalidInstanceException;

/**
 * ----- WePay -----
 * @method \Gavin\Ums\WePay\Order WePayOrder($options = []) static 发起微信支付
 */
class Ums
{
    /**
     * 静态配置
     * @var DataArray
     */
    private static $config;

    public static function config($option = null)
    {
        if (is_array($option)) {
            self::$config = new DataArray($option);
        }
        if (self::$config instanceof DataArray) {
            return self::$config->get();
        }
        return [];
    }

    /**
     * 静态魔术加载方法
     * @param string $name 静态类名
     * @param array $arguments 参数集合
     * @return mixed
     * @throws InvalidInstanceException
     */
    public static function __callStatic(string $name, array $arguments)
    {
        if (substr($name, 0, 6) === 'UacPay') {
            $class = 'UacPay\\' . substr($name, 6);
        } elseif (substr($name, 0, 6) === 'AliPay') {
            $class = 'AliPay\\' . substr($name, 6);
        } elseif (substr($name, 0, 5) === 'WePay') {
            $class = 'WePay\\' . substr($name, 5);
        }
        if (!empty($class) && class_exists($class)) {
            $option = array_shift($arguments);
            $config = is_array($option) ? $option : self::$config->get();
            return new $class($config);
        }
        throw new InvalidInstanceException("class {$name} not found");
    }
}