<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Service\Geocoder\GoogleApiGeocoder;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Contracts\HttpClient\Exception\TransportExceptionInterface;

class CoordinatesControllerTest extends WebTestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        self::bootKernel();
    }

    /**
     * @dataProvider requestDataProvider
     * @throws TransportExceptionInterface
     */
    public function testCoordinatesWillReturnSuccess(array $requestData, int $expectedResponse): void
    {
        $client = static::$container->get('test.client');
        $router = static::$container->get('router');

        $url = $router->generate('coordinates', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $mockResponse = new Response(200, [], $this->response());
        $mockHandler = self::$container->get('test.mock_handler');
        $mockHandler->append($mockResponse);

        $headers = [
            'CONTENT_TYPE' => 'application/json',
        ];

        $client->request('POST', $url, [], [], $headers, json_encode($requestData));

        $this->assertEquals($expectedResponse, $client->getResponse()->getStatusCode());
    }

    public function requestDataProvider(): array
    {
        return [
            [
                [
                    'countryCode' => 'LT',
                    'city' => 'Vilnius',
                    'street' => 'Minties g. 12',
                    'postcode' => '09225'
                ],
                200
            ],
        ];
    }

    private function response(): string
    {
        return '{
          "results": [
            {
              "address_components": [
                {
                  "long_name": "12",
                  "short_name": "12",
                  "types": [
                    "street_number"
                  ]
                },
                {
                  "long_name": "Minties gatvė",
                  "short_name": "Minties g.",
                  "types": [
                    "route"
                  ]
                },
                {
                  "long_name": "Vilnius",
                  "short_name": "Vilnius",
                  "types": [
                    "locality",
                    "political"
                  ]
                },
                {
                  "long_name": "Vilniaus apskritis",
                  "short_name": "Vilniaus apskr.",
                  "types": [
                    "administrative_area_level_1",
                    "political"
                  ]
                },
                {
                  "long_name": "Lithuania",
                  "short_name": "LT",
                  "types": [
                    "country",
                    "political"
                  ]
                },
                {
                  "long_name": "09225",
                  "short_name": "09225",
                  "types": [
                    "postal_code"
                  ]
                }
              ],
              "formatted_address": "Minties g. 12, 09225 Vilnius, Lithuania",
              "geometry": {
                "location": {
                  "lat": 54.7034459,
                  "lng": 25.2998139
                },
                "location_type": "ROOFTOP",
                "viewport": {
                  "northeast": {
                    "lat": 54.704781880291,
                    "lng": 25.301273480292
                  },
                  "southwest": {
                    "lat": 54.702083919708,
                    "lng": 25.298575519708
                  }
                }
              },
              "place_id": "ChIJJZtYMaSW3UYRBPQ5KRcVDGQ",
              "plus_code": {
                "compound_code": "P73X+9W Vilnius, Vilnius City Municipality, Lithuania",
                "global_code": "9G67P73X+9W"
              },
              "types": [
                "street_address"
              ]
            }
          ],
          "status": "OK"
        }';
    }
}
