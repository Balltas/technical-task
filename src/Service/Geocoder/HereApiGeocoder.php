<?php

declare(strict_types=1);

namespace App\Service\Geocoder;

use App\ValueObject\Address;
use App\ValueObject\Coordinates;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class HereApiGeocoder implements GeocoderInterface
{
    private Client $client;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function geocode(Address $address): ?Coordinates
    {
        $apiKey = $_ENV['HEREMAPS_GEOCODING_API_KEY'];

        $params = [
            'query' => [
                'qq' => implode(';', [
                    "country={$address->getCountry()}",
                    "city={$address->getCity()}",
                    "street={$address->getStreet()}",
                    "postalCode={$address->getPostcode()}"
                ]),
                'apiKey' => $apiKey
            ]
        ];

        try {
            $response = $this->client->get('https://geocode.search.hereapi.com/v1/geocode', $params);
        } catch (GuzzleException $e) {
            return null;
        }

        $decodedResponse = json_decode($response->getBody()->getContents());

        if (count($decodedResponse->items) === 0) {
            return null;
        }

        $firstItem = $decodedResponse->items[0];

        if ($firstItem->resultType !== 'houseNumber') {
            return null;
        }

        return new Coordinates((string)$firstItem->position->lat, (string)$firstItem->position->lng);
    }
}
