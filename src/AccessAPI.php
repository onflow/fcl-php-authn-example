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

            // strip 0x prefix from public key
            $public_key = substr($data["public_key"], 2);

            return new AccountKey(
                intval($data["index"]),
                $public_key,
                $data["signing_algorithm"],
                $data["hashing_algorithm"],
                intval($data["weight"]),
                $data["revoked"]
            );
        };

        $keys = array_map($json_to_account_key, $response["keys"]);

        return new Account($address, $keys);
    }
}
