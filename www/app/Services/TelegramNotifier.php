<?php

namespace App\Services;

use App\Models\Student;
use App\Models\Transaction;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonImmutable;
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
            $response = Http::timeout(30)
                ->connectTimeout(20)
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

    public function generateReportMessage(): string
    {
        $namaSekolah = setting('nama_sekolah', '-');
        $namaKelas = setting('nama_kelas', '-');
        $iuranKas = (float) setting('iuran_kas', 0);
        $iuranKomite = (float) setting('iuran_komite', 0);

        $totalPemasukan = (float) Transaction::income()->sum('jumlah');
        $totalPengeluaran = (float) Transaction::expense()->sum('jumlah');
        $totalSaldo = $totalPemasukan - $totalPengeluaran;

        $totalSaldoKas = (float) Transaction::income()
            ->where('kategori', 'Iuran Kas')
            ->sum('jumlah');

        $totalSaldoKomite = (float) Transaction::income()
            ->where('kategori', 'Iuran Komite')
            ->sum('jumlah');

        $awalAjaran = CarbonImmutable::create(tahunAjaranTahun(now()), 7, 1);
        $bulanBerjalan = CarbonImmutable::instance(now()->startOfMonth());

        $lunas = 0;
        $belumLunas = 0;

        foreach (Student::orderBy('nama')->get() as $siswa) {
            $posisi = posisiKasSiswa($siswa->id);
            $kasOk = false;

            if ($iuranKas > 0) {
                if ($posisi !== null) {
                    $posisiDate = CarbonImmutable::parse($posisi.'-01');
                    $lunasSampai = $posisiDate->subMonth();
                    $kasOk = $lunasSampai->gte($bulanBerjalan);
                } else {
                    $kasOk = $bulanBerjalan->lt($awalAjaran);
                }
            } else {
                $kasOk = true;
            }

            $komitePaid = (float) Transaction::income()
                ->where('kategori', 'Iuran Komite')
                ->where('student_id', $siswa->id)
                ->where('tanggal', '>=', $awalAjaran->toDateString())
                ->sum('jumlah');

            $komiteOk = $iuranKomite <= 0 || $komitePaid >= $iuranKomite;

            if ($kasOk && $komiteOk) {
                $lunas++;
            } else {
                $belumLunas++;
            }
        }

        $tanggal = now()->timezone('Asia/Jakarta')->format('d M Y');
        $jam = now()->timezone('Asia/Jakarta')->format('H:i');

        $msg = "📊 <b>Laporan Keuangan {$namaSekolah}</b>\n";
        $msg .= "🏫 Kelas: {$namaKelas}\n";
        $msg .= "📅 {$tanggal} jam {$jam} WIB\n";
        $msg .= "━━━━━━━━━━━━━━━━━━━━━\n\n";

        $msg .= "💰 <b>Saldo Kas</b>: ".rupiah($totalSaldoKas)."\n";
        $msg .= "🏛️ <b>Saldo Komite</b>: ".rupiah($totalSaldoKomite)."\n\n";

        $msg .= "📈 <b>Total Pemasukan</b>: ".rupiah($totalPemasukan)."\n";
        $msg .= "📉 <b>Total Pengeluaran</b>: ".rupiah($totalPengeluaran)."\n";
        $msg .= "💵 <b>Saldo Bersih</b>: ".rupiah($totalSaldo)."\n\n";

        $msg .= "━━━━━━━━━━━━━━━━━━━━━\n";
        $msg .= "👨‍🎓 <b>Status Pembayaran Siswa</b>\n";
        $msg .= "✅ Lunas: {$lunas} siswa\n";
        $msg .= "❌ Belum Lunas: {$belumLunas} siswa\n";

        return $msg;
    }

    public function sendReport(): bool
    {
        $message = $this->generateReportMessage();
        $sentText = $this->send($message);

        $pdfPath = $this->generatePdf();

        if ($pdfPath) {
            $this->sendDocument($pdfPath);
            @unlink($pdfPath);
        }

        return $sentText;
    }

    protected function sendDocument(string $filePath, ?string $caption = null): bool
    {
        if (! $this->enabled()) {
            Log::debug('Notifikasi Telegram dilewati: TELEGRAM_NOTIFY_ENABLED, token, atau chat id belum dikonfigurasi.');

            return false;
        }

        try {
            $multipart = [
                [
                    'name' => 'chat_id',
                    'contents' => $this->chatId,
                ],
                [
                    'name' => 'document',
                    'contents' => fopen($filePath, 'r'),
                    'filename' => basename($filePath),
                ],
            ];

            if ($caption !== null) {
                $multipart[] = [
                    'name' => 'caption',
                    'contents' => $caption,
                ];
            }

            $response = Http::timeout(60)
                ->connectTimeout(20)
                ->asMultipart()
                ->post("https://api.telegram.org/bot{$this->token}/sendDocument", $multipart);

            if (! $response->successful()) {
                Log::warning('Pengiriman dokumen Telegram gagal.', ['body' => $response->body()]);

                return false;
            }

            return true;
        } catch (\Throwable $e) {
            Log::warning('Pengiriman dokumen Telegram gagal.', ['error' => $e->getMessage()]);

            return false;
        }
    }

    protected function generatePdf(): ?string
    {
        try {
            $rows = $this->buildReportRows();

            $pdf = Pdf::loadView('pages.transaksi.laporan_pdf', [
                'rows' => $rows,
                'iuranKas' => (float) setting('iuran_kas', 0),
                'iuranKomite' => (float) setting('iuran_komite', 0),
                'namaSekolah' => (string) setting('nama_sekolah', ''),
                'namaKelas' => (string) setting('nama_kelas', ''),
            ])->setPaper('a4', 'landscape');

            $path = tempnam(sys_get_temp_dir(), 'laporan').'.pdf';
            file_put_contents($path, $pdf->output());

            return $path;
        } catch (\Throwable $e) {
            Log::warning('Gagal membuat PDF laporan.', ['error' => $e->getMessage()]);

            return null;
        }
    }

    protected function buildReportRows(): array
    {
        $kas = (float) setting('iuran_kas', 0);
        $komite = (float) setting('iuran_komite', 0);

        $awalAjaran = CarbonImmutable::create(tahunAjaranTahun(now()), 7, 1);
        $bulanBerjalan = CarbonImmutable::instance(now()->startOfMonth());

        $rows = [];

        foreach (Student::orderBy('nama')->get() as $siswa) {
            $posisi = posisiKasSiswa($siswa->id);

            $kasStatus = 'info';
            $kasKurangBulan = 0;
            $kasKurangDaftar = [];
            $kasSampai = null;

            if ($kas > 0) {
                if ($posisi === null) {
                    if ($bulanBerjalan->gte($awalAjaran)) {
                        $kasStatus = 'Belum Lunas';
                        $kasKurangBulan = (int) $awalAjaran->diffInMonths($bulanBerjalan) + 1;
                        $kasKurangDaftar = $this->bulanBerurutan($awalAjaran, $bulanBerjalan);
                    } else {
                        $kasStatus = 'info';
                    }
                } else {
                    $posisiDate = CarbonImmutable::parse($posisi.'-01');
                    $lunasSampai = $posisiDate->subMonth();
                    $kasSampai = namaBulanSingkat($lunasSampai->month).' '.$lunasSampai->year;

                    if ($lunasSampai->gte($bulanBerjalan)) {
                        $kasStatus = 'Lunas';
                    } else {
                        $kasStatus = 'Belum Lunas';
                        $kasKurangBulan = (int) $posisiDate->diffInMonths($bulanBerjalan) + 1;
                        $kasKurangDaftar = $this->bulanBerurutan($posisiDate, $bulanBerjalan);
                    }
                }
            }

            $komitePaid = (float) Transaction::income()
                ->where('kategori', 'Iuran Komite')
                ->where('student_id', $siswa->id)
                ->where('tanggal', '>=', $awalAjaran->toDateString())
                ->sum('jumlah');

            $komiteStatus = 'info';
            $komiteKurangRupiah = 0;

            if ($komite > 0) {
                if ($komitePaid >= $komite) {
                    $komiteStatus = $komitePaid > $komite ? 'Lebih' : 'Lunas';
                } else {
                    $komiteStatus = 'Belum Lunas';
                    $komiteKurangRupiah = (int) round($komite - $komitePaid);
                }
            }

            $kasKurangRupiah = (int) round($kasKurangBulan * $kas);

            $rows[] = [
                'nama' => $siswa->nama,
                'status' => $siswa->status,
                'kasStatus' => $kasStatus,
                'kasKurangBulan' => $kasKurangBulan,
                'kasKurangRupiah' => $kasKurangRupiah,
                'kasKurangDaftar' => $kasKurangDaftar,
                'kasSampai' => $kasSampai,
                'komiteStatus' => $komiteStatus,
                'komitePaid' => $komitePaid,
                'komiteKurangRupiah' => $komiteKurangRupiah,
                'totalKurangRupiah' => $kasKurangRupiah + $komiteKurangRupiah,
            ];
        }

        return $rows;
    }

    protected function bulanBerurutan(CarbonImmutable $dari, CarbonImmutable $sampai): array
    {
        $daftar = [];
        $m = $dari->copy();
        $batas = $sampai->copy();

        while ($m->lte($batas)) {
            $daftar[] = namaBulanSingkat($m->month).' '.$m->year;
            $m = $m->addMonth();
        }

        return $daftar;
    }
}
