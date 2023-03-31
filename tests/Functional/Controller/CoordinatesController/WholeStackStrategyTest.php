<?php

declare(strict_types=1);

namespace App\Tests\Functional\Controller\CoordinatesController;

use App\Entity\ResolvedAddress;
use App\Tests\Functional\Controller\CoordinatesControllerTestCase;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Response;
use PHPUnit\Framework\MockObject\MockObject;

class WholeStackStrategyTest extends CoordinatesControllerTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        $this->coordinatesController =
            self::$container->get('App\Controller\CoordinatesController.test.WholeStackStrategy');
    }

    /**
     * @dataProvider requestDataProvider
     */
    public function testCoordinatesWillReturnSuccess(array $requestData, int $expectedResponse): void
    {
        /** @var MockObject $mockedGuzzleClient */
        $mockedGuzzleClient = self::$container->get(Client::class);

        $mockedGuzzleClient
            ->expects(self::once())
            ->method('get')
            ->willReturn(new Response(200, [], $this->googleApiExpectedResponse()));

        $client = self::$container->get('test.client');
        $client->request('POST', $this->url, [], [], $this->headers, json_encode($requestData));

        $this->assertEquals($expectedResponse, $client->getResponse()->getStatusCode());

        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');
        $resolvedAddressRepository = $entityManager->getRepository(ResolvedAddress::class);
        $resolvedAddress = $resolvedAddressRepository->findOneBy($requestData);
        $this->assertNotNull($resolvedAddress);
    }

    /**
     * @dataProvider requestDataProvider
     */
    public function testCoordinatesWillReturnSuccessOnSecondAttempt(array $requestData, int $expectedResponse): void
    {
        /** @var MockObject $mockedGuzzleClient */
        $mockedGuzzleClient = self::$container->get(Client::class);

        $mockedGuzzleClient
            ->expects(self::exactly(2))
            ->method('get')
            ->willReturn(
                new Response(200, [], '{"status" : "ZERO_RESULTS"}'),
                new Response(200, [], $this->hereApiExpectedResponse())
            );

        $client = self::$container->get('test.client');
        $client->request('POST', $this->url, [], [], $this->headers, json_encode($requestData));

        $this->assertEquals($expectedResponse, $client->getResponse()->getStatusCode());

        $entityManager = self::$container->get('doctrine.orm.default_entity_manager');
        $resolvedAddressRepository = $entityManager->getRepository(ResolvedAddress::class);
        $resolvedAddress = $resolvedAddressRepository->findOneBy($requestData);
        $this->assertNotNull($resolvedAddress);
    }

    /**
     * @dataProvider requestDataProvider
     */
    public function testCoordinatesWillReturnSuccessFromDB(array $requestData, int $expectedResponse): void
    {
        $client = self::$container->get('test.client');
        $entityManager = $client->getContainer()->get('doctrine.orm.default_entity_manager');

        $resolvedAddress = (new ResolvedAddress())
            ->setCountryCode($requestData['countryCode'])
            ->setCity($requestData['city'])
            ->setStreet($requestData['street'])
            ->setPostcode($requestData['postcode'])
            ->setLat('159')
            ->setLng('357');

        $entityManager->persist($resolvedAddress);
        $entityManager->flush();

        $client = self::$container->get('test.client');
        $client->request('POST', $this->url, [], [], $this->headers, json_encode($requestData));
        $response = $client->getResponse();
        $content = $response->getContent();

        $this->assertEquals('{"lat":"159","lng":"357"}', $response->getContent());
        $this->assertEquals($expectedResponse, $response->getStatusCode());
    }
}
