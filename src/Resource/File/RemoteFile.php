<?php

namespace OneToMany\AI\Resource\File;

use OneToMany\AI\Exception\InvalidArgumentException;
use OneToMany\AI\Vendor;

use function sprintf;
use function strtolower;
use function trim;

final readonly class RemoteFile
{
    public Vendor $vendor;

    /**
     * @var non-empty-string
     */
    public string $id;

    /**
     * @var ?non-empty-string
     */
    public ?string $uri;

    /**
     * @var non-empty-lowercase-string
     */
    public string $mimeType;

    /**
     * @param ?non-empty-string $purpose
     *
     * @throws InvalidArgumentException when the file ID is empty
     * @throws InvalidArgumentException when the vendor is Gemini and the URI is empty
     * @throws InvalidArgumentException when the MIME type is empty
     */
    public function __construct(
        string|Vendor $vendor,
        ?string $id,
        ?string $uri,
        ?string $mimeType,
        public ?\DateTimeImmutable $expiresAt = null,
        public ?string $purpose = null,
    ) {
        $this->vendor = Vendor::create($vendor);

        if ('' === $id = trim((string) $id)) {
            throw new InvalidArgumentException('The file ID cannot be empty.');
        }

        $this->id = $id;

        if (null !== $uri) {
            $uri = trim($uri);
        }

        if ($this->vendor->isGemini()) {
            if (null === $uri || '' === $uri) {
                throw new InvalidArgumentException(sprintf('%s requires a non-empty URI.', $this->vendor->getName()));
            }
        }

        $this->uri = '' !== $uri ? $uri : null;

        if ('' === $mimeType = trim((string) $mimeType)) {
            throw new InvalidArgumentException('The MIME type cannot be empty.');
        }

        $this->mimeType = strtolower($mimeType);
    }

    /**
     * @return non-empty-string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return ?non-empty-string
     */
    public function getUri(): ?string
    {
        return $this->uri;
    }

    /**
     * @return non-empty-lowercase-string
     */
    public function getMimeType(): string
    {
        return $this->mimeType;
    }

    public function getExpiresAt(): ?\DateTimeImmutable
    {
        return $this->expiresAt;
    }

    /**
     * @return ?non-empty-string
     */
    public function getPurpose(): ?string
    {
        return $this->purpose;
    }
}
