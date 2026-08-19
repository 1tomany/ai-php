<?php

namespace OneToMany\AI\Bridge\Common\Response\Error;

use function is_null;
use function sprintf;
use function str_ends_with;
use function trim;

final readonly class GenericError
{
    /**
     * @var ?non-empty-string
     */
    public ?string $code;

    /**
     * @var ?non-empty-string
     */
    public ?string $message;

    public function __construct(
        int|string|null $code = null,
        ?string $message = null,
    ) {
        if (false === is_null($code)) {
            $code = trim((string) $code);
        }

        $this->code = '' !== $code ? $code : null;

        if (null !== $message) {
            if ('' !== $message = trim($message)) {
                if (!str_ends_with($message, '.')) {
                    $message = sprintf('%s.', $message);
                }
            }
        }

        $this->message = '' !== $message ? $message : null;
    }
}
