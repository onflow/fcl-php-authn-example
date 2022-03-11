<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class AccessAPITest extends TestCase
{
    public function testCanGetAccount(): void
    {
        $client = new AccessAPI("https://rest-mainnet.onflow.org");

        $account = $client->get_account("07c48471ca70a25c");

        $this->assertEquals(
            "07c48471ca70a25c",
            $account->address
        );
    }
}
