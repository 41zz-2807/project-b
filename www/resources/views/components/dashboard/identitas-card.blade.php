@php
    $namaSekolah = setting('nama_sekolah') ?: '—';
    $namaKelas = setting('nama_kelas') ?: '—';
@endphp

<div
    class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-light-500 via-blue-light-600 to-blue-light-800 p-6 text-white shadow-theme-xl sm:p-8">
    <div class="pointer-events-none absolute -end-16 -top-16 h-48 w-48 rounded-full bg-white/10"></div>
    <div class="pointer-events-none absolute -bottom-20 -start-8 h-56 w-56 rounded-full bg-white/10"></div>

    <div class="relative flex items-center gap-4">
        <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-xl bg-white text-blue-light-700 shadow-theme-sm">
            <svg width="30" height="30" viewBox="0 0 24 24" fill="currentColor">
                <path d="M12 2 2 8v1h20V8L12 2Z" />
                <path d="M4.5 19V10h3v9h-3Z" />
                <path d="M10.5 19V10h3v9h-3Z" />
                <path d="M16.5 19V10h3v9h-3Z" />
                <path d="M2 20h20v2H2v-2Z" />
            </svg>
        </div>
        <div class="min-w-0">
            <p class="text-theme-xs font-medium uppercase tracking-wider text-white/70">Identitas Sekolah</p>
            <p class="mt-1 truncate text-lg font-semibold text-white">{{ $namaSekolah }}</p>
            <p class="mt-1.5 inline-flex rounded-full bg-white/15 px-2.5 py-0.5 text-theme-xs font-medium text-white">
                Kelas {{ $namaKelas }}
            </p>
        </div>
    </div>
</div>