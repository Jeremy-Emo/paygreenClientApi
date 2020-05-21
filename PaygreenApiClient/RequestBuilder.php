<?php

namespace PaygreenApiClient;

use Exception;

class RequestBuilder
{
    /**
     * Private key
     * @var string
     */
    private $privateKey;

    public function __construct(string $privateKey)
    {
        $this->privateKey = $privateKey;
    }

    /**
     * Base function for request Paygreen API. Need curl or fopen
     * @param string $url
     * @param string $verb
     * @param array|null $data
     * @return array
     * @throws Exception
     */
    public function requestApi(string $url, string $verb = 'GET', ?array $data = null) : array
    {
        $content = ($data !== null ? json_encode($data) : '');

        if (extension_loaded('curl')) {
            $result = $this->requestWithCurl($url, $verb, $content);
        } else if (ini_get('allow_url_fopen')) {
            $result = $this->requestWithoutCurl($url, $verb, $content);
        } else {
            error_log("PaygreenApiClient\RequestBuilder - requestApi : curl or fopen must be used.");
            throw new Exception("PaygreenApiClient\RequestBuilder - requestApi : curl or fopen must be used.");
        }

        return $result;
    }

    /**
     * Request with curl
     * @param string $url
     * @param string $verb
     * @param string $content
     * @return array
     * @throws Exception
     */
    private function requestWithCurl(string $url, string $verb, string $content) : array
    {
        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => $verb,
            CURLOPT_POSTFIELDS => $content,
            CURLOPT_HTTPHEADER => [
                "accept: application/json",
                "Authorization: Bearer " . $this->privateKey,
                "cache-control: no-cache",
                "content-type: application/json",
            ],
        ]);

        $response = curl_exec($ch);

        if ($curlError = curl_errno($ch)) {
            error_log("PaygreenApiClient\RequestBuilder - requestWithCurl : curl error n°" . $curlError);
            throw new Exception("PaygreenApiClient\RequestBuilder - requestWithCurl : curl error n°" . $curlError);
        } else {
            switch ($httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE)) {
                case 200:
                    return [
                        'error' => false,
                        'data' => json_decode($response, true)
                    ];
                default:
                    return [
                        'error' => true,
                        'httpCode' => $httpCode,
                    ];
            }
        }
    }

    /**
     * Request with fopen
     * @param string $url
     * @param string $verb
     * @param string $content
     * @return array
     */
    private function requestWithoutCurl(string $url, string $verb, string $content) : array
    {
        $opts = [
            'http' => [
                'method'    =>  $verb,
                'header'    =>  "Accept: application/json\r\n" .
                    "Content-Type: application/json\r\n".
                    "Authorization: Bearer " . $this->privateKey,
                'content'   =>  $content,
                'ignore_errors' => true,
            ]
        ];
        $context = stream_context_create($opts);
        try {
            $response = file_get_contents($url, false, $context);
            $httpResponseStatus = $http_response_header[0] ?? null;
            preg_match('{HTTP\/\S*\s(\d{3})}', $httpResponseStatus, $statusCode);
            if ($response === false) {
                throw new Exception("can't get informations with fopen.");
            } else if ($statusCode[1] !== 200) {
                return [
                    'error' => true,
                    'httpCode' => $statusCode[1],
                ];
            } else {
                return [
                    'error' => false,
                    'data' => json_decode($response, true)
                ];
            }
        } catch (Exception $e) {
            error_log("PaygreenApiClient\RequestBuilder - requestWithoutCurl : raised exception : " . $e);
            return [
                'error' => true,
                'httpCode' => false,
            ];
        }
    }
}