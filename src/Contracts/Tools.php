<?php

namespace Contracts;

use Exceptions\InvalidResponseException;
use Exceptions\LocalCacheException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class Tools
{
    /**
     * 缓存路径
     * @var null
     */
    public static $cache_path = null;

    /**
     * 缓存写入操作
     * @var array
     */
    public static $cache_callable = [
        'set' => null, // 写入缓存
        'get' => null, // 获取缓存
        'del' => null, // 删除缓存
        'put' => null, // 写入文件
    ];

    /**
     * 网络缓存
     * @var array
     */
    private static $cache_curl = [];

    /**
     * 产生随机字符串
     * @param int $length 指定字符长度
     * @param string $str 字符串前缀
     * @return string
     */
    public static function createNoncestr(int $length = 32, string $str = ""): string
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * 以Post请求接口
     * @param $url
     * @param array $data
     * @param bool $needAuth
     * @return mixed
     * @throws GuzzleException
     * @throws InvalidResponseException
     */
    public static function callPostApi($url, array $data)
    {
        $options = [
            'headers' => [
                'Content-Type' => 'application/json;charset=utf-8'
            ],
            'json' => $data
        ];
        $client = new Client();
        try {
            $response = $client->post($url, $options)->getBody()->getContents();
            $rs = json_decode($response, true);
            if(in_array($rs['errCode'], ['SUCCESS', '0000'])){
                return $rs;
            }
            throw new InvalidResponseException($rs['errInfo'] ?? '请求失败');
        } catch (\Exception $exception) {
            throw new InvalidResponseException($exception->getMessage(), $exception->getCode());
        }
    }

    /**
     * 缓存配置与存储
     * @param string $name 缓存名称
     * @param string $value 缓存内容
     * @param int $expired 缓存时间(0表示永久缓存)
     * @return string
     * @throws LocalCacheException
     */
    public static function setCache($name, $value = '', $expired = 3600)
    {
        if (is_callable(self::$cache_callable['set'])) {
            return call_user_func_array(self::$cache_callable['set'], func_get_args());
        }
        $file = self::_getCacheName($name);
        $data = ['name' => $name, 'value' => $value, 'expired' => time() + intval($expired)];
        if (!file_put_contents($file, serialize($data))) {
            throw new LocalCacheException('local cache error.', '0');
        }
        return $file;
    }

    /**
     * 获取缓存内容
     * @param string $name 缓存名称
     * @return null|mixed
     */
    public static function getCache($name)
    {
        if (is_callable(self::$cache_callable['get'])) {
            return call_user_func_array(self::$cache_callable['get'], func_get_args());
        }
        $file = self::_getCacheName($name);
        if (file_exists($file) && is_file($file) && ($content = file_get_contents($file))) {
            $data = unserialize($content);
            if (isset($data['expired']) && (intval($data['expired']) === 0 || intval($data['expired']) >= time())) {
                return $data['value'];
            }
            self::delCache($name);
        }
        return null;
    }

    /**
     * 移除缓存文件
     * @param string $name 缓存名称
     * @return boolean
     */
    public static function delCache($name)
    {
        if (is_callable(self::$cache_callable['del'])) {
            return call_user_func_array(self::$cache_callable['del'], func_get_args());
        }
        $file = self::_getCacheName($name);
        return !file_exists($file) || @unlink($file);
    }

    /**
     * 应用缓存目录
     * @param string $name
     * @return string
     */
    private static function _getCacheName($name)
    {
        if (empty(self::$cache_path)) {
            self::$cache_path = dirname(dirname(__DIR__)) . DIRECTORY_SEPARATOR . 'cache' . DIRECTORY_SEPARATOR . 'File' . DIRECTORY_SEPARATOR;
        }
        self::$cache_path = rtrim(self::$cache_path, '/\\') . DIRECTORY_SEPARATOR;
        file_exists(self::$cache_path) || mkdir(self::$cache_path, 0777, true);
        return self::$cache_path . $name;
    }
}