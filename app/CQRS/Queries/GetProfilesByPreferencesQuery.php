<?php

declare(strict_types=1);

namespace App\CQRS\Queries;

final readonly class GetProfilesByPreferencesQuery implements QueryInterface
{
    /**
     * GetProfilesByPreferencesQuery constructor.
     *
     * @param array $preferences
     * @param int|null $limit
     * @param int|null $offset
     */
    public function __construct(
        private array $preferences,
        private ?int $limit = 20,
        private ?int $offset = 0
    ) {
    }

    /**
     * Get the preferences.
     *
     * @return array
     */
    public function getPreferences(): array
    {
        return $this->preferences;
    }

    /**
     * Get the limit.
     *
     * @return int|null
     */
    public function getLimit(): ?int
    {
        return $this->limit;
    }

    /**
     * Get the offset.
     *
     * @return int|null
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }
}
