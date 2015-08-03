<?php

namespace Omnipay\NestPay\Message;

/**
 * NestPay Purchase Request
 * 
 * (c) Yasin Kuyu
 * 2015, insya.com
 * http://www.github.com/yasinkuyu/omnipay-nestpay
 */
class CreditRequest extends PurchaseRequest {

    public function getData() {

        $this->validate('amount');
        $currency = $this->getCurrency();

        $data['Type'] = 'PostAuth';
        $data['OrderId'] = $this->getOrderId();
        $data['Currency'] = $this->currencies[$currency];
        $data['Total'] = $this->getAmount();
        $data['Number'] = $this->getCard()->getNumber();

        return $data;
    }

}
