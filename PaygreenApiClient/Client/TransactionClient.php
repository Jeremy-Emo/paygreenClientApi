<?php

namespace PaygreenApiClient\Client;

use Exception;
use PaygreenApiClient\Entity\Transaction;
use PaygreenApiClient\Exception\DataException;

class TransactionClient extends GenericClient
{
    /**
     * TransactionClient constructor.
     * @param string $id
     * @param string $privateKey
     * @param string $baseUrl
     */
    public function __construct(string $id, string $privateKey, string $baseUrl)
    {
        parent::__construct($id, $privateKey, $baseUrl);
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
            $this->loggingHttpError(__FUNCTION__, $apiResult['httpCode']);
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
            $this->loggingHttpError(__FUNCTION__, $apiResult['httpCode']);
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
            $this->loggingHttpError(__FUNCTION__, $apiResult['httpCode']);
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
            $this->loggingHttpError(__FUNCTION__, $apiResult['httpCode']);
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
            throw new DataException(get_class($this) . " - " . __FUNCTION__ . " : buyer or card missing in Transaction object.");
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
            throw new DataException(get_class($this) . " - " . __FUNCTION__ . " : orderDetails or card missing in Transaction object.");
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
            throw new DataException(get_class($this) . " - " . __FUNCTION__ . " : orderDetails or card missing in Transaction object.");
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
            throw new DataException(get_class($this) . " - " . __FUNCTION__ . " : buyer or card missing in Transaction object.");
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
            $this->loggingHttpError($methodForLogging, $apiResult['httpCode']);
            return null;
        } else {
            return $apiResult['data'];
        }
    }
}