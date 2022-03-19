<?php

declare(strict_types=1);

/*
Sample JSON:

{
    "f_type": "CompositeSignature",
    "f_vsn": "1.0.0",
    "addr": "0xe7b6c0eb222b9f6f",
    "keyId": 0,
    "signature": "64875393dea7247574d67008f93caf9b309dc270a48eb36c03ef5f5ef373bde4cc59b2b627764bdb22f3b30fee341061b3dcff3ccb69b26b7094874f52679ac5"
}

*/
final class AccountSignature
{
    public $address;
    public $key_index;
    public $signature;

    public static function from_json(array $json): AccountSignature
    {
        return new AccountSignature(
            $json["addr"],
            intval($json["keyId"]),
            $json["signature"]
        );
    }

    private function __construct(
        string $address,
        int $key_index,
        string $signature
    ) {
        $this->address = $address;
        $this->key_index = $key_index;
        $this->signature = $signature;
    }
}
