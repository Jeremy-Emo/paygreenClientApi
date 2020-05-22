<?php

namespace PaygreenApiClient\Client;

use Exception;
use PaygreenApiClient\RequestBuilder;

class GenericClient
{
    /**
     * Unique Id
     * @var string
     */
    protected $id;

    /**
     * Base url for Paygreen API
     * @var string
     */
    protected $baseUrl;

    /**
     * Request builder
     * @var RequestBuilder
     */
    protected $builder;

    /**
     * Last error code
     * @var int|null
     */
    protected $lastErrorCode;

    /**
     * GenericClient constructor.
     * @param string $id
     * @param string|null $privateKey
     * @param string $baseUrl
     * @throws Exception
     */
    public function __construct(string $id, ?string $privateKey = null, string $baseUrl = '')
    {
        if($privateKey === null) {
            error_log(get_class($this) . " - " . __FUNCTION__ . " : Private key not provided.");
            throw new Exception(get_class($this) . " - " . __FUNCTION__ . " : Private key not provided.");
        }
        $this->id = $id;
        $this->builder = new RequestBuilder($privateKey);
        $this->setBaseUrl($baseUrl);
        $this->lastErrorCode = null;
    }

    /**
     * @return int|null
     */
    public function getLastHttpErrorCode() : ?int
    {
        return $this->lastErrorCode;
    }

    /**
     * Setter for baseUrl
     * @param string $url
     */
    protected function setBaseUrl(string $url)
    {
        if (empty($url)) {
            $url = "https://paygreen.fr";
        }
        $this->baseUrl = $url . "/api";
    }

    /**
     * @param string $methodName
     * @param string|null $httpCode
     */
    protected function loggingHttpError(string $methodName, ?string $httpCode) {
        error_log(get_class($this) . " - " . $methodName . " : HttpCode = " . ($httpCode ?? "not provided"));
        $this->lastErrorCode = $httpCode ?? null;
    }
}