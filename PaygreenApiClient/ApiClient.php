<?php

namespace PaygreenApiClient;

use Exception;
use PaygreenApiClient\Entity\Buyer;
use PaygreenApiClient\Entity\Card;
use PaygreenApiClient\Entity\OrderDetails;

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
     * Request builder
     * @var RequestBuilder
     */
    private $builder;

    /**
     * Last error code
     * @var int|null
     */
    private $lastErrorCode;

    /**
     * ApiClient constructor.
     * @param string $id
     * @param string $privateKey
     * @param string $baseUrl
     */
    public function __construct(string $id, string $privateKey, string $baseUrl = '')
    {
        $this->id = $id;
        $this->builder = new RequestBuilder($privateKey);
        $this->setBaseUrl($baseUrl);
        $this->lastErrorCode = null;
    }

    /**
     * Getter for lastErrorCode, getting last Http Error Code
     * @return int|null
     */
    public function getLastHttpErrorCode() : ?int
    {
        return $this->lastErrorCode;
    }

    /**
     * Request to API for getting Payment Types, return null if it fails
     * @return array|null
     * @throws Exception
     */
    public function getPaymentType() : ?array
    {
        $url = $this->baseUrl . "/" . $this->id . "/paymenttype";
        $apiResult = $this->builder->requestApi($url);
        if ($apiResult['error']) {
            switch ($apiResult['httpCode']) {
                case 400 :
                    error_log("PaygreenApiClient\ApiClient - getPaymentType : Invalid ID " . $this->id);
                    break;
                case 404 :
                    error_log("PaygreenApiClient\ApiClient - getPaymentType : PartnerConfig Not Found");
                    break;
                case false :
                    error_log("PaygreenApiClient\ApiClient - getPaymentType : Error but no http code provided");
                    break;
                default :
                    error_log("PaygreenApiClient\ApiClient - getPaymentType : Error http " . $apiResult['httpCode']);
                    break;
            }
            $this->lastErrorCode = $apiResult['httpCode'] ?? null;
            return null;
        } else {
            return $apiResult['data'];
        }
    }

    /**
     * Request to API for getting transaction $id informations, return null if it fails
     * @param string $id
     * @return array|null
     * @throws Exception
     */
    public function getTransactionInfos(string $id) : ?array
    {
        $url = $this->baseUrl . "/" . $this->id . "/payins/transaction/" . $id;
        $apiResult = $this->builder->requestApi($url);
        if ($apiResult['error']) {
            switch ($apiResult['httpCode']) {
                case 400 :
                    error_log("PaygreenApiClient\ApiClient - getTransactionInfos : Invalid ID " . $this->id);
                    break;
                case 404 :
                    error_log("PaygreenApiClient\ApiClient - getTransactionInfos : Transaction Not Found");
                    break;
                case false :
                    error_log("PaygreenApiClient\ApiClient - getTransactionInfos : Error but no http code provided");
                    break;
                default :
                    error_log("PaygreenApiClient\ApiClient - getTransactionInfos : Error http " . $apiResult['httpCode']);
                    break;
            }
            $this->lastErrorCode = $apiResult['httpCode'] ?? null;
            return null;
        } else {
            return $apiResult['data'];
        }
    }

    /**
     * Request to API for validate transaction $id, return null if it fails
     * @param string $id
     * @param int $amount these are cents not euros
     * @param string $message
     * @return array|null
     * @throws Exception
     */
    public function validateTransaction(string $id, int $amount, string $message) : ?array
    {
        $url = $this->baseUrl . "/" . $this->id . "/payins/transaction/" . $id;
        $data = [
            'amount' => $amount,
            'message' => $message
        ];
        $apiResult = $this->builder->requestApi($url, 'PUT', $data);
        if ($apiResult['error']) {
            switch ($apiResult['httpCode']) {
                case 400 :
                    error_log("PaygreenApiClient\ApiClient - validateTransaction : Invalid ID " . $this->id);
                    break;
                case 404 :
                    error_log("PaygreenApiClient\ApiClient - validateTransaction : Transaction Not Found");
                    break;
                case false :
                    error_log("PaygreenApiClient\ApiClient - validateTransaction : Error but no http code provided");
                    break;
                default :
                    error_log("PaygreenApiClient\ApiClient - validateTransaction : Error http " . $apiResult['httpCode']);
                    break;
            }
            $this->lastErrorCode = $apiResult['httpCode'] ?? null;
            return null;
        } else {
            return $apiResult['data'];
        }
    }

    /**
     * Request to API for modify amount of transaction $id, return null if it fails
     * @param string $id
     * @param int $amount these are cents not euros
     * @return array|null
     * @throws Exception
     */
    public function modifyAmount(string $id, int $amount) : ?array
    {
        $url = $this->baseUrl . "/" . $this->id . "/payins/transaction/" . $id;
        $data = [
            'amount' => $amount
        ];
        $apiResult = $this->builder->requestApi($url, 'PATCH', $data);
        if ($apiResult['error']) {
            switch ($apiResult['httpCode']) {
                case 400 :
                    error_log("PaygreenApiClient\ApiClient - modifyAmount : Invalid ID " . $this->id);
                    break;
                case 404 :
                    error_log("PaygreenApiClient\ApiClient - modifyAmount : Transaction Not Found");
                    break;
                case false :
                    error_log("PaygreenApiClient\ApiClient - modifyAmount : Error but no http code provided");
                    break;
                default :
                    error_log("PaygreenApiClient\ApiClient - modifyAmount : Error http " . $apiResult['httpCode']);
                    break;
            }
            $this->lastErrorCode = $apiResult['httpCode'] ?? null;
            return null;
        } else {
            return $apiResult['data'];
        }
    }

    /**
     * Request to API for refund a transaction $id, return null if it fails
     * @param string $id
     * @param int|null $amount these are cents not euros
     * @return array|null
     * @throws Exception
     */
    public function refund(string $id, ?int $amount) : ?array
    {
        $url = $this->baseUrl . "/" . $this->id . "/payins/transaction/" . $id;
        $data = ($amount !== null ? ['amount' => $amount] : null);
        $apiResult = $this->builder->requestApi($url, 'DELETE', $data);
        if ($apiResult['error']) {
            switch ($apiResult['httpCode']) {
                case 400 :
                    error_log("PaygreenApiClient\ApiClient - refund : Invalid ID " . $this->id);
                    break;
                case 404 :
                    error_log("PaygreenApiClient\ApiClient - refund : Transaction Not Found");
                    break;
                case false :
                    error_log("PaygreenApiClient\ApiClient - refund : Error but no http code provided");
                    break;
                default :
                    error_log("PaygreenApiClient\ApiClient - refund : Error http " . $apiResult['httpCode']);
                    break;
            }
            $this->lastErrorCode = $apiResult['httpCode'] ?? null;
            return null;
        } else {
            return $apiResult['data'];
        }
    }

    /**
     * Request to API for cash payment
     * @param int $amount
     * @param string $orderId
     * @param string $currency
     * @param Buyer $buyer
     * @param Card $card
     * @param array $additionalData
     * @return array|null
     * @throws Exception
     */
    public function cashPayment(int $amount, string $orderId, string $currency, Buyer $buyer, Card $card, array $additionalData = []) : ?array
    {
        $additionalData += [
            'buyer' => $buyer->castToArray(),
            'card' => $card->castToArray()
        ];
        return $this->payment($amount, $orderId, $currency, 'cash', $additionalData);
    }

    /**
     * Request to API for subscription payment
     * @param int $amount
     * @param string $orderId
     * @param string $currency
     * @param OrderDetails $orderDetails
     * @param Card $card
     * @param array $additionalData
     * @return array|null
     * @throws Exception
     */
    public function subscriptionPayment(int $amount, string $orderId, string $currency, OrderDetails $orderDetails, Card $card, array $additionalData = []) : ?array
    {
        $additionalData += [
            'orderDetails' => $orderDetails->castToArray(),
            'card' => $card->castToArray()
        ];
        return $this->payment($amount, $orderId, $currency, 'subscription', $additionalData);
    }

    /**
     * Request to API for multiple times payment
     * @param int $amount
     * @param string $orderId
     * @param string $currency
     * @param OrderDetails $orderDetails
     * @param Card $card
     * @param array $additionalData
     * @return array|null
     * @throws Exception
     */
    public function xTimePayment(int $amount, string $orderId, string $currency, OrderDetails $orderDetails, Card $card, array $additionalData = []) : ?array
    {
        $additionalData += [
            'orderDetails' => $orderDetails->castToArray(),
            'card' => $card->castToArray()
        ];
        return $this->payment($amount, $orderId, $currency, 'xtime', $additionalData);
    }

    /**
     * Request to API for payment with confirmation
     * @param int $amount
     * @param string $orderId
     * @param string $currency
     * @param Buyer $buyer
     * @param Card $card
     * @param array $additionalData
     * @return array|null
     * @throws Exception
     */
    public function withConfirmationPayment(int $amount, string $orderId, string $currency, Buyer $buyer, Card $card, array $additionalData = []) : ?array
    {
        $additionalData += [
            'orderDetails' => $buyer->castToArray(),
            'card' => $card->castToArray()
        ];
        return $this->payment($amount, $orderId, $currency, 'tokenize', $additionalData);
    }

    /**
     * Request to API for cancelling a payment
     * @return array|null
     */
    public function cancelPayment() : ?array
    {
        //TODO : implement method, documentation is inaccurate
        return null;
    }


    /**
     * Setter for baseUrl
     * @param string $url
     */
    private function setBaseUrl(string $url)
    {
        if (empty($url)) {
            $url = "https://paygreen.fr";
        }
        $this->baseUrl = $url . "/api";
    }

    /**
     * Common method for payment
     * @param int $amount
     * @param string $orderId
     * @param string $currency
     * @param array $additionalData
     * @param string $type
     * @return array|null
     * @throws Exception
     */
    private function payment(int $amount, string $orderId, string $currency, string $type, array $additionalData = []) : ?array
    {
        $url = $this->baseUrl . "/" . $this->id . "/payins/transaction/";
        switch ($type) {
            case 'cash':
                $url .= "cash";
                break;
            case 'subscription':
                $url .= "subscription";
                break;
            case 'xtime':
                $url .= "xtime";
                break;
            case 'tokenize':
                $url .= "tokenize";
                break;
            default:
                error_log("PaygreenApiClient\ApiClient - payment : Invalid Argument for type of transaction.");
                throw new Exception("PaygreenApiClient\ApiClient - payment : Invalid Argument for type of transaction.");
        }
        $data = [
            'orderId' => $orderId,
            'amount' => $amount,
            'currency' => $currency
        ];
        $data += $additionalData;
        $apiResult = $this->builder->requestApi($url, 'POST', $data);
        if ($apiResult['error']) {
            switch ($apiResult['httpCode']) {
                case 400 :
                    error_log("PaygreenApiClient\ApiClient - payment : Invalid ID " . $this->id);
                    break;
                case 404 :
                    error_log("PaygreenApiClient\ApiClient - payment : Transaction Not Found");
                    break;
                case false :
                    error_log("PaygreenApiClient\ApiClient - payment : Error but no http code provided");
                    break;
                default :
                    error_log("PaygreenApiClient\ApiClient - payment : Error http " . $apiResult['httpCode']);
                    break;
            }
            $this->lastErrorCode = $apiResult['httpCode'] ?? null;
            return null;
        } else {
            return $apiResult['data'];
        }
    }
}