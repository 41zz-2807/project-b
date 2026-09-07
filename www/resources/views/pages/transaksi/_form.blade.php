@php
    $kategoris = \App\Models\Transaction::kategoriList($type);
    $label = $type === 'income' ? 'Pemasukan' : 'Pengeluaran';
    $siswaList ??= collect();
    $kategoriSiswa = ['Iuran Kas', 'Iuran Komite'];
    $defaultKategori = old('kategori', $transaction?->kategori ?? $kategoris[0] ?? '');
    $initialJumlah = old('jumlah', $transaction?->jumlah ?? 0);
    $initJumlahRaw = (string) (int) round((float) $initialJumlah);
    $initJumlahFormatted = ((float) $initialJumlah) > 0 ? number_format((float) $initialJumlah, 0, ',', '.') : '';
@endphp

<div class="rounded-2xl border border-gray-200 bg-white p-5 dark:border-gray-800 dark:bg-white/[0.03] sm:p-6">
    <div class="mb-6">
        <h3 class="text-lg font-semibold text-gray-800 dark:text-white/90">
            {{ $transaction ? 'Edit ' . $label : 'Catat ' . $label }}
        </h3>
        <p class="mt-1 text-theme-sm text-gray-500 dark:text-gray-400">
            {{ $transaction ? 'Perbarui data ' . strtolower($label) . ' di bawah ini.' : 'Isi detail ' . strtolower($label) . ' di bawah ini.' }}
        </p>
    </div>

    <form
        method="POST"
        action="{{ $transaction ? route('transaksi.update', $transaction) : route('transaksi.store') }}"
        enctype="multipart/form-data"
        x-data="{
            iuranKas: {{ (float) setting('iuran_kas', 0) }},
            iuranKomite: {{ (float) setting('iuran_komite', 0) }},
            kategori: '{{ $defaultKategori }}',
            jumlah: '{{ $initJumlahRaw }}',
            tampilJumlah: '{{ $initJumlahFormatted }}',
            tanggal: '{{ old('tanggal', $transaction?->tanggal?->format('Y-m-d') ?? now()->format('Y-m-d')) }}',
            metode: '{{ old('metode', $transaction?->metode ?? 'Tunai') }}',
            student_id: '{{ old('student_id', $transaction?->student_id ?? '') }}',
            posisiKasSiswa: @js($posisiKasSiswa ?? []),
            daftarFile: [],

            get nominal() {
                return parseFloat(String(this.jumlah).replace(/[^\d.-]/g, '')) || 0;
            },

            konversiRp(value) {
                return 'Rp ' + Number(value).toLocaleString('id-ID');
            },

            setJumlah(e) {
                const digits = e.target.value.replace(/\D/g, '').replace(/^0+(?=\d)/, '');
                this.jumlah = digits;
                this.tampilJumlah = digits ? Number(digits).toLocaleString('id-ID') : '';
            },

            parseTanggal(s) {
                if (!s) {
                    return null;
                }

                const parts = String(s).split('-');
                return new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, parseInt(parts[2], 10));
            },

            posisiAwalKas() {
                const posisi = (this.posisiKasSiswa || {})[this.student_id];

                if (posisi) {
                    const parts = String(posisi).split('-');
                    return new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, 1);
                }

                // Fallback: calculate from all existing transactions via API
                if (this.student_id) {
                    return this.posisiAwalKasFromServer();
                }

                const d = this.parseTanggal(this.tanggal) || new Date();
                const mulai = d.getMonth() + 1 >= 7 ? d.getFullYear() : d.getFullYear() - 1;

                return new Date(mulai, 6, 1);
            },

            async posisiAwalKasFromServer() {
                try {
                    const response = await fetch('/transaksi/posisi-kas?student_id=' + this.student_id);
                    const data = await response.json();
                    const posisi = data[this.student_id];
                    if (posisi) {
                        const parts = String(posisi).split('-');
                        return new Date(parseInt(parts[0], 10), parseInt(parts[1], 10) - 1, 1);
                    }
                } catch (e) {
                    // ignore error, fallback to tanggal
                }

                const d = this.parseTanggal(this.tanggal) || new Date();
                const mulai = d.getMonth() + 1 >= 7 ? d.getFullYear() : d.getFullYear() - 1;

                return new Date(mulai, 6, 1);
            },

            pilihFiles(e) {
                this.daftarFile = Array.from(e.target.files).map((f) => f.name);
            },

            hitungKas() {
                if (this.kategori !== 'Iuran Kas' || this.iuranKas <= 0 || this.nominal <= 0) {
                    return null;
                }

                const n = Math.floor(this.nominal / this.iuranKas);
                const nama = ['', 'Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
                const posisi = this.posisiAwalKas();

                const daftar = [];
                for (let i = 0; i < n; i++) {
                    daftar.push(nama[posisi.getMonth() + 1] + ' ' + posisi.getFullYear());
                    posisi.setMonth(posisi.getMonth() + 1);
                }

                return {
                    n,
                    daftar,
                    sisa: this.nominal - (this.iuranKas * n),
                };
            },

            hitungKomite() {
                if (this.kategori !== 'Iuran Komite' || this.iuranKomite <= 0 || this.nominal <= 0) {
                    return null;
                }

                if (this.nominal === this.iuranKomite) {
                    return { status: 'lunas', label: 'Lunas', selisih: 0 };
                }

                if (this.nominal > this.iuranKomite) {
                    const selisih = this.nominal - this.iuranKomite;
                    return { status: 'lebih', label: 'Lunas (kelebihan ' + this.konversiRp(selisih) + ')', selisih };
                }

                const selisih = this.iuranKomite - this.nominal;
                return { status: 'kurang', label: 'Belum lunas, kurang ' + this.konversiRp(selisih), selisih };
            },
        }"
    >
        @csrf
        @if ($transaction)
            @method('PUT')
        @endif

        <input type="hidden" name="type" value="{{ $type }}" />

        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <div>
                <label for="kategori" class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Kategori</label>
                <select
                    id="kategori"
                    name="kategori"
                    x-model="kategori"
                    class="mt-2 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"
                >
                    @foreach ($kategoris as $kategori)
                        <option value="{{ $kategori }}" @selected($defaultKategori === $kategori)>
                            {{ $kategori }}
                        </option>
                    @endforeach
                </select>
                @error('kategori')
                    <p class="mt-1 text-theme-xs text-error-600 dark:text-error-500">{{ $message }}</p>
                @enderror
            </div>

            @if ($type === 'income')
                <div x-show="kategori === 'Iuran Kas' || kategori === 'Iuran Komite'">
                    <label for="student_id" class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Pilih Siswa</label>
                    <select
                        id="student_id"
                        name="student_id"
                        x-model="student_id"
                        class="mt-2 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"
                    >
                        <option value="">-- Pilih Siswa --</option>
                        @foreach ($siswaList as $siswa)
                            <option value="{{ $siswa->id }}" @selected((string) old('student_id', $transaction?->student_id) === (string) $siswa->id)>
                                {{ $siswa->nama }}
                            </option>
                        @endforeach
                    </select>
                    @error('student_id')
                        <p class="mt-1 text-theme-xs text-error-600 dark:text-error-500">{{ $message }}</p>
                    @enderror
                    @if (!$siswaList->count())
                        <p class="mt-1 text-theme-xs text-warning-600 dark:text-warning-500">Belum ada siswa aktif. Tambahkan di menu Data Siswa.</p>
                    @endif
                </div>

                <div x-show="kategori === 'Sumbangan' || kategori === 'Lainnya'" x-cloak>
                    <label for="nama" class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Nama Pengirim / Donatur</label>
                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        value="{{ old('nama', $transaction?->nama ?? '') }}"
                        placeholder="Nama pemberi / penyumbang"
                        class="mt-2 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    />
                    @error('nama')
                        <p class="mt-1 text-theme-xs text-error-600 dark:text-error-500">{{ $message }}</p>
                    @enderror
                </div>
            @else
                <div>
                    <label for="nama" class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Nama Penerima / Toko</label>
                    <input
                        type="text"
                        id="nama"
                        name="nama"
                        value="{{ old('nama', $transaction?->nama ?? '') }}"
                        placeholder="Nama penerima / toko"
                        class="mt-2 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    />
                    @error('nama')
                        <p class="mt-1 text-theme-xs text-error-600 dark:text-error-500">{{ $message }}</p>
                    @enderror
                </div>
            @endif

            <div>
                <label for="metode" class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Metode Pembayaran</label>
                <select
                    id="metode"
                    name="metode"
                    x-model="metode"
                    class="mt-2 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"
                >
                    <option value="Tunai" @selected(old('metode', $transaction?->metode ?? 'Tunai') === 'Tunai')>Tunai</option>
                    <option value="Transfer" @selected(old('metode', $transaction?->metode ?? 'Tunai') === 'Transfer')>Transfer</option>
                </select>
                @error('metode')
                    <p class="mt-1 text-theme-xs text-error-600 dark:text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="jumlah" class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Jumlah</label>
                <div class="relative mt-2">
                    <span class="pointer-events-none absolute left-4 top-1/2 -translate-y-1/2 text-theme-sm text-gray-500 dark:text-gray-400">Rp</span>
                    <input
                        type="text"
                        inputmode="numeric"
                        autocomplete="off"
                        id="jumlah"
                        x-ref="inputJumlah"
                        :value="tampilJumlah"
                        @focus="if (parseFloat(this.jumlah) === 0) $refs.inputJumlah.select()"
                        @input="setJumlah($event)"
                        placeholder="0"
                        class="h-11 w-full rounded-lg border border-gray-300 bg-transparent ps-12 pe-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                    />
                    <input type="hidden" name="jumlah" :value="jumlah" />
                </div>
                @error('jumlah')
                    <p class="mt-1 text-theme-xs text-error-600 dark:text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="tanggal" class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Tanggal</label>
                <input
                    type="date"
                    id="tanggal"
                    name="tanggal"
                    x-model="tanggal"
                    value="{{ old('tanggal', $transaction?->tanggal?->format('Y-m-d') ?? now()->format('Y-m-d')) }}"
                    class="mt-2 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:focus:border-brand-800"
                />
                @error('tanggal')
                    <p class="mt-1 text-theme-xs text-error-600 dark:text-error-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="lg:col-span-3">
                <label for="keterangan" class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Keterangan</label>
                <input
                    type="text"
                    id="keterangan"
                    name="keterangan"
                    value="{{ old('keterangan', $transaction?->keterangan ?? '') }}"
                    placeholder="Opsional"
                    class="mt-2 h-11 w-full rounded-lg border border-gray-300 bg-transparent px-4 py-2.5 text-sm text-gray-800 shadow-theme-xs placeholder:text-gray-400 focus:border-brand-300 focus:outline-hidden focus:ring-3 focus:ring-brand-500/10 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 dark:focus:border-brand-800"
                />
                @error('keterangan')
                    <p class="mt-1 text-theme-xs text-error-600 dark:text-error-500">{{ $message }}</p>
                @enderror
            </div>
        </div>

        {{-- Upload Bukti --}}
        <div class="mt-5">
            <label class="text-theme-sm font-medium text-gray-800 dark:text-white/90">Bukti {{ $type === 'income' ? 'Pembayaran' : 'Transaksi' }}</label>
            <div
                class="mt-2 flex h-28 w-full cursor-pointer flex-col items-center justify-center gap-1.5 rounded-xl border-2 border-dashed border-gray-300 bg-gray-50 text-gray-500 transition-colors hover:border-brand-400 hover:bg-brand-50/50 dark:border-gray-700 dark:bg-white/[0.03] dark:text-gray-400 dark:hover:border-brand-500 dark:hover:bg-brand-500/5"
                @click="$refs.fileBukti.click()"
            >
                <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M12 16V4m0 0 4 4m-4-4-4 4M4 16v2a3 3 0 0 0 3 3h10a3 3 0 0 0 3-3v-2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
                <span class="text-theme-sm font-medium">Klik untuk pilih bukti</span>
                <span class="text-theme-xs">Format JPG, JPEG, PNG, atau PDF · bisa lebih dari satu file</span>
                <input
                    type="file"
                    x-ref="fileBukti"
                    name="files[]"
                    multiple
                    accept=".jpg,.jpeg,.png,.pdf"
                    @change="pilihFiles($event)"
                    class="hidden"
                />
            </div>

            <p x-show="metode === 'Transfer'" class="mt-1.5 text-theme-xs font-medium text-error-600 dark:text-error-500">
                Wajib unggah minimal 1 bukti untuk metode Transfer.
            </p>

            @error('files')
                <p class="mt-1.5 text-theme-xs text-error-600 dark:text-error-500">{{ $message }}</p>
            @enderror

            <template x-if="daftarFile.length">
                <ul class="mt-2 space-y-1">
                    <template x-for="(namaFile, idx) in daftarFile" :key="idx">
                        <li class="flex items-center gap-2 text-theme-sm text-gray-600 dark:text-gray-300">
                            <svg class="shrink-0 text-brand-500 dark:text-brand-400" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8l-5-5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                <path d="M14 3v5h5" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                            </svg>
                            <span x-text="namaFile"></span>
                        </li>
                    </template>
                </ul>
            </template>

            @if ($transaction && $transaction->files->count())
                <div class="mt-3 space-y-2">
                    <p class="text-theme-xs font-medium text-gray-500 dark:text-gray-400">Bukti tersimpan:</p>
                    @foreach ($transaction->files as $file)
                        <div class="flex items-center justify-between gap-3 rounded-lg border border-gray-200 px-3 py-2 dark:border-gray-700">
                            <a
                                href="{{ asset('storage/' . $file->file_path) }}"
                                target="_blank"
                                class="inline-flex min-w-0 items-center gap-2 text-theme-sm font-medium text-brand-600 hover:text-brand-700 dark:text-brand-400 dark:hover:text-brand-300"
                            >
                                <svg class="shrink-0" width="16" height="16" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M14 3H7a2 2 0 0 0-2 2v14a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V8l-5-5Z" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                    <path d="M14 3v5h5" stroke="currentColor" stroke-width="1.5" stroke-linejoin="round" />
                                </svg>
                                <span class="truncate">{{ $file->original_name ?: basename($file->file_path) }}</span>
                            </a>
                            <form method="POST" action="{{ route('transaksi.bukti.destroy', $file) }}" onsubmit="return confirm('Hapus bukti ini?')">
                                @csrf
                                @method('DELETE')
                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-1 rounded-lg px-2 py-1 text-theme-xs font-medium text-error-600 transition-colors hover:bg-error-50 dark:text-error-500 dark:hover:bg-error-500/10"
                                >
                                    Hapus
                                </button>
                            </form>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Preview iuran --}}
        <div
            x-show="kategori === 'Iuran Kas' || kategori === 'Iuran Komite'"
            x-cloak
            class="mt-5 rounded-xl border border-gray-200 bg-gray-50 p-4 dark:border-gray-700 dark:bg-white/[0.03]"
        >
            {{-- Iuran Kas --}}
            <div x-show="kategori === 'Iuran Kas'">
                <template x-if="hitungKas()">
                    <div>
                        <p x-show="hitungKas().n > 0" class="text-theme-sm font-medium text-gray-800 dark:text-white/90">
                            <span class="text-success-600 dark:text-success-500" x-text="hitungKas().n + ' bulan lunas'"></span>
                            <span x-show="hitungKas().daftar.length" x-text="': ' + hitungKas().daftar.join(', ')"></span>
                        </p>
                        <p x-show="hitungKas().n === 0" class="mt-1 text-theme-xs text-warning-600 dark:text-warning-500">
                            Nominal belum mencukupi 1 bulan iuran kas ({{ rupiah(setting('iuran_kas', 0)) }}/bulan).
                        </p>
                        <p x-show="hitungKas().n > 0 && hitungKas().sisa > 0" class="mt-1 text-theme-xs text-gray-500 dark:text-gray-400">
                            Sisa <span x-text="konversiRp(hitungKas().sisa)"></span> belum cukup menginjak bulan berikutnya.
                        </p>
                    </div>
                </template>
                <template x-if="!hitungKas()">
                    <p class="text-theme-sm text-gray-500 dark:text-gray-400">
                        Masukkan nominal untuk melihat bulan yang lunas. Pembayaran dilanjutkan dari bulan yang belum dibayar, mulai Juli tahun ajaran berjalan ({{ rupiah(setting('iuran_kas', 0)) }}/bulan).
                    </p>
                </template>
            </div>

            {{-- Iuran Komite --}}
            <div x-show="kategori === 'Iuran Komite'">
                <template x-if="hitungKomite()">
                    <p
                        class="text-theme-sm font-medium"
                        :class="{
                            'text-success-600 dark:text-success-500': hitungKomite().status === 'lunas',
                            'text-warning-600 dark:text-warning-500': hitungKomite().status === 'lebih',
                            'text-error-600 dark:text-error-500': hitungKomite().status === 'kurang',
                        }"
                        x-text="hitungKomite().label"
                    ></p>
                </template>
                <template x-if="!hitungKomite()">
                    <p class="text-theme-sm text-gray-500 dark:text-gray-400">
                        Masukkan nominal iuran komite ({{ rupiah(setting('iuran_komite', 0)) }}) untuk memeriksa status pelunasan.
                    </p>
                </template>
            </div>
        </div>

        <div class="mt-6 flex flex-wrap items-center justify-end gap-3">
            @if ($transaction)
                <a
                    href="{{ route($type === 'income' ? 'transaksi.pemasukan' : 'transaksi.pengeluaran') }}"
                    class="inline-flex items-center justify-center gap-2 rounded-lg border border-gray-300 bg-white px-5 py-3 text-theme-sm font-medium text-gray-700 shadow-theme-xs hover:bg-gray-50 hover:text-gray-800 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400 dark:hover:bg-white/[0.03] dark:hover:text-gray-200"
                >
                    Batal
                </a>
            @endif
            <button
                type="submit"
                class="inline-flex items-center justify-center gap-2 rounded-lg bg-brand-500 px-5 py-3 text-theme-sm font-semibold text-white shadow-theme-xs transition-colors hover:bg-brand-600 dark:bg-brand-500 dark:hover:bg-brand-600"
            >
                {{ $transaction ? 'Simpan Perubahan' : 'Simpan ' . $label }}
            </button>
        </div>
    </form>
</div>