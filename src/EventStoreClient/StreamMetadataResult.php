<?php

declare(strict_types=1);

namespace Prooph\EventStoreClient;

class StreamMetadataResult
{
    /** @var string */
    private $stream;
    /** @var bool */
    private $isStreamDeleted;
    /** @var int */
    private $metastreamVersion;
    /** @var string */
    private $streamMetadata;

    /** @internal */
    public function __construct(string $stream, bool $isStreamDeleted, int $metastreamVersion, string $streamMetadata)
    {
        if (empty($stream)) {
            throw new \InvalidArgumentException('Stream cannot be empty');
        }

        $this->stream = $stream;
        $this->isStreamDeleted = $isStreamDeleted;
        $this->metastreamVersion = $metastreamVersion;
        $this->streamMetadata = $streamMetadata;
    }

    public function stream(): string
    {
        return $this->stream;
    }

    public function isStreamDeleted(): bool
    {
        return $this->isStreamDeleted;
    }

    public function metastreamVersion(): int
    {
        return $this->metastreamVersion;
    }

    public function streamMetadata(): string
    {
        return $this->streamMetadata;
    }
}
