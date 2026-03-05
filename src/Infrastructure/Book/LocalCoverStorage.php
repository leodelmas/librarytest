<?php

namespace App\Infrastructure\Book;

use App\Domain\Book\Port\CoverStorageInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class LocalCoverStorage implements CoverStorageInterface
{
    public function __construct(
        private HttpClientInterface $httpClient,
        private string $projectDir,
    ) {
    }

    public function store(string $bookKey, string $sourceUrl): ?string
    {
        try {
            $response = $this->httpClient->request('GET', $sourceUrl);
            if ($response->getStatusCode() !== 200) {
                return null;
            }

            $filename = str_replace('/', '-', trim($bookKey, '/')) . '.jpg';
            $coversDir = "{$this->projectDir}/public/uploads/covers";
            file_put_contents("{$coversDir}/{$filename}", $response->getContent());

            return "/uploads/covers/{$filename}";
        } catch (\Throwable) {
            return null;
        }
    }
}
