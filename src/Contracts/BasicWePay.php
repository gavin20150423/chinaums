<?php

namespace Contracts;

use Contracts\Base;
use Contracts\DataArray;
use Exceptions\InvalidArgumentException;
use Exceptions\InvalidResponseException;
use Exceptions\LocalCacheException;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

date_default_timezone_set('PRC');

/**
 * 微信支付基础类
 * Class BasicPay
 */
class BasicWePay
{
    /**
     * 商户配置
     * @var DataArray
     */
    protected $config;

    /**
     * 当前请求数据
     * @var DataArray
     */
    protected $params;

    /**
     * 静态缓存
     * @var static
     */
    protected static $cache;

    /**
     * WeChat constructor.
     * @param array $options
     */
    public function __construct(array $options)
    {
        if (empty($options['app_id'])) {
            throw new InvalidArgumentException("Missing Config -- [app_id]");
        }
        if (empty($options['app_key'])) {
            throw new InvalidArgumentException("Missing Config -- [app_key]");
        }
        if (empty($options['mid'])) {
            throw new InvalidArgumentException("Missing Config -- [mid]");
        }
        if (empty($options['tid'])) {
            throw new InvalidArgumentException("Missing Config -- [tid]");
        }

        $this->config = new DataArray($options);
        // 商户基础参数
        $this->params = new DataArray([
            'mid' => $this->config->get('mid'),
            'tid' => $this->config->get('tid'),
            'instMid' => $this->config->get('instmid'),
            'requestTimestamp' => date('Y-m-d H:i:s')
        ]);
        // 商户参数支持
        if ($this->config->get('sub_appid')) {
            $this->params->set('subAppId', $this->config->get('sub_appid'));
        }
        if ($this->config->get('notify_url')) {
            $this->params->set('notifyUrl', $this->config->get('notify_url'));
        }
        if ($this->config->get('return_url')) {
            $this->params->set('returnUrl', $this->config->get('return_url'));
        }
        if($this->config->get('limit_credit_card')){
            $this->params->set('limitCreditCard', $this->config->get('limit_credit_card'));
        }
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
     * 生成支付签名
     * @param array $data 参与签名的数据
     * @param string $signType 参与签名的类型
     * @param string $buff 参与签名字符串前缀
     * @return string
     */
    public function getPaySign(array $data, string $signType = 'sha256', string $buff = '')
    {
        ksort($data);
        if (isset($data['sign'])) unset($data['sign']);
        if (isset($data['signMethod'])) unset($data['signMethod']);
        foreach ($data as $k => $v) $buff .= $v;
        $buff .= $this->config->get('app_key');
        if (strtoupper($signType) === 'MD5') {
            return strtoupper(md5($buff));
        }
        return hash('SHA256', $buff);
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
    protected function callPostApi($url, array $data, bool $needAuth = false)
    {
        $options = [
            'headers' => [
                'Content-Type' => 'application/json;charset=utf-8'
            ]
        ];
        if ($needAuth) {
            $base = new Base($this->config->get());
            $accessToken = $base->getAccessToken();
            $options['headers']['Authorization'] = "OPEN-ACCESS-TOKEN AccessToken=" . $accessToken;
        }
        $client = new Client();
        try {
            $params = $this->params->merge($data);
            $params['sign'] = $this->getPaySign($params);
            $options['json'] = $params;
            $response = $client->post($url, $options)->getBody()->getContents();
            $rs = json_decode($response, true);
            if (in_array($rs['errCode'], ['SUCCESS', '0000'])) {
                return $rs;
            }
            throw new InvalidResponseException(json_encode($rs, 256));
        } catch (\Exception $exception) {
            throw new InvalidResponseException($exception->getMessage(), $exception->getCode());
        }
    }
}