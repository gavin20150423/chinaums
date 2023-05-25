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