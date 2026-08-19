<?php

namespace OneToMany\AI\Bridge\Common\Response;

use function is_string;
use function trim;

final readonly class Error
{
    public int|string|null $code;

    /**
     * @var ?non-empty-string
     */
    public ?string $message;

    public function __construct(
        int|string|null $code = null,
        ?string $message = null,
    ) {
        if (is_string($code)) {
            $code = trim($code);
        }

        $this->code = '' !== $code ? $code : null;

        if (null !== $message) {
            $message = trim($message);
        }

        $this->message = '' !== $message ? $message : null;
    }
}
