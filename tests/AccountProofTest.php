<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class AccountProofTest extends TestCase
{
    public function testCanEncode(): void
    {
        $accountProof = new AccountProof(
            "My Sample App",
            "07c48471ca70a25c",
            "75f8587e5bd5f9dcc9909d0dae1f0ac5814458b2ae129620502cb936fde7120a"
        );

        $this->assertEquals(
            "f8388d4d792053616d706c65204170708807c48471ca70a25ca075f8587e5bd5f9dcc9909d0dae1f0ac5814458b2ae129620502cb936fde7120a",
            $accountProof->encode()
        );
    }
}
