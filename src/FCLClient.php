<?php

declare(strict_types=1);

final class FCLClient extends HTTPClient
{
    public const PENDING = "PENDING";
    public const APPROVED = "APPROVED";
    public const DENIED = "DENIED";

    // Call this function to initiate an authentication session
    // with a unique nonce for the user.
    public function authn(
        string $endpoint,
        string $app_id,
        string $nonce,
        array $headers = array()
    ) {
        $data = array(
            "appIdentifier" => $app_id,
            "nonce" => $nonce
        );

        $response = $this->call("POST", $endpoint, $data, $headers);

        return AuthnResponse::from_json($response);
    }

    // Call this function to poll for updates to an
    // authnetication session.
    public function authn_poll(string $endpoint, array $params): AuthnPollResponse
    {
        $response = $this->call("GET", $endpoint, $params);
        
        return AuthnPollResponse::from_json($response);
    }
}

/*
Sample JSON:

{
    "f_type": "PollingResponse",
    "f_vsn": "1.0.0",
    "status": "PENDING",
    "reason": "",
    "updates": {
        "f_type": "Service",
        "f_vsn": "1.0.0",
        "type": "back-channel-rpc",
        "endpoint": "https://localhost:8000/api/fcl/authn",
        "method": "HTTP/GET",
        "params": {
            "authnID": "961cfa4e-0105-4337-9ea0-cc4fc9a5f4b0"
        }
    },
    "local": {
        "f_type": "Service",
        "f_vsn": "1.0.0",
        "type": "local-view",
        "endpoint": "https://localhost:8000/fcl/authn",
        "method": "VIEW/POP",
        "params": {
            "authnID": "961cfa4e-0105-4337-9ea0-cc4fc9a5f4b0",
        }
    }
}

*/
final class AuthnResponse
{
    public $updates;
    public $local;

    public static function from_json(array $json): AuthnResponse
    {
        return new AuthnResponse(
            AuthnUpdates::from_json($json["updates"]),
            AuthnLocal::from_json($json["local"])
        );
    }

    private function __construct(
        AuthnUpdates $updates,
        AuthnLocal $local
    ) {
        $this->updates = $updates;
        $this->local = $local;
    }
}

/*
Sample JSON:

{
    "f_type": "Service",
    "f_vsn": "1.0.0",
    "type": "back-channel-rpc",
    "endpoint": "https://localhost:8000/api/fcl/authn",
    "method": "HTTP/GET",
    "params": {
        "authnID": "961cfa4e-0105-4337-9ea0-cc4fc9a5f4b0"
    }
}

*/
final class AuthnUpdates
{
    public $endpoint;
    public $params;

    public static function from_json(array $json): AuthnUpdates
    {
        return new AuthnUpdates(
            $json["endpoint"],
            $json["params"]
        );
    }

    private function __construct(
        string $endpoint,
        array $params
    ) {
        $this->endpoint = $endpoint;
        $this->params = $params;
    }
}

/*
Sample JSON:

{
    "f_type": "Service",
    "f_vsn": "1.0.0",
    "type": "local-view",
    "endpoint": "https://localhost:8000/fcl/authn",
    "method": "VIEW/POP",
    "params": {
        "authnID": "961cfa4e-0105-4337-9ea0-cc4fc9a5f4b0",
    }
}

*/
final class AuthnLocal
{
    public $endpoint;
    public $params;

    public static function from_json(array $json): AuthnLocal
    {
        return new AuthnLocal(
            $json["endpoint"],
            $json["params"]
        );
    }

    private function __construct(
        string $endpoint,
        array $params
    ) {
        $this->endpoint = $endpoint;
        $this->params = $params;
    }

    public function url(): string
    {
        return sprintf(
            "%s?%s",
            $this->endpoint,
            http_build_query($this->params)
        );
    }
}

/*
Sample JSON (pending):

{
    "f_type": "PollingResponse",
    "f_vsn": "1.0.0",
    "status": "PENDING",
    "reason": "",
    "updates": {
        "f_type": "Service",
        "f_vsn": "1.0.0",
        "type": "back-channel-rpc",
        "endpoint": "https://localhost:8000/api/fcl/authn",
        "method": "HTTP/GET",
        "params": {
            "authnID": "961cfa4e-0105-4337-9ea0-cc4fc9a5f4b"
        }
    }
}

Sample JSON (declined):

{
    "f_type": "PollingResponse",
    "f_vsn": "1.0.0",
    "status": "DECLINED",
    "reason": "",
    "updates": {
        "f_type": "Service",
        "f_vsn": "1.0.0",
        "type": "back-channel-rpc",
        "endpoint": "https://localhost:8000/api/fcl/authn",
        "method": "HTTP/GET",
        "params": {
            "authnID": "961cfa4e-0105-4337-9ea0-cc4fc9a5f4b"
        }
    }
}

Sample JSON (approved):

{
    "f_type": "PollingResponse",
    "f_vsn": "1.0.0",
    "status": "APPROVED",
    "reason": "",
    "data": {
        "f_type": "AuthnResponse",
        "f_vsn": "1.0.0",
        "addr": "0xe7b6c0eb222b9f6f",
        "services": [
            {
                "f_type": "Service",
                "f_vsn": "1.0.0",
                "type": "authn",
                "method": "DATA",
                "uid": "example-wallet#authn",
                "endpoint": "https://localhost:8000/fcl/authn",
                "id": "0xe7b6c0eb222b9f6f",
                "identity": {
                    "f_type": "Identity",
                    "f_vsn": "1.0.0",
                    "address": "0xe7b6c0eb222b9f6f"
                },
                "provider": {
                    "f_type": "ServiceProvider",
                    "f_vsn": "1.0.0",
                    "address": "0x82ec283f88a62e65"
                }
            },
            {
                "f_type": "Service",
                "f_vsn": "1.0.0",
                "type": "account-proof",
                "uid": "example-wallet#account-proof",
                "method": "DATA",
                "data": {
                    "f_type": "Service",
                    "f_vsn": "1.0.0",
                    "signatures": [
                        {
                            "f_type": "CompositeSignature",
                            "f_vsn": "1.0.0",
                            "addr": "0xe7b6c0eb222b9f6f",
                            "keyId": 0,
                            "signature": "64875393dea7247574d67008f93caf9b309dc270a48eb36c03ef5f5ef373bde4cc59b2b627764bdb22f3b30fee341061b3dcff3ccb69b26b7094874f52679ac5"
                        }
                    ],
                    "address": "0xe7b6c0eb222b9f6f",
                    "nonce": "75f8587e5bd5f9dcc9909d0dae1f0ac5814458b2ae129620502cb936fde7120a"
                }
            }
        ]
    }
}

*/
final class AuthnPollResponse
{
    public $status;
    public $address;
    public $nonce;
    public $signatures;

    public static function from_json(array $json): AuthnPollResponse
    {
        $status = $json["status"];

        if ($status != FCLClient::APPROVED) {
            return new AuthnPollResponse(
                $status,
                "",
                "",
                array()
            );
        }

        $services = $json["data"]["services"];

        foreach ($services as $service) {
            if ($service["type"] == "account-proof") {
                return new AuthnPollResponse(
                    $status,
                    $service["data"]["address"],
                    $service["data"]["nonce"],
                    array_map(
                        function (array $json) {
                            return AccountSignature::from_json($json);
                        },
                        $service["data"]["signatures"]
                    )
                );
            }
        }
    }

    public function __construct(
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
