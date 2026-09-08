<?php

namespace App\Http\Controllers;

use App\Models\Transaction;

class PublikController extends Controller
{
    public function index()
    {
        $totalPemasukan = (float) Transaction::income()->sum('jumlah');
        $totalPengeluaran = (float) Transaction::expense()->sum('jumlah');
        $totalSaldo = $totalPemasukan - $totalPengeluaran;

        $transaksis = Transaction::with('student')
            ->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(15)
            ->get();

        $runningText = setting('running_text') ?? "Selamat datang di sistem informasi pembayaran kas dan komite {{ \$namaSekolah }} — Kelas {{ \$namaKelas }} — Transparansi keuangan untuk masa depan yang lebih baik —";

        return view('pages.publik.index', [
            'title' => setting('nama_sekolah') ?: 'Publik',
            'namaSekolah' => setting('nama_sekolah') ?: '—',
            'namaKelas' => setting('nama_kelas') ?: '—',
            'namaBank' => setting('nama_bank') ?: '—',
            'noRekening' => setting('no_rekening') ?: '',
            'namaPemilik' => setting('nama_pemilik') ?: '—',
            'totalPemasukan' => $totalPemasukan,
            'totalPengeluaran' => $totalPengeluaran,
            'totalSaldo' => $totalSaldo,
            'transaksis' => $transaksis,
            'runningText' => $runningText,
        ]);
    }
}
