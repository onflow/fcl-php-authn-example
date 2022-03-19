<?php

declare(strict_types=1);

final class CadenceValue
{
    public $value;

    public function __construct($value)
    {
        $this->value = $value;
    }

    public function base64(): string
    {
        return base64_encode(json_encode($this->value));
    }
}

// Cadence is a decoder for the JSON-Cadence data interchange format.
//
// Reference: https://docs.onflow.org/cadence/json-cadence-spec/
final class Cadence
{
    public static function address(string $value): CadenceValue
    {
        return new CadenceValue(array(
            "type" => "Address",
            "value" => $value,
        ));
    }

    public static function string(string $value): CadenceValue
    {
        return new CadenceValue(array(
            "type" => "String",
            "value" => $value,
        ));
    }

    public static function int(int $value): CadenceValue
    {
        return new CadenceValue(array(
            "type" => "Int",
            "value" => strval($value),
        ));
    }

    public static function array_string(array $value): CadenceValue
    {
        $to_string = function (string $value) {
            return array(
                "type" => "String",
                "value" => $value,
            );
        };

        return new CadenceValue(array(
            "type" => "Array",
            "value" => array_map($to_string, $value),
        ));
    }

    public static function array_int(array $value): CadenceValue
    {
        $to_int = function (int $value) {
            return array(
                "type" => "Int",
                "value" => strval($value),
            );
        };

        return new CadenceValue(array(
            "type" => "Array",
            "value" => array_map($to_int, $value),
        ));
    }
}
