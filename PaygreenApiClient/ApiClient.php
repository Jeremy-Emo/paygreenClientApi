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
     * https://paygreen.fr/documentation/api-documentation-categorie?cat=paiement#tag/Les-moyens-de-paiement%2Fpaths%2F~1api~1%7Bidentifiant%7D~1paymenttype%2Fget
     * @return array|null
     * @throws Exception
     */
    public function getPaymentType() : ?array
    {
        $url = $this->baseUrl . "/" . $this->id . "/paymenttype";
        $apiResult = $this->builder->requestApi($url);
        if ($apiResult['error']) {
            $this->loggingError(__FUNCTION__, $apiResult['httpCode']);
            return null;
        } else {
            return $apiResult['data'];
        }
    }

    /**
     * Request to API for getting transaction $id informations, return null if it fails
     * https://paygreen.fr/documentation/api-documentation-categorie?cat=paiement#tag/Les-transactions%2Fpaths%2F~1api~1%7Bidentifiant%7D~1payins~1transaction~1%7Bid%7D%2Fget
     * @param string $id
     * @return array|null
     * @throws Exception
     */
    public function getTransactionInfos(string $id) : ?array
    {
        $url = $this->baseUrl . "/" . $this->id . "/payins/transaction/" . $id;
        $apiResult = $this->builder->requestApi($url);
        if ($apiResult['error']) {
            $this->loggingError(__FUNCTION__, $apiResult['httpCode']);
            return null;
        } else {
            return $apiResult['data'];
        }
    }

    /**
     * Request to API for validate transaction $id, return null if it fails
     * https://paygreen.fr/documentation/api-documentation-categorie?cat=paiement#tag/Les-transactions%2Fpaths%2F~1api~1%7Bidentifiant%7D~1payins~1transaction~1%7Bid%7D%2Fput
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
            $this->loggingError(__FUNCTION__, $apiResult['httpCode']);
            return null;
        } else {
            return $apiResult['data'];
        }
    }

    /**
     * Request to API for modify amount of transaction $id, return null if it fails
     * https://paygreen.fr/documentation/api-documentation-categorie?cat=paiement#tag/Les-transactions%2Fpaths%2F~1api~1%7Bidentifiant%7D~1payins~1transaction~1%7Bid%7D%2Fpatch
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
            $this->loggingError(__FUNCTION__, $apiResult['httpCode']);
            return null;
        } else {
            return $apiResult['data'];
        }
    }

    /**
     * Request to API for refund a transaction $id, return null if it fails
     * https://paygreen.fr/documentation/api-documentation-categorie?cat=paiement#tag/Les-transactions%2Fpaths%2F~1api~1%7Bidentifiant%7D~1payins~1transaction~1%7Bid%7D%2Fdelete
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
            $this->loggingError(__FUNCTION__, $apiResult['httpCode']);
            return null;
        } else {
            return $apiResult['data'];
        }
    }

    /**
     * Request to API for cash payment
     * https://paygreen.fr/documentation/api-documentation-categorie?cat=paiement#tag/Les-transactions%2Fpaths%2F~1api~1%7Bidentifiant%7D~1payins~1transaction~1cash%2Fpost
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
        return $this->payment(__FUNCTION__, $amount, $orderId, $currency, 'cash', $additionalData);
    }

    /**
     * Request to API for subscription payment
     * https://paygreen.fr/documentation/api-documentation-categorie?cat=paiement#tag/Les-transactions%2Fpaths%2F~1api~1%7Bidentifiant%7D~1payins~1transaction~1subscription%2Fpost
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
        return $this->payment(__FUNCTION__, $amount, $orderId, $currency, 'subscription', $additionalData);
    }

    /**
     * Request to API for multiple times payment
     * https://paygreen.fr/documentation/api-documentation-categorie?cat=paiement#tag/Les-transactions%2Fpaths%2F~1api~1%7Bidentifiant%7D~1payins~1transaction~1xtime%2Fpost
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
        return $this->payment(__FUNCTION__, $amount, $orderId, $currency, 'xtime', $additionalData);
    }

    /**
     * Request to API for payment with confirmation
     * https://paygreen.fr/documentation/api-documentation-categorie?cat=paiement#tag/Les-transactions%2Fpaths%2F~1api~1%7Bidentifiant%7D~1payins~1transaction~1tokenize%2Fpost
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
        return $this->payment(__FUNCTION__, $amount, $orderId, $currency, 'tokenize', $additionalData);
    }

    /**
     * Request to API for cancelling a payment
     * https://paygreen.fr/documentation/api-documentation-categorie?cat=paiement#tag/Les-transactions%2Fpaths%2F~1api~1%7Bidentifiant%7D~1payins~1transaction~1cancel%2Fpost
     * @return array|null
     */
    public function cancelPayment() : ?array
    {
        //TODO : implement method, documentation is inaccurate
        return null;
    }

    /**
     * Request to API for cardprint creation
     * https://paygreen.fr/documentation/api-documentation-categorie?cat=paiement#tag/L'empreinte-de-carte%2Fpaths%2F~1api~1%7Bidentifiant%7D~1payins~1cardprint%2Fpost
     * @param string $orderId
     * @param Buyer $buyer
     * @param array $additionalData
     * @return array|null
     * @throws Exception
     */
    public function newCardprint(string $orderId, Buyer $buyer, array $additionalData = []) : ?array
    {
        $url = $this->baseUrl . "/" . $this->id . "/payins/cardprint";
        $data = [
            'orderId' => $orderId,
            'buyer' => $buyer->castToArray()
        ];
        $data += $additionalData;
        $apiResult = $this->builder->requestApi($url, 'POST', $data);
        if ($apiResult['error']) {
            $this->loggingError(__FUNCTION__, $apiResult['httpCode']);
            return null;
        } else {
            return $apiResult['data'];
        }
    }

    /**
     * Request to API for getting cardprints
     * https://paygreen.fr/documentation/api-documentation-categorie?cat=paiement#tag/L'empreinte-de-carte%2Fpaths%2F~1api~1%7Bidentifiant%7D~1payins~1cardprint%2Fget
     * @return array|null
     * @throws Exception
     */
    public function getCardPrintList() : ?array
    {
        $url = $this->baseUrl . "/" . $this->id . "/payins/cardprint";
        $apiResult = $this->builder->requestApi($url, 'GET');
        if ($apiResult['error']) {
            $this->loggingError(__FUNCTION__, $apiResult['httpCode']);
            return null;
        } else {
            return $apiResult['data'];
        }
    }

    /**
     * Request to API for getting cardprint $id informations
     * https://paygreen.fr/documentation/api-documentation-categorie?cat=paiement#tag/L'empreinte-de-carte%2Fpaths%2F~1api~1%7Bidentifiant%7D~1payins~1cardprint~1%7Bid%7D%2Fget
     * @param string $id
     * @return array|null
     * @throws Exception
     */
    public function getCardPrintInfos(string $id) : ?array
    {
        $url = $this->baseUrl . "/" . $this->id . "/payins/cardprint" . $id;
        $apiResult = $this->builder->requestApi($url, 'GET');
        if ($apiResult['error']) {
            $this->loggingError(__FUNCTION__, $apiResult['httpCode']);
            return null;
        } else {
            return $apiResult['data'];
        }
    }

    /**
     * Request to API for deleting cardprint $id
     * https://paygreen.fr/documentation/api-documentation-categorie?cat=paiement#tag/L'empreinte-de-carte%2Fpaths%2F~1api~1%7Bidentifiant%7D~1payins~1cardprint~1%7Bid%7D%2Fdelete
     * @param string $id
     * @return array|null
     * @throws Exception
     */
    public function deleteCardPrint(string $id) : ?array
    {
        $url = $this->baseUrl . "/" . $this->id . "/payins/cardprint" . $id;
        $apiResult = $this->builder->requestApi($url, 'DELETE');
        if ($apiResult['error']) {
            $this->loggingError(__FUNCTION__, $apiResult['httpCode']);
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
        $this->baseUrl = $url . "/api";
    }

    /**
     * Common method for payment
     * @param string $methodForLogging
     * @param int $amount
     * @param string $orderId
     * @param string $currency
     * @param string $type
     * @param array $additionalData
     * @return array|null
     * @throws Exception
     */
    private function payment(string $methodForLogging, int $amount, string $orderId, string $currency, string $type, array $additionalData = []) : ?array
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
                error_log(get_class($this) . " - " . __FUNCTION__ . " : Invalid Argument for type of transaction.");
                throw new Exception(get_class($this) . " - " . __FUNCTION__ . " : Invalid Argument for type of transaction.");
        }
        $data = [
            'orderId' => $orderId,
            'amount' => $amount,
            'currency' => $currency
        ];
        $data += $additionalData;
        $apiResult = $this->builder->requestApi($url, 'POST', $data);
        if ($apiResult['error']) {
            $this->loggingError($methodForLogging, $apiResult['httpCode']);
            return null;
        } else {
            return $apiResult['data'];
        }
    }

    private function loggingError(string $methodName, ?string $httpCode) {
        error_log(get_class($this) . " - " . $methodName . " : HttpCode = " . ($httpCode ?? "not provided"));
        $this->lastErrorCode = $apiResult['httpCode'] ?? null;
    }
}