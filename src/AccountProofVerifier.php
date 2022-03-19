<?php

declare(strict_types=1);

const TESTNET = "testnet";
const MAINNET = "mainnet";

final class AccountProofVerifier
{
    private $network;
    private $client;

    public function __construct(
        string $network,
        AccessAPIClient $client,
    ) {
        $this->network = $network;
        $this->client = $client;
    }

    public function verify(
        AccountProof $account_proof,
        array $signatures
    ): bool {
        $address = $account_proof->address;
        $message = $account_proof->encode();

        $verify_script = $this::get_verify_script($this->network);

        $key_indices = array_map(
            function (AccountSignature $sig) {
                return $sig->key_index;
            },
            $signatures
        );

        $raw_signatures = array_map(
            function (AccountSignature $sig) {
                return $sig->signature;
            },
            $signatures
        );

        $result = $this->client->execute_script(
            $verify_script,
            array(
                Cadence::address($address),
                Cadence::string($message),
                Cadence::array_int($key_indices),
                Cadence::array_string($raw_signatures),
            )
        );

        return $result["value"];
    }

    private static function get_verify_script(string $network): string
    {
        $contractAddress = AccountProofVerifier::get_contract_address($network);

        return <<<EOD
import FCLCrypto from ${contractAddress}

pub fun main(
    address: Address, 
    message: String, 
    keyIndices: [Int], 
    signatures: [String]
): Bool {
    return FCLCrypto.verifyAccountProofSignatures(
        address: address,
        message: message,
        keyIndices: keyIndices,
        signatures: signatures
    )
}
EOD;
    }

    private static function get_contract_address(string $network): string
    {
        switch ($network) {
        case TESTNET:
            return "0x74daa6f9c7ef24b1";
        case MAINNET:
            return "0xb4b82a1c9d21d284";
        }
    }
}
