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

            {{-- Tombol Masuk, Informasi & Download PDF --}}
            <div x-data="{ infoOpen: false }">
                <div class="flex flex-nowrap items-center gap-2 justify-start sm:gap-3">
                    <a href="{{ route('login') }}" class="btn-aksi btn-masuk flex-shrink-0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" class="me-2">
                            <path fill="none" d="M0 0h24v24H0z"></path>
                            <path fill="currentColor" d="M1 14.5a6.496 6.496 0 0 1 3.064-5.519 8.001 8.001 0 0 1 15.872 0 6.5 6.5 0 0 1-2.936 12L7 21c-3.356-.274-6-3.078-6-6.5zm15.848 4.487a4.5 4.5 0 0 0 2.03-8.309l-.807-.503-.12-.942a6.001 6.001 0 0 0-11.903 0l-.12.942-.805.503a4.5 4.5 0 0 0 2.029 8.309l.173.013h9.35l.173-.013zM13 12h3l-4 5-4-5h3V8h2v4z"></path>
                        </svg>
                        <span>Masuk</span>
                    </a>

                    <button type="button" class="btn-aksi btn-informasi flex-shrink-0" @click="infoOpen = true">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="18" height="18" class="me-2">
                            <path fill="none" d="M0 0h24v24H0z"></path>
                            <path fill="currentColor" d="M11 7h2v2h-2V7zm0 4h2v6h-2v-6zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"></path>
                        </svg>
                        <span>Informasi</span>
                    </button>

                    <a
                        href="{{ route('publik.laporan.pdf') }}"
                        class="btn-aksi btn-pdf flex-shrink-0"
                    >
                        <svg class="shrink-0 fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2a1 1 0 0 1 1 1v9.59l2.3-2.3a1 1 0 1 1 1.4 1.42l-4 4a1 1 0 0 1-1.4 0l-4-4A1 1 0 0 1 8.7 11.3L11 13.59V3a1 1 0 0 1 1-1ZM4 16a1 1 0 0 1 1 1v2a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2a1 1 0 1 1 2 0v2a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3v-2a1 1 0 0 1 1-1Z" />
                        </svg>
                        Download PDF
                    </a>
                </div>

                {{-- Modal Informasi --}}
                <div x-show="infoOpen" x-cloak
                     @keydown.escape.window="infoOpen = false"
                     class="fixed inset-0 z-99999 flex items-center justify-center overflow-y-auto p-5">
                    <div @click="infoOpen = false" class="fixed inset-0 h-full w-full bg-gray-400/50 backdrop-blur-[32px]"
                         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"></div>

                    <div @click.stop class="relative w-full max-w-xl rounded-3xl bg-white p-6 shadow-theme-xl dark:bg-gray-900 sm:p-8"
                         x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95"
                         x-transition:enter-end="opacity-100 transform scale-100" x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100 transform scale-100" x-transition:leave-end="opacity-0 transform scale-95">
                        <button @click="infoOpen = false"
                            class="absolute right-3 top-3 flex h-9.5 w-9.5 items-center justify-center rounded-full bg-gray-100 text-gray-400 transition-colors hover:bg-gray-200 hover:text-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-gray-700 dark:hover:text-white sm:right-6 sm:top-6 sm:h-11 sm:w-11">
                            <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path fillRule="evenodd" clipRule="evenodd" d="M6.04289 16.5413C5.65237 16.9318 5.65237 17.565 6.04289 17.9555C6.43342 18.346 7.06658 18.346 7.45711 17.9555L11.9987 13.4139L16.5408 17.956C16.9313 18.3466 17.5645 18.3466 17.955 17.956C18.3455 17.5655 18.3455 16.9323 17.955 16.5418L13.4129 11.9997L17.955 7.4576C18.3455 7.06707 18.3455 6.43391 17.955 6.04338C17.5645 5.65286 16.9313 5.65286 16.5408 6.04338L13.4129 10.5855L7.45711 6.0439C7.06658 5.65338 6.43342 5.65338 6.04289 6.0439C5.65237 6.43442 5.65237 7.06759 6.04289 7.45811L10.5845 11.9997L6.04289 16.5413Z" fill="currentColor" />
                            </svg>
                        </button>

                        <div class="flex items-center gap-3 border-b border-gray-100 pb-4 dark:border-gray-800">
                            <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500">
                                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="22" height="22" fill="currentColor">
                                    <path d="M11 7h2v2h-2V7zm0 4h2v6h-2v-6zm1-9C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm0 18c-4.41 0-8-3.59-8-8s3.59-8 8-8 8 3.59 8 8-3.59 8-8 8z"></path>
                                </svg>
                            </div>
                            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Informasi</h3>
                        </div>

                        <div class="mt-4 text-theme-sm leading-relaxed whitespace-pre-line text-gray-600 dark:text-gray-400">
                            {!! nl2br(e(str_replace(["\r\n", "\r"], "\n", $infoText))) !!}
                        </div>
                    </div>
                </div>
            </div>

            <style>
                [x-cloak] {
                    display: none;
                }

                .btn-aksi {
                    display: inline-flex;
                    align-items: center;
                    font-family: inherit;
                    cursor: pointer;
                    font-weight: 500;
                    font-size: 14px;
                    padding: 0.5em 1em 0.5em 0.8em;
                    color: white;
                    border: none;
                    letter-spacing: 0.025em;
                    border-radius: 9999px;
                    text-decoration: none;
                    transition: box-shadow 0.2s ease, transform 0.1s ease;
                }

                .btn-aksi:hover {
                    filter: brightness(1.05);
                }

                .btn-aksi:active {
                    transform: scale(0.98);
                }

                .btn-masuk {
                    background: linear-gradient(to bottom right, #0ea5e9, #0284c7, #0369a1);
                    box-shadow: 0 4px 12px -2px rgba(14, 165, 233, 0.5);
                }

                .btn-masuk:hover {
                    box-shadow: 0 6px 16px -3px rgba(14, 165, 233, 0.6);
                }

                .btn-informasi {
                    background: linear-gradient(to bottom right, #22c55e, #16a34a, #15803d);
                    box-shadow: 0 4px 12px -2px rgba(34, 197, 94, 0.5);
                }

                .btn-informasi:hover {
                    box-shadow: 0 6px 16px -3px rgba(34, 197, 94, 0.6);
                }

                .btn-pdf {
                    background: #465fff;
                    box-shadow: 0 4px 12px -2px rgba(70, 95, 255, 0.5);
                }

                .btn-pdf:hover {
                    box-shadow: 0 6px 16px -3px rgba(70, 95, 255, 0.6);
                }

                @media (max-width: 480px) {
                    .btn-aksi {
                        font-size: 11px;
                        padding: 0.4em 0.6em 0.4em 0.45em;
                    }

                    .btn-aksi .me-2,
                    .btn-aksi svg {
                        width: 15px;
                        height: 15px;
                    }

                    .btn-aksi svg.me-2 {
                        margin-inline-end: 4px;
                    }
                }
            </style>

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