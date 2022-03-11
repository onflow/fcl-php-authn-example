<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class AccountProofVerifierTest extends TestCase
{
    public function testCanVerify(): void
    {
        $address = "07c48471ca70a25c";
        $app_id = "My Sample App";
        $nonce = "75f8587e5bd5f9dcc9909d0dae1f0ac5814458b2ae129620502cb936fde7120a";

        $account = new Account(
            $address,
            array(
                new AccountKey(
                    0,
                    "4519e9fbf966c6589fafe60903c0da5f55c5cb50aee5d870f097b35dfb6de13c170718cd92f50811cdd9290e51c2766440b696e0423a5031ae482cca79e3c479",
                    "ECDSA_P256",
                    "SHA3_256",
                    500,
                    false
                ),
                new AccountKey(
                    1,
                    "4519e9fbf966c6589fafe60903c0da5f55c5cb50aee5d870f097b35dfb6de13c170718cd92f50811cdd9290e51c2766440b696e0423a5031ae482cca79e3c479",
                    "ECDSA_P256",
                    "SHA3_256",
                    500,
                    false
                )
            )
        );

        $verifier = new AccountProofVerifier(
            $account,
            $app_id,
            $nonce,
            // MockSignatureVerifier always returns true for signature verification
            new MockSignatureVerifier()
        );

        $signatures = array(
            new AccountSignature(
                $address,
                0,
                "9f6e2264844069f20a2a594ed25f67550e4b27c8f51b11c24685d58162a2ccc72c7e0eb559ec4755a0f5ff940e8cd484d7c352b56f53e709ab5088f9a2724c84"
            ),
            new AccountSignature(
                $address,
                1,
                "9f6e2264844069f20a2a594ed25f67550e4b27c8f51b11c24685d58162a2ccc72c7e0eb559ec4755a0f5ff940e8cd484d7c352b56f53e709ab5088f9a2724c84"
            )
        );

        $this->assertTrue($verifier->verify($signatures));
    }
}
