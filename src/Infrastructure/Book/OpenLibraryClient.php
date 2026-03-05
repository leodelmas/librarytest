<?php

namespace App\Infrastructure\Book;

use App\Domain\Book\Port\BookCatalogInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class OpenLibraryClient implements BookCatalogInterface
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

    public function getWorkDescription(string $workKey): ?string
    {
        // workKey is like "/works/OL42900939W"
        try {
            $response = $this->httpClient->request('GET', "https://openlibrary.org{$workKey}.json");
            $data = $response->toArray();
        } catch (\Throwable) {
            return null;
        }

        $description = $data['description'] ?? null;

        if (\is_string($description)) {
            return $description;
        }

        if (\is_array($description) && isset($description['value'])) {
            return $description['value'];
        }

        return null;
    }
}
