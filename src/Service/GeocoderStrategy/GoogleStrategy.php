<?php

declare(strict_types=1);

namespace App\Service\GeocoderStrategy;

use App\Repository\ResolvedAddressRepository;
use App\Service\Geocoder\GeocoderInterface;
use App\Service\Geocoder\GoogleApiGeocoder;
use App\ValueObject\Address;
use App\ValueObject\Coordinates;
use Exception;

class GoogleStrategy implements GeocoderStrategyInterface
{
    private ResolvedAddressRepository $repository;

    private GeocoderInterface $googleApiGeocoder;

    public function __construct(
        ResolvedAddressRepository $repository,
        GoogleApiGeocoder $googleApiGeocoder
    ) {
        $this->repository = $repository;
        $this->googleApiGeocoder = $googleApiGeocoder;
    }

    /**
     * @throws Exception
     */
    public function getCoordinates(Address $address): ?Coordinates
    {
        $coordinates = $this->googleApiGeocoder->geocode($address);

        if ($coordinates) {
            $this->repository->saveResolvedAddress($address, $coordinates);
            return $coordinates;
        }

        return null;
    }
}
