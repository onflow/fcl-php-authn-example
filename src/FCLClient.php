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
