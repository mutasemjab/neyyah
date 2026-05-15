<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

interface SmsServiceInterface
{
    public function send(string $phone, string $code): bool;
}

class SmsService implements SmsServiceInterface
{
    public function send(string $phone, string $code): bool
    {
        Log::channel('daily')->info("SMS OTP to {$phone}: {$code}");

        return true;
    }
}
