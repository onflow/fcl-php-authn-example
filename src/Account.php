<?php

declare(strict_types=1);

final class AccountKey
{
    public $index;
    public $public_key;
    public $sig_algo;
    public $hash_algo;
    public $weight;
    public $revoked;

    public function __construct(
        int $index,
        string $public_key,
        string $sig_algo,
        string $hash_algo,
        int $weight,
        bool $revoked
    ) {
        $this->index = $index;
        $this->public_key = $public_key;
        $this->sig_algo = $sig_algo;
        $this->hash_algo = $hash_algo;
        $this->weight = $weight;
        $this->revoked = $revoked;
    }
}

final class Account
{
    public $address;
    public $keys;

    public function __construct(string $address, array $keys)
    {
        $this->address = $address;
        $this->keys = $keys;
    }

    public function get_key(int $key_index)
    {
        foreach ($this->keys as $key) {
            if ($key->index == $key_index) {
                return $key;
            }
        }

        return null;
    }
}
