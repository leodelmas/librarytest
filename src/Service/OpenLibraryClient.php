<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenLibraryClient
{
    public function __construct(
        private HttpClientInterface $httpClient,
    ) {
    }

    public function search(string $query, int $limit = 20): array
    {
        $response = $this->httpClient->request('GET', 'https://openlibrary.org/search.json', [
            'query' => [
                'q' => $query,
                'limit' => $limit,
            ],
        ]);

        return $response->toArray();
    }
}
