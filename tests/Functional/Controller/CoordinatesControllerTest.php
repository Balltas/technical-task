<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller;

use App\Tests\Functional\ClientTestCase;
use Symfony\Bundle\FrameworkBundle\KernelBrowser;

class CoordinatesControllerTest extends ClientTestCase
{
    private static KernelBrowser $client;

    public function setUp(): void
    {
        parent::setUp();
        self::$client = self::createClient();
    }

    protected function tearDown(): void
    {
        $this->ensureKernelShutdown();
        parent::tearDown();
    }

    /**
     * @dataProvider requestDataProvider
     */
    public function testCoordinatesWillReturnSuccess(array $requestData, int $expectedResponse): void
    {
        $url = $this->generateRoute('coordinates');

        $headers = ['CONTENT_TYPE' => 'application/json'];

        self::$client->request(
            'POST',
            $url,
            [],
            [],
            $headers,
            json_encode($requestData)
        );

        $response = self::$client->getResponse();
        $this->assertEquals($expectedResponse, $response->getStatusCode());
    }

    public function requestDataProvider(): array
    {
        return [
            [
                [],
                422
            ],
            [
                [
                    'countryCode' => 'LT',
                    'city' => 'Kaunas',
                    'street' => 'savanoriu 54',
                    'postcode' => '01112'
                ],
                200
            ],
            [
                [
                    'countryCode' => 'LT',
                    'city' => 'Vilnius',
                    'street' => 'jasinskio 16',
                    'postcode' => '01112'
                ],
                200
            ],
            [
                [
                    'countryCode' => 'LT',
                    'city' => 'Kaišiadorys',
                    'street' => 'Kęstučio g. 12',
                    'postcode' => '56121'
                ],
                200
            ],
        ];
    }
}
