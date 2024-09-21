<?php
namespace Omnipay\NestPay\Message;

use DOMDocument;
use Omnipay\Common\Message\AbstractRequest;
use Omnipay\NestPay\ThreeDPayHostingGateway;

/**
 *
 * NestPay 3D Pay Hosting Post Request
 *
 * @author Burak USGURLU <burak@uskur.com.tr>
 *
 */
class PostRequest extends AbstractRequest
{

    protected $endpoint = '';

    protected $endpoints = [
        'test' => 'https://entegrasyon.asseco-see.com.tr/fim/est3Dgate',
        'isbank' => 'https://sanalpos.isbank.com.tr/servlet/est3Dgate'
    ];

    public function getData()
    {
        $this->validate('card', 'transactionId');
        if ($this->getStoreType() == '3d_pay') {
            $this->getCard()->validate();
        }
        $data = [
            //amount|BillToCompany|BillToName|callbackUrl|clientid|currency|failUrl|hashAlgorithm|Instalment|lang|okurl|refreshtime|rnd|storetype|TranType|storeKey
            'amount' => $this->getAmount(),
            'BillToCompany' => '',
            'BillToName' => mb_substr("{$this->getCard()->getFirstName()} {$this->getCard()->getLastName()}", 0, 255),
            'callbackUrl' => $this->getNotifyUrl(),
            'clientid' => $this->getClientId(),
            'currency' => $this->getCurrencyNumeric(),
            'failUrl' => $this->getReturnUrl(),
            'hashAlgorithm' => 'ver3',
            'Instalment' => $this->getInstallment(),
            'lang' => $this->getLang(), // en, tr
            'okurl' => $this->getReturnUrl(),
            'refreshtime' => $this->getRefreshtime(),
            'rnd' => $this->getRnd(),
            'storetype' => $this->getStoreType(),
            'TranType' => $this->getType(),
        ];
        $plaintext = "";
        foreach($data as $key => $value) {
            $plaintext .= str_replace("|", "\\|", str_replace("\\", "\\\\", $value)) . "|";
        }
        $plaintext .= str_replace("|", "\\|", str_replace("\\", "\\\\", $this->getStoreKey()));
        $data['hash'] = base64_encode(pack('H*', hash('sha512', $plaintext)));
        $data['oid'] = $this->getTransactionId();
        $data['Email'] = $this->getCard()->getEmail();
        $data['tel'] = mb_substr($this->getCard()->getPhone(), 0, 32);
        $data['Fadres'] = mb_substr($this->getCard()->getAddress1(), 0, 255);
        $data['Fadres2'] = mb_substr($this->getCard()->getAddress2(), 0, 255);

        return $data;
    }

    public function sendData($data)
    {
        return $this->response = new PostResponse($this);
    }

    public function getBank()
    {
        return $this->getParameter('bank');
    }

    public function setBank($value)
    {
        return $this->setParameter('bank', $value);
    }

    public function getUserName()
    {
        return $this->getParameter('username');
    }

    public function setUserName($value)
    {
        return $this->setParameter('username', $value);
    }

    public function getClientId()
    {
        return $this->getParameter('clientId');
    }

    public function setClientId($value)
    {
        return $this->setParameter('clientId', $value);
    }

    public function getRnd()
    {
        return microtime();
    }

    public function getPassword()
    {
        return $this->getParameter('password');
    }

    public function setPassword($value)
    {
        return $this->setParameter('password', $value);
    }

    public function getInstallment()
    {
        return $this->getParameter('installment');
    }

    public function setInstallment($value)
    {
        return $this->setParameter('installment', $value);
    }

    public function getType()
    {
        return $this->getParameter('type');
    }

    public function setType($value)
    {
        return $this->setParameter('type', $value);
    }

    public function getMoneyPoints()
    {
        return $this->getParameter('moneypoints');
    }

    public function setMoneyPoints($value)
    {
        return $this->setParameter('moneypoints', $value);
    }

    public function getStoreType()
    {
        return $this->getParameter('storetype');
    }

    public function setStoreType($value)
    {
        return $this->setParameter('storetype', $value);
    }

    public function getStoreKey()
    {
        return $this->getParameter('storekey');
    }

    public function setStoreKey($value)
    {
        return $this->setParameter('storekey', $value);
    }

    public function getLang()
    {
        return $this->getParameter('lang');
    }

    public function setLang($value)
    {
        return $this->setParameter('lang', $value);
    }

    public function getRefreshtime()
    {
        return $this->getParameter('refreshtime') ? $this->getParameter('refreshtime') : 30;
    }

    public function setRefreshtime($value)
    {
        return $this->setParameter('refreshtime', $value);
    }

    public function getEndpoint()
    {
        return $this->endpoints[$this->getTestMode() == TRUE ? 'test' : $this->getBank()];
    }

}
