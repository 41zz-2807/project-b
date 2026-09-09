<?php

namespace App\Http\Controllers;

use App\Exports\LaporanSiswaExport;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\TransactionFile;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Maatwebsite\Excel\Facades\Excel;

class TransactionController extends Controller
{
    protected const KATEGORI_SISWA = ['Iuran Kas', 'Iuran Komite'];

    public function pemasukan(Request $request)
    {
        $editId = $request->integer('edit') ?: null;

        return view('pages.transaksi.pemasukan', [
            'type' => 'income',
            'transaction' => $editId ? Transaction::find($editId) : null,
            'siswaList' => $this->siswaList(),
            'posisiKasSiswa' => $this->posisiKasSiswaList($editId),
            'transaksis' => Transaction::income()->orderByDesc('tanggal')->orderByDesc('id')->limit(10)->get(),
            'total' => Transaction::income()->sum('jumlah'),
        ]);
    }

    public function laporan(Request $request)
    {
        return view('pages.transaksi.laporan', $this->dataLaporan());
    }

    public function laporanPdf(Request $request)
    {
        $pdf = Pdf::loadView('pages.transaksi.laporan_pdf', $this->dataLaporan())
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-kas-komite-'.now()->format('Y-m-d').'.pdf');
    }

    public function laporanPdfPublik(Request $request)
    {
        $pdf = Pdf::loadView('pages.transaksi.laporan_pdf', $this->dataLaporan())
            ->setPaper('a4', 'landscape');

        return $pdf->download('laporan-kas-komite-'.now()->format('Y-m-d').'.pdf');
    }

    public function laporanXlsx(Request $request)
    {
        $data = $this->dataLaporan();

        return Excel::download(
            new LaporanSiswaExport($data['rows'], $data['namaSekolah'], $data['namaKelas']),
            'laporan-kas-komite-'.now()->format('Y-m-d').'.xlsx'
        );
    }

    protected function dataLaporan(): array
    {
        $rows = $this->buildLaporanPerSiswa();

        return [
            'rows' => $rows,
            'iuranKas' => (float) setting('iuran_kas', 0),
            'iuranKomite' => (float) setting('iuran_komite', 0),
            'namaSekolah' => (string) setting('nama_sekolah', ''),
            'namaKelas' => (string) setting('nama_kelas', ''),
        ];
    }

    protected function buildLaporanPerSiswa(): array
    {
        $kas = (float) setting('iuran_kas', 0);
        $komite = (float) setting('iuran_komite', 0);

        $awalAjaran = CarbonImmutable::create(tahunAjaranTahun(now()), 7, 1);
        $bulanBerjalan = CarbonImmutable::instance(now()->startOfMonth());

        $rows = [];

        foreach (Student::orderBy('nama')->get() as $siswa) {
            $posisi = posisiKasSiswa($siswa->id);

            $kasStatus = 'info';
            $kasKurangBulan = 0;
            $kasKurangDaftar = [];
            $kasSampai = null;

            if ($kas > 0) {
                if ($posisi === null) {
                    if ($bulanBerjalan->gte($awalAjaran)) {
                        $kasStatus = 'Belum Lunas';
                        $kasKurangBulan = (int) $awalAjaran->diffInMonths($bulanBerjalan) + 1;
                        $kasKurangDaftar = $this->bulanBerurutan($awalAjaran, $bulanBerjalan);
                    } else {
                        $kasStatus = 'info';
                    }
                } else {
                    $posisiDate = CarbonImmutable::parse($posisi.'-01');
                    $lunasSampai = $posisiDate->subMonth();
                    $kasSampai = namaBulanSingkat($lunasSampai->month).' '.$lunasSampai->year;

                    if ($lunasSampai->gte($bulanBerjalan)) {
                        $kasStatus = 'Lunas';
                    } else {
                        $kasStatus = 'Belum Lunas';
                        $kasKurangBulan = (int) $posisiDate->diffInMonths($bulanBerjalan) + 1;
                        $kasKurangDaftar = $this->bulanBerurutan($posisiDate, $bulanBerjalan);
                    }
                }
            }

            $komitePaid = (float) Transaction::income()
                ->where('kategori', 'Iuran Komite')
                ->where('student_id', $siswa->id)
                ->where('tanggal', '>=', $awalAjaran->toDateString())
                ->sum('jumlah');

            $komiteStatus = 'info';
            $komiteKurangRupiah = 0;

            if ($komite > 0) {
                if ($komitePaid >= $komite) {
                    $komiteStatus = $komitePaid > $komite ? 'Lebih' : 'Lunas';
                } else {
                    $komiteStatus = 'Belum Lunas';
                    $komiteKurangRupiah = (int) round($komite - $komitePaid);
                }
            }

            $kasKurangRupiah = (int) round($kasKurangBulan * $kas);

            $rows[] = [
                'nama' => $siswa->nama,
                'status' => $siswa->status,
                'kasStatus' => $kasStatus,
                'kasKurangBulan' => $kasKurangBulan,
                'kasKurangRupiah' => $kasKurangRupiah,
                'kasKurangDaftar' => $kasKurangDaftar,
                'kasSampai' => $kasSampai,
                'komiteStatus' => $komiteStatus,
                'komitePaid' => $komitePaid,
                'komiteKurangRupiah' => $komiteKurangRupiah,
                'totalKurangRupiah' => $kasKurangRupiah + $komiteKurangRupiah,
            ];
        }

        return $rows;
    }

