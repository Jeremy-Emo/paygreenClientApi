<?php

namespace PaygreenApiClient;

use PaygreenApiClient\Client\CardprintClient;
use PaygreenApiClient\Client\DonationClient;
use PaygreenApiClient\Client\PaymentTypeClient;
use PaygreenApiClient\Client\TransactionClient;
use PaygreenApiClient\Client\TransferClient;

class ApiClient
{
    /**
     * Unique Id
     * @var string
     */
    private $id;

    /**
     * Base url for Paygreen API
     * @var string
     */
    private $baseUrl;

    /**
     * Private key
     * @var string
     */
    private $privateKey;

    /**
     * @var mixed
     */
    private $lastClientInstantiate;

    /**
     * ApiClient constructor.
     * @param string $id
     * @param string $privateKey
     * @param string $baseUrl
     */
    public function __construct(string $id, string $privateKey, string $baseUrl = '')
    {
        $this->id = $id;
        $this->baseUrl = $baseUrl;
        $this->privateKey = $privateKey;
    }

    /**
     * @return PaymentTypeClient
     */
    public function getPaymentTypeClient() : PaymentTypeClient
    {
        if ($this->lastClientInstantiate instanceof PaymentTypeClient ) {
            return $this->lastClientInstantiate;
        } else {
            $this->lastClientInstantiate = new PaymentTypeClient($this->id, $this->privateKey, $this->baseUrl);
            return $this->lastClientInstantiate;
        }
    }

    /**
     * @return TransactionClient
     */
    public function getTransactionClient() : TransactionClient
    {
        if ($this->lastClientInstantiate instanceof TransactionClient ) {
            return $this->lastClientInstantiate;
        } else {
            $this->lastClientInstantiate = new TransactionClient($this->id, $this->privateKey, $this->baseUrl);
            return $this->lastClientInstantiate;
        }
    }

    /**
     * @return CardprintClient
     */
    public function getCardprintCLient() : CardprintClient
    {
        if ($this->lastClientInstantiate instanceof CardprintClient ) {
            return $this->lastClientInstantiate;
        } else {
            $this->lastClientInstantiate = new CardprintClient($this->id, $this->privateKey, $this->baseUrl);
            return $this->lastClientInstantiate;
        }
    }

    /**
     * @return DonationClient
     */
    public function getDonationClient() : DonationClient
    {
        if ($this->lastClientInstantiate instanceof DonationClient ) {
            return $this->lastClientInstantiate;
        } else {
            $this->lastClientInstantiate = new DonationClient($this->id, $this->privateKey, $this->baseUrl);
            return $this->lastClientInstantiate;
        }
    }

    /**
     * @return TransferClient
     */
    public function getTransferClient() : TransferClient
    {
        if ($this->lastClientInstantiate instanceof TransferClient ) {
            return $this->lastClientInstantiate;
        } else {
            $this->lastClientInstantiate = new TransferClient($this->id, $this->privateKey, $this->baseUrl);
            return $this->lastClientInstantiate;
        }
    }
}