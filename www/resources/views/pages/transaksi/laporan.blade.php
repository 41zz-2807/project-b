@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Laporan Pembayaran" />

    <div class="space-y-6">
        {{-- Ringkasan --}}
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div class="rounded-2xl border border-brand-200 bg-brand-50 p-5 dark:border-brand-800 dark:bg-brand-500/10">
                <p class="text-theme-sm font-medium text-brand-700 dark:text-brand-500">{{ $namaSekolah ?: 'Sekolah' }} · {{ $namaKelas ?: 'Kelas' }}</p>
                <p class="mt-1 text-theme-sm text-brand-700 dark:text-brand-500">Posisi per {{ tglIndo(now()) }}</p>
            </div>
            <div class="rounded-2xl border border-warning-200 bg-warning-50 p-5 dark:border-warning-800 dark:bg-warning-500/10">
                <p class="text-theme-sm font-medium text-warning-700 dark:text-warning-500">Iuran Kas</p>
                <p class="mt-1 text-title-md font-semibold text-warning-700 dark:text-warning-500">{{ $iuranKas > 0 ? rupiah($iuranKas) . '/bulan' : 'Belum diatur' }}</p>
                <p class="mt-1 text-theme-sm font-medium text-warning-700 dark:text-warning-500">Iuran Komite</p>
                <p class="mt-1 text-theme-sm font-semibold text-warning-700 dark:text-warning-500">{{ $iuranKomite > 0 ? rupiah($iuranKomite) . '/tahun ajaran' : 'Belum diatur' }}</p>
            </div>
        </div>

        {{-- Tabel --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            <div class="mb-4 flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Rekap Kas &amp; Komite per Siswa</h3>
                    <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
                        Menampilkan {{ count($rows) }} siswa · kas lunas s/d bulan berjalan, komite per tahun ajaran
                    </p>
                </div>
                <div class="flex items-center gap-2">
                    <a
                        href="{{ route('transaksi.laporan.pdf') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-4 py-2.5 text-theme-sm font-semibold text-white shadow-theme-xs transition-colors hover:bg-brand-600 dark:bg-brand-500 dark:hover:bg-brand-600"
                    >
                        <svg class="shrink-0 fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M12 2a1 1 0 0 1 1 1v9.59l2.3-2.3a1 1 0 1 1 1.4 1.42l-4 4a1 1 0 0 1-1.4 0l-4-4A1 1 0 0 1 8.7 11.3L11 13.59V3a1 1 0 0 1 1-1ZM4 16a1 1 0 0 1 1 1v2a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1v-2a1 1 0 1 1 2 0v2a3 3 0 0 1-3 3H6a3 3 0 0 1-3-3v-2a1 1 0 0 1 1-1Z" />
                        </svg>
                        Export PDF
                    </a>
                    <a
                        href="{{ route('transaksi.laporan.xlsx') }}"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-success-500 px-4 py-2.5 text-theme-sm font-semibold text-white shadow-theme-xs transition-colors hover:bg-success-600 dark:bg-success-500 dark:hover:bg-success-600"
                    >
                        <svg class="shrink-0 fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M8 3a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h8a2 2 0 0 0 2-2V5a2 2 0 0 0-2-2H8Zm.5 2H13v6H6.5V5A.5.5 0 0 1 7 5h.5ZM17 5h-.5v6h-2V5H15v0A.5.5 0 0 1 15.5 5h.5V5Zm-7.25 10.5a.75.75 0 0 0-1.28.53v.94a.75.75 0 0 0 1.28.53l2.5-2.5a.75.75 0 0 0 0-1.06l-2.5-2.5a.75.75 0 0 0-1.28.53v.94a.75.75 0 0 0 1.28.53l.97-.97.97.97a.75.75 0 0 0 1.28-.53v-.94a.75.75 0 0 0-1.28-.53l-2.5 2.5a.75.75 0 0 0 0 1.06l2.5 2.5a.75.75 0 0 0 1.28-.53v-.94a.75.75 0 0 0-1.28-.53l-.97-.97-.97.97ZM7.5 14.5h.75a.75.75 0 0 0 0-1.5h-.75v-2a.75.75 0 0 1 1.5 0v2h.75a.75.75 0 0 1 0 1.5h-.75v2a.75.75 0 0 1-1.5 0v-2Z" />
                        </svg>
                        Export XLSX
                    </a>
                </div>
            </div>

            <div class="max-w-full overflow-x-auto custom-scrollbar">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-t border-gray-100 dark:border-gray-800">
                            <th class="py-3 text-start">
                                <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">No</p>
                            </th>
                            <th class="py-3 text-start">
                                <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Nama Siswa</p>
                            </th>
                            <th class="py-3 text-start">
                                <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Status Iuran Kas</p>
                            </th>
                            <th class="py-3 text-start">
                                <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Status Iuran Komite</p>
                            </th>
                            <th class="py-3 text-end">
                                <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Total Kurang</p>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($rows as $i => $row)
                            <tr class="border-t border-gray-100 transition-colors hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/[0.03]">
                                <td class="whitespace-nowrap py-3 pe-4 text-theme-sm text-gray-500 dark:text-gray-400">
                                    {{ $i + 1 }}
                                </td>
                                <td class="whitespace-nowrap py-3 pe-4">
                                    <span class="text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $row['nama'] }}</span>
                                    <span class="mt-0.5 block text-theme-xs {{ $row['status'] === 'aktif' ? 'text-success-600 dark:text-success-500' : 'text-gray-400 dark:text-gray-600' }}">
                                        {{ $row['status'] === 'aktif' ? 'Aktif' : 'Nonaktif' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap py-3 pe-8">
                                    @if ($row['kasStatus'] === 'Lunas')
                                        <span class="rounded-full bg-success-50 px-2.5 py-1 text-theme-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500">Lunas</span>
                                        @if ($row['kasSampai'])
                                            <span class="mt-1 block text-theme-xs text-gray-500 dark:text-gray-400">Lunas s/d {{ $row['kasSampai'] }}</span>
                                        @endif
                                    @elseif ($row['kasStatus'] === 'Belum Lunas')
                                        <span class="rounded-full bg-error-50 px-2.5 py-1 text-theme-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500">Belum Lunas</span>
                                        <span class="mt-1 block text-theme-xs font-medium text-error-600 dark:text-error-500">
                                            Kurang {{ $row['kasKurangBulan'] }} bulan
                                        </span>
                                        @if ($row['kasKurangDaftar'])
                                            <span class="mt-0.5 block text-theme-xs text-gray-500 dark:text-gray-400">{{ implode(', ', $row['kasKurangDaftar']) }}</span>
                                        @endif
                                        <span class="mt-0.5 block text-theme-xs font-medium text-error-600 dark:text-error-500">{{ rupiah($row['kasKurangRupiah']) }}</span>
                                    @else
                                        <span class="rounded-full bg-gray-100 px-2.5 py-1 text-theme-xs font-medium text-gray-600 dark:bg-white/[0.05] dark:text-gray-400">Belum Diatur</span>
                                        <span class="mt-1 block text-theme-xs text-gray-500 dark:text-gray-400">Iuran kas belum diatur</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap py-3 pe-8">
                                    @if ($row['komiteStatus'] === 'Lunas')
                                        <span class="rounded-full bg-success-50 px-2.5 py-1 text-theme-xs font-medium text-success-600 dark:bg-success-500/15 dark:text-success-500">Lunas</span>
                                        <span class="mt-1 block text-theme-xs text-gray-500 dark:text-gray-400">{{ rupiah($row['komitePaid']) }}</span>
                                    @elseif ($row['komiteStatus'] === 'Lebih')
                                        <span class="rounded-full bg-warning-50 px-2.5 py-1 text-theme-xs font-medium text-warning-600 dark:bg-warning-500/15 dark:text-warning-500">Lunas (Lebih)</span>
                                        <span class="mt-1 block text-theme-xs text-warning-600 dark:text-warning-500">Lebih {{ rupiah($row['komitePaid'] - $iuranKomite) }}</span>
                                    @elseif ($row['komiteStatus'] === 'Belum Lunas')
                                        <span class="rounded-full bg-error-50 px-2.5 py-1 text-theme-xs font-medium text-error-600 dark:bg-error-500/15 dark:text-error-500">Belum Lunas</span>
                                        <span class="mt-1 block text-theme-xs font-medium text-error-600 dark:text-error-500">
                                            Kurang {{ rupiah($row['komiteKurangRupiah']) }}
                                        </span>
                                        @if ($row['komitePaid'] > 0)
                                            <span class="mt-0.5 block text-theme-xs text-gray-500 dark:text-gray-400">Dibayar {{ rupiah($row['komitePaid']) }}</span>
                                        @endif
                                    @else
                                        <span class="rounded-full bg-gray-100 px-2.5 py-1 text-theme-xs font-medium text-gray-600 dark:bg-white/[0.05] dark:text-gray-400">Belum Diatur</span>
                                        <span class="mt-1 block text-theme-xs text-gray-500 dark:text-gray-400">Iuran komite belum diatur</span>
                                    @endif
                                </td>
                                <td class="whitespace-nowrap py-3 text-end">
                                    @if ($row['totalKurangRupiah'] > 0)
                                        <span class="text-theme-sm font-semibold text-error-600 dark:text-error-500">{{ rupiah($row['totalKurangRupiah']) }}</span>
                                    @else
                                        <span class="text-theme-sm font-semibold text-success-600 dark:text-success-500">Rp 0</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr class="border-t border-gray-100 dark:border-gray-800">
                                <td colspan="6" class="py-10 text-center">
                                    <p class="text-theme-sm text-gray-500 dark:text-gray-400">Belum ada data siswa.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    @if (count($rows) > 0)
                        <tfoot>
                            <tr class="border-t border-gray-200 bg-gray-50 dark:border-gray-800 dark:bg-white/[0.02]">
                                <td colspan="3" class="py-3 pe-4 text-theme-xs font-semibold text-gray-800 dark:text-white/90 sm:text-theme-sm">
                                    Total Kurang Kas
                                    <span class="block font-medium text-gray-500 dark:text-gray-400">
                                        {{ array_sum(array_column($rows, 'kasKurangBulan')) }} bulan ·
                                        {{ rupiah(array_sum(array_column($rows, 'kasKurangRupiah'))) }}
                                    </span>
                                </td>
                                <td class="py-3 pe-4 text-theme-xs font-semibold text-gray-800 dark:text-white/90 sm:text-theme-sm">
                                    Total Kurang Komite
                                    <span class="block font-medium text-gray-500 dark:text-gray-400">
                                        {{ rupiah(array_sum(array_column($rows, 'komiteKurangRupiah'))) }}
                                    </span>
                                </td>
                                <td class="py-3 text-end text-theme-xs font-semibold text-error-600 dark:text-error-500 sm:text-theme-sm">
                                    {{ rupiah(array_sum(array_column($rows, 'totalKurangRupiah'))) }}
                                </td>
                            </tr>
                        </tfoot>
                    @endif
                </table>
            </div>
        </div>
    </div>
@endsection