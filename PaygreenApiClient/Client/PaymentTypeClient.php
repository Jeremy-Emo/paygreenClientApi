<?php

namespace PaygreenApiClient\Client;

use Exception;

class PaymentTypeClient extends GenericClient
{
    /**
     * PaymentTypeClient constructor.
     * @param string $id
     * @param string $privateKey
     * @param string $baseUrl
     */
    public function __construct(string $id, string $privateKey, string $baseUrl)
    {
        parent::__construct($id, $privateKey, $baseUrl);
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
            $this->loggingHttpError(__FUNCTION__, $apiResult['httpCode']);
            return null;
        } else {
            return $apiResult['data'];
        }
    }
}