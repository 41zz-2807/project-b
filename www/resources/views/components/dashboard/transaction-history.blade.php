@props(['transaksi' => []])

@php
    $jenisBadge = [
        'income' => 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500',
        'expense' => 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500',
    ];
@endphp

<div
    class="overflow-hidden rounded-2xl border border-gray-200 bg-white px-4 pb-3 pt-4 dark:border-gray-800 dark:bg-white/[0.03] sm:px-6 sm:pt-6">
    <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">History Transaksi</h3>
            <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
                Menampilkan {{ count($transaksi) }} transaksi terbaru
            </p>
        </div>

        <a
            href="{{ route('transaksi.riwayat') }}"
            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200">
            Lihat Semua
            <svg class="fill-current rtl:rotate-180" width="16" height="16" viewBox="0 0 20 20" fill="none"
                xmlns="http://www.w3.org/2000/svg">
                <path fill-rule="evenodd" clip-rule="evenodd"
                    d="M7.29289 14.7071C6.90237 14.3166 6.90237 13.6834 7.29289 13.2929L10.5858 10L7.29289 6.70711C6.90237 6.31658 6.90237 5.68342 7.29289 5.29289C7.68342 4.90237 8.31658 4.90237 8.70711 5.29289L12.7071 9.29289C13.0976 9.68342 13.0976 10.3166 12.7071 10.7071L8.70711 14.7071C8.31658 15.0976 7.68342 15.0976 7.29289 14.7071Z" />
            </svg>
        </a>
    </div>

    <div class="max-w-full overflow-x-auto custom-scrollbar">
        <table class="min-w-full">
            <thead>
                <tr class="border-t border-gray-100 dark:border-gray-800">
                    <th class="py-3 text-start">
                        <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Tanggal</p>
                    </th>
                    <th class="py-3 text-start">
                        <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Keterangan</p>
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
                @forelse ($transaksi as $transaksiItem)
                    <tr class="border-t border-gray-100 transition-colors hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/[0.03]">
                        <td class="whitespace-nowrap py-3 pe-4 text-theme-sm text-gray-500 dark:text-gray-400">
                            {{ tglIndo($transaksiItem->tanggal) }}
                        </td>
                        <td class="whitespace-nowrap py-3 pe-4">
                            <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">
                                {{ $transaksiItem->keterangan ?? 'Transaksi' }}
                            </p>
                        </td>
                        <td class="whitespace-nowrap py-3 pe-4">
                            <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">
                                {{ $transaksiItem->student?->nama ?: ($transaksiItem->nama ?: '—') }}
                            </p>
                        </td>
                        <td class="whitespace-nowrap py-3 pe-4">
                            <span class="text-theme-sm text-gray-500 dark:text-gray-400">
                                {{ $transaksiItem->kategori }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap py-3 pe-4">
                            <span class="rounded-full px-2.5 py-1 text-theme-xs font-medium {{ $jenisBadge[$transaksiItem->type] }}">
                                {{ $transaksiItem->type === 'income' ? 'Pemasukan' : 'Pengeluaran' }}
                            </span>
                        </td>
                        <td class="whitespace-nowrap py-3 text-end">
                            <span class="text-theme-sm font-semibold {{ $transaksiItem->type === 'income' ? 'text-success-600 dark:text-success-500' : 'text-error-600 dark:text-error-500' }}">
                                {{ $transaksiItem->type === 'income' ? '+' : '-' }}{{ rupiah(abs($transaksiItem->jumlah)) }}
                            </span>
                        </td>
                    </tr>
                @empty
                    <tr class="border-t border-gray-100 dark:border-gray-800">
                        <td colspan="6" class="py-10 text-center">
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