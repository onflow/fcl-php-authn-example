<?php

declare(strict_types=1);

use Elliptic\EC;

interface SignatureVerifier
{
    public function verify(
        string $public_key, // hexadecimal string encoding of a 64-byte array representing the public key [x||y] where x and y are the key coordinates, each left-padded to 32 bytes
        string $sig_algo,   // one of "ECDSA_P256" or "ECDSA_secp256k1"
        string $hash_algo,  // one of "SHA2_256" or "SHA3_256"
        string $message,    // hexadeciaml string encoding of the encoded account proof message
        string $signature   // hexadecimal string encoding of a 64-byte array representing the signature [r||s] where r and s are the signature components, each left-padded to 32 bytes
    ): bool;
}

final class MockSignatureVerifier implements SignatureVerifier {
    public function verify(
        string $public_key,
        string $sig_algo,
        string $hash_algo,
        string $message,
        string $signature
    ): bool {
        return true;
    }
}

final class SampleSignatureVerifier implements SignatureVerifier {
    public function verify(
        string $public_key,
        string $sig_algo,
        string $hash_algo,
        string $message,
        string $signature
    ): bool {
        $key = $this->parse_public_key($sig_algo, $public_key);
        $digest = $this->hash($hash_algo, $message);

        $sig = array(
            "r" => substr($signature, 0, strlen($signature) / 2),
            "s" => substr($signature, strlen($signature) / 2)
        );

        return $key->verify($digest, $sig);
    }

    private function hash(string $hash_algo, string $message): string {
        switch($hash_algo) {
            case "SHA2_256":
                return hash("sha256", hex2bin($message));
            case "SHA3_256":
                return hash("sha3-256", hex2bin($message));
        }
    }

    private function parse_public_key(string $sig_algo, string $public_key) {
        $ec = $this->new_ec($sig_algo);

        $raw_public_key = "04" . $public_key;
        return $ec->keyFromPublic($raw_public_key, "hex");
    }

    private function new_ec(string $sig_algo) {
        switch($sig_algo) {
            case "ECDSA_P256":
                return new EC("p256");
            case "ECDSA_secp256k1":
                return new EC("secp256k1");
        } 
    }
}
