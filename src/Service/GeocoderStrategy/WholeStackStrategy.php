<?php

declare(strict_types=1);

namespace App\Service\GeocoderStrategy;

use App\Repository\ResolvedAddressRepository;
use App\ValueObject\Address;
use App\ValueObject\Coordinates;
use Exception;

/**
 * Wholes-tack strategy checks database first. Then loops through all geocoders and tries to get coordinates from them
 * one by one until it finds. Also saves coordinates to database, so the next time it will take those coordinates from
 * database directly.
 */
class WholeStackStrategy implements GeocoderStrategyInterface
{
    private ResolvedAddressRepository $repository;

    private iterable $geocoders;

    public function __construct(
        ResolvedAddressRepository $repository,
        iterable $geocoders
    ) {
        $this->repository = $repository;
        $this->geocoders = $geocoders;
    }

    /**
     * @throws Exception
     */
    public function getCoordinates(Address $address): ?Coordinates
    {
        $resolvedAddress = $this->repository->getByAddress($address);

        if ($resolvedAddress) {
            return new Coordinates($resolvedAddress->getLat(), $resolvedAddress->getLng());
        }

        foreach ($this->geocoders as $geocoder) {
            $coordinates = $geocoder->geocode($address);

            if ($coordinates) {
                $this->repository->saveResolvedAddress($address, $coordinates);
                return $coordinates;
            }
        }

        return null;
    }
}
