<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

final class FCLTest extends TestCase
{
    protected function setUp(): void
    {
        $this->markTestSkipped(
            "Disable this line to unskip the test."
        );
    }

    public function testCanPerformAuthn(): void
    {
        // App ID uniquely identifies your application with the wallet
        $app_id = "My Sample App";

        $fcl = new FCL($app_id);

        // Generate a secure 32-byte nonce for this authentication session
        $nonce = "75f8587e5bd5f9dcc9909d0dae1f0ac5814458b2ae129620502cb936fde7120a";

        // Call this to initiate an authentication session
        $authn_init = $fcl->authn("http://localhost:3000/api/authn", $nonce);

        // These values are passed to the client and rendered in a web frame
        //
        // $view_endpoint = $authn_init->local->endpoint;
        // $view_params = $authn_init->local->params;

        // Call this function in a loop until 
        // $authn_update->status == "APPROVED" or "DENIED"
        //
        $authn_update = $fcl->authn_poll(
            $authn_init->updates->endpoint,
            $authn_init->updates->params
        );

        if ($authn_update->status == "APPROVED") {
            
            $address = $authn_update->address;
            $nonce = $authn_update->nonce;
            $signatures = $authn_update->signatures;


            // Fetch the user account data from the Flow blockchain
            $client = new AccessAPI("https://rest-mainnet.onflow.org");
            $account = $client->get_account(address);

            $verify_func = function(
                string $public_key, // hexadecimal string encoding of a 64-byte array representing the public key [x||y] where x and y are the key coordinates, each left-padded to 32 bytes
                string $sig_algo,   // one of "ECDSA_P256" or "ECDSA_secp256k1"
                string $hash_algo,  // one of "SHA2_256" or "SHA3_256"
                string $message,    // hexadeciaml string encoding of the encoded account proof message
                string $signature   // hexadecimal string encoding of a 64-byte array representing the signature [r||s] where r and s are the signature components, each left-padded to 32 bytes
            ): bool {
                return true;
            };
    
            $account_proof_verifier = new AccountProofVerifier(
                $account,
                $app_id,
                $nonce,
                $verify_func
            );

            $verified = $account_proof_verifier->verify($signatures);
        }
    }
}
