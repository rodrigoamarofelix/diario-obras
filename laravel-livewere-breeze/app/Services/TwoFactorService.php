<?php

namespace App\Services;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;

class TwoFactorService
{
    /**
     * Gera uma chave secreta para 2FA
     */
    public function generateSecret(): string
    {
        // Gerar chave base32 válida para TOTP
        $chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = '';
        for ($i = 0; $i < 32; $i++) {
            $secret .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $secret;
    }

    /**
     * Gera códigos de backup
     */
    public function generateBackupCodes(): array
    {
        $codes = [];
        for ($i = 0; $i < 8; $i++) {
            $codes[] = strtoupper(Str::random(8));
        }
        return $codes;
    }

    /**
     * Gera código TOTP baseado na chave secreta
     */
    public function generateTOTP(string $secret): string
    {
        $time = floor(time() / 30);
        $key = $this->base32Decode($secret);
        $time = pack('N*', 0) . pack('N*', $time);
        $hm = hash_hmac('sha1', $time, $key, true);
        $offset = ord(substr($hm, -1)) & 0x0F;
        $hashpart = substr($hm, $offset, 4);
        $value = unpack('N', $hashpart);
        $value = $value[1];
        $value = $value & 0x7FFFFFFF;
        $modulo = pow(10, 6);
        return str_pad($value % $modulo, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Gera código TOTP para um tempo específico
     */
    private function generateTOTPForTime(string $secret, int $time): string
    {
        $key = $this->base32Decode($secret);
        $time = pack('N*', 0) . pack('N*', $time);
        $hm = hash_hmac('sha1', $time, $key, true);
        $offset = ord(substr($hm, -1)) & 0x0F;
        $hashpart = substr($hm, $offset, 4);
        $value = unpack('N', $hashpart);
        $value = $value[1];
        $value = $value & 0x7FFFFFFF;
        $modulo = pow(10, 6);
        return str_pad($value % $modulo, 6, '0', STR_PAD_LEFT);
    }

    /**
     * Verifica se o código fornecido é válido
     */
    public function verifyCode(string $secret, string $code): bool
    {
        // Limpar espaços e garantir que seja string
        $code = trim((string) $code);

        // Verificar se tem 6 dígitos
        if (strlen($code) !== 6 || !ctype_digit($code)) {
            return false;
        }

        // Verificar códigos em uma janela de tempo maior (±2 minutos)
        for ($i = -4; $i <= 4; $i++) {
            $time = floor((time() + ($i * 30)) / 30);
            $testCode = $this->generateTOTPForTime($secret, $time);
            if ($testCode === $code) {
                return true;
            }
        }

        return false;
    }

    /**
     * Decodifica string base32
     */
    private function base32Decode(string $input): string
    {
        $map = [
            'A' => 0, 'B' => 1, 'C' => 2, 'D' => 3, 'E' => 4, 'F' => 5, 'G' => 6, 'H' => 7,
            'I' => 8, 'J' => 9, 'K' => 10, 'L' => 11, 'M' => 12, 'N' => 13, 'O' => 14, 'P' => 15,
            'Q' => 16, 'R' => 17, 'S' => 18, 'T' => 19, 'U' => 20, 'V' => 21, 'W' => 22, 'X' => 23,
            'Y' => 24, 'Z' => 25, '2' => 26, '3' => 27, '4' => 28, '5' => 29, '6' => 30, '7' => 31
        ];

        $input = strtoupper($input);
        $input = str_replace('=', '', $input);
        $output = '';

        for ($i = 0; $i < strlen($input); $i += 8) {
            $chunk = substr($input, $i, 8);
            $chunk = str_pad($chunk, 8, '0');

            $bits = 0;
            $bitCount = 0;

            for ($j = 0; $j < strlen($chunk); $j++) {
                if (isset($map[$chunk[$j]])) {
                    $bits = ($bits << 5) | $map[$chunk[$j]];
                    $bitCount += 5;

                    if ($bitCount >= 8) {
                        $output .= chr(($bits >> ($bitCount - 8)) & 0xFF);
                        $bitCount -= 8;
                    }
                }
            }
        }

        return $output;
    }

    /**
     * Gera URL para QR Code
     */
    public function generateQRCodeUrl(string $email, string $secret, string $issuer = 'SGC'): string
    {
        $label = urlencode($email);
        $issuer = urlencode($issuer);
        $secret = urlencode($secret);

        // Formato correto para apps autenticadores
        return "otpauth://totp/{$issuer}:{$label}?secret={$secret}&issuer={$issuer}&algorithm=SHA1&digits=6&period=30";
    }

    /**
     * Testa se o TOTP está funcionando corretamente
     */
    public function testTOTP(string $secret): array
    {
        $currentTime = time();
        $timeStep = floor($currentTime / 30);

        return [
            'current_time' => $currentTime,
            'time_step' => $timeStep,
            'current_code' => $this->generateTOTP($secret),
            'previous_code' => $this->generateTOTPForTime($secret, $timeStep - 1),
            'next_code' => $this->generateTOTPForTime($secret, $timeStep + 1),
            'secret' => $secret,
        ];
    }

    /**
     * Verifica se um código de backup é válido
     */
    public function verifyBackupCode(array $backupCodes, string $code): bool
    {
        $index = array_search($code, $backupCodes);
        if ($index !== false) {
            unset($backupCodes[$index]);
            return true;
        }
        return false;
    }
}