    protected function bulanBerurutan(CarbonImmutable $dari, CarbonImmutable $sampai): array
    {
        $daftar = [];
        $m = $dari->copy();
        $batas = $sampai->copy();

        while ($m->lte($batas)) {
            $daftar[] = namaBulanSingkat($m->month).' '.$m->year;
            $m = $m->addMonth();
        }

        return $daftar;
    }

    public function posisiKasSiswa(Request $request)
    {
        $studentId = $request->integer('student_id');

        $posisi = \App\Helpers\posisiKasSiswa($studentId);

        return response()->json($posisi ? [(int) $studentId => $posisi] : []);
    }

    public function pengeluaran(Request $request)
    {
        $editId = $request->integer('edit') ?: null;

        return view('pages.transaksi.pengeluaran', [
            'type' => 'expense',
            'transaction' => $editId ? Transaction::find($editId) : null,
            'siswaList' => [],
            'transaksis' => Transaction::expense()->orderByDesc('tanggal')->orderByDesc('id')->limit(10)->get(),
            'total' => Transaction::expense()->sum('jumlah'),
        ]);
    }

    public function riwayat(Request $request)
    {
        $bulan = $this->sanitizeBulan($request->input('bulan'));
        $jenis = in_array($request->input('jenis'), ['income', 'expense'], true) ? $request->input('jenis') : null;
        $kategori = (string) $request->input('kategori', '');

        $query = Transaction::query()
            ->when($bulan, function ($q) use ($bulan) {
                $start = CarbonImmutable::parse($bulan.'-01')->startOfMonth();
                $q->whereBetween('tanggal', [$start->toDateString(), $start->endOfMonth()->toDateString()]);
            })
            ->when($jenis, fn ($q) => $q->where('type', $jenis))
            ->when($kategori !== '', fn ($q) => $q->where('kategori', $kategori))
            ->orderByDesc('tanggal')
            ->orderByDesc('id');

        $transaksis = $query->paginate(15)->withQueryString();

        return view('pages.transaksi.riwayat', [
            'transaksis' => $transaksis,
            'bulan' => $bulan,
            'jenis' => $jenis,
            'kategori' => $kategori,
            'bulanOptions' => $this->bulanOptions(),
            'kategoriOptions' => array_unique(array_merge(Transaction::KATEGORI_INCOME, Transaction::KATEGORI_EXPENSE)),
            'totalPemasukan' => Transaction::income()->sum('jumlah'),
            'totalPengeluaran' => Transaction::expense()->sum('jumlah'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        $files = $validated['files'] ?? [];
        unset($validated['files']);

        $transaction = Transaction::create($validated);

        $this->simpanBukti($transaction, $files);

        $route = $transaction->type === 'income' ? 'transaksi.pemasukan' : 'transaksi.pengeluaran';

        return redirect()->route($route)
            ->with('status', ($transaction->type === 'income' ? 'Pemasukan' : 'Pengeluaran').' berhasil dicatat.');
    }

    public function update(Request $request, Transaction $transaction)
    {
        $validated = $this->validateData($request, $transaction);

        $files = $validated['files'] ?? [];
        unset($validated['files']);

        $transaction->update($validated);

        $this->simpanBukti($transaction, $files);

        $route = $transaction->type === 'income' ? 'transaksi.pemasukan' : 'transaksi.pengeluaran';

        return redirect()->route($route)
            ->with('status', 'Transaksi berhasil diperbarui.');
    }

    public function destroy(Transaction $transaction)
    {
        foreach ($transaction->files as $file) {
            Storage::disk('public')->delete($file->file_path);
        }

        $transaction->delete();

        return redirect()->back()
            ->with('status', 'Transaksi berhasil dihapus.');
    }

    public function hapusBukti(TransactionFile $transactionFile)
    {
        Storage::disk('public')->delete($transactionFile->file_path);
        $transactionFile->delete();

        return redirect()->back()
            ->with('status', 'Bukti pembayaran berhasil dihapus.');
    }

    protected function siswaList()
    {
        return Student::aktif()->orderBy('nama')->get();
    }

    protected function posisiKasSiswaList(?int $excludeTransactionId = null): array
    {
        $map = [];

        foreach ($this->siswaList() as $siswa) {
            $map[(int) $siswa->id] = posisiKasSiswa($siswa->id, $excludeTransactionId);
        }

        return $map;
    }

    protected function simpanBukti(Transaction $transaction, array $files): void
    {
        foreach ($files as $file) {
            TransactionFile::create([
                'transaction_id' => $transaction->id,
                'file_path' => $file->store('bukti', 'public'),
                'original_name' => $file->getClientOriginalName(),
            ]);
        }
    }

    protected function validateData(Request $request, ?Transaction $transaction = null): array
    {
        $isIncome = $request->input('type') === 'income';
        $kategori = (string) $request->input('kategori', '');

        $rules = [
            'type' => ['required', 'in:income,expense'],
            'kategori' => ['required', 'string', 'max:100'],
            'jumlah' => ['required', 'numeric', 'min:0'],
            'tanggal' => ['required', 'date'],
            'metode' => ['required', 'in:Tunai,Transfer'],
            'files' => ['array'],
            'files.*' => ['file', 'mimes:jpg,jpeg,png,pdf', 'max:5120'],
            'keterangan' => ['nullable', 'string', 'max:255'],
        ];

        if ($isIncome && in_array($kategori, self::KATEGORI_SISWA, true)) {
            $rules['student_id'] = ['required', 'integer', 'exists:students,id'];
            $rules['nama'] = ['nullable', 'string', 'max:100'];
        } else {
            $rules['student_id'] = ['nullable'];
            $rules['nama'] = ['required', 'string', 'max:100'];
        }

        if ($request->input('metode') === 'Transfer' && ! ($transaction && $transaction->files->count() > 0)) {
            $rules['files'] = ['required', 'array', 'min:1'];
        }

        $messages = [
            'metode.required' => 'Metode pembayaran wajib dipilih.',
            'student_id.required' => 'Pilih siswa terlebih dahulu.',
            'student_id.exists' => 'Siswa yang dipilih tidak valid.',
            'nama.required' => 'Nama wajib diisi.',
            'files.required' => 'Bukti pembayaran wajib diunggah untuk metode transfer.',
            'files.min' => 'Minimal unggah 1 file bukti.',
            'files.*.mimes' => 'Format bukti harus JPG, JPEG, PNG, atau PDF.',
            'files.*.max' => 'Ukuran file bukti maksimal 5 MB.',
        ];

        return $request->validate($rules, $messages);
    }

    protected function sanitizeBulan($bulan): ?string
    {
        if (preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', (string) $bulan)) {
            return (string) $bulan;
        }

        return null;
    }

    protected function bulanOptions(): array
    {
        return bulanOptionsTahunAjaran();
    }
}
