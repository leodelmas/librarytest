<?php

namespace App\Domain\Book\Port;

interface CoverStorageInterface
{
    /**
     * Downloads the cover from the given URL and stores it locally.
     * Returns the public path to the stored file, or null on failure.
     */
    public function store(string $bookKey, string $sourceUrl): ?string;
}
