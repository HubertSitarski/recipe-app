<?php

namespace App\Provider;

use App\Enum\MealProviderSource;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Log\LoggerInterface;

class TheMealDbProvider implements MealProviderInterface
{
    private const BASE_URL = 'https://www.themealdb.com/api/json/v1/1';

    public function __construct(
        private readonly HttpClientInterface $httpClient,
        private readonly LoggerInterface $logger
    ) {}

    public function fetchByLetter(string $letter): array
    {
        if (strlen($letter) !== 1) {
            throw new \InvalidArgumentException('Letter parameter must be a single character');
        }

        return $this->sendRequest('/search.php', ['f' => $letter]);
    }

    public function fetchById(string $id): array
    {
        return $this->sendRequest('/lookup.php', ['i' => $id]);
    }

    private function sendRequest(string $endpoint, array $parameters = []): array
    {
        try {
            $response = $this->httpClient->request('GET', self::BASE_URL . $endpoint, [
                'query' => $parameters,
            ]);

            $content = $response->toArray();

            return $content['meals'] ?? [];

        } catch (\Exception $e) {
            $this->logger->error('Error fetching from TheMealDB API: ' . $e->getMessage(), [
                'endpoint' => $endpoint,
                'parameters' => $parameters,
            ]);

            return [];
        }
    }

    public function getSource(): string
    {
        return MealProviderSource::THEMEALDB->value;
    }
}
