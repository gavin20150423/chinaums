<?php

namespace Contracts;

use Exceptions\InvalidArgumentException;

class Base
{
    /**
     * 当前微信配置
     * @var DataArray
     */
    public $config;

    /**
     * 访问AccessToken
     * @var string
     */
    public $access_token = '';

    /**
     * 静态缓存
     * @var static
     */
    protected static $cache;

    public function __construct(array $options)
    {
        if (empty($options['app_id'])) {
            throw new InvalidArgumentException("Missing Config -- [app_id]");
        }
        if (empty($options['app_key'])) {
            throw new InvalidArgumentException("Missing Config -- [app_id]");
        }
        $this->config = new DataArray($options);
    }

    /**
     * 静态创建对象
     * @param array $config
     * @return static
     */
    public static function instance(array $config)
    {
        $key = md5(get_called_class() . serialize($config));
        if (isset(self::$cache[$key])) return self::$cache[$key];
        return self::$cache[$key] = new static($config);
    }

    /**
     * 生成签名
     * @return string
     */
    public function getSign(array $data, string $buff = ''): string
    {
        if (isset($data['sign'])) unset($data['sign']);
        if (isset($data['signMethod'])) unset($data['signMethod']);
        foreach ($data as $k => $v) $buff .= $v;
        $buff .= $this->config->get('app_key');
        return hash('sha256', $buff);
    }

    /**
     * 获取缓存
     * @return mixed|string|null
     */
    public function getAccessToken()
    {
        if (!empty($this->access_token)) {
            return $this->access_token;
        }
        $cache = $this->config->get('app_id') . '_ums_access_token';
        $this->access_token = Tools::getCache($cache);
        if (!empty($this->access_token)) {
            return $this->access_token;
        }
        $data = [
            'appId' => $this->config->get('app_id'),
            'timestamp' => date('YmdHis'),
            'nonce' => Tools::createNoncestr(),
            'signMethod' => 'SHA256',
        ];
        $data['signature'] = $this->getSign($data);
        $url = "https://api-mop.chinaums.com/v1/token/access";
        $result = Tools::callPostApi($url, $data);

        if (!empty($result['accessToken'])) {
            Tools::setCache($cache, $result['accessToken'], 3000);
        }
        return $this->access_token = $result['accessToken'];
    }

    /**
     * 设置缓存
     * @param $accessToken
     * @return void
     */
    public function setAccessToken($accessToken)
    {
        if (!is_string($accessToken)) {
            throw new InvalidArgumentException("Invalid AccessToken type, need string.");
        }
        $cache = $this->config->get('appid') . '_ums_access_token';
        Tools::setCache($cache, $this->access_token = $accessToken);
    }

    /**
     * 删除缓存的token
     * @return bool
     */
    public function delAccessToken(): bool
    {
        $this->access_token = '';
        return Tools::delCache($this->config->get('appid') . '_ums_access_token');
    }
}