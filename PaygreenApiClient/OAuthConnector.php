<?php

namespace PaygreenApiClient;

use Exception;

class OAuthConnector
{
    /**
     * @var string
     */
    private $accessPublic;

    /**
     * @var string
     */
    private  $accessPrivate;

    /**
     * @var string
     */
    private $ipAddress;

    /**
     * OAuthConnector constructor.
     */
    public function __construct()
    {
        $this->setIpAddress();
    }

    /**
     * @param string $url
     * @param string $email
     * @param string $name
     * @param string|null $phone
     * @return OAuthConnector|null
     * @throws Exception
     */
    public function generateKeys(string $url, string $email, string $name, ?string $phone) : ?self
    {
        $builder = new RequestBuilder();
        $data = [
            'email' => $email,
            'name' => $name,
            'ipAddress' => $this->ipAddress,
        ];
        if ($phone !== null) {
            $data['phoneNumber'] = $phone;
        }
        $apiResult = $builder->requestApi($url . '/api/auth/', 'POST', $data);
        if ($apiResult['error']) {
            error_log(get_class($this) . " - " . __FUNCTION__ . " : HttpCode = " . ($apiResult['httpCode'] ?? "not provided"));
            return null;
        } else {
            $this->accessPublic = $apiResult['data']['data']['accessPublic'];
            $this->accessPrivate = $apiResult['data']['data']['accessSecret'];
            return $this;
        }
    }

    /**
     * @param string $baseUrl
     * @return string
     */
    public function getAuthorizeUrl(string $baseUrl) : string
    {
        return $baseUrl . "/api/auth/authorize";
    }

    /**
     * @param string $url
     * @param string $grantType
     * @param string $code
     * @return array|null
     * @throws Exception
     */
    public function getDataPostAuth(string $url, string $grantType, string $code) : ?array
    {
        $builder = new RequestBuilder();
        $data = [
            'client_id' => $this->accessPublic,
            'grant_type' => $grantType,
            'code' => $code
        ];
        $apiResult = $builder->requestApi($url . '/api/auth/accessToken', 'POST', $data);
        if ($apiResult['error']) {
            error_log(get_class($this) . " - " . __FUNCTION__ . " : HttpCode = " . ($apiResult['httpCode'] ?? "not provided"));
            return null;
        } else {
            return $apiResult['data'];
        }
    }

    /**
     * @return string
     */
    public function getAccessPublic() : string
    {
        return $this->accessPublic;
    }

    /**
     * @return string
     */
    public function getAccessPrivate() : string
    {
        return $this->accessPrivate;
    }

    /**
     * Auto-setter for ipAddress
     */
    private function setIpAddress()
    {
        $this->ipAddress = $_SERVER['SERVER_ADDR'] ?? $_SERVER['REMOTE_ADDR'] ?? $_SERVER['SERVER_NAME'] ?? '127.0.0.1';
    }
}