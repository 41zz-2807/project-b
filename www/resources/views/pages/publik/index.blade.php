@extends('layouts.fullscreen-layout')

@section('content')
    <div class="relative z-1 bg-white dark:bg-gray-900">
        <div class="mx-auto w-full max-w-6xl space-y-6 px-4 py-8 sm:px-6 sm:py-10">

            {{-- Baris 1: Nama sekolah --}}
            <header class="text-center">
                <h1 class="text-title-md font-bold tracking-tight text-gray-800 dark:text-white/90">
                    {{ $namaSekolah }}
                </h1>
                <p class="mt-2 text-theme-sm text-gray-500 dark:text-gray-400">
                    {{ $namaKelas }}
                </p>
            </header>

            <x-dashboard.rekening-card />

            {{-- Baris 2: Pemasukan, pengeluaran, saldo --}}
            <x-dashboard.summary-cards
                :totalPemasukan="$totalPemasukan"
                :totalPengeluaran="$totalPengeluaran"
                :totalSaldo="$totalSaldo"
            />

            {{-- Baris 3: History transaksi --}}
            <div class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6">
                <div class="mb-4">
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">History Transaksi</h3>
                    <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
                        Menampilkan {{ count($transaksis) }} transaksi terbaru
                    </p>
                </div>

                <div class="max-w-full overflow-x-auto custom-scrollbar">
                    <table class="min-w-full">
                        <thead>
                            <tr class="border-t border-gray-100 dark:border-gray-800">
                                <th class="py-3 text-start">
                                    <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Tanggal</p>
                                </th>
                                <th class="py-3 text-start">
                                    <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Nama</p>
                                </th>
                                <th class="py-3 text-start">
                                    <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Kategori</p>
                                </th>
                                <th class="py-3 text-start">
                                    <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Jenis</p>
                                </th>
                                <th class="py-3 text-end">
                                    <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Jumlah</p>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($transaksis as $transaksi)
                                <tr class="border-t border-gray-100 transition-colors hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/[0.03]">
                                    <td class="whitespace-nowrap py-3 pe-4 text-theme-sm text-gray-500 dark:text-gray-400">
                                        {{ tglIndo($transaksi->tanggal) }}
                                    </td>
                                    <td class="whitespace-nowrap py-3 pe-4">
                                        <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">
                                            {{ $transaksi->student?->nama ?: ($transaksi->nama ?: '—') }}
                                        </p>
                                        @if ($transaksi->metode)
                                            <span class="mt-0.5 block text-theme-xs text-gray-500 dark:text-gray-400">
                                                {{ $transaksi->metode }}
                                            </span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap py-3 pe-4">
                                        <span class="text-theme-sm text-gray-500 dark:text-gray-400">
                                            {{ $transaksi->kategori }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap py-3 pe-4">
                                        <span
                                            class="rounded-full px-2.5 py-1 text-theme-xs font-medium {{ $transaksi->type === 'income' ? 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500' : 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500' }}">
                                            {{ $transaksi->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                        </span>
                                    </td>
                                    <td class="whitespace-nowrap py-3 text-end">
                                        <span
                                            class="text-theme-sm font-semibold {{ $transaksi->type === 'income' ? 'text-success-600 dark:text-success-500' : 'text-error-600 dark:text-error-500' }}">
                                            {{ $transaksi->type === 'income' ? '+' : '-' }}{{ rupiah(abs($transaksi->jumlah)) }}
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr class="border-t border-gray-100 dark:border-gray-800">
                                    <td colspan="5" class="py-10 text-center">
                                        <p class="text-theme-sm text-gray-500 dark:text-gray-400">
                                            Belum ada transaksi.
                                        </p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <p class="text-center text-theme-xs text-gray-400 dark:text-gray-600">
                {{ $namaSekolah }} · Data diperbarui {{ tglIndo(now()) }}
            </p>
        </div>
    </div>
@endsection