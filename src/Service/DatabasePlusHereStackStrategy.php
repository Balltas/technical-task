<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\ResolvedAddressRepository;
use App\ValueObject\Address;
use App\ValueObject\Coordinates;

class DatabasePlusHereStackStrategy implements GeocoderStrategyInterface
{
    private ResolvedAddressRepository $repository;

    private GeocoderInterface $hereApiGeocoder;

    public function __construct(
        ResolvedAddressRepository $repository,
        GeocoderInterface $hereApiGeocoder
    ) {
        $this->repository = $repository;
        $this->hereApiGeocoder = $hereApiGeocoder;
    }

    public function getCoordinates(Address $address): ?Coordinates
    {
        $resolvedAddress = $this->repository->getByAddress($address);

        if ($resolvedAddress) {
            return new Coordinates($resolvedAddress->getLat(), $resolvedAddress->getLng());
        }

        $coordinates = $this->hereApiGeocoder->geocode($address);

        if ($coordinates) {
            $this->repository->saveResolvedAddress($address, $coordinates);
        }

        return $coordinates;
    }
}
