<?php

declare(strict_types=1);

namespace App\ValueObject;

use Symfony\Component\Validator\Constraints as Assert;

class Address
{
    /**
     * @Assert\NotBlank
     * @Assert\Country
     */
    private ?string $countryCode;

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private ?string $city;

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private ?string $street;

    /**
     * @Assert\NotBlank
     * @Assert\Type("string")
     */
    private ?string $postcode;

    public function __construct(
        ?string $countryCode = null,
        ?string $city = null,
        ?string $street = null,
        ?string $postcode = null
    ) {
        $this->countryCode = $countryCode ?? null;
        $this->city = $city ?? null;
        $this->street = $street ?? null;
        $this->postcode = $postcode ?? null;
    }

    public function getCountry(): ?string
    {
        return $this->countryCode;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function getStreet(): ?string
    {
        return $this->street;
    }

    public function getPostcode(): ?string
    {
        return $this->postcode;
    }
}
