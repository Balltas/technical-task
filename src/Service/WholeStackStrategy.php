<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\ResolvedAddressRepository;
use App\ValueObject\Address;
use App\ValueObject\Coordinates;

class WholeStackStrategy implements GeocoderStrategyInterface
{
    private ResolvedAddressRepository $repository;

    private GeocoderInterface $hereApiGeocoder;

    private GeocoderInterface $googleApisGeocoder;

    public function __construct(
        ResolvedAddressRepository $repository,
        GeocoderInterface $googleApisGeocoder,
        GeocoderInterface $hereApiGeocoder
    ) {
        $this->repository = $repository;
        $this->hereApiGeocoder = $hereApiGeocoder;
        $this->googleApisGeocoder = $googleApisGeocoder;
    }

    public function getCoordinates(Address $address): ?Coordinates
    {
        $resolvedAddress = $this->repository->getByAddress($address);

        if ($resolvedAddress) {
            return new Coordinates($resolvedAddress->getLat(), $resolvedAddress->getLng());
        }

        $coordinates = $this->googleApisGeocoder->geocode($address);

        if ($coordinates) {
            $this->repository->saveResolvedAddress($address, $coordinates);
            return $coordinates;
        }

        $coordinates = $this->hereApiGeocoder->geocode($address);

        if ($coordinates) {
            $this->repository->saveResolvedAddress($address, $coordinates);
        }

        return $coordinates;
    }
}
