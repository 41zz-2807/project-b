<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Process;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::pluck('value', 'key');

        return view('pages.settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'nama_sekolah' => ['required', 'string', 'max:255'],
            'nama_kelas' => ['required', 'string', 'max:255'],
            'iuran_kas' => ['required', 'numeric', 'min:0'],
            'iuran_komite' => ['required', 'numeric', 'min:0'],
            'nama_bank' => ['nullable', 'string', 'max:255'],
            'no_rekening' => ['nullable', 'string', 'max:50'],
            'nama_pemilik' => ['nullable', 'string', 'max:255'],
            'running_text' => ['nullable', 'string', 'max:2000'],
        ]);

        $map = [
            'nama_sekolah' => $validated['nama_sekolah'],
            'nama_kelas' => $validated['nama_kelas'],
            'iuran_kas' => (string) $validated['iuran_kas'],
            'iuran_komite' => (string) $validated['iuran_komite'],
            'nama_bank' => $validated['nama_bank'] ?? '',
            'no_rekening' => $validated['no_rekening'] ?? '',
            'nama_pemilik' => $validated['nama_pemilik'] ?? '',
            'running_text' => $validated['running_text'] ?? '',
        ];

        foreach ($map as $key => $value) {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        return back()->with('status', 'Pengaturan berhasil disimpan.');
    }

    public function backup()
    {
        $connection = config('database.connections.pgsql');

        $filename = $connection['database'] . '_' . now()->format('Ymd_His') . '.dmp';
        $dumpPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . $filename;

        $command = 'PGPASSWORD=' . escapeshellarg($connection['password'])
            . ' pg_dump'
            . ' --host=' . escapeshellarg($connection['host'])
            . ' --port=' . escapeshellarg($connection['port'])
            . ' --username=' . escapeshellarg($connection['username'])
            . ' --dbname=' . escapeshellarg($connection['database'])
            . ' --format=custom'
            . ' --file=' . escapeshellarg($dumpPath)
            . ' 2>&1';

        $result = Process::timeout(180)->run($command);

        if ($result->failed() || ! file_exists($dumpPath)) {
            $message = trim($result->output() ?: $result->errorOutput());

            return back()->with('error', 'Backup gagal. ' . ($message ?: 'Perintah pg_dump tidak dapat dijalankan.'));
        }

        return response()->download(
            $dumpPath,
            $filename,
            [
                'Content-Type' => 'application/octet-stream',
                'Content-Description' => 'File Transfer',
            ]
        )->deleteFileAfterSend(true);
    }
}