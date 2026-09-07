@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Dashboard" />

    <div class="space-y-6">
        <x-dashboard.identity-card />

        <x-dashboard.summary-cards
            :totalPemasukan="$totalPemasukan"
            :totalPengeluaran="$totalPengeluaran"
            :totalSaldo="$totalSaldo"
        />

        <x-dashboard.transaction-history :transaksi="$transaksiTerbaru" />
    </div>
@endsection