<?php

declare(strict_types=1);

final class AuthnUpdates {
    public $endpoint;
    public $params;

    function __construct(
        string $endpoint,
        array $params
    ) {
        $this->endpoint = $endpoint;
        $this->params = $app_id;
    }
}

final class AuthnLocal {
    public $endpoint;
    public $params;

    function __construct(
        string $endpoint,
        array $params
    ) {
        $this->endpoint = $endpoint;
        $this->params = $app_id;
    }
}

final class AuthnResponse {
    public $updates;
    public $local;

    function __construct(
        AuthnUpdates $updates,
        AuthnLocal $local
    ) {
        $this->updates = $updates;
        $this->local = $local;
    }
}

final class AuthnPollResponse {
    public $status;
    public $address;
    public $nonce;
    public $signatures;

    function __construct(
        string $status,
        string $address,
        string $nonce,
        array $signatures
    ) {
        $this->status = $status;
        $this->address = $address;
        $this->nonce = $nonce;
        $this->signatures = $signatures;
    }
}

final class FCL extends HTTPClient 
{
    private $app_id;

    function __construct(
        string $app_id
    ) {
        $this->app_id = $app_id;
    }

    // Call this function to initiate an authentication session
    // with a unique nonce for the user.
    public function authn(string $endpoint, string $nonce)
    {
        $data = array(
            "appIdentifier" => $this->app_id,
            "nonce" => $nonce
        );

        $response = $this->call("POST", $endpoint, $data);

        return new AuthnResponse(
            new AuthnUpdates(
                $response["updates"]["endpoint"],
                $response["updates"]["params"]
            ),
            new AuthnLocal(
                $response["local"]["endpoint"],
                $response["local"]["params"]
            )
        );
    }

    // Call this function to poll for updates to an
    // authnetication session.
    public function authn_poll(string $endpoint, array $params)
    {
        $response = $this->call("GET", $endpoint, $params);

        $status = $response["status"];

        if ($status != "APPROVED") {
            return new AuthnPollResponse(
                $status,
                null, null, null
            );
        }

        $services = $response["data"]["services"];

        $json_to_account_sig = function($data): AccountSignature {
            return new AccountSignature(
                $data["addr"],
                intval($data["keyId"]),
                $data["signature"]
            );
        };

        foreach ($services as $service) {
            if ($service["type"] == "account-proof") {
                return new AuthnPollResponse(
                    $status,
                    $service["data"]["address"],
                    $service["data"]["nonce"],
                    array_map($json_to_account_sig, $service["data"]["signatures"])
                );
            }
        }

        return null;
    
    }
}
