<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $alamat = [
            'provinsi_id'    => '32',
            'provinsi_nama'  => 'Jawa Barat',
            'kota_id'        => '3277',
            'kota_nama'      => 'Kota Cimahi',
            'kecamatan_id'   => '3277010',
            'kecamatan_nama' => 'Cimahi Selatan',
            'kelurahan_id'   => '3277010003',
            'kelurahan_nama' => 'Melong',
            'kode_pos'       => '40534',
            'rw'             => '016',
        ];

        $pwd = Hash::make('password');

        // ===== Pengurus =====
        $this->upsert([
            'nik'  => '3277010003000001',
            'name' => 'H. Bambang Sulistyo',
            'role' => 'ketua_rw',
            'no_hp'=> '081200000001',
            'no_kk'=> '3277010003000001',
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1968-04-12',
            'status_keluarga' => 'kepala_keluarga',
            'is_kepala_keluarga' => true,
            'rt' => '01', 'no_rumah' => '1',
            'alamat_detail' => 'Jl. Melong Asih Raya',
            'pekerjaan' => 'Wiraswasta',
            'password' => $pwd,
            'akun_aktif' => true,
        ] + $alamat);

        $this->upsert([
            'nik'  => '3277010003000002',
            'name' => 'Ratna Dewi',
            'role' => 'admin',
            'no_hp'=> '081200000002',
            'no_kk'=> '3277010003000002',
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => '1985-09-21',
            'status_keluarga' => 'kepala_keluarga',
            'is_kepala_keluarga' => true,
            'rt' => '02', 'no_rumah' => '2',
            'alamat_detail' => 'Jl. Melong Asih Raya',
            'pekerjaan' => 'PNS',
            'password' => $pwd,
            'akun_aktif' => true,
        ] + $alamat);

        // ===== Keluarga 1 — Budi Santoso (RT 03) =====
        $kk1 = '3277010003001001';
        $this->upsert([
            'nik'  => '3277010003001001',
            'name' => 'Budi Santoso',
            'role' => 'user',
            'no_hp'=> '081234560001',
            'no_kk'=> $kk1,
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1980-02-14',
            'status_keluarga' => 'kepala_keluarga',
            'is_kepala_keluarga' => true,
            'rt' => '03', 'no_rumah' => '12A',
            'alamat_detail' => 'Jl. Melong Asih Gg. Mawar',
            'pekerjaan' => 'Karyawan Swasta',
            'password' => $pwd,
        ] + $alamat);

        $this->upsert([
            'nik'  => '3277010003001002',
            'name' => 'Siti Aminah',
            'role' => 'user',
            'no_hp'=> '081234560002',
            'no_kk'=> $kk1,
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => '1983-07-30',
            'status_keluarga' => 'istri',
            'rt' => '03', 'no_rumah' => '12A',
            'alamat_detail' => 'Jl. Melong Asih Gg. Mawar',
            'pekerjaan' => 'Ibu Rumah Tangga',
            'password' => $pwd,
        ] + $alamat);

        $this->upsert([
            'nik'  => '3277010003001003',
            'name' => 'Andi Saputra',
            'role' => 'user',
            'no_hp'=> null,
            'no_kk'=> $kk1,
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => Carbon::now()->subYears(15)->format('Y-m-d'),
            'status_keluarga' => 'anak',
            'rt' => '03', 'no_rumah' => '12A',
            'alamat_detail' => 'Jl. Melong Asih Gg. Mawar',
            'pekerjaan' => 'Pelajar',
            'password' => $pwd,
            'akun_aktif' => false,
        ] + $alamat);

        $this->upsert([
            'nik'  => '3277010003001004',
            'name' => 'Rina Saputri',
            'role' => 'user',
            'no_hp'=> null,
            'no_kk'=> $kk1,
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => Carbon::now()->subYears(9)->format('Y-m-d'),
            'status_keluarga' => 'anak',
            'rt' => '03', 'no_rumah' => '12A',
            'alamat_detail' => 'Jl. Melong Asih Gg. Mawar',
            'pekerjaan' => 'Pelajar',
            'password' => $pwd,
            'akun_aktif' => false,
        ] + $alamat);

        // ===== Keluarga 2 — Joko Widodo (RT 04) =====
        $kk2 = '3277010003002001';
        $this->upsert([
            'nik'  => '3277010003002001',
            'name' => 'Joko Pramono',
            'role' => 'user',
            'no_hp'=> '081234560003',
            'no_kk'=> $kk2,
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1962-11-05',
            'status_keluarga' => 'kepala_keluarga',
            'is_kepala_keluarga' => true,
            'rt' => '04', 'no_rumah' => '8',
            'alamat_detail' => 'Jl. Melong Tengah',
            'pekerjaan' => 'Pensiunan',
            'password' => $pwd,
        ] + $alamat);

        $this->upsert([
            'nik'  => '3277010003002002',
            'name' => 'Iriana Suryani',
            'role' => 'user',
            'no_hp'=> '081234560004',
            'no_kk'=> $kk2,
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => '1965-06-18',
            'status_keluarga' => 'istri',
            'rt' => '04', 'no_rumah' => '8',
            'alamat_detail' => 'Jl. Melong Tengah',
            'pekerjaan' => 'Ibu Rumah Tangga',
            'password' => $pwd,
        ] + $alamat);

        // ===== Keluarga 3 — Hendro (RT 05, dengan pengajuan pending sebagai contoh) =====
        $kk3 = '3277010003003001';
        $this->upsert([
            'nik'  => '3277010003003001',
            'name' => 'Hendro Wibowo',
            'role' => 'user',
            'no_hp'=> '081234560005',
            'no_kk'=> $kk3,
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => '1990-03-22',
            'status_keluarga' => 'kepala_keluarga',
            'is_kepala_keluarga' => true,
            'rt' => '05', 'no_rumah' => '21',
            'alamat_detail' => 'Jl. Melong Permai',
            'pekerjaan' => 'Driver',
            'password' => $pwd,
            'akun_aktif' => false, // contoh: belum mengajukan akun
        ] + $alamat);

        $this->upsert([
            'nik'  => '3277010003003002',
            'name' => 'Bayu Pratama',
            'role' => 'user',
            'no_hp'=> null,
            'no_kk'=> $kk3,
            'jenis_kelamin' => 'L',
            'tanggal_lahir' => Carbon::now()->subYears(14)->format('Y-m-d'),
            'status_keluarga' => 'anak',
            'rt' => '05', 'no_rumah' => '21',
            'alamat_detail' => 'Jl. Melong Permai',
            'pekerjaan' => 'Pelajar',
            'password' => $pwd,
            'akun_aktif' => false,
        ] + $alamat);

        $this->upsert([
            'nik'  => '3277010003003003',
            'name' => 'Diah Larasati',
            'role' => 'user',
            'no_hp'=> null,
            'no_kk'=> $kk3,
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => Carbon::now()->subYears(5)->format('Y-m-d'),
            'status_keluarga' => 'anak',
            'rt' => '05', 'no_rumah' => '21',
            'alamat_detail' => 'Jl. Melong Permai',
            'pekerjaan' => '-',
            'password' => $pwd,
            'akun_aktif' => false,
        ] + $alamat);

        // ===== Lansia mandiri (RT 02) =====
        $kk4 = '3277010003004001';
        $this->upsert([
            'nik'  => '3277010003004001',
            'name' => 'Sumarni',
            'role' => 'user',
            'no_hp'=> '081234560006',
            'no_kk'=> $kk4,
            'jenis_kelamin' => 'P',
            'tanggal_lahir' => '1955-12-01',
            'status_keluarga' => 'kepala_keluarga',
            'is_kepala_keluarga' => true,
            'rt' => '02', 'no_rumah' => '5',
            'alamat_detail' => 'Jl. Melong Asih Gg. Anggrek',
            'pekerjaan' => 'Pensiunan',
            'password' => $pwd,
        ] + $alamat);
    }

    protected function upsert(array $data): void
    {
        // Default akun_aktif jika tidak diset
        if (!array_key_exists('akun_aktif', $data)) {
            $data['akun_aktif'] = true;
        }
        $data['status_warga']   = $data['status_warga']   ?? 'tetap';
        $data['notif_wa_aktif'] = $data['notif_wa_aktif'] ?? true;

        User::updateOrCreate(['nik' => $data['nik']], $data);
    }
}
