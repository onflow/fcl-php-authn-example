<?php

declare(strict_types=1);

final class AccountSignature {

    public $address;
    public $key_index;
    public $signature;

    function __construct(
        string $address,
        int $key_index,
        string $signature
    ) {
        $this->address = $address;
        $this->key_index = $key_index;
        $this->signature = $signature;
    }
}
