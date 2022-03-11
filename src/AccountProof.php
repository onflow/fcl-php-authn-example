<?php

declare(strict_types=1);

use Web3p\RLP\RLP;

/*
 * This file is part of <package name>.
 *
 * This source file is subject to the license that is bundled
 * with this source code in the file LICENSE.
 */

final class AccountProof
{
    private $app_id;
    private $address;
    private $nonce;

    // Domain separation tag (DST) for account proof signatures.
    //
    // This is the UTF-8 encoding of "FCL-ACCOUNT-PROOF-V0.0", right-padded to 32 bytes.
    const FCL_ACCOUNT_PROOF_DOMAIN_TAG = "46434c2d4143434f554e542d50524f4f462d56302e3000000000000000000000";

    // $app_id - an arbitrary length string
    // $address - 8 bytes, encoded as a hexadecimal string (without a leading 0x prefix)
    // $nonce - 32 bytes or more, encoded as a hexadecimal string (without a leading 0x prefix)
    function __construct(string $app_id, string $address, string $nonce)
    {
        $this->app_id = $app_id;
        $this->address = $address;
        $this->nonce = $nonce;
    }

    public function encode(): string
    {
        $rlp = new RLP;

        $message = $rlp->encode([
            $this->app_id,
            "0x" . $this->address,
            "0x" . $this->nonce,
        ]);

        return self::FCL_ACCOUNT_PROOF_DOMAIN_TAG . $message;
    }
}
