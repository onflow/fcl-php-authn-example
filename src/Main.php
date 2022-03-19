<?php

declare(strict_types=1);

class Main
{
    public static function run()
    {
        $fcl = new FCLClient();

        $fcl_authn_endpoint = getenv("FCL_AUTHN_URL");
        $http_referer = getenv("FCL_HTTP_REFERER");

        if (!$fcl_authn_endpoint) {
            printf("Must set FCL_AUTHN_URL environment variable.\n");
        }

        if (!$http_referer) {
            printf("Must set FCL_HTTP_REFERER environment variable.\n");
        }

        if (!$fcl_authn_endpoint || !$http_referer) {
            return;
        }

        // App ID uniquely identifies your application with the wallet
        $app_id = "My Sample App";

        // Generate a secure 32-byte nonce for this authentication session
        $nonce = "75f8587e5bd5f9dcc9909d0dae1f0ac5814458b2ae129620502cb936fde7120a";

        // Call this to initiate an authentication session
        $authn_init = $fcl->authn(
            $fcl_authn_endpoint,
            $app_id,
            $nonce,
            // Some wallets (e.g. Dapper Wallet) require the HTTP Referer header:
            // https://en.wikipedia.org/wiki/HTTP_referer
            array("Referer: " . $http_referer)
        );

        // The view URL is passed to the client and rendered in a web frame
        //
        $authn_view_url = $authn_init->local->url();

        echo "Open authentication URL:\n\n" . $authn_view_url . "\n\n";

        // Call this function in a loop until
        // $authn_update->status != "PENDING"
        //
        $authn_update = $fcl->authn_poll(
            $authn_init->updates->endpoint,
            $authn_init->updates->params
        );

        while ($authn_update->status == "PENDING") {
            printf("Polling authn endpoint...");

            sleep(1);

            $authn_update = $fcl->authn_poll(
                $authn_init->updates->endpoint,
                $authn_init->updates->params
            );

            printf($authn_update->status . "\n");
        }

        if ($authn_update->status == "DENIED") {
            printf("Authentication denied by user.");
        }

        printf("\n");

        if ($authn_update->status == "APPROVED") {
            $address = $authn_update->address;
            $nonce = $authn_update->nonce;
            $signatures = $authn_update->signatures;

            // Fetch the user account data from the Flow blockchain
            $client = new AccessAPIClient("https://rest-testnet.onflow.org");
            $account = $client->get_account($address);

            $account_proof = new AccountProof(
                $app_id,
                $address,
                $nonce
            );

            $verifier = new AccountProofVerifier(TESTNET, $client);

            $verified = $verifier->verify($account_proof, $signatures);

            if ($verified) {
                printf("Account " . $address . " is verified!\n");
            } else {
                printf("Account " . $address . " verification failed.\n");
            }
        }
    }
}
