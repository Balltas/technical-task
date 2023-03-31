<?php

declare(strict_types=1);

namespace App\Service\Geocoder;

use App\ValueObject\Address;
use App\ValueObject\Coordinates;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class GoogleApiGeocoder implements GeocoderInterface
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * @throws Exception
     */
    public function geocode(Address $address): ?Coordinates
    {
        $apiKey = $_ENV['GOOGLE_GEOCODING_API_KEY'];

        $params = [
            'query' => [
                'address' => $address->getStreet(),
                'components' => implode('|', [
                    "country:{$address->getCountry()}",
                    "locality:{$address->getCity()}",
                    "postal_code:{$address->getPostcode()}"
                ]),
                'key' => $apiKey
            ]
        ];

        try {
            $response = $this->client->get('https://maps.googleapis.com/maps/api/geocode/json', $params);
        } catch (GuzzleException $e) {
            return null;
        }

        $decodedResponse = json_decode($response->getBody()->getContents());

        if (
            $decodedResponse->status === 'ZERO_RESULTS' ||
            $decodedResponse->results[0]->geometry->location_type !== 'ROOFTOP'
        ) {
            return null;
        }

        if ($decodedResponse->status === 'OK') {
            $location = $decodedResponse->results[0]->geometry->location;
            return new Coordinates((string)$location->lat, (string)$location->lng);
        }

        throw new Exception('request denied. Unexpected exception.');
    }
}
