<?php

declare(strict_types=1);

namespace App\Service\GeocoderStrategy;

use App\Service\Geocoder\GeocoderInterface;
use App\Service\Geocoder\GoogleApiGeocoder;
use App\ValueObject\Address;
use App\ValueObject\Coordinates;
use Exception;

/**
 * GoogleStrategy is very simple one. It just tries to use googleApiGeocoder component.
 */
class GoogleStrategy implements GeocoderStrategyInterface
{
    private GeocoderInterface $googleApiGeocoder;

    public function __construct(
        GoogleApiGeocoder $googleApiGeocoder
    ) {
        $this->googleApiGeocoder = $googleApiGeocoder;
    }

    /**
     * @throws Exception
     */
    public function getCoordinates(Address $address): ?Coordinates
    {
        $coordinates = $this->googleApiGeocoder->geocode($address);

        if ($coordinates) {
            return $coordinates;
        }

        return null;
    }
}
