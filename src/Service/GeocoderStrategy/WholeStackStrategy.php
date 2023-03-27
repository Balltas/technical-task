<?php

declare(strict_types=1);

namespace App\Service\GeocoderStrategy;

use App\Repository\ResolvedAddressRepository;
use App\ValueObject\Address;
use App\ValueObject\Coordinates;
use Exception;

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
