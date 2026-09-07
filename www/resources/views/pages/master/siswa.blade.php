@extends('layouts.app')

@section('content')
    <x-common.page-breadcrumb pageTitle="Data Siswa" />

    <div class="space-y-6">
        @if (session('status'))
            <div class="flex items-center gap-3 rounded-xl border border-success-200 bg-success-50 px-4 py-3 text-theme-sm font-medium text-success-700 dark:border-success-800 dark:bg-success-500/10 dark:text-success-500">
                <svg class="shrink-0 fill-current" width="18" height="18" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" clip-rule="evenodd" d="M10 18a8 8 0 1 0 0-16 8 8 0 0 0 0 16Zm3.7-9.3a1 1 0 0 0-1.4-1.4L9 10.6l-1.3-1.3a1 1 0 1 0-1.4 1.4l2 2a1 1 0 0 0 1.4 0l4-4Z" />
                </svg>
                {{ session('status') }}
            </div>
        @endif

        {{-- Form Siswa --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
                    {{ $edit ? 'Edit Siswa' : 'Tambah Siswa' }}
                </h3>
                <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
                    {{ $edit ? 'Perbarui data siswa di bawah ini.' : 'Tambahkan siswa baru ke master data.' }}
                </p>
            </div>

            <form method="POST" action="{{ $edit ? route('siswa.update', $edit) : route('siswa.store') }}">
                @csrf
                @if ($edit)
                    @method('PUT')
                @endif

                <div class="grid grid-cols-1 gap-5 sm:grid-cols-2">
                    <div>
                        <label for="nama" class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Nama Siswa</label>
                        <input
                            type="text"
                            id="nama"
                            name="nama"
                            value="{{ old('nama', $edit?->nama ?? '') }}"
                            placeholder="Nama lengkap siswa"
                            class="mt-2 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                        />
                        @error('nama')
                            <p class="mt-1 text-theme-xs text-error-600 dark:text-error-500">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="status" class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Status</label>
                        <select
                            id="status"
                            name="status"
                            class="mt-2 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"
                        >
                            <option value="aktif" @selected(old('status', $edit?->status ?? 'aktif') === 'aktif')>Aktif</option>
                            <option value="nonaktif" @selected(old('status', $edit?->status ?? 'aktif') === 'nonaktif')>Non-Aktif</option>
                        </select>
                        @error('status')
                            <p class="mt-1 text-theme-xs text-error-600 dark:text-error-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mt-6 flex flex-wrap items-center justify-end gap-3">
                    @if ($edit)
                        <a
                            href="{{ route('siswa.index') }}"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
                        >
                            Batal
                        </a>
                    @endif
                    <button
                        type="submit"
                        class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-5 py-3 text-theme-sm font-semibold text-white shadow-theme-xs transition-colors hover:bg-brand-600 dark:bg-brand-500 dark:hover:bg-brand-600"
                    >
                        {{ $edit ? 'Simpan Perubahan' : 'Tambah Siswa' }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Upload Daftar Siswa --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            <div class="mb-4">
                <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Upload Daftar Siswa</h3>
                <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
                    Unggah file <span class="font-medium text-gray-700 dark:text-gray-300">.txt</span> / <span class="font-medium text-gray-700 dark:text-gray-300">.csv</span> berisi daftar nama siswa. Satu nama per baris. Nama yang sudah ada akan diperbarui statusnya.
                </p>
                <p class="mt-2 text-theme-xs text-gray-500 dark:text-gray-400">
                    Format: <span class="font-medium text-gray-700 dark:text-gray-300">Budi Santoso</span> (status Aktif) atau
                    <span class="font-medium text-gray-700 dark:text-gray-300">Budi Santoso, nonaktif</span> / <span class="font-medium text-gray-700 dark:text-gray-300">Budi Santoso;aktif</span>.
                </p>
            </div>

            <form method="POST" action="{{ route('siswa.import') }}" enctype="multipart/form-data" class="flex flex-col gap-3 sm:flex-row sm:items-end">
                @csrf
                <div class="w-full sm:max-w-md">
                    <input
                        type="file"
                        id="file"
                        name="file"
                        accept=".txt,.csv,.tsv,text/plain,text/csv"
                        class="block w-full cursor-pointer text-sm text-gray-600 file:me-3 file:h-11 file:cursor-pointer file:rounded-lg file:border-0 file:bg-gray-100 file:px-4 file:text-sm file:font-medium file:text-gray-700 hover:file:bg-gray-200 dark:text-gray-400 dark:file:bg-white/[0.05] dark:file:text-gray-300 dark:hover:file:bg-white/10"
                    />
                    @error('file')
                        <p class="mt-1 text-theme-xs text-error-600 dark:text-error-500">{{ $message }}</p>
                    @enderror
                </div>
                <button
                    type="submit"
                    class="inline-flex h-11 items-center justify-center gap-2 rounded-lg bg-brand-500 px-5 text-theme-sm font-semibold text-white shadow-theme-xs transition-colors hover:bg-brand-600 dark:bg-brand-500 dark:hover:bg-brand-600"
                >
                    <svg class="shrink-0 fill-current" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M11.47 2.47a.75.75 0 0 1 1.06 0l4 4a.75.75 0 1 1-1.06 1.06l-2.72-2.72V15a.75.75 0 0 1-1.5 0V4.81L8.53 7.53a.75.75 0 0 1-1.06-1.06l4-4ZM4 15.5a.75.75 0 0 1 .75.75V19.5c0 .414.336.75.75.75h13c.414 0 .75-.336.75-.75v-3.25a.75.75 0 0 1 1.5 0V19.5a2.25 2.25 0 0 1-2.25 2.25h-13A2.25 2.25 0 0 1 2.98 19.5v-3.25A.75.75 0 0 1 3.73 15.5Z" />
                    </svg>
                    Upload
                </button>
            </form>
        </div>

        {{-- Daftar Siswa --}}
        <div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
            <div class="mb-4 flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">Daftar Siswa</h3>
                    <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
                        Total {{ $students->total() }} siswa
                    </p>
                </div>

                <form method="GET" action="{{ route('siswa.index') }}" class="flex items-center gap-2">
                    <div class="relative">
                        <input
                            type="text"
                            name="cari"
                            value="{{ $search }}"
                            placeholder="Cari nama..."
                            class="h-11 w-full rounded-lg border border-gray-300 bg-transparent ps-4 pe-10 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800 sm:w-64"
                        />
                        <button
                            type="submit"
                            class="absolute end-2 top-1/2 -translate-y-1/2 inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 hover:text-brand-600 dark:text-gray-400 dark:hover:text-brand-400"
                            title="Cari"
                        >
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M21 21l-4.35-4.35M17 10.5a6.5 6.5 0 1 1-13 0 6.5 6.5 0 0 1 13 0Z" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                        </button>
                    </div>
                    @if ($search)
                        <a
                            href="{{ route('siswa.index') }}"
                            class="inline-flex h-11 items-center justify-center rounded-lg border border-gray-300 bg-white px-4 text-theme-sm font-medium text-gray-700 hover:bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03]"
                        >
                            Reset
                        </a>
                    @endif
                </form>
            </div>

            <div class="max-w-full overflow-x-auto custom-scrollbar">
                <table class="min-w-full">
                    <thead>
                        <tr class="border-t border-gray-100 dark:border-gray-800">
                            <th class="py-3 text-start">
                                <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">No</p>
                            </th>
                            <th class="py-3 text-start">
                                <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Nama</p>
                            </th>
                            <th class="py-3 text-start">
                                <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Status</p>
                            </th>
                            <th class="py-3 text-end">
                                <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Aksi</p>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($students as $student)
                            <tr class="border-t border-gray-100 transition-colors hover:bg-gray-50 dark:border-gray-800 dark:hover:bg-white/[0.03]">
                                <td class="whitespace-nowrap py-3 pe-4 text-theme-sm text-gray-500 dark:text-gray-400">
                                    {{ $students->firstItem() + $loop->index }}
                                </td>
                                <td class="whitespace-nowrap py-3 pe-4">
                                    <p class="text-theme-sm font-medium text-gray-800 dark:text-white/90">{{ $student->nama }}</p>
                                </td>
                                <td class="whitespace-nowrap py-3 pe-4">
                                    <span class="rounded-full px-2.5 py-1 text-theme-xs font-medium {{ $student->status === 'aktif' ? 'bg-success-50 text-success-600 dark:bg-success-500/15 dark:text-success-500' : 'bg-gray-100 text-gray-600 dark:bg-white/[0.05] dark:text-gray-400' }}">
                                        {{ $student->status === 'aktif' ? 'Aktif' : 'Non-Aktif' }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap py-3 text-end">
                                    <div class="flex items-center justify-end gap-1">
                                        <a
                                            href="{{ route('siswa.index', ['edit' => $student->id, 'cari' => $search]) }}"
                                            class="inline-flex h-8 w-8 items-center justify-center rounded-lg text-gray-500 transition-colors hover:bg-gray-100 hover:text-brand-600 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-brand-400"
                                            title="Edit"
                                        >
                                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                                <path d="M16.862 4.487 19.5 7.125l-8.5 8.5-3.5.5.5-3.5 8.5-8.5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                                <path d="M15.5 5.9 18.1 8.5" stroke="currentColor" stroke-width="1.5" />
                                            </svg>
                                        </a>
                                        <form method="POST" action="{{ route('siswa.destroy', $student) }}" onsubmit="return confirm('Hapus siswa ini?')">
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
                                <td colspan="4" class="py-10 text-center">
                                    <p class="text-theme-sm text-gray-500 dark:text-gray-400">Belum ada data siswa.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-4">
                {{ $students->links() }}
            </div>
        </div>
    </div>
@endsection