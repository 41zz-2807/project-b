<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramNotifier
{
    protected string $token;

    protected string $chatId;

    public function __construct()
    {
        $this->token = (string) config('services.telegram.token', '');
        $this->chatId = (string) config('services.telegram.chat_id', '');
    }

    public function enabled(): bool
    {
        return (bool) config('services.telegram.enabled', false)
            && $this->token !== ''
            && $this->chatId !== '';
    }

    public function send(string $message): bool
    {
        if (! $this->enabled()) {
            Log::debug('Notifikasi Telegram dilewati: TELEGRAM_NOTIFY_ENABLED, token, atau chat id belum dikonfigurasi.');

            return false;
        }

        try {
            $response = Http::timeout(10)
                ->post("https://api.telegram.org/bot{$this->token}/sendMessage", [
                    'chat_id' => $this->chatId,
                    'text' => $message,
                    'parse_mode' => 'HTML',
                    'disable_web_page_preview' => true,
                ]);

            if (! $response->successful()) {
                Log::warning('Notifikasi Telegram gagal dikirim.', ['body' => $response->body()]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning('Notifikasi Telegram gagal dikirim.', ['error' => $e->getMessage()]);

            return false;
        }
    }
}
