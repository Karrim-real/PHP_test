<?php

namespace App\Core;

class JWT
{
    // Secret key used for signing the token
    private static $secretKey;



    // Encode a payload into a JWT token with expiration
    public static function encode($payload, $exp = 3600) // Default expiry: 1 hour
    {
        $header = json_encode(['alg' => 'HS256', 'typ' => 'JWT']);

        // Add expiration timestamp to payload
        $payload['exp'] = time() + $exp;

        // Base64Url encode the header and payload
        $base64Header = self::base64UrlEncode($header);
        $base64Payload = self::base64UrlEncode(json_encode($payload));

        // Create the signature
        $signature = self::createSignature($base64Header, $base64Payload);

        // Return the final JWT token
        return $base64Header . '.' . $base64Payload . '.' . $signature;
    }

    // Decode a JWT token and verify it
    public static function decode($jwt)
    {
        $parts = explode('.', $jwt);
        if (count($parts) !== 3) {
            throw new \Exception('Invalid token format');
        }

        list($base64Header, $base64Payload, $signature) = $parts;

        // Decode the header and payload
        $header = json_decode(self::base64UrlDecode($base64Header), true);
        $payload = json_decode(self::base64UrlDecode($base64Payload), true);

        // Verify the token signature
        $expectedSignature = self::createSignature($base64Header, $base64Payload);
        if (!hash_equals($expectedSignature, $signature)) {
            throw new \Exception('Invalid token signature');
        }

        // Check expiration
        if (isset($payload['exp']) && $payload['exp'] < time()) {
            throw new \Exception('Token has expired');
        }

        return $payload;
    }

    // Helper function to create the signature
    private static function createSignature($base64Header, $base64Payload)
    {
        $data = $base64Header . '.' . $base64Payload;
        $env = parse_ini_file(__DIR__ . '/../../.env');
        self::$secretKey = $env['JWT_SECRET'];
        return self::base64UrlEncode(hash_hmac('sha256', $data, self::$secretKey, true));
    }

    // Helper function for Base64Url encoding
    private static function base64UrlEncode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    // Helper function for Base64Url decoding
    private static function base64UrlDecode($data)
    {
        return base64_decode(strtr($data, '-_', '+/'));
    }
}
