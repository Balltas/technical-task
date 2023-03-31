<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use GuzzleHttp\Client;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CoordinatesControllerTestCase extends WebTestCase
{
    protected string $url;

    /**
     * @var array<string>
     */
    protected array $headers;

    protected function setUp(): void
    {
        parent::setUp();

        self::bootKernel();

        $this->url = self::$container
            ->get('router')
            ->generate('coordinates', [], UrlGeneratorInterface::ABSOLUTE_URL);

        $this->headers = [
            'CONTENT_TYPE' => 'application/json',
        ];

        self::$container->set(
            Client::class,
            $this->createMock(Client::class)
        );
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

    protected function googleApiExpectedResponse(): string
    {
        return '{
          "results": [
            {
              "geometry": {
                "location": {
                  "lat": 10000.7034459,
                  "lng": 20000.2998139
                },
                "location_type": "ROOFTOP"
              }
            }
          ],
          "status": "OK"
        }';
    }

    protected function hereApiExpectedResponse(): string
    {
        return '{"items": [{"resultType" : "houseNumber", "position" : {"lat" : 123, "lng": 456}}]}';
    }
}
