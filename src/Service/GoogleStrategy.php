<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\ResolvedAddressRepository;
use App\ValueObject\Address;
use App\ValueObject\Coordinates;

class GoogleStrategy implements GeocoderStrategyInterface
{
    private ResolvedAddressRepository $repository;

    private GeocoderInterface $googleApisGeocoder;

    public function __construct(
        ResolvedAddressRepository $repository,
        GeocoderInterface $googleApisGeocoder
    ) {
        $this->repository = $repository;
        $this->googleApisGeocoder = $googleApisGeocoder;
    }

    public function getCoordinates(Address $address): ?Coordinates
    {
        $coordinates = $this->googleApisGeocoder->geocode($address);

        if ($coordinates) {
            $this->repository->saveResolvedAddress($address, $coordinates);
        }

        return $coordinates;
    }
}
