<?php

namespace PaygreenApiClient\Client;

use Exception;

class TransferClient extends GenericClient
{
    /**
     * TransferClient constructor.
     * @param string $id
     * @param string $privateKey
     * @param string $baseUrl
     */
    public function __construct(string $id, string $privateKey, string $baseUrl)
    {
        parent::__construct($id, $privateKey, $baseUrl);
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
            $this->loggingHttpError(__FUNCTION__, $apiResult['httpCode']);
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
            $this->loggingHttpError(__FUNCTION__, $apiResult['httpCode']);
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
            $this->loggingHttpError(__FUNCTION__, $apiResult['httpCode']);
            return null;
        } else {
            return $apiResult['data'];
        }
    }
}