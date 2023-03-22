<?php

declare(strict_types=1);

namespace App\ValueObject;

class Coordinates
{
    private string $lat;
    private string $lng;

    public function __construct(string $lat, string $lng)
    {
        $this->lat = $lat;
        $this->lng = $lng;
    }

    public function getLat(): string
    {
        return $this->lat;
    }

    public function getLng(): string
    {
        return $this->lng;
    }
}
