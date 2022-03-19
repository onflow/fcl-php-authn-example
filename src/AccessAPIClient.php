<?php

declare(strict_types=1);

final class AccessAPIClient extends HTTPClient
{
    private $host;

    public function __construct(string $host)
    {
        $this->host = $host;
    }

    public function get_account(string $address): Account
    {
        $url = $this->host . "/v1/accounts/" . $address;

        $data = array("expand" => "keys");

        $response = $this->call("GET", $url, $data);

        $address = $response["address"];


        $json_to_account_key = function ($data): AccountKey {

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

    public function execute_script(string $script, array $args)
    {
        $url = $this->host . "/v1/scripts";

        // Script and arguments are base64 encoded
        $script_base64 = base64_encode($script);
        $args_base64 = array_map(
            function (CadenceValue $arg) {
                return $arg->base64();
            },
            $args
        );

        $data = array(
            "script" => $script_base64,
            "arguments" => $args_base64,
        );

        $response = $this->call("POST", $url, $data);

        $value = base64_decode($response);

        return json_decode($value, true);
    }
}
