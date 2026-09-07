<?php

namespace App\Console\Commands;

use App\Models\Student;
use App\Models\Transaction;
use App\Models\TransactionFile;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class CleanTransaksi extends Command
{
    protected $signature = 'clean:transaksi {--force : Lewati konfirmasi interaktif}';

    protected $description = 'Menghapus seluruh data transaksi, bukti pembayaran, rekap kas & komite, laporan, dan siswa (saldo kembali nol).';

    public function handle(): int
    {
        $jumlahTransaksi = Transaction::count();
        $jumlahSiswa = Student::count();
        $jumlahFile = count($this->semuaDokumen());

        if ($jumlahTransaksi === 0 && $jumlahSiswa === 0 && $jumlahFile === 0) {
            $this->info('Tidak ada data transaksi, bukti pembayaran, rekap kas & komite, laporan, atau siswa yang perlu dibersihkan.');

            return self::SUCCESS;
        }

        $this->line('Ditemukan:');
        $this->line("  - Transaksi          : {$jumlahTransaksi}");
        $this->line("  - Siswa              : {$jumlahSiswa}");
        $this->line('  - Bukti pembayaran   : '.count(Storage::disk('public')->allFiles('bukti')));
        $this->line("  - File rekap/laporan : {$jumlahFile}");

        if (! $this->option('force')) {
            if (! $this->input->isInteractive()) {
                $this->warn('Pakai --force agar skrip berjalan tanpa konfirmasi.');

                return self::SUCCESS;
            }

            if (! $this->confirm('Yakin menghapus SEMUA transaksi, bukti pembayaran, rekap kas & komite, laporan, dan siswa? Data tidak dapat dipulihkan.')) {
                $this->warn('Dibatalkan.');

                return self::SUCCESS;
            }
        }

        $fileHapus = 0;
        foreach (Storage::disk('public')->allFiles('bukti') as $path) {
            Storage::disk('public')->delete($path);
            $fileHapus++;
        }

        $laporanHapus = 0;
        foreach ($this->semuaDokumen() as $path) {
            File::delete($path);
            $laporanHapus++;
        }

        Transaction::query()->delete();
        TransactionFile::query()->delete();
        Student::query()->delete();

        $this->info("Selesai. {$jumlahTransaksi} transaksi, {$jumlahSiswa} siswa, {$fileHapus} file bukti, dan {$laporanHapus} file rekap/laporan berhasil dihapus.");
        $this->info('Rekap kas & komite dan laporan ikut otomatis kosong (dihitung ulang dari data transaksi).');

        return self::SUCCESS;
    }

    private function semuaDokumen(): array
    {
        $hasil = [];

        foreach (['laporan', 'public/laporan', 'rekap', 'public/rekap'] as $folder) {
            $dir = storage_path('app/'.$folder);

            if (! is_dir($dir)) {
                continue;
            }

            foreach (File::allFiles($dir) as $file) {
                $hasil[] = $file->getPathname();
            }
        }

        return $hasil;
    }
}
