<?php

namespace App\Support;

final class Totp
{
    /**
     * Generate a cryptographically random base32 secret.
     */
    public static function generateSecret(int $length = 16): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';

        for ($i = 0; $i < $length; $i++) {
            $secret .= $alphabet[random_int(0, 31)];
        }

        return $secret;
    }

    /**
     * Build an otpauth:// provisioning URI for QR codes.
     */
    public static function provisioningUri(string $secret, string $label): string
    {
        return 'otpauth://totp/'.rawurlencode('GameVault:'.$label)
            .'?secret='.rawurlencode($secret)
            .'&issuer=GameVault'
            .'&algorithm=SHA1&digits=6&period=30';
    }

    /**
     * Verify a 6-digit code against the secret within the given window.
     */
    public static function verify(string $code, string $secret, int $window = 1): bool
    {
        $code = trim($code);

        if (! ctype_digit($code) || strlen($code) !== 6) {
            return false;
        }

        $counter = intdiv(time(), 30);

        for ($i = -$window; $i <= $window; $i++) {
            if (hash_equals(self::codeAt($secret, $counter + $i), $code)) {
                return true;
            }
        }

        return false;
    }

    /**
     * Compute the TOTP code for a given counter.
     */
    public static function codeAt(string $secret, int $counter): string
    {
        $key = self::base32Decode($secret);
        $binaryCounter = pack('N*', 0, $counter); // 8-byte big-endian

        $hash = hash_hmac('sha1', $binaryCounter, $key, true);
        $offset = ord($hash[strlen($hash) - 1]) & 0x0F;
        $value = (
            ((ord($hash[$offset]) & 0x7F) << 24)
            | ((ord($hash[$offset + 1]) & 0xFF) << 16)
            | ((ord($hash[$offset + 2]) & 0xFF) << 8)
            | (ord($hash[$offset + 3]) & 0xFF)
        );

        return str_pad((string) ($value % 1_000_000), 6, '0', STR_PAD_LEFT);
    }

    private static function base32Decode(string $secret): string
    {
        $alphabet = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $buffer = 0;
        $bits = 0;
        $output = '';

        foreach (str_split(strtoupper($secret)) as $char) {
            $value = strpos($alphabet, $char);

            if ($value === false) {
                continue;
            }

            $buffer = ($buffer << 5) | $value;
            $bits += 5;

            if ($bits >= 8) {
                $output .= chr(($buffer >> ($bits - 8)) & 0xFF);
                $bits -= 8;
            }
        }

        return $output;
    }
}
