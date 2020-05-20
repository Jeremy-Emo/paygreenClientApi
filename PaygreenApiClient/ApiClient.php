<?php

namespace PaygreenApiClient;

use Exception;

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
        $url = $this->baseUrl . '/' . $this->id . '/paymenttype';
        $apiResult = $this->builder->requestApi($url);
        if ($apiResult['error']) {
            switch ($apiResult['httpCode']) {
                case 400 :
                    error_log("PaygreenApiClient\ApiClient - getPaymentType : Invalid ID " . $this->id);
                    break;
                case 404 :
                    error_log("PaygreenApiClient\ApiClient - getPaymentType : PartnerConfig Not Found");
                    break;
                default:
                    error_log("PaygreenApiClient\ApiClient - getPaymentType : Error but no http code provided");
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
        $url = $this->baseUrl . '/' . $this->id . '/payins/transaction/' . $id;
        $apiResult = $this->builder->requestApi($url);
        if ($apiResult['error']) {
            switch ($apiResult['httpCode']) {
                case 400 :
                    error_log("PaygreenApiClient\ApiClient - getTransactionInfos : Invalid ID " . $this->id);
                    break;
                case 404 :
                    error_log("PaygreenApiClient\ApiClient - getTransactionInfos : Transaction Not Found");
                    break;
                default:
                    error_log("PaygreenApiClient\ApiClient - getTransactionInfos : Error but no http code provided");
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
        $url = $this->baseUrl . '/' . $this->id . '/payins/transaction/' . $id;
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
                default:
                    error_log("PaygreenApiClient\ApiClient - validateTransaction : Error but no http code provided");
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
        $url = $this->baseUrl . '/' . $this->id . '/payins/transaction/' . $id;
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
                default:
                    error_log("PaygreenApiClient\ApiClient - modifyAmount : Error but no http code provided");
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
        $url = $this->baseUrl . '/' . $this->id . '/payins/transaction/' . $id;
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
                default:
                    error_log("PaygreenApiClient\ApiClient - refund : Error but no http code provided");
                    break;
            }
            $this->lastErrorCode = $apiResult['httpCode'] ?? null;
            return null;
        } else {
            return $apiResult['data'];
        }
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
        $this->baseUrl = $url . '/api';
    }
}