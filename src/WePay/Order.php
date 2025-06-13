<?php

namespace Gavin\Ums\WePay;

use Contracts\BasicWePay;

class Order extends BasicWePay
{
    /**
     * 统一下单
     * @param array $options
     * @return mixed
     */
    public function create(array $options)
    {
        if ($this->config->get('msgsrcid')) {
            //如果商户订单号不包含指定前缀 则增加前缀
            if (!strstr($options['merOrderId'], $this->config->get('msgsrcid'))) {
                $options['merOrderId'] = $this->config->get('msgsrcid') . $options['merOrderId'];
            }
        }
        $options['tradeType'] = 'MINI';
        $url = 'https://api-mop.chinaums.com/v1/netpay/wx/unified-order';
        return $this->callPostApi($url, $options, true);
    }

    /**
     * 创建APP支付
     * @param array $options
     * @return mixed
     */
    public function createAppPay(array $options)
    {
        if ($this->config->get('msgsrcid')) {
            //如果商户订单号不包含指定前缀 则增加前缀
            if (!strstr($options['merOrderId'], $this->config->get('msgsrcid'))) {
                $options['merOrderId'] = $this->config->get('msgsrcid') . $options['merOrderId'];
            }
        }
        
        // 设置交易类型为APP
        $options['tradeType'] = 'APP';
        
        // 如果没有设置msgId，则自动生成
        if (!isset($options['msgId'])) {
            $options['msgId'] = md5(date('YmdHis') . mt_rand(100, 999));
        }
        
        // 确保金额格式正确（单位：分）
        if (isset($options['totalAmount']) && !is_string($options['totalAmount'])) {
            $options['totalAmount'] = (string)$options['totalAmount'];
        }
        
        $url = 'https://api-mop.chinaums.com/v1/netpay/wx/app-pre-order';
        return $this->callPostApi($url, $options, true);
    }

    /**
     * 创建扫码支付
     * @param array $options
     * @return mixed
     */
    public function createQrCode(array $options)
    {
        $url = 'https://api-mop.chinaums.com/v1/netpay/bills/get-qrcode';
        return $this->callPostApi($url, $options, true, false);
    }


    /**
     * 查询订单
     * @param array $options
     * @return mixed
     */
    public function query(array $options)
    {
        $url = 'https://api-mop.chinaums.com/v1/netpay/query';
        return $this->callPostApi($url, $options, true);
    }

    /**
     * 关闭订单
     * @param $merOrderId
     * @return mixed
     */
    public function close($merOrderId)
    {
        $url = 'https://api-mop.chinaums.com/v1/netpay/close';
        return $this->callPostApi($url, ['merOrderId' => $merOrderId], true);
    }
}