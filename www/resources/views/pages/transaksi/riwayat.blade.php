@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Riwayat Transaksi" />

    <div class="space-y-6">
        @if (session('status'))
            <div class="flex items-center gap-3 rounded-xl border border-success-200 bg-success-50 px-4 py-3 text-theme-sm font-medium text-success-700 dark:border-success-800 dark:bg-success-500/10 dark:text-success-500">
                <svg class="shrink-0 fill-current" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.7-9.3a1 1 0 0 0-1.4-1.4L9 10.6l-1.3-1.3a1 1 0 1 0-1.4 1.4l2 2a1 1 0 0 0 1.4 0l4-4Z" />
                </svg>
                {{ session('status') }}
            </div>
        @endif

        {{-- Ringkasan --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="rounded-2xl border border-success-200 bg-success-50 p-5 dark:border-success-800 dark:bg-success-500/10">
                <p class="text-theme-sm font-medium text-success-700 dark:text-success-500">Total Pemasukan</p>
                <p class="mt-1 text-title-md font-semibold text-success-700 dark:text-success-500">
                    {{ rupiah($totalPemasukan) }}
                </p>
            </div>
            <div class="rounded-2xl border border-error-200 bg-error-50 p-5 dark:border-error-800 dark:bg-error-500/10">
                <p class="text-theme-sm font-medium text-error-700 dark:text-error-500">Total Pengeluaran</p>
                <p class="mt-1 text-title-md font-semibold text-error-700 dark:text-error-500">
                    {{ rupiah($totalPengeluaran) }}
                </p>
            </div>
        </div>

        {{-- Filter --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            <form method="GET" action="{{ route('transaksi.riwayat') }}" class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div>
                    <label for="bulan" class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Bulan</label>
                    <select
                        id="bulan"
                        name="bulan"
                        class="mt-2 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"
                    >
                        <option value="">Semua Bulan</option>
                        @foreach ($bulanOptions as $option)
                            <option value="{{ $option['value'] }}" @selected($bulan === $option['value'])>{{ $option['label'] }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="jenis" class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Jenis</label>
                    <select
                        id="jenis"
                        name="jenis"
                        class="mt-2 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"
                    >
                        <option value="">Semua</option>
                        <option value="income" @selected($jenis === 'income')>Pemasukan</option>
                        <option value="expense" @selected($jenis === 'expense')>Pengeluaran</option>
                    </select>
                </div>

                <div>
                    <label for="kategori" class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Kategori</label>
                    <select
                        id="kategori"
                        name="kategori"
                        class="mt-2 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"
                    >
                        <option value="">Semua Kategori</option>
                        @foreach ($kategoriOptions as $option)
                            <option value="{{ $option }}" @selected($kategori === $option)>{{ $option }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end sm:col-span-3">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-5 py-3 text-theme-sm font-semibold text-white shadow-theme-xs transition-colors hover:bg-brand-600 dark:bg-brand-500 dark:hover:bg-brand-600"
                    >
                        Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        {{-- Tabel --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Daftar Transaksi</h3>
                <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
                    Menampilkan {{ $transaksis->total() }} transaksi
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
                                <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Jenis</p>
                            </th>
                            <th class="py-3 text-start">
                                <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Kategori</p>
                            </th>
                            <th class="py-3 text-start">
                                <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Keterangan</p>
                            </th>
                            <th class="py-3 text-start">
                                <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Bukti</p>
                            </th>
                            <th class="py-3 text-end">
                                <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Jumlah</p>
                            </th>
                            <th class="py-3 text-end">
                                <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Aksi</p>
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
                                    <span class="rounded-full px-2.5 py-1 text-theme-xs font-medium {{ $transaksi->type === 'income' ? 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500' : 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500' }}">
                                        {{ $transaksi->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap py-3 pe-4">
                                    <span class="text-theme-sm text-gray-800 dark:text-white/90">{{ $transaksi->kategori }}</span>
                                    @if ($transaksi->metode)
                                        <span class="mt-1 block text-theme-xs font-medium {{ $transaksi->metode === 'Transfer' ? 'text-brand-600 dark:text-brand-400' : 'text-gray-500 dark:text-gray-400' }}">
                                            {{ $transaksi->metode }}
                                        </span>
                                    @endif
                                    @if ($transaksi->student)
                                        <span class="mt-0.5 block text-theme-xs text-gray-500 dark:text-gray-400">{{ $transaksi->student->nama }}</span>
                                    @elseif ($transaksi->nama)
                                        <span class="mt-0.5 block text-theme-xs text-gray-500 dark:text-gray-400">{{ $transaksi->nama }}</span>
                                    @endif
                                    @if ($transaksi->type === 'income' && in_array($transaksi->kategori, ['Iuran Kas', 'Iuran Komite']))
                                        @php
                                            $info = infoIuranPemasukan($transaksi);
                                            $infoBadge = [
                                                'lunas' => 'text-success-600 dark:text-success-500',
                                                'lebih' => 'text-warning-600 dark:text-warning-500',
                                                'kurang' => 'text-error-600 dark:text-error-500',
                                                'info' => 'text-gray-500 dark:text-gray-400',
                                            ];
                                        @endphp
                                        @if ($info['labels'])
                                            <div class="mt-1 space-y-0.5">
                                                @foreach ($info['labels'] as $line)
                                                    <span class="block text-theme-xs {{ $infoBadge[$info['status']] }}">{{ $line }}</span>
                                                @endforeach
                                            </div>
                                        @endif
                                    @endif
                                </td>
                                <td class="whitespace-nowrap py-3 pe-4">
                                    <span class="text-theme-sm text-gray-500 dark:text-gray-400">{{ $transaksi->keterangan ?: '—' }}</span>
                                </td>
                                <td class="whitespace-nowrap py-3 pe-4">
                                    @if ($transaksi->files->count())
                                        <a
                                            href="{{ asset('storage/' . $transaksi->files->first()->file_path) }}"
                                            target="_blank"
                                            class="inline-flex items-center gap-1.5 text-theme-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300"
                                            title="Lihat bukti"
                                        >
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8l-5-5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                                <path d="M14 3v5h5" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                            </svg>
                                            {{ $transaksi->files->count() }} file
                                        </a>
                                    @else
                                        <span class="text-theme-sm text-gray-300 dark:text-gray-600">—</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap py-3 text-end">
                                    <span class="text-theme-sm font-semibold {{ $transaksi->type === 'income' ? 'text-success-600 dark:text-success-500' : 'text-error-600 dark:text-error-500' }}">
                                        {{ $transaksi->type === 'income' ? '+' : '-' }}{{ rupiah(abs($transaksi->jumlah)) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap py-3 text-end">
                                    <div class="flex items-center justify-end gap-1">
                                        <a
                                            href="{{ route($transaksi->type === 'income' ? 'transaksi.pemasukan' : 'transaksi.pengeluaran', ['edit' => $transaksi->id]) }}"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 transition-colors hover:bg-gray-100 hover:text-brand-600 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-brand-400"
                                            title="Edit"
                                        >
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M16.862 4.487 19.5 7.125l-8.5 8.5-3.5.5.5-3.5 8.5-8.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                                <path d="M15.5 5.9 18.1 8.5" stroke="currentColor" stroke-width="1.5" />
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('transaksi.destroy', $transaksi) }}" onsubmit="return confirm('Hapus transaksi ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button
                                                type="submit"
                                                class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 transition-colors hover:bg-error-50 hover:text-error-600 dark:text-gray-400 dark:hover:bg-error-500/10 dark:hover:text-error-500"
                                                title="Hapus"
                                            >
                                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                    <path d="M3 6h18M8 6V4a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6M10 11v6M14 11v6" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr class="border-t border-gray-100 dark:border-gray-800">
                                <td colspan="7" class="py-10 text-center">
                                    <p class="text-theme-sm text-gray-500 dark:text-gray-400">Belum ada transaksi.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $transaksis->links() }}
            </div>
        </div>
    </div>
@endsection