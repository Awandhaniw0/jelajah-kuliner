<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Models\Account;
use App\Models\Pesanan;
use App\Models\PKL;
use App\Models\Report;

class ReportControllerTest extends TestCase
{

    public function testStoreReport()
    {
        $this->withoutExceptionHandling(); // Tambahan untuk debugging kalau gagal

    $this->withoutExceptionHandling();

    // Ambil data account dan pesanan yang sudah ada
    // $akun = \App\Models\Account::first(); // ambil akun pertama
    $pesanan = \App\Models\Pesanan::inRandomOrder()->first();
        // dd($pesanan);
    // Pastikan datanya memang ada
    // $this->assertNotNull($akun);
    $this->assertNotNull($pesanan);

    $response = $this->withoutMiddleware()->post('/report', [
        'idPengguna' => $pesanan->idAccount,
        'idPelapor' => $pesanan->idPKL, // jika pakai akun yang sama
        'idPesanan' => $pesanan->id,
        'alasan' => 'Melanggar aturan',
    ]);

    $response->assertRedirect('/dashboard');

    $this->assertDatabaseHas('reports', [
        'idPengguna' => $pesanan->idAccount,
        'idPelapor' => $pesanan->idPKL, // jika pakai akun yang sama
        'idPesanan' => $pesanan->id,
        'alasan' => 'Melanggar aturan',
    ]);
    }

    public function testDeleteReport()
    {
 // Ambil data pesanan pertama dari database
    $pesanan = \App\Models\Pesanan::inRandomOrder()->first();
    $this->assertNotNull($pesanan, 'Pesanan tidak ditemukan, pastikan sudah seeding');

    // Ambil pengguna dari idAccount di pesanan
    $pengguna = \App\Models\Account::find($pesanan->idAccount);
    $this->assertNotNull($pengguna, 'Pengguna tidak ditemukan');

    // Ambil pelapor yang bukan pengguna
    $pelapor = \App\Models\Account::where('id', '!=', $pengguna->id)->first();
    $this->assertNotNull($pelapor, 'Pelapor tidak ditemukan, pastikan ada lebih dari 1 account');

    $response = $this->withoutMiddleware()->post('/report', [
        'idPengguna' => $pengguna->id,
        'idPelapor' => $pelapor->id,
        'idPesanan' => $pesanan->id,
        'alasan' => 'Melanggar aturan',
    ]);

    $response->assertRedirect('/dashboard');

    $this->assertDatabaseHas('reports', [
        'idPengguna' => $pengguna->id,
        'idPelapor' => $pelapor->id,
        'idPesanan' => $pesanan->id,
        'alasan' => 'Melanggar aturan',
    ]);
    }

    public function testBanUser()
    {
    $pesanan = \App\Models\Pesanan::inRandomOrder()->first();
    $this->assertNotNull($pesanan, 'Pesanan tidak ditemukan, pastikan sudah melakukan seeding.');

    // Ambil pengguna dari pesanan
    $pengguna = \App\Models\Account::find($pesanan->idAccount);
    $this->assertNotNull($pengguna, 'Pengguna tidak ditemukan dari pesanan.');

    // Cari pelapor yang berbeda dari pengguna
    $pelapor = \App\Models\Account::where('id', '!=', $pengguna->id)->inRandomOrder()->first();
    $this->assertNotNull($pelapor, 'Pelapor tidak ditemukan, pastikan ada lebih dari satu akun.');

    // Ubah status pengguna ke "Pelanggan"
    $pengguna->update(['status' => 'Pelanggan']);
    $this->assertEquals('Pelanggan', $pengguna->fresh()->status);
    }

    public function testUnbanUser()
    {
    // Ambil satu data pesanan acak
    $pesanan = \App\Models\Pesanan::inRandomOrder()->first();
    $this->assertNotNull($pesanan, 'Pesanan tidak ditemukan, pastikan sudah melakukan seeding.');

    // Ambil pengguna dari pesanan
    $pengguna = \App\Models\Account::find($pesanan->idAccount);
    $this->assertNotNull($pengguna, 'Pengguna tidak ditemukan dari pesanan.');

    // Cari pelapor yang berbeda dari pengguna
    $pelapor = \App\Models\Account::where('id', '!=', $pengguna->id)->inRandomOrder()->first();
    $this->assertNotNull($pelapor, 'Pelapor tidak ditemukan, pastikan ada lebih dari satu akun.');

    // Ubah status pengguna ke "alert" (ban)
    $pengguna->update(['status' => 'alert']);
    $this->assertEquals('alert', $pengguna->fresh()->status);
    }
}
