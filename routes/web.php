<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\FotoController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\ProfileController;
use App\Models\Foto;
use App\Models\Jadwal;
use App\Models\Kegiatan;
use App\Models\Keuangan;
use App\Models\Laporan;
use App\Models\User;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public / Auth routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login',     [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login',    [AuthController::class, 'login']);
    Route::get('/register',  [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('/', fn () => auth()->check() ? redirect('/beranda') : redirect('/login'));

/*
|--------------------------------------------------------------------------
| Beranda + profile
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/beranda', function () {
        $masuk  = (int) Keuangan::where('tipe', 'masuk')->sum('jumlah');
        $keluar = (int) Keuangan::where('tipe', 'keluar')->sum('jumlah');

        return view('beranda', [
            'stats' => [
                'total_user'     => User::count(),
                'total_kegiatan' => Kegiatan::count(),
                'total_jadwal'   => Jadwal::where('tanggal', '>=', now()->toDateString())->count(),
                'laporan_aktif'  => Laporan::whereIn('status', ['baru', 'diproses'])->count(),
                'saldo_kas'      => $masuk - $keluar,
            ],
            'recent_kegiatan' => Kegiatan::latest()->take(5)->get(),
            'recent_fotos'    => Foto::latest()->take(8)->get(),
        ]);
    })->name('beranda');

    Route::post('/profile/update', [ProfileController::class, 'updateProfile'])->name('profile.update');
});

/*
|--------------------------------------------------------------------------
| Dashboard (pengurus + admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:ketua_rw,admin'])
    ->get('/dashboard', [DashboardController::class, 'index'])
    ->name('dashboard');

/*
|--------------------------------------------------------------------------
| Manager routes (pengurus/admin) — daftarkan SEBELUM rute /{id}
| untuk menghindari konflik dengan parameter route binding.
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:ketua_rw,admin'])->group(function () {

    Route::resource('jadwal',   JadwalController::class)->except(['index', 'show']);
    Route::resource('foto',     FotoController::class)->except(['index', 'show']);
    Route::post('foto/bulk-delete', [FotoController::class, 'bulkDelete'])->name('foto.bulkDelete');
    Route::resource('keuangan', KeuanganController::class)->except(['index', 'show']);

    Route::get('/kegiatan/{kegiatan}/edit', [KegiatanController::class, 'edit'])->name('kegiatan.edit');
    Route::put('/kegiatan/{kegiatan}',      [KegiatanController::class, 'update'])->name('kegiatan.update');
    Route::delete('/kegiatan/{kegiatan}',   [KegiatanController::class, 'destroy'])->name('kegiatan.destroy');

    Route::get('/laporan/{laporan}/edit',   [LaporanController::class, 'edit'])->name('laporan.edit');
    Route::put('/laporan/{laporan}',        [LaporanController::class, 'update'])->name('laporan.update');

    Route::get('/users', function () {
        return view('users.index', ['users' => User::orderBy('name')->get()]);
    })->name('users.index');
});

/*
|--------------------------------------------------------------------------
| Modul utama (warga lihat / buat)
| Constraint `where(... , '[0-9]+')` mencegah /create tertangkap show.
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {

    Route::get('/jadwal',          [JadwalController::class, 'index'])->name('jadwal.index');
    Route::get('/jadwal/{jadwal}', [JadwalController::class, 'show'])->name('jadwal.show')->whereNumber('jadwal');

    Route::get('/kegiatan',                  [KegiatanController::class, 'index'])->name('kegiatan.index');
    Route::get('/kegiatan/create',           [KegiatanController::class, 'create'])->name('kegiatan.create');
    Route::post('/kegiatan',                 [KegiatanController::class, 'store'])->name('kegiatan.store');
    Route::get('/kegiatan/{kegiatan}',       [KegiatanController::class, 'show'])->name('kegiatan.show')->whereNumber('kegiatan');

    Route::get('/foto',          [FotoController::class, 'index'])->name('foto.index');
    Route::get('/foto/{foto}',   [FotoController::class, 'show'])->name('foto.show')->whereNumber('foto');

    Route::get('/keuangan',      [KeuanganController::class, 'index'])->name('keuangan.index');

    Route::get('/laporan',                  [LaporanController::class, 'index'])->name('laporan.index');
    Route::get('/laporan/create',           [LaporanController::class, 'create'])->name('laporan.create');
    Route::post('/laporan',                 [LaporanController::class, 'store'])->name('laporan.store');
    Route::get('/laporan/{laporan}',        [LaporanController::class, 'show'])->name('laporan.show')->whereNumber('laporan');
});

/*
|--------------------------------------------------------------------------
| Catch-all
|--------------------------------------------------------------------------
*/
Route::fallback(fn () => auth()->check() ? redirect('/beranda') : redirect('/login'));
