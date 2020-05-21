<?php

namespace PaygreenApiClient\Entity;

class Buyer
{
    /**
     * @var string
     */
    private $id;

    /**
     * @var string
     */
    private $lastName;

    /**
     * @var string
     */
    private $firstName;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $country;

    /**
     * @var string
     */
    private $companyName;

    /**
     * Buyer constructor.
     * @param string $id
     * @param string $lastName
     * @param string $firstName
     * @param string $email
     * @param string $country
     * @param string $companyName
     */
    public function __construct(string $id, string $lastName, string $firstName, string $email, string $country, string $companyName)
    {
        $this->id = $id;
        $this->lastName = $lastName;
        $this->firstName = $firstName;
        $this->email = $email;
        $this->country = $country;
        $this->companyName = $companyName;
    }

    /**
     * Cast properly this object to array
     * @return array
     */
    public function castToArray() : array
    {
        return [
            'id' => $this->id,
            'lastName' => $this->lastName,
            'firstName' => $this->firstName,
            'email' => $this->email,
            'country' => $this->country,
            'companyName' => $this->companyName
        ];
    }
}