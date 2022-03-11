<?php

declare(strict_types=1);

final class AccessAPI extends HTTPClient
{
    private $host;

    function __construct(string $host)
    {
        $this->host = $host;
    }

    public function get_account(string $address): Account
    {
        $url = $this->host . "/v1/accounts/" . $address;

        $data = array("expand" => "keys");

        $response = $this->call("GET", $url, $data);

        $address = $response["address"];

        $json_to_account_key = function($data): AccountKey {
            return new AccountKey(
                intval($data["index"]),
                $data["public_key"],
                $data["signing_algorithm"],
                $data["hashing_algorithm"],
                intval($data["weight"]),
                $data["revoked"]
            );
        };

        $keys = array_map($json_to_account_key, $response["keys"]);

        return new Account($address, $keys);
    }

    private function json_to_account_key($data): AccountKey {
        return new AccountKey(
            $data["key_index"],
            $data["public_key"],
            $data["signing_algorithm"],
            $data["hashing_algorithm"],
            intval($data["weight"]),
            $data["revoked"]
        );
    }
}
