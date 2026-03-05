<?php

namespace App\Domain\Book\Port;

interface BookCatalogInterface
{
    public function search(string $query, int $limit = 20): array;

    public function getWorkDescription(string $workKey): ?string;
}
