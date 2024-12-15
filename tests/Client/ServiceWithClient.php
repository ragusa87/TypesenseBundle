<?php

namespace Biblioteca\TypesenseBundle\Tests\Client;

use Biblioteca\TypesenseBundle\Client\ClientInterface;
use Psr\Log\LoggerInterface;

readonly class ServiceWithClient
{
    public function __construct(
        private ClientInterface $client,
        public LoggerInterface $logger,
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
