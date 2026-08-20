<?php

namespace OneToMany\AI\Resource\File;

final readonly class RemoteFile
{
    /**
     * @param non-empty-string $id
     * @param ?non-empty-string $uri
     * @param ?non-empty-string $purpose
     */
    public function __construct(
        public string $id,
        public ?\DateTimeImmutable $expiresAt = null,
        public ?string $uri = null,
        public ?string $purpose = null,
    ) {
    }

    /**
     * @return non-empty-string
     */
    public function getId(): string
    {
        return $this->id;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    /**
     * @return ?non-empty-string
     */
    public function getUri(): ?string
    {
        return $this->uri;
    }

    /**
     * @return ?non-empty-string
     */
    public function getPurpose(): ?string
    {
        return $this->purpose;
    }
}
