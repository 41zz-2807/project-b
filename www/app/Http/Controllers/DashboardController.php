<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $totalPemasukan = (float) Transaction::income()->sum('jumlah');
        $totalPengeluaran = (float) Transaction::expense()->sum('jumlah');
        $totalSaldo = $totalPemasukan - $totalPengeluaran;

        $transaksiTerbaru = Transaction::with('student')->orderByDesc('tanggal')
            ->orderByDesc('id')
            ->limit(10)
            ->get();

        return view('pages.dashboard.index', compact(
            'totalPemasukan',
            'totalPengeluaran',
            'totalSaldo',
            'transaksiTerbaru',
        ));
    }
}
