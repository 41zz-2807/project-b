<?php

namespace App\Console\Commands;

use App\Services\TelegramNotifier;
use Illuminate\Console\Command;

class TelegramReport extends Command
{
    protected $signature = 'telegram:report {--force : Kirim laporan tanpa memeriksa jadwal}';

    protected $description = 'Kirim laporan keuangan ringkas ke Telegram';

    public function handle(TelegramNotifier $notifier): int
    {
        if (! $notifier->enabled()) {
            $this->error('Notifikasi Telegram belum dikonfigurasi. Atur token, chat ID, dan aktifkan di halaman Pengaturan.');

            return self::FAILURE;
        }

        $jamSetting = (string) setting('telegram_report_jam', '');
        $hariSetting = (string) setting('telegram_report_hari', '');

        if (! $this->option('force')) {
            $jamSekarang = now()->timezone('Asia/Jakarta')->format('H:i');
            $jamKirim = substr($jamSetting, 0, 5);

            if ($jamSetting !== '' && $jamSekarang !== $jamKirim) {
                $this->info("Belum waktunya kirim laporan. Jadwal: {$jamKirim}, sekarang: {$jamSekarang}.");

                return self::SUCCESS;
            }

            if ($hariSetting !== '') {
                $hariDipilih = array_map('trim', explode(',', $hariSetting));
                $hariSekarang = strtolower(now()->timezone('Asia/Jakarta')->locale('id')->isoFormat('dddd'));

                if (! in_array($hariSekarang, $hariDipilih, true)) {
                    $this->info("Hari ini ({$hariSekarang}) tidak termasuk jadwal pengiriman. Hari terpilih: ".implode(', ', $hariDipilih).'.');

                    return self::SUCCESS;
                }
            }
        }

        $this->info('Mengirim laporan ke Telegram...');

        $message = $notifier->generateReportMessage();
        $sent = $notifier->send($message);

        if ($sent) {
            $this->info('Laporan berhasil dikirim ke Telegram.');

            return self::SUCCESS;
        }

        $this->error('Gagal mengirim laporan ke Telegram. Periksa log untuk detail.');

        return self::FAILURE;
    }
}
