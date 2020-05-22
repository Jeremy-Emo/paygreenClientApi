<?php

namespace PaygreenApiClient;

use Exception;
use PaygreenApiClient\Entity\Buyer;
use PaygreenApiClient\Entity\Card;
use PaygreenApiClient\Entity\OrderDetails;
use PaygreenApiClient\Entity\Transaction;

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
     * @param Transaction $transaction
     * @return array|null
     * @throws Exception
     */
    public function cashPayment(Transaction $transaction) : ?array
    {
        if (!$transaction->testIfBuyerAndCardAreSet()) {
            error_log(get_class($this) . " - " . __FUNCTION__ . " : buyer or card missing in Transaction object.");
            throw new Exception(get_class($this) . " - " . __FUNCTION__ . " : buyer or card missing in Transaction object.");
        }
        return $this->payment(__FUNCTION__, $transaction, 'cash');
    }

    /**
     * Request to API for subscription payment
     * https://paygreen.fr/documentation/api-documentation-categorie?cat=paiement#tag/Les-transactions%2Fpaths%2F~1api~1%7Bidentifiant%7D~1payins~1transaction~1subscription%2Fpost
     * @param Transaction $transaction
     * @return array|null
     * @throws Exception
     */
    public function subscriptionPayment(Transaction $transaction) : ?array
    {
        if (!$transaction->testIfOrderDetailsAndCardAreSet()) {
            error_log(get_class($this) . " - " . __FUNCTION__ . " : orderDetails or card missing in Transaction object.");
            throw new Exception(get_class($this) . " - " . __FUNCTION__ . " : orderDetails or card missing in Transaction object.");
        }
        return $this->payment(__FUNCTION__, $transaction, 'subscription');
    }

    /**
     * Request to API for multiple times payment
     * https://paygreen.fr/documentation/api-documentation-categorie?cat=paiement#tag/Les-transactions%2Fpaths%2F~1api~1%7Bidentifiant%7D~1payins~1transaction~1xtime%2Fpost
     * @param Transaction $transaction
     * @return array|null
     * @throws Exception
     */
    public function xTimePayment(Transaction $transaction) : ?array
    {
        if (!$transaction->testIfOrderDetailsAndCardAreSet()) {
            error_log(get_class($this) . " - " . __FUNCTION__ . " : orderDetails or card missing in Transaction object.");
            throw new Exception(get_class($this) . " - " . __FUNCTION__ . " : orderDetails or card missing in Transaction object.");
        }
        return $this->payment(__FUNCTION__, $transaction, 'xtime');
    }

    /**
     * Request to API for payment with confirmation
     * https://paygreen.fr/documentation/api-documentation-categorie?cat=paiement#tag/Les-transactions%2Fpaths%2F~1api~1%7Bidentifiant%7D~1payins~1transaction~1tokenize%2Fpost
     * @param Transaction $transaction
     * @return array|null
     * @throws Exception
     */
    public function withConfirmationPayment(Transaction $transaction) : ?array
    {
        if (!$transaction->testIfBuyerAndCardAreSet()) {
            error_log(get_class($this) . " - " . __FUNCTION__ . " : buyer or card missing in Transaction object.");
            throw new Exception(get_class($this) . " - " . __FUNCTION__ . " : buyer or card missing in Transaction object.");
        }
        return $this->payment(__FUNCTION__, $transaction, 'tokenize');
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
        $url = $this->baseUrl . "/" . $this->id . "/payins/cardprint/" . $id;
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
        $url = $this->baseUrl . "/" . $this->id . "/payins/cardprint/" . $id;
        $apiResult = $this->builder->requestApi($url, 'DELETE');
        if ($apiResult['error']) {
            $this->loggingError(__FUNCTION__, $apiResult['httpCode']);
            return null;
        } else {
            return $apiResult['data'];
        }
    }

    /**
     * Request to API for creating donation for transaction $id
     * https://paygreen.fr/documentation/api-documentation-categorie?cat=paiement#tag/Les-dons%2Fpaths%2F~1api~1%7Bidentifiant%7D~1solidarity~1%7Bid%7D%2Fpatch
     * @param string $id
     * @param string $associationId
     * @param string $currency
     * @param int $amount
     * @return array|null
     * @throws Exception
     */
    public function donate(string $id, string $associationId, string $currency, int $amount) : ?array
    {
        $url = $this->baseUrl . "/" . $this->id . "/solidarity/" . $id;
        $data = [
            'associationId' => $associationId,
            'currency' => $currency,
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
     * Request to API for getting informations of donation of transaction $id
     * https://paygreen.fr/documentation/api-documentation-categorie?cat=paiement#tag/Les-dons%2Fpaths%2F~1api~1%7Bidentifiant%7D~1solidarity~1%7Bid%7D%2Fget
     * @param string $id
     * @return array|null
     * @throws Exception
     */
    public function showDonation(string $id) : ?array
    {
        $url = $this->baseUrl . "/" . $this->id . "/solidarity/" . $id;
        $apiResult = $this->builder->requestApi($url, 'GET');
        if ($apiResult['error']) {
            $this->loggingError(__FUNCTION__, $apiResult['httpCode']);
            return null;
        } else {
            return $apiResult['data'];
        }
    }

    /**
     * Request to API for deleting donation of transaction $id
     * https://paygreen.fr/documentation/api-documentation-categorie?cat=paiement#tag/Les-dons%2Fpaths%2F~1api~1%7Bidentifiant%7D~1solidarity~1%7Bid%7D%2Fdelete
     * @param string $id
     * @return array|null
     * @throws Exception
     */
    public function deleteDonation(string $id) : ?array
    {
        $url = $this->baseUrl . "/" . $this->id . "/solidarity/" . $id;
        $apiResult = $this->builder->requestApi($url, 'DELETE');
        if ($apiResult['error']) {
            $this->loggingError(__FUNCTION__, $apiResult['httpCode']);
            return null;
        } else {
            return $apiResult['data'];
        }
    }

    /**
     * Request to API for getting transfer informations
     * https://paygreen.fr/documentation/api-documentation-categorie?cat=paiement#tag/Les-virements%2Fpaths%2F~1api~1%7Bidentifiant%7D~1payout~1transfer~1%7Bid%7D%2Fget
     * @param $id
     * @return array|null
     * @throws Exception
     */
    public function getTransfer(string $id) : ?array
    {
        $url = $this->baseUrl . "/" . $this->id . "/payout/transfer/" . $id;
        $apiResult = $this->builder->requestApi($url, 'GET');
        if ($apiResult['error']) {
            $this->loggingError(__FUNCTION__, $apiResult['httpCode']);
            return null;
        } else {
            return $apiResult['data'];
        }
    }

    /**
     * Request to API for creating transfer
     * https://paygreen.fr/documentation/api-documentation-categorie?cat=paiement#tag/Les-virements%2Fpaths%2F~1api~1%7Bidentifiant%7D~1payout~1transfer%2Fpost
     * @param string $amount
     * @param string $currency
     * @param int $bankId
     * @param string $callbackUrl
     * @return array|null
     * @throws Exception
     */
    public function createTransfer(string $amount, string $currency, int $bankId, string $callbackUrl = '') : ?array
    {
        $url = $this->baseUrl . "/" . $this->id . "/payout/transfer";
        $data = [
            'amount' => $amount,
            'currency' => $currency,
            'bankId' => $bankId
        ];
        if (!empty($callbackUrl)) {
            $data['callbackUrl'] = $callbackUrl;
        }
        $apiResult = $this->builder->requestApi($url, 'POST', $data);
        if ($apiResult['error']) {
            $this->loggingError(__FUNCTION__, $apiResult['httpCode']);
            return null;
        } else {
            return $apiResult['data'];
        }
    }

    /**
     * Request to API for getting transfers list
     * https://paygreen.fr/documentation/api-documentation-categorie?cat=paiement#tag/Les-virements%2Fpaths%2F~1api~1%7Bidentifiant%7D~1payout~1transfer%2Fget
     * @return array|null
     * @throws Exception
     */
    public function getTransfersList() : ?array
    {
        $url = $this->baseUrl . "/" . $this->id . "/payout/transfer";
        $apiResult = $this->builder->requestApi($url, 'GET');
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
     * @param Transaction $transaction
     * @param string $type
     * @return array|null
     * @throws Exception
     */
    private function payment(string $methodForLogging, Transaction $transaction, string $type) : ?array
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
        $data = $transaction->castToArray();
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