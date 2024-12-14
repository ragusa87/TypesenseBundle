<?php

namespace Biblioteca\TypesenseBundle\Tests;

use Biblioteca\TypesenseBundle\Client\ClientInterface;
use Psr\Log\LoggerInterface;

class ServiceWithClient
{
    public function __construct(
        ClientInterface $client,
        LoggerInterface $logger,
    ) {
        $logger->debug(
            'ServiceWithClient::__construct',
            ['client' => $client]
        );
    }
}
