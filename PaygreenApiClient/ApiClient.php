<?php

namespace PaygreenApiClient;

class ApiClient
{
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

    public function __construct(string $id, string $privateKey, string $baseUrl = '')
    {
        $this->builder = new RequestBuilder($id, $privateKey);
        $this->setBaseUrl($baseUrl);
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