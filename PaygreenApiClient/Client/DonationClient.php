<?php

namespace PaygreenApiClient\Client;

use Exception;

class DonationClient extends GenericClient
{
    /**
     * DonationClient constructor.
     * @param string $id
     * @param string $privateKey
     * @param string $baseUrl
     */
    public function __construct(string $id, string $privateKey, string $baseUrl)
    {
        parent::__construct($id, $privateKey, $baseUrl);
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
            $this->loggingHttpError(__FUNCTION__, $apiResult['httpCode']);
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
            $this->loggingHttpError(__FUNCTION__, $apiResult['httpCode']);
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
            $this->loggingHttpError(__FUNCTION__, $apiResult['httpCode']);
            return null;
        } else {
            return $apiResult['data'];
        }
    }
}