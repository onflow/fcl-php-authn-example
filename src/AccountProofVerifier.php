<?php

declare(strict_types=1);

final class AccountProofVerifier {

    private $account;
    private $account_proof;
    private $verifier;

    function __construct(
        Account $account,
        string $app_id,
        string $nonce,
        SignatureVerifier $verifier
    ) {
        $this->account = $account;
        $this->account_proof = new AccountProof(
            $app_id, 
            $account->address,
            $nonce
        );

        $this->verifier = $verifier;
    }

    function verify(array $signatures): bool {
        $message = $this->account_proof->encode();

        $weight = 0;

        foreach ($signatures as $signature) {

            $key = $this->account->get_key($signature->key_index);

            if ($key == null) {
                return false;
            }

            if ($key->revoked) {
                return false;
            }

            $is_valid = $this->verifier->verify(
                $key->public_key,
                $key->sig_algo,
                $key->hash_algo,
                $message,
                $signature->signature
            );

            if (!$is_valid) {
                return false;
            }

            $weight += $key->weight;
        }

        return $weight >= 1000;
    }
}
