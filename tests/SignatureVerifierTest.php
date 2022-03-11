<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class SignatureVerifierTest extends TestCase
{
    public function testCanVerifySignature(): void
    {
        
        // one of "ECDSA_P256" or "ECDSA_secp256k1"
        $sig_algo = "ECDSA_P256";

        // one of "SHA2_256" or "SHA3_256"
        $hash_algo = "SHA3_256";

        // hexadeciaml string encoding of the encoded account proof message
        $message = "464c4f572d56302e302d75736572000000000000000000000000000000000000464f4f";

        // hexadecimal string encoding of a 64-byte array representing 
        // the signature [r||s] where r and s are the signature components, 
        // each left-padded to 32 bytes
        $signature = "9f6e2264844069f20a2a594ed25f67550e4b27c8f51b11c24685d58162a2ccc72c7e0eb559ec4755a0f5ff940e8cd484d7c352b56f53e709ab5088f9a2724c84";

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
