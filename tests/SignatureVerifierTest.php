<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class SignatureVerifierTest extends TestCase
{
    public function testCanVerifySignature(): void
    {

        $public_key = "4519e9fbf966c6589fafe60903c0da5f55c5cb50aee5d870f097b35dfb6de13c170718cd92f50811cdd9290e51c2766440b696e0423a5031ae482cca79e3c479";
        $sig_algo = "ECDSA_P256";
        $hash_algo = "SHA3_256";

        $signature = "9f6e2264844069f20a2a594ed25f67550e4b27c8f51b11c24685d58162a2ccc72c7e0eb559ec4755a0f5ff940e8cd484d7c352b56f53e709ab5088f9a2724c84";

        $message = "464c4f572d56302e302d75736572000000000000000000000000000000000000464f4f";

        $verifier = new SampleSignatureVerifier();

        $this->assertTrue(
            $verifier->verify(
                $public_key,
                $sig_algo,
                $hash_algo,
                $message,
                $signature
            )
        );
    }
}
