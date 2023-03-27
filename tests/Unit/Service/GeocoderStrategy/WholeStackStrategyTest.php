<?php

declare(strict_types=1);

namespace App\Tests\Unit\Service\GeocoderStrategy;

use App\Repository\ResolvedAddressRepository;
use App\Service\Geocoder\GoogleApiGeocoder;
use App\Service\Geocoder\HereApiGeocoder;
use App\Service\GeocoderStrategy\WholeStackStrategy;
use App\ValueObject\Address;
use App\ValueObject\Coordinates;
use Exception;
use PHPUnit\Framework\TestCase;

class WholeStackStrategyTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testGetCoordinatesOnfirstProvider(): void
    {
        $repositoryMock = $this->createMock(ResolvedAddressRepository::class);

        $geocoderMock1 = $this->createMock(GoogleApiGeocoder::class);
        $geocoderMock2 = $this->createMock(HereApiGeocoder::class);

        $geocoderMock1->expects($this->once())
            ->method('geocode')
            ->willReturn(new Coordinates('12.34', '56.78'));

        $geocoders = [$geocoderMock1, $geocoderMock2];

        $wholeStackStrategy = new WholeStackStrategy($repositoryMock, $geocoders);

        $address = new Address('LT', 'Kaugonys', 'Gatviu 2', '10001');

        $coordinates = $wholeStackStrategy->getCoordinates($address);
        $this->assertInstanceOf(Coordinates::class, $coordinates);
        $this->assertEquals('12.34', $coordinates->getLat());
        $this->assertEquals('56.78', $coordinates->getLng());
    }

    /**
     * @throws Exception
     */
    public function testGetCoordinatesOnSecondProvider(): void
    {
        $repositoryMock = $this->createMock(ResolvedAddressRepository::class);

        $geocoderMock1 = $this->createMock(GoogleApiGeocoder::class);
        $geocoderMock2 = $this->createMock(HereApiGeocoder::class);

        $geocoderMock1->expects($this->once())
            ->method('geocode')
            ->willReturn(null);

        $geocoderMock2->expects($this->once())
            ->method('geocode')
            ->willReturn(new Coordinates('12.34', '56.78'));

        $geocoders = [$geocoderMock1, $geocoderMock2];

        $wholeStackStrategy = new WholeStackStrategy($repositoryMock, $geocoders);

        $address = new Address('LT', 'Kaugonys', 'Gatviu 2', '10001');

        $coordinates = $wholeStackStrategy->getCoordinates($address);
        $this->assertInstanceOf(Coordinates::class, $coordinates);
        $this->assertEquals('12.34', $coordinates->getLat());
        $this->assertEquals('56.78', $coordinates->getLng());
    }
}
