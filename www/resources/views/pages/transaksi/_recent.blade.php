@php
    $jenisBadge = [
        'income' => 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500',
        'expense' => 'bg-error-50 text-error-600 dark:bg-error-500/15 dark:text-error-500',
    ];
    $aksiRoute = $type === 'income' ? 'transaksi.pemasukan' : 'transaksi.pengeluaran';
    $iuranBadge = [
        'lunas' => 'text-success-600 dark:text-success-500',
        'lebih' => 'text-warning-600 dark:text-warning-500',
        'kurang' => 'text-error-600 dark:text-error-500',
        'info' => 'text-gray-500 dark:text-gray-400',
    ];
@endphp

{{-- Daftar Transaksi --}}
<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
    <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Riwayat {{ $title }}</h3>
            <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
                Total:
                <span class="font-semibold {{ $type === 'income' ? 'text-success-600 dark:text-success-500' : 'text-error-600 dark:text-error-500' }}">
                    {{ 'Rp ' . number_format((float) $total, 0, ',', '.') }}
                </span>
            </p>
        </div>

        <a
            href="{{ route('transaksi.riwayat', ['jenis' => $type]) }}"
            class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
        >
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
                                @php $info = infoIuranPemasukan($transaksi); @endphp
                                @if ($info['labels'])
                                    <div class="mt-1 space-y-0.5">
                                        @foreach ($info['labels'] as $line)
                                            <span class="block text-theme-xs {{ $iuranBadge[$info['status']] }}">{{ $line }}</span>
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
                                    href="{{ route($aksiRoute, ['edit' => $transaksi->id]) }}"
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
                        <td colspan="6" class="py-10 text-center">
                            <p class="text-theme-sm text-gray-500 dark:text-gray-400">Belum ada {{ strtolower($title) }}.</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>