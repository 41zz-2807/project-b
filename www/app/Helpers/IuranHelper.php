<?php

use Carbon\CarbonImmutable;
use Illuminate\Support\Carbon;

if (! function_exists('tahunAjaranTahun')) {
    function tahunAjaranTahun($tanggal = null): int
    {
        $date = $tanggal ? Carbon::parse($tanggal) : now();

        return $date->month >= 7 ? $date->year : $date->year - 1;
    }
}

if (! function_exists('transaksiKasSiswa')) {
    function transaksiKasSiswa($studentId): \Illuminate\Support\Collection
    {
        return \App\Models\Transaction::query()
            ->where('type', 'income')
            ->where('kategori', 'Iuran Kas')
            ->where('student_id', $studentId)
            ->orderBy('tanggal')
            ->orderBy('id')
            ->get(['id', 'jumlah', 'tanggal']);
    }
}

if (! function_exists('cakupanKasSiswa')) {
    function cakupanKasSiswa($studentId): array
    {
        static $cache = [];

        $key = (string) $studentId;

        if (array_key_exists($key, $cache)) {
            return $cache[$key];
        }

        $kas = (float) setting('iuran_kas', 0);
        $result = [];

        if ($studentId && $kas > 0) {
            $transaksis = transaksiKasSiswa($studentId);

            if ($transaksis->isNotEmpty()) {
                $posisi = CarbonImmutable::create(tahunAjaranTahun($transaksis->first()->tanggal), 7, 1);

                foreach ($transaksis as $t) {
                    $n = (int) floor((float) $t->jumlah / $kas);
                    $daftar = [];
                    $p = $posisi;

                    for ($i = 0; $i < $n; $i++) {
                        $daftar[] = namaBulanSingkat($p->month) . ' ' . $p->year;
                        $p = $p->addMonth();
                    }

                    $result[$t->id] = [
                        'n' => $n,
                        'daftar' => $daftar,
                        'sisa' => (float) $t->jumlah - ($kas * $n),
                    ];

                    $posisi = $p;
                }
            }
        }

        $cache[$key] = $result;

        return $result;
    }
}

if (! function_exists('posisiKasSiswa')) {
    /**
     * Bulan pertama yang akan dilunasi oleh siswa (format Y-m).
     *
     * Return null ketika siswa belum punya riwayat pembayaran iuran kas.
     * Saat $referenceTransactionId diisi, posisi dihitung dari transaksi
     * yang berada sebelum transaksi tersebut (untuk mode edit).
     */
    function posisiKasSiswa($studentId, $referenceTransactionId = null): ?string
    {
        if (! $studentId) {
            return null;
        }

        $kas = (float) setting('iuran_kas', 0);

        if ($kas <= 0) {
            return null;
        }

        $transaksis = transaksiKasSiswa($studentId);

        if ($transaksis->isEmpty()) {
            return null;
        }

        $posisi = CarbonImmutable::create(tahunAjaranTahun($transaksis->first()->tanggal), 7, 1);

        foreach ($transaksis as $t) {
            if ($referenceTransactionId !== null && (int) $t->id === (int) $referenceTransactionId) {
                return $posisi->format('Y-m');
            }

            $posisi = $posisi->addMonths((int) floor((float) $t->jumlah / $kas));
        }

        return $posisi->format('Y-m');
    }
}

if (! function_exists('bulanLunasKas')) {
    function bulanLunasKas($jumlah, $tanggal = null): array
    {
        $kas = (float) setting('iuran_kas', 0);
        $jumlah = (float) $jumlah;

        if ($kas <= 0 || $jumlah <= 0) {
            return ['jumlah' => 0, 'daftar' => [], 'sisa' => 0];
        }

        $n = (int) floor($jumlah / $kas);
        $posisi = CarbonImmutable::create(tahunAjaranTahun($tanggal), 7, 1);

        $daftar = [];
        for ($i = 0; $i < $n; $i++) {
            $daftar[] = namaBulanSingkat($posisi->month) . ' ' . $posisi->year;
            $posisi = $posisi->addMonth();
        }

        return ['jumlah' => $n, 'daftar' => $daftar, 'sisa' => $jumlah - ($kas * $n)];
    }
}

if (! function_exists('infoIuranKomite')) {
    function infoIuranKomite($jumlah): array
    {
        $komite = (float) setting('iuran_komite', 0);
        $jumlah = (float) $jumlah;

        if ($komite <= 0) {
            return ['status' => 'info', 'labels' => ['Iuran komite belum diatur di Pengaturan.']];
        }

        if ($jumlah === $komite) {
            return ['status' => 'lunas', 'labels' => ['Lunas']];
        }

        if ($jumlah > $komite) {
            return ['status' => 'lebih', 'labels' => ['Kelebihan ' . rupiah($jumlah - $komite)]];
        }

        return ['status' => 'kurang', 'labels' => ['Kurang ' . rupiah($komite - $jumlah)]];
    }
}

if (! function_exists('infoIuranPemasukan')) {
    function infoIuranPemasukan($transaction): array
    {
        $jumlah = $transaction->jumlah ?? 0;
        $tanggal = $transaction->tanggal;
        $kategori = $transaction->kategori ?? null;

        if ($kategori === 'Iuran Kas') {
            $studentId = $transaction->student_id ?? null;
            $info = null;

            if ($studentId) {
                $cakupan = cakupanKasSiswa($studentId);
                $c = $cakupan[$transaction->id] ?? null;

                if ($c) {
                    $info = ['jumlah' => $c['n'], 'daftar' => $c['daftar'], 'sisa' => $c['sisa']];
                }
            }

            $info ??= bulanLunasKas($jumlah, $tanggal);

            $labels = [];
            if ($info['jumlah'] <= 0) {
                $labels[] = 'Belum cukup 1 bulan';
            } else {
                $labels[] = $info['jumlah'] . ' bulan lunas: ' . implode(' · ', $info['daftar']);
                if ($info['sisa'] > 0) {
                    $labels[] = 'Sisa ' . rupiah($info['sisa']) . ' (belum menginjak bulan berikutnya)';
                }
            }

            return ['status' => $info['jumlah'] > 0 ? 'lunas' : 'info', 'labels' => $labels];
        }

        if ($kategori === 'Iuran Komite') {
            return infoIuranKomite($jumlah);
        }

        return ['status' => 'info', 'labels' => []];
    }
}