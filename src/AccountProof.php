<?php

declare(strict_types=1);

use Web3p\RLP\RLP;

final class AccountProof
{
    public $app_id;
    public $address;
    public $nonce;

    // $app_id - an arbitrary length string
    // $address - 8 bytes, encoded as a hexadecimal string (without a leading 0x prefix)
    // $nonce - 32 bytes or more, encoded as a hexadecimal string (without a leading 0x prefix)
    public function __construct(string $app_id, string $address, string $nonce)
    {
        $this->app_id = $app_id;
        $this->address = $address;
        $this->nonce = $nonce;
    }

    public function encode(): string
    {
        $rlp = new RLP();

        return $rlp->encode([
            $this->app_id,
            "0x" . $this->address,
            "0x" . $this->nonce,
        ]);
    }
}
