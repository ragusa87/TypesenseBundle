<?php

namespace Biblioteca\TypesenseBundle\Client;

use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface as HttpClient;
use Typesense\Client;
use Typesense\Exceptions\ConfigError;

class ClientFactory
{
    /**
     * @param array<string, mixed> $defaultConfig
     */
    public function __construct(
        private readonly string $uri,
        #[\SensitiveParameter] private readonly string $apiKey,
        private readonly ?HttpClient $httpClient,
        private readonly int $connectionTimeoutSeconds = 5,
        private readonly array $defaultConfig = [],
    ) {
    }

    /**
     * @throws ConfigError
     */
    public function __invoke(): ClientInterface
    {
        return new ClientAdapter(new Client($this->getConfiguration()));
    }

    /**
     * @return array<string, mixed>
     */
    private function getConfiguration(): array
    {
        $urlParsed = parse_url($this->uri);
        if ($urlParsed === false || empty($urlParsed['host']) || empty($urlParsed['port']) || empty($urlParsed['scheme'])) {
            throw new \InvalidArgumentException('Invalid URI .'.$this->uri);
        }

        $config = [
            'nodes' => [
                [
                    'host' => $urlParsed['host'],
                    'port' => $urlParsed['port'],
                    'protocol' => $urlParsed['scheme'],
                ],
            ],
            'client' => $this->getClient(),
            'api_key' => $this->apiKey,
            'connection_timeout_seconds' => $this->connectionTimeoutSeconds,
        ];

        return array_merge($this->defaultConfig, $config);
    }

    public function getClient(): HttpClient
    {
        return $this->httpClient ?? (new Psr18ClientDiscovery())->find();
    }
}
