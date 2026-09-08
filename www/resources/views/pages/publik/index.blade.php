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

            <div class="w-full">
                <div class="flex items-center gap-4">
                    <a href="{{ route('login') }}" class="btn-masuk flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" class="mr-2">
                            <path fill="none" d="M0 0h24v24H0z"></path>
                            <path fill="currentColor" d="M1 14.5a6.496 6.496 0 0 1 3.064-5.519 8.001 8.001 0 0 1 15.872 0 6.5 6.5 0 0 1-2.936 12L7 21c-3.356-.274-6-3.078-6-6.5zm15.848 4.487a4.5 4.5 0 0 0 2.03-8.309l-.807-.503-.12-.942a6.001 6.001 0 0 0-11.903 0l-.12.942-.805.503a4.5 4.5 0 0 0 2.029 8.309l.173.013h9.35l.173-.013zM13 12h3l-4 5-4-5h3V8h2v4z"></path>
                        </svg>
                        <span>Masuk</span>
                    </a>

                    <div class="flex-1 overflow-hidden">
                        <div class="relative h-8" aria-live="polite">
                            <div class="absolute inset-0 animate-marquee whitespace-nowrap flex items-center gap-8"
                                style="animation-duration: 20s;">
                                <span class="text-theme-sm text-gray-500 dark:text-gray-400 font-medium">
                                    {{ $runningText }}
                                </span>
                                <span class="text-theme-sm text-gray-500 dark:text-gray-400 font-medium">
                                    {{ $runningText }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>

                <style>
                    .btn-masuk {
                        display: inline-flex;
                        align-items: center;
                        font-family: inherit;
                        cursor: pointer;
                        font-weight: 500;
                        font-size: 14px;
                        padding: 0.5em 1em 0.5em 0.8em;
                        color: white;
                        background: linear-gradient(to bottom right, #0ea5e9, #0284c7, #0369a1);
                        border: none;
                        box-shadow: 0 4px 12px -2px rgba(14, 165, 233, 0.5);
                        letter-spacing: 0.025em;
                        border-radius: 9999px;
                        text-decoration: none;
                        transition: box-shadow 0.2s ease, transform 0.1s ease;
                    }

                    .btn-masuk:hover {
                        box-shadow: 0 6px 16px -3px rgba(14, 165, 233, 0.6);
                    }

                    .btn-masuk:active {
                        box-shadow: 0 2px 8px -2px rgba(14, 165, 233, 0.5);
                        transform: scale(0.98);
                    }

                    @keyframes marquee {
                        0% { transform: translateX(0); }
                        100% { transform: translateX(-50%); }
                    }

                    .animate-marquee {
                        animation: marquee linear infinite;
                    }
                </style>
            </div>

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