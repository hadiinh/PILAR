<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DataWargaController;
use App\Http\Controllers\FotoController;
use App\Http\Controllers\JadwalController;
use App\Http\Controllers\KegiatanController;
use App\Http\Controllers\KeluargaController;
use App\Http\Controllers\KeuanganController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\NotifikasiLogController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\PengajuanAkunController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\StatistikWargaController;
use App\Http\Controllers\WargaController;
use App\Http\Controllers\WilayahController;
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
    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:10,1');

    // Lupa kata sandi flow
    Route::get('/password/forgot',  [PasswordResetController::class, 'showForgotForm'])->name('password.forgot.show');
    Route::post('/password/forgot',  [PasswordResetController::class, 'requestOtp'])->middleware('throttle:5,1')->name('password.forgot.request');
    Route::get('/password/verify-otp',  [PasswordResetController::class, 'showVerifyForm'])->name('password.verify-otp.show');
    Route::post('/password/verify-otp',  [PasswordResetController::class, 'verifyOtp'])->middleware('throttle:10,1')->name('password.verify-otp.submit');
    Route::get('/password/reset',  [PasswordResetController::class, 'showResetForm'])->name('password.reset.show');
    Route::post('/password/reset',  [PasswordResetController::class, 'resetPassword'])->name('password.reset.submit');

    // Pengajuan akun (registrasi dilakukan via pengajuan)
    Route::get('/pengajuan-akun',  [PengajuanAkunController::class, 'create'])->name('pengajuan.create');
    Route::post('/pengajuan-akun', [PengajuanAkunController::class, 'store'])
        ->middleware('throttle:5,1')
        ->name('pengajuan.store');
});

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

// Force change password route (sebelum check_account_active karena user baru harus ganti password)
Route::middleware('auth')->group(function () {
    Route::get('/password/force-change',  [PasswordResetController::class, 'showForceChangeForm'])->name('password.force-change.show');
    Route::post('/password/force-change', [PasswordResetController::class, 'submitForceChange'])->name('password.force-change.submit');
});

Route::get('/', fn () => auth()->check() ? redirect('/beranda') : redirect('/login'));

/*
|--------------------------------------------------------------------------
| API Wilayah Indonesia (proxy + cache)
|--------------------------------------------------------------------------
*/
Route::prefix('api/wilayah')->name('wilayah.')->group(function () {
    Route::get('/provinsi',                [WilayahController::class, 'provinsi'])->name('provinsi');
    Route::get('/kota/{provinsiId}',       [WilayahController::class, 'kota'])->name('kota')->whereNumber('provinsiId');
    Route::get('/kecamatan/{kotaId}',      [WilayahController::class, 'kecamatan'])->name('kecamatan')->whereNumber('kotaId');
    Route::get('/kelurahan/{kecamatanId}', [WilayahController::class, 'kelurahan'])->name('kelurahan')->whereNumber('kecamatanId');
});

/*
|--------------------------------------------------------------------------
| Beranda + profile
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check_account_active', 'force_change_password'])->group(function () {

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

    Route::post('/profile/update',          [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::post('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.changePassword');
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
| Manager routes (pengurus/admin)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:ketua_rw,admin'])->group(function () {

    Route::resource('jadwal',   JadwalController::class)->except(['index', 'show']);
    Route::resource('foto',     FotoController::class)->except(['index', 'show']);
    Route::post('foto/bulk-delete', [FotoController::class, 'bulkDelete'])->name('foto.bulkDelete');
    Route::resource('keuangan', KeuanganController::class)->except(['index', 'show']);
    Route::resource('kegiatan', KegiatanController::class)->except(['index', 'show']);

    Route::get('/laporan/{laporan}/edit', [LaporanController::class, 'edit'])->name('laporan.edit');
    Route::put('/laporan/{laporan}',      [LaporanController::class, 'update'])->name('laporan.update');

    // Manajemen data warga
    Route::get('/data-warga',                  [DataWargaController::class, 'index'])->name('data-warga.index');
    Route::get('/data-warga/create',           [DataWargaController::class, 'create'])->name('data-warga.create');
    Route::post('/data-warga',                 [DataWargaController::class, 'store'])->name('data-warga.store');
    Route::get('/data-warga/{data_warga}/edit',[DataWargaController::class, 'edit'])->name('data-warga.edit');
    Route::put('/data-warga/{data_warga}',    [DataWargaController::class, 'update'])->name('data-warga.update');
    Route::delete('/data-warga/{data_warga}', [DataWargaController::class, 'destroy'])->name('data-warga.destroy');

    // Manajemen warga (akun)
    Route::get('/warga',                       [WargaController::class, 'index'])->name('warga.index');
    Route::get('/warga/create',                [WargaController::class, 'create'])->name('warga.create');
    Route::post('/warga',                      [WargaController::class, 'store'])->name('warga.store');
    Route::get('/warga/{warga}/edit',          [WargaController::class, 'edit'])->name('warga.edit');
    Route::put('/warga/{warga}',               [WargaController::class, 'update'])->name('warga.update');
    Route::delete('/warga/{warga}',            [WargaController::class, 'destroy'])->name('warga.destroy');
    Route::post('/warga/{warga}/deactivate',   [WargaController::class, 'deactivate'])->name('warga.deactivate');
    Route::post('/warga/{warga}/activate',     [WargaController::class, 'activate'])->name('warga.activate');
    Route::post('/warga/{warga}/reset-password', [WargaController::class, 'resetPassword'])->name('warga.resetPassword');

    // Keluarga
    Route::get('/keluarga',          [KeluargaController::class, 'index'])->name('keluarga.index');
    Route::get('/keluarga/{noKk}',   [KeluargaController::class, 'show'])->name('keluarga.show')->where('noKk', '[0-9]+');

    // Statistik warga
    Route::get('/statistik', [StatistikWargaController::class, 'index'])->name('statistik.index');

    // Pengajuan akun
    Route::get('/pengajuan',                       [PengajuanAkunController::class, 'index'])->name('pengajuan.index');
    Route::get('/pengajuan/{pengajuan}',           [PengajuanAkunController::class, 'show'])->name('pengajuan.show');
    Route::post('/pengajuan/{pengajuan}/approve',  [PengajuanAkunController::class, 'approve'])->name('pengajuan.approve');
    Route::post('/pengajuan/{pengajuan}/reject',   [PengajuanAkunController::class, 'reject'])->name('pengajuan.reject');

    // Log notifikasi
    Route::get('/notifikasi',                [NotifikasiLogController::class, 'index'])->name('notifikasi.index');
    Route::post('/notifikasi/{log}/retry',   [NotifikasiLogController::class, 'retry'])->name('notifikasi.retry');

    Route::get('/users', function () {
        return view('users.index', ['users' => User::orderBy('name')->get()]);
    })->name('users.index');
});

/*
|--------------------------------------------------------------------------
| Modul utama (warga lihat / buat)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'check_account_active'])->group(function () {

    Route::get('/jadwal',          [JadwalController::class, 'index'])->name('jadwal.index');
    Route::get('/jadwal/{jadwal}', [JadwalController::class, 'show'])->name('jadwal.show')->whereNumber('jadwal');

    Route::get('/kegiatan',            [KegiatanController::class, 'index'])->name('kegiatan.index');
    Route::get('/kegiatan/{kegiatan}', [KegiatanController::class, 'show'])->name('kegiatan.show')->whereNumber('kegiatan');

    Route::get('/foto',          [FotoController::class, 'index'])->name('foto.index');
    Route::get('/foto/{foto}',   [FotoController::class, 'show'])->name('foto.show')->whereNumber('foto');

    Route::get('/keuangan',      [KeuanganController::class, 'index'])->name('keuangan.index');
    Route::get('/keuangan/chart-data', [KeuanganController::class, 'chartData'])->name('keuangan.chart-data');

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
