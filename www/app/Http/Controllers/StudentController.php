<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('cari');

        $students = Student::query()
            ->when($search, fn ($query) => $query->where('nama', 'ilike', "%{$search}%"))
            ->orderByDesc('id')
            ->paginate(15)
            ->withQueryString();

        $editId = $request->integer('edit') ?: null;
        $edit = $editId ? Student::find($editId) : null;

        return view('pages.master.siswa', compact('students', 'search', 'edit'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateData($request);

        Student::create($validated);

        return redirect()->route('siswa.index')
            ->with('status', 'Siswa berhasil ditambahkan.');
    }

    public function update(Request $request, Student $student)
    {
        $validated = $this->validateData($request);

        $student->update($validated);

        return redirect()->route('siswa.index')
            ->with('status', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('siswa.index')
            ->with('status', 'Siswa berhasil dihapus.');
    }

    protected function validateData(Request $request): array
    {
        return $request->validate([
            'nama' => ['required', 'string', 'max:255'],
            'status' => ['required', 'in:aktif,nonaktif'],
        ]);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:txt,csv,tsv', 'max:2048'],
        ]);

        $content = (string) file_get_contents($request->file('file')->getRealPath());
        $content = preg_replace('/^\xEF\xBB\xBF/', '', $content);
        $lines = preg_split('/\R/', $content);

        $created = 0;
        $updated = 0;
        $skipped = 0;
        $isFirst = true;

        foreach ($lines as $line) {
            $line = trim($line);

            if ($line === '') {
                continue;
            }

            [$nama, $status] = $this->parseBarisSiswa($line);

            if ($nama === null) {
                $skipped++;

                continue;
            }

            if ($isFirst && in_array(mb_strtolower($nama), ['nama', 'nama siswa'], true)) {
                $isFirst = false;

                continue;
            }
            $isFirst = false;

            $student = Student::where('nama', $nama)->first();

            if ($student) {
                if ($student->status === $status) {
                    $skipped++;
                } else {
                    $student->update(['status' => $status]);
                    $updated++;
                }
            } else {
                Student::create(['nama' => $nama, 'status' => $status]);
                $created++;
            }
        }

        $message = "Import selesai: {$created} siswa baru ditambahkan, {$updated} status diperbarui, {$skipped} baris dilewati (duplikat/tidak valid).";

        return redirect()->route('siswa.index')->with('status', $message);
    }

    protected function parseBarisSiswa(string $line): array
    {
        $parts = array_map('trim', str_contains($line, ';') ? explode(';', $line) : explode(',', $line));

        $nama = $parts[0] ?? '';
        $status = isset($parts[1]) ? mb_strtolower($parts[1]) : '';

        if ($nama === '' || mb_strlen($nama) > 255) {
            return [null, null];
        }

        $status = in_array($status, ['aktif', 'nonaktif'], true) ? $status : 'aktif';

        return [$nama, $status];
    }
}
