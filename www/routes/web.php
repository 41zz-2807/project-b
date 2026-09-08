<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\TransactionController;

// autentikasi
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// halaman publik (tanpa autentikasi)
Route::get('/publik', [\App\Http\Controllers\PublikController::class, 'index'])->name('publik.index');
Route::get('/', function () {
    return redirect()->route('login');
});
Route::middleware('auth')->group(function () {

    // dashboard pages
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // master data
    Route::get('/siswa', [StudentController::class, 'index'])->name('siswa.index');
    Route::post('/siswa', [StudentController::class, 'store'])->name('siswa.store');
    Route::post('/siswa/import', [StudentController::class, 'import'])->name('siswa.import');
    Route::put('/siswa/{student}', [StudentController::class, 'update'])->name('siswa.update');
    Route::delete('/siswa/{student}', [StudentController::class, 'destroy'])->name('siswa.destroy');

    // transaksi
    Route::get('/transaksi/pemasukan', [TransactionController::class, 'pemasukan'])->name('transaksi.pemasukan');
    Route::get('/transaksi/posisi-kas', [TransactionController::class, 'posisiKasSiswa'])->name('transaksi.posisi-kas');
    Route::get('/transaksi/laporan', [TransactionController::class, 'laporan'])->name('transaksi.laporan');
    Route::get('/transaksi/laporan/pdf', [TransactionController::class, 'laporanPdf'])->name('transaksi.laporan.pdf');
    Route::get('/transaksi/laporan/xlsx', [TransactionController::class, 'laporanXlsx'])->name('transaksi.laporan.xlsx');
    Route::get('/transaksi/pengeluaran', [TransactionController::class, 'pengeluaran'])->name('transaksi.pengeluaran');
    Route::get('/transaksi/riwayat', [TransactionController::class, 'riwayat'])->name('transaksi.riwayat');
    Route::post('/transaksi', [TransactionController::class, 'store'])->name('transaksi.store');
    Route::put('/transaksi/{transaction}', [TransactionController::class, 'update'])->name('transaksi.update');
    Route::delete('/transaksi/{transaction}', [TransactionController::class, 'destroy'])->name('transaksi.destroy');
    Route::delete('/transaksi/bukti/{transactionFile}', [TransactionController::class, 'hapusBukti'])->name('transaksi.bukti.destroy');

    // pengaturan
    Route::get('/pengaturan', [SettingController::class, 'index'])->name('pengaturan.index');
    Route::post('/pengaturan', [SettingController::class, 'update'])->name('pengaturan.update');
    Route::get('/pengaturan/backup', [SettingController::class, 'backup'])->name('pengaturan.backup');

    // calender pages
    Route::get('/calendar', function () {
        return view('pages.calender', ['title' => 'Calendar']);
    })->name('calendar');

    // form pages
    Route::get('/form-elements', function () {
        return view('pages.form.form-elements', ['title' => 'Form Elements']);
    })->name('form-elements');

    // tables pages
    Route::get('/basic-tables', function () {
        return view('pages.tables.basic-tables', ['title' => 'Basic Tables']);
    })->name('basic-tables');

    // pages

    Route::get('/blank', function () {
        return view('pages.blank', ['title' => 'Blank']);
    })->name('blank');

    // error pages
    Route::get('/error-404', function () {
        return view('pages.errors.error-404', ['title' => 'Error 404']);
    })->name('error-404');

    // chart pages
    Route::get('/line-chart', function () {
        return view('pages.chart.line-chart', ['title' => 'Line Chart']);
    })->name('line-chart');

    Route::get('/bar-chart', function () {
        return view('pages.chart.bar-chart', ['title' => 'Bar Chart']);
    })->name('bar-chart');

    // ui elements pages
    Route::get('/alerts', function () {
        return view('pages.ui-elements.alerts', ['title' => 'Alerts']);
    })->name('alerts');

    Route::get('/avatars', function () {
        return view('pages.ui-elements.avatars', ['title' => 'Avatars']);
    })->name('avatars');

    Route::get('/badge', function () {
        return view('pages.ui-elements.badges', ['title' => 'Badges']);
    })->name('badges');

    Route::get('/buttons', function () {
        return view('pages.ui-elements.buttons', ['title' => 'Buttons']);
    })->name('buttons');

    Route::get('/image', function () {
        return view('pages.ui-elements.images', ['title' => 'Images']);
    })->name('images');

    Route::get('/videos', function () {
        return view('pages.ui-elements.videos', ['title' => 'Videos']);
    })->name('videos');
});

// diag (sementara)
Route::get('/diag', function (\Illuminate\Http\Request $request) {
    \Illuminate\Support\Facades\File::append(storage_path('logs/diag.log'), (string) $request->query('d', '') . "\n");

    return 'ok';
});
Route::get('/slow', function () {
    usleep(25000000);

    return 'x';
});
