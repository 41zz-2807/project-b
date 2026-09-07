@php
    $namaBank = setting('nama_bank') ?: '—';
    $noRek = preg_replace('/\D/', '', (string) setting('no_rekening', ''));
    $noRekDisplay = $noRek !== '' ? trim(chunk_split($noRek, 4, ' ')) : '—';
    $namaPemilik = setting('nama_pemilik') ?: '—';
@endphp

<div
    class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-light-500 via-blue-light-600 to-blue-light-800 p-6 text-white shadow-theme-xl sm:p-8">
    <div class="pointer-events-none absolute -end-16 -top-16 h-48 w-48 rounded-full bg-white/10"></div>
    <div class="pointer-events-none absolute -bottom-20 -start-8 h-56 w-56 rounded-full bg-white/10"></div>

    <div class="relative">
        <div class="flex items-center gap-4">
            <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-white text-blue-light-700 shadow-theme-sm">
                <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor">
                    <path d="M2 7.5A2.5 2.5 0 0 1 4.5 5h15A2.5 2.5 0 0 1 22 7.5v9a2.5 2.5 0 0 1-2.5 2.5h-15A2.5 2.5 0 0 1 2 16.5v-9Zm2.5 4.25a.75.75 0 0 0-.75.75v4a.75.75 0 0 0 1.5 0v-4a.75.75 0 0 0-.75-.75Zm4 0a.75.75 0 0 0-.75.75v4a.75.75 0 0 0 1.5 0v-4a.75.75 0 0 0-.75-.75Zm4 0a.75.75 0 0 0-.75.75v4a.75.75 0 0 0 1.5 0v-4a.75.75 0 0 0-.75-.75Zm4 0a.75.75 0 0 0-.75.75v4a.75.75 0 0 0 1.5 0v-4a.75.75 0 0 0-.75-.75Zm4 0a.75.75 0 0 0-.75.75v4a.75.75 0 0 0 1.5 0v-4a.75.75 0 0 0-.75-.75ZM4.5 6.25A.75.75 0 0 0 3.75 7v.25h16.5V7a.75.75 0 0 0-.75-.75h-15Z" />
                </svg>
            </div>
            <div class="min-w-0">
                <p class="text-theme-xs font-medium uppercase tracking-wider text-white/70">Nama Bank</p>
                <p class="mt-1 truncate text-lg font-semibold text-white">{{ $namaBank }}</p>
            </div>
        </div>

        <div class="mt-5 border-t border-white/15 pt-4">
            <p class="text-theme-xs font-medium uppercase tracking-wider text-white/70">Nomor Rekening</p>
            <p class="mt-1 break-all font-mono text-xl font-semibold tracking-wider text-white sm:text-2xl">
                {{ $noRekDisplay }}
            </p>
            <p class="mt-2 truncate text-theme-sm text-white/80">Atas Nama: {{ $namaPemilik }}</p>

            @guest
                <a
                    href="{{ route('login') }}"
                    class="mt-4 inline-flex items-center gap-1.5 text-theme-sm font-medium text-white/80 transition-colors hover:text-white"
                >
                    <svg class="fill-current rtl:rotate-180" width="16" height="16" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M10.48 3.72a.75.75 0 0 1 1.06 0l5.25 5.25a.75.75 0 0 1 0 1.06l-5.25 5.25a.75.75 0 1 1-1.06-1.06l3.97-3.97H4a.75.75 0 0 1 0-1.5h10.44l-3.96-3.97a.75.75 0 0 1 0-1.06Z" />
                    </svg>
                    Masuk
                </a>
            @endguest
        </div>
    </div>
</div>