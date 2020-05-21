<?php

namespace PaygreenApiClient\Entity;

class Card
{
    /**
     * @var string
     */
    private $token;

    /**
     * Card constructor.
     * @param string $token
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Cast properly this object to array
     * @return array
     */
    public function castToArray() : array
    {
        return [
            'token' => $this->token,
        ];
    }
}