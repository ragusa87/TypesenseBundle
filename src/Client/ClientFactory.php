<?php

namespace Biblioteca\TypesenseBundle\Client;

use Http\Discovery\Psr18ClientDiscovery;
use Psr\Http\Client\ClientInterface as HttpClient;
use Typesense\Client;
use Typesense\Exceptions\ConfigError;

readonly class ClientFactory
{
    /**
     * @param array<string, mixed> $defaultConfig
     */
    public function __construct(
        private string $uri,
        #[\SensitiveParameter]
        private string $apiKey,
        private ?HttpClient $client,
        private int $connectionTimeoutSeconds = 5,
        private array $defaultConfig = [],
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
        return $this->client ?? (new Psr18ClientDiscovery())->find();
    }
}
