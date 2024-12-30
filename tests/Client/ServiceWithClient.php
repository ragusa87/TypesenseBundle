<?php

namespace Biblioteca\TypesenseBundle\Tests\Client;

use Biblioteca\TypesenseBundle\Client\ClientInterface;
use Psr\Log\LoggerInterface;

class ServiceWithClient
{
    public function __construct(
        private readonly ClientInterface $client,
        public readonly LoggerInterface $logger,
    ) {
        $logger->debug(
            'ServiceWithClient::__construct',
            ['client' => $client]
        );
    }

    public function getClient(): ClientInterface
    {
        return $this->client;
    }
}
