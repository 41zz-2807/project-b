@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Pengaturan" />

    <div class="space-y-6">
        {{-- Flash messages --}}
        @if (session('status'))
            <div class="flex items-center gap-3 rounded-xl border border-success-200 bg-success-50 px-4 py-3 text-theme-sm font-medium text-success-700 dark:border-success-800 dark:bg-success-500/10 dark:text-success-500">
                <svg class="shrink-0 fill-current" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.7-9.3a1 1 0 0 0-1.4-1.4L9 10.6l-1.3-1.3a1 1 0 1 0-1.4 1.4l2 2a1 1 0 0 0 1.4 0l4-4Z" />
                </svg>
                {{ session('status') }}
            </div>
        @endif

        @if (session('error'))
            <div class="flex items-center gap-3 rounded-xl border border-error-200 bg-error-50 px-4 py-3 text-theme-sm font-medium text-error-700 dark:border-error-800 dark:bg-error-500/10 dark:text-error-500">
                <svg class="shrink-0 fill-current" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a1 1 0 0 1 1 1v4a1 1 0 1 1-2 0V6a1 1 0 0 1 1-1Zm0 9a1.25 1.25 0 1 0 0-2.5A1.25 1.25 0 0 0 10 14Z" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        {{-- Card Identitas Sekolah & Iuran --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Identitas Sekolah & Iuran</h3>
                <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
                    Informasi sekolah, kelas, dan besaran iuran kas maupun komite.
                </p>
            </div>

            <form method="POST" action="{{ route('pengaturan.update') }}">
                @csrf

                {{-- Nilai rekening ikut dikirim agar validasi tetap lolos --}}
                <input type="hidden" name="nama_bank" value="{{ old('nama_bank', $settings['nama_bank'] ?? '') }}" />
                <input type="hidden" name="no_rekening" value="{{ old('no_rekening', $settings['no_rekening'] ?? '') }}" />
                <input type="hidden" name="nama_pemilik" value="{{ old('nama_pemilik', $settings['nama_pemilik'] ?? '') }}" />
                <input type="hidden" name="telegram_report_enabled" value="{{ old('telegram_report_enabled', $settings['telegram_report_enabled'] ?? '0') }}" />
                <input type="hidden" name="telegram_report_jam" value="{{ old('telegram_report_jam', $settings['telegram_report_jam'] ?? '') }}" />
                @foreach (explode(',', $settings['telegram_report_hari'] ?? '') as $hari)
                    @if ($hari !== '')
                        <input type="hidden" name="telegram_report_hari[]" value="{{ $hari }}" />
                    @endif
                @endforeach

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="nama_sekolah" class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Nama Sekolah</label>
                        <input
                            type="text"
                            id="nama_sekolah"
                            name="nama_sekolah"
                            value="{{ old('nama_sekolah', $settings['nama_sekolah'] ?? '') }}"
                            placeholder="Masukkan nama sekolah"
                            class="mt-2 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        />
                        @error('nama_sekolah')
                            <p class="mt-1 text-theme-xs text-error-600 dark:text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nama_kelas" class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Nama Kelas</label>
                        <input
                            type="text"
                            id="nama_kelas"
                            name="nama_kelas"
                            value="{{ old('nama_kelas', $settings['nama_kelas'] ?? '') }}"
                            placeholder="Contoh: IX - A"
                            class="mt-2 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        />
                        @error('nama_kelas')
                            <p class="mt-1 text-theme-xs text-error-600 dark:text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="iuran_kas" class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Iuran Kas per Bulan</label>
                        <div class="relative mt-2">
                            <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-theme-sm text-gray-500 dark:text-gray-400">Rp</span>
                            <input
                                type="number"
                                step="500"
                                min="0"
                                id="iuran_kas"
                                name="iuran_kas"
                                value="{{ old('iuran_kas', $settings['iuran_kas'] ?? 0) }}"
                                placeholder="0"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent ps-12 pe-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                            />
                        </div>
                        @error('iuran_kas')
                            <p class="mt-1 text-theme-xs text-error-600 dark:text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="iuran_komite" class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Iuran Komite per Tahun</label>
                        <div class="relative mt-2">
                            <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-theme-sm text-gray-500 dark:text-gray-400">Rp</span>
                            <input
                                type="number"
                                step="500"
                                min="0"
                                id="iuran_komite"
                                name="iuran_komite"
                                value="{{ old('iuran_komite', $settings['iuran_komite'] ?? 0) }}"
                                placeholder="0"
                                class="h-11 w-full rounded-lg border border-gray-300 bg-transparent ps-12 pe-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                            />
                        </div>
                        @error('iuran_komite')
                            <p class="mt-1 text-theme-xs text-error-600 dark:text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="info_text" class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Informasi / Berita</label>
                        <textarea
                            id="info_text"
                            name="info_text"
                            rows="6"
                            placeholder="Teks informasi yang akan ditampilkan di halaman publik..."
                            class="mt-2 h-40 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        >{{ old('info_text', $settings['info_text'] ?? '') }}</textarea>
                        @error('info_text')
                            <p class="mt-1 text-theme-xs text-error-600 dark:text-error-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-theme-xs text-gray-500 dark:text-gray-400">Kosongkan untuk menggunakan teks default.</p>
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-5 py-3 text-theme-sm font-semibold text-white shadow-theme-xs transition-colors hover:bg-brand-600 dark:bg-brand-500 dark:hover:bg-brand-600"
                    >
                        <svg class="fill-current" width="17" height="17" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path fill-rule="evenodd" clip-rule="evenodd" d="M3 2.75A.75.75 0 0 0 2.25 3.5v13a.75.75 0 0 0 .75.75h14a.75.75 0 0 0 .75-.75V6.06a.75.75 0 0 0-.22-.53l-3.31-3.31a.75.75 0 0 0-.53-.22H3Zm6 2.5h3v2.5H9V5.25Zm1.5-3.5h3a1.5 1.5 0 0 1 1.06.44l2.25 2.25A1.5 1.5 0 0 1 17.25 5.5V17a1.5 1.5 0 0 1-1.5 1.5H4.25A1.5 1.5 0 0 1 2.75 17v-2.25H4a.75.75 0 1 0 0-1.5H2.75v-2H4a.75.75 0 1 0 0-1.5H2.75v-2H4a.75.75 0 1 0 0-1.5H2.75v-2H4a.75.75 0 1 0 0-1.5H2.75V3.5A.75.75 0 0 1 3.5 2.75h7Z" />
                        </svg>
                        Simpan Pengaturan
                    </button>
                </div>
            </form>
        </div>

        {{-- Card Rekening Bank --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Rekening Bank</h3>
                <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
                    Informasi rekening tujuan pembayaran iuran.
                </p>
            </div>

            <form method="POST" action="{{ route('pengaturan.update') }}">
                @csrf

                {{-- Nilai identitas & iuran ikut dikirim agar validasi tetap lolos --}}
                <input type="hidden" name="nama_sekolah" value="{{ old('nama_sekolah', $settings['nama_sekolah'] ?? '') }}" />
                <input type="hidden" name="nama_kelas" value="{{ old('nama_kelas', $settings['nama_kelas'] ?? '') }}" />
                <input type="hidden" name="iuran_kas" value="{{ old('iuran_kas', $settings['iuran_kas'] ?? 0) }}" />
                <input type="hidden" name="iuran_komite" value="{{ old('iuran_komite', $settings['iuran_komite'] ?? 0) }}" />
                <input type="hidden" name="telegram_report_enabled" value="{{ old('telegram_report_enabled', $settings['telegram_report_enabled'] ?? '0') }}" />
                <input type="hidden" name="telegram_report_jam" value="{{ old('telegram_report_jam', $settings['telegram_report_jam'] ?? '') }}" />
                @foreach (explode(',', $settings['telegram_report_hari'] ?? '') as $hari)
                    @if ($hari !== '')
                        <input type="hidden" name="telegram_report_hari[]" value="{{ $hari }}" />
                    @endif
                @endforeach

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="nama_bank" class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Nama Bank</label>
                        <input
                            type="text"
                            id="nama_bank"
                            name="nama_bank"
                            value="{{ old('nama_bank', $settings['nama_bank'] ?? '') }}"
                            placeholder="Contoh: BRI, BCA, Mandiri"
                            class="mt-2 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        />
                        @error('nama_bank')
                            <p class="mt-1 text-theme-xs text-error-600 dark:text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="no_rekening" class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Nomor Rekening</label>
                        <input
                            type="text"
                            id="no_rekening"
                            name="no_rekening"
                            value="{{ old('no_rekening', $settings['no_rekening'] ?? '') }}"
                            placeholder="Contoh: 1234567890"
                            class="mt-2 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        />
                        @error('no_rekening')
                            <p class="mt-1 text-theme-xs text-error-600 dark:text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="nama_pemilik" class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Nama Pemilik Rekening (Account Name)</label>
                        <input
                            type="text"
                            id="nama_pemilik"
                            name="nama_pemilik"
                            value="{{ old('nama_pemilik', $settings['nama_pemilik'] ?? '') }}"
                            placeholder="Nama pemilik rekening"
                            class="mt-2 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        />
                        @error('nama_pemilik')
                            <p class="mt-1 text-theme-xs text-error-600 dark:text-error-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-5 py-3 text-theme-sm font-semibold text-white shadow-theme-xs transition-colors hover:bg-brand-600 dark:bg-brand-500 dark:hover:bg-brand-600"
                    >
                        Simpan Rekening
                    </button>
                </div>
            </form>
        </div>

        {{-- Backup Database --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Laporan Telegram</h3>
                <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
                    Kirim laporan keuangan ringkas secara otomatis atau manual ke Telegram.
                </p>
            </div>

            <form method="POST" action="{{ route('pengaturan.update') }}">
                @csrf

                <input type="hidden" name="nama_sekolah" value="{{ old('nama_sekolah', $settings['nama_sekolah'] ?? '') }}" />
                <input type="hidden" name="nama_kelas" value="{{ old('nama_kelas', $settings['nama_kelas'] ?? '') }}" />
                <input type="hidden" name="iuran_kas" value="{{ old('iuran_kas', $settings['iuran_kas'] ?? 0) }}" />
                <input type="hidden" name="iuran_komite" value="{{ old('iuran_komite', $settings['iuran_komite'] ?? 0) }}" />
                <input type="hidden" name="nama_bank" value="{{ old('nama_bank', $settings['nama_bank'] ?? '') }}" />
                <input type="hidden" name="no_rekening" value="{{ old('no_rekening', $settings['no_rekening'] ?? '') }}" />
                <input type="hidden" name="nama_pemilik" value="{{ old('nama_pemilik', $settings['nama_pemilik'] ?? '') }}" />
                <input type="hidden" name="info_text" value="{{ old('info_text', $settings['info_text'] ?? '') }}" />

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Kirim Laporan Otomatis</label>
                        <div class="mt-2 flex items-center gap-3" x-data="{ enabled: {{ (old('telegram_report_enabled', $settings['telegram_report_enabled'] ?? '0') === '1') ? 'true' : 'false' }} }">
                            <label class="flex cursor-pointer items-center gap-3">
                                <div class="relative">
                                    <input type="hidden" name="telegram_report_enabled" :value="enabled ? '1' : '0'" />
                                    <input
                                        type="checkbox"
                                        class="sr-only"
                                        @change="enabled = !enabled"
                                        :checked="enabled"
                                    />
                                    <div class="block h-6 w-11 rounded-full"
                                        :class="enabled ? 'bg-brand-500 dark:bg-brand-500' : 'bg-gray-200 dark:bg-white/10'">
                                    </div>
                                    <div :class="enabled ? 'ltr:translate-x-full rtl:-translate-x-full' : 'translate-x-0'"
                                        class="shadow-theme-sm absolute top-0.5 ltr:left-0.5 rtl:right-0.5 h-5 w-5 rounded-full bg-white duration-300 ease-linear">
                                    </div>
                                </div>
                                <span class="text-theme-sm text-gray-700 dark:text-gray-300" x-text="enabled ? 'Aktif' : 'Nonaktif'"></span>
                            </label>
                        </div>
                        <p class="mt-1 text-theme-xs text-gray-500 dark:text-gray-400">Aktifkan untuk mengirim laporan otomatis sesuai jadwal.</p>
                    </div>

                    <div>
                        <label for="telegram_report_jam" class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Jam Pengiriman</label>
                        <input
                            type="time"
                            id="telegram_report_jam"
                            name="telegram_report_jam"
                            value="{{ old('telegram_report_jam', $settings['telegram_report_jam'] ?? '') }}"
                            class="mt-2 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        />
                        @error('telegram_report_jam')
                            <p class="mt-1 text-theme-xs text-error-600 dark:text-error-500">{{ $message }}</p>
                        @enderror
                        <p class="mt-1 text-theme-xs text-gray-500 dark:text-gray-400">Waktu pengiriman laporan harian (WIB).</p>
                    </div>
                </div>

                <div class="mt-5" x-data="{ hari: @js(collect(explode(',', $settings['telegram_report_hari'] ?? ''))->map(fn($v) => trim($v))->filter()->values()->all()) }">
                    <label class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Hari Pengiriman</label>
                    <p class="mt-1 text-theme-xs text-gray-500 dark:text-gray-400">Pilih hari laporan otomatis dikirim. Kosongkan untuk mengirim setiap hari.</p>
                    <div class="mt-3 flex flex-wrap gap-2">
                        @php
                            $daftarHari = [
                                'senin' => 'Senin',
                                'selasa' => 'Selasa',
                                'rabu' => 'Rabu',
                                'kamis' => 'Kamis',
                                'jumat' => 'Jumat',
                                'sabtu' => 'Sabtu',
                                'minggu' => 'Minggu',
                            ];
                        @endphp
                        @foreach ($daftarHari as $kode => $label)
                            <label class="inline-flex cursor-pointer items-center">
                                <input
                                    type="checkbox"
                                    class="sr-only"
                                    :checked="hari.includes('{{ $kode }}')"
                                    @change="hari.includes('{{ $kode }}') ? hari.splice(hari.indexOf('{{ $kode }}'), 1) : hari.push('{{ $kode }}')"
                                />
                                <span
                                    class="rounded-lg border px-4 py-2 text-theme-sm font-medium transition-colors"
                                    :class="hari.includes('{{ $kode }}')
                                        ? 'border-brand-500 bg-brand-500 text-white'
                                        : 'border-gray-300 bg-white text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-300 dark:hover:bg-white/[0.03]'"
                                >{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                    <template x-for="h in hari" :key="h">
                        <input type="hidden" name="telegram_report_hari[]" :value="h" />
                    </template>
                </div>

                <div class="mt-6 flex flex-col-reverse gap-3 sm:flex-row sm:items-center sm:justify-end">
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-5 py-3 text-theme-sm font-semibold text-white shadow-theme-xs transition-colors hover:bg-brand-600 dark:bg-brand-500 dark:hover:bg-brand-600"
                    >
                        Simpan Pengaturan Telegram
                    </button>
                </div>
            </form>

            <div class="mt-5 border-t border-gray-200 pt-5 dark:border-gray-800">
                <div class="flex flex-col gap-3 sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Kirim Laporan Manual</p>
                        <p class="mt-1 text-theme-xs text-gray-500 dark:text-gray-400">
                            Kirim laporan keuangan saat ini ke Telegram tanpa menunggu jadwal.
                        </p>
                    </div>
                    <form method="POST" action="{{ route('pengaturan.telegram.laporan') }}">
                        @csrf
                        <button
                            type="submit"
                            class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
                        >
                            <svg class="stroke-current fill-white dark:fill-gray-800" width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M22 2L11 13" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                                <path d="M22 2L15 22L11 13L2 9L22 2Z" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/>
                            </svg>
                            Kirim ke Telegram
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Backup Database --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Backup Database</h3>
                    <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
                        Unduh salinan database dalam format <code class="rounded bg-gray-100 px-1.5 py-0.5 text-theme-xs font-semibold dark:bg-gray-800">.dmp</code> untuk diarsipkan atau dipulihkan.
                    </p>
                </div>

                <a
                    href="{{ route('pengaturan.backup') }}"
                    class="inline-flex shrink-0 items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
                >
                    <svg class="stroke-current fill-white dark:fill-gray-800" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M10 2.75v8.75m0 0 3.5-3.5M10 11.5 6.5 8" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                        <path d="M3.5 12.75v2A1.75 1.75 0 0 0 5.25 16.5h9.5a1.75 1.75 0 0 0 1.75-1.75v-2" stroke="" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                    </svg>
                    Unduh Backup (.dmp)
                </a>
            </div>
        </div>
    </div>
@endsection