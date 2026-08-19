<?php

namespace OneToMany\AI\Tests\Resource\Query;

use OneToMany\AI\Exception\RuntimeException;
use OneToMany\AI\Provider;
use OneToMany\AI\Resource\Query\Response;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

use function json_validate;
use function uniqid;

use const PHP_INT_MAX;

#[Group('UnitTests')]
#[Group('ResourceTests')]
#[Group('QueryTests')]
final class ResponseTest extends TestCase
{
    public function testDecodeRequiresJsonObjectOrArray(): void
    {
        $text = (string) PHP_INT_MAX;
        $this->assertTrue(json_validate($text));

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessageIs('The model output did not contain a JSON object or array.');

        new Response(Provider::OpenAI, uniqid(), true, $text)->decode();
    }
}
