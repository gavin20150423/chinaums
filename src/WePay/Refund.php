<?php

namespace Gavin\Ums\WePay;

use Contracts\BasicWePay;

class Refund extends BasicWePay
{
    /**
     * 创建退款订单
     * @param array $options
     * @return mixed
     */
    public function create(array $options)
    {
        $url = 'https://api-mop.chinaums.com/v1/netpay/refund';
        return $this->callPostApi($url, $options, true);
    }

    /**
     * 查询退款
     * @param array $options
     * @return mixed
     */
    public function query(array $options)
    {
        $url = 'https://api-mop.chinaums.com/v1/netpay/refund-query';
        return $this->callPostApi($url, $options, true);
    }
}