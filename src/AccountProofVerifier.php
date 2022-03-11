<?php

declare(strict_types=1);

final class AccountProofVerifier {

    private $account;
    private $account_proof;
    private $verify_func;

    function __construct(
        Account $account,
        string $app_id,
        string $nonce,
        $verify_func
    ) {
        $this->account = $account;
        $this->account_proof = new AccountProof(
            $account->address, 
            $app_id, 
            $nonce
        );

        $this->verify_func = $verify_func;
    }

    function verify(array $signatures): bool {
        $message = $this->account_proof->encode();

        $weight = 0;

        $verify = $this->verify_func;

        foreach ($signatures as $signature) {

            $key = $this->account->get_key($signature->key_index);

            if ($key == null) {
                return false;
            }

            if ($key->revoked) {
                return false;
            }

            $is_valid = $verify(
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
