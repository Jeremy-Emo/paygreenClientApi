<?php

namespace PaygreenApiClient\Entity;

class Fingerprint
{
    /**
     * @var string
     */
    private $orderId;

    /**
     * @var string
     */
    private $paymentType;

    /**
     * @var string
     */
    private $returnedUrl;

    /**
     * @var string
     */
    private $notifiedUrl;

    /**
     * @var Buyer
     */
    private $buyer;

    /**
     * @var string
     */
    private $ttl;

    /**
     * Fingerprint constructor.
     * @param string $orderId
     * @param Buyer $buyer
     */
    public function __construct(string $orderId, Buyer $buyer)
    {
        $this->orderId = $orderId;
        $this->buyer = $buyer;
    }

    /**
     * @param string $type
     * @return $this
     */
    public function setPaymentType(string $type) : self
    {
        $this->paymentType = $type;

        return $this;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setReturnedUrl(string $url) : self
    {
        $this->returnedUrl = $url;

        return $this;
    }

    /**
     * @param string $url
     * @return $this
     */
    public function setNotifiedUrl(string $url) : self
    {
        $this->notifiedUrl = $url;

        return $this;
    }

    /**
     * @param string $ttl
     * @return $this
     */
    public function setTtl(string $ttl) : self
    {
        $this->ttl = $ttl;

        return $this;
    }

    /**
     * Cast properly this object to array
     * @return array
     */
    public function castToArray() : array
    {
        $array = [
            'orderId' => $this->orderId,
            'buyer' => $this->buyer->castToArray()
        ];

        $valuesToCheck = [
            'paymentType' => $this->paymentType ?? null,
            'returned_url' => $this->returnedUrl ?? null,
            'notified_url' => $this->notifiedUrl ?? null,
            'idFingerprint' => $this->idFingerPrint ?? null,
            'ttl' => $this->ttl ?? null
        ];

        foreach ($valuesToCheck as $key => $value) {
            if ($value !== null) {
                $array[$key] = $value;
            }
        }

        return $array;
    }
}