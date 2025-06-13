<?php

namespace Gavin\Ums\WePay;

use Contracts\BasicWePay;

class Pay extends BasicWePay
{
    /**
     * 统一下单
     * @param array $options
     * @return mixed
     */
    public function createOrder(array $options)
    {
        return Order::instance($this->config->get())->create($options);
    }

    /**
     * 创建APP支付订单
     * @param array $options
     * @return mixed
     */
    public function createAppOrder(array $options)
    {
        return Order::instance($this->config->get())->createAppPay($options);
    }

     /**
     * @param array $options
     * @return mixed
     */
    public function createQRCodeOrder(array $options)
    {
        return Order::instance($this->config->get())->createQrCode($options);
    }

    /**
     * 查询订单
     * @param array $options
     * @return mixed
     */
    public function queryOrder(array $options)
    {
        return Order::instance($this->config->get())->query($options);
    }

    /**
     * 关闭订单
     * @param $out_trade_no
     * @return mixed
     */
    public function closeOrder($out_trade_no)
    {
        return Order::instance($this->config->get())->close($out_trade_no);
    }

    /**
     * 申请退款
     * @param array $options
     * @return mixed
     */
    public function createRefund(array $options)
    {
        return Refund::instance($this->config->get())->create($options);
    }

    /**
     * 查询退款
     * @param array $options
     * @return mixed
     */
    public function queryRefund(array $options)
    {
        return Refund::instance($this->config->get())->query($options);
    }
}