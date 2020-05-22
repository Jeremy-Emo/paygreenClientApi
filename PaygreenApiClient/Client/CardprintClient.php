<?php

namespace PaygreenApiClient\Client;

use Exception;
use PaygreenApiClient\Entity\Fingerprint;

class CardprintClient extends GenericClient
{
    /**
     * CardprintClient constructor.
     * @param string $id
     * @param string $privateKey
     * @param string $baseUrl
     */
    public function __construct(string $id, string $privateKey, string $baseUrl)
    {
        parent::__construct($id, $privateKey, $baseUrl);
    }

    /**
     * Request to API for cardprint creation
     * https://paygreen.fr/documentation/api-documentation-categorie?cat=paiement#tag/L'empreinte-de-carte%2Fpaths%2F~1api~1%7Bidentifiant%7D~1payins~1cardprint%2Fpost
     * @param Fingerprint $fp
     * @return array|null
     * @throws Exception
     */
    public function newCardprint(Fingerprint $fp) : ?array
    {
        $url = $this->baseUrl . "/" . $this->id . "/payins/cardprint";
        $data = $fp->castToArray();
        $apiResult = $this->builder->requestApi($url, 'POST', $data);
        if ($apiResult['error']) {
            $this->loggingHttpError(__FUNCTION__, $apiResult['httpCode']);
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
            $this->loggingHttpError(__FUNCTION__, $apiResult['httpCode']);
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
            $this->loggingHttpError(__FUNCTION__, $apiResult['httpCode']);
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
            $this->loggingHttpError(__FUNCTION__, $apiResult['httpCode']);
            return null;
        } else {
            return $apiResult['data'];
        }
    }
}