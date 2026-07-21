<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use RuntimeException;

class CustomerOtpService
{
    public function issue(string $phone): string
    {
        if (config('customer_otp.driver') === 'log' && ! app()->environment(['local', 'testing'])) {
            throw new RuntimeException('El proveedor SMS para códigos OTP no está configurado.');
        }

        $code = (string) random_int(100000, 999999);
        Cache::put($this->key($phone), [
            'hash' => Hash::make($code),
            'attempts' => 0,
        ], now()->addMinutes((int) config('customer_otp.ttl_minutes', 5)));

        Log::info('Código OTP de cliente emitido.', [
            'telefono' => $this->mask($phone),
            'codigo' => $code,
            'ttl_minutos' => (int) config('customer_otp.ttl_minutes', 5),
        ]);

        return $code;
    }

    public function verify(string $phone, string $code): bool
    {
        $key = $this->key($phone);
        $challenge = Cache::get($key);

        if (! is_array($challenge) || ($challenge['attempts'] ?? 0) >= (int) config('customer_otp.max_attempts', 5)) {
            Cache::forget($key);
            return false;
        }

        if (! Hash::check($code, $challenge['hash'])) {
            $challenge['attempts'] = ($challenge['attempts'] ?? 0) + 1;
            Cache::put($key, $challenge, now()->addMinutes((int) config('customer_otp.ttl_minutes', 5)));
            return false;
        }

        Cache::forget($key);
        return true;
    }

    private function key(string $phone): string
    {
        return 'customer-otp:'.hash('sha256', $phone);
    }

    private function mask(string $phone): string
    {
        return str_repeat('*', max(0, strlen($phone) - 3)).substr($phone, -3);
    }
}
