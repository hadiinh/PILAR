<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SmokeTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // Seed minimal user untuk testing
        $this->seed(\Database\Seeders\UserSeeder::class);
    }

    public function test_critical_authed_routes_render(): void
    {
        $user = User::where('role', 'ketua_rw')->first();
        $this->assertNotNull($user, 'Seeder gagal: tidak ada user ketua_rw');

        $routes = [
            '/beranda',
            '/dashboard',
            '/warga',
            '/warga/create',
            '/keluarga',
            '/statistik',
            '/pengajuan',
            '/notifikasi',
            '/keuangan',
            '/jadwal',
            '/laporan',
            '/foto',
        ];

        foreach ($routes as $url) {
            $response = $this->actingAs($user)->get($url);
            $this->assertContains(
                $response->status(),
                [200, 302],
                "Route {$url} returned HTTP {$response->status()}: " . substr($response->getContent() ?? '', 0, 300)
            );
        }
    }

    public function test_public_pages_render_without_auth(): void
    {
        foreach (['/login', '/pengajuan-akun'] as $url) {
            $response = $this->get($url);
            $this->assertEquals(200, $response->status(), "Route {$url} returned {$response->status()}");
        }
    }
}
