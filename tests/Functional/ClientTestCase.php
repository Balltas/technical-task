<?php

declare(strict_types=1);

namespace App\Tests\Functional;

use Symfony\Bundle\FrameworkBundle\KernelBrowser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class ClientTestCase extends WebTestCase
{
    private const DEFAULT_HEADERS = [
        'CONTENT_TYPE' => 'application/json',
    ];

    private ?KernelBrowser $client = null;

    protected function getKernelBrowser(): KernelBrowser
    {
        if (!$this->client) {
            static::ensureKernelShutdown();
            $this->client = static::createClient([], self::DEFAULT_HEADERS);
        }

        return $this->client;
    }

    protected function generateRoute(string $name, array $params = []): string
    {
        return $this->getKernelBrowser()->getContainer()->get('router')->generate($name, $params);
    }
}
