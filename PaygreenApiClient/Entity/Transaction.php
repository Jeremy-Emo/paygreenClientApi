<?php

namespace PaygreenApiClient\Entity;

class Transaction
{
    /**
     * @var string
     */
    private $orderId;

    /**
     * @var int
     */
    private $amount;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var Buyer
     */
    private $buyer;

    /**
     * @var OrderDetails
     */
    private $orderDetails;

    /**
     * @var Card
     */
    private $card;

    /**
     * @var string
     */
    private $ttl;

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
     * @var int
     */
    private $idFingerPrint;

    /**
     * @var array
     */
    private $metadata;

    /**
     * @var array
     */
    private $eligibleAmount;

    /**
     * Transaction constructor.
     * @param string $orderId
     * @param int $amount
     * @param string $currency
     */
    public function __construct(string $orderId, int $amount, string $currency)
    {
        $this->orderId = $orderId;
        $this->amount = $amount;
        $this->currency = $currency;
    }

    /**
     * @param Buyer $buyer
     * @param Card $card
     * @return $this
     */
    public function setBuyerAndCard(Buyer $buyer, Card $card) : self
    {
        $this->buyer = $buyer;
        $this->card = $card;

        return $this;
    }

    /**
     * @return bool
     */
    public function testIfBuyerAndCardAreSet() : bool
    {
        return ($this->buyer !== null && $this->card !== null);
    }

    /**
     * @return bool
     */
    public function testIfOrderDetailsAndCardAreSet() : bool
    {
        return ($this->orderDetails !== null && $this->card !== null);
    }

    /**
     * @param OrderDetails $od
     * @param Card $card
     * @return $this
     */
    public function setOrderDetailsAndCard(OrderDetails $od, Card $card) : self
    {
        $this->orderDetails = $od;
        $this->card = $card;

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
     * @param int $id
     * @return $this
     */
    public function setIdFingerPrint(int $id) : self
    {
        $this->idFingerPrint = $id;

        return $this;
    }

    /**
     * @param array $metadata
     * @return $this
     */
    public function setMetadata(array $metadata) : self
    {
        $this->metadata = $metadata;

        return $this;
    }

    /**
     * @param array $amount
     * @return $this
     */
    public function setEligibleAmount(array $amount) : self
    {
        $this->eligibleAmount = $amount;

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
            'amount' => $this->amount,
            'currency' => $this->currency
        ];

        $valuesToCheck = [
            'paymentType' => $this->paymentType ?? null,
            'returned_url' => $this->returnedUrl ?? null,
            'notified_url' => $this->notifiedUrl ?? null,
            'idFingerprint' => $this->idFingerPrint ?? null,
            'orderDetails' => $this->orderDetails !== null ? $this->orderDetails->castToArray() : null,
            'buyer' => $this->buyer !== null ? $this->buyer->castToArray() : null,
            'card' => $this->card !== null ? $this->card->castToArray() : null,
            'metadata' => $this->metadata ?? null,
            'eligibleAmount' => $this->eligibleAmount ?? null,
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