<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\View\View;

class HelpController extends Controller
{
    public function index(): View
    {
        $faqs = [
            ['q' => 'Apa itu Sistem Rekber / Escrow GameVault?', 'a' => 'Dana pembeli ditahan aman oleh GameVault sampai akun berhasil di-handover dan dikonfirmasi. Seller baru menerima uang setelah kedua belah pihak beres.'],
            ['q' => 'Bagaimana cara handover akun di Live Room?', 'a' => 'Setelah pembayaran terverifikasi, kedua pihak masuk ruang escrow live. Seller menyerahkan email & password, buyer mengganti email/password, lalu konfirmasi.'],
            ['q' => 'Berapa biaya layanan GameVault?', 'a' => 'Biaya escrow 5% dari transaksi dikali nilai terbesar, maksimum Rp 1.000.000 per transaksi. Transaksi pakai Saldo Rekber bebas biaya layanan.'],
            ['q' => 'Bagaimana jika akun di-hack/lock setelah transaksi (hackback)?', 'a' => 'Saldo escrow menahan dana 7 hari. Jika terjadi hackback, buyer melapor ke dispute dan dana otomatis dikembalikan 100%.'],
            ['q' => 'Bagaimana cara menjadi penjual terverifikasi?', 'a' => "Isi formulir 'Jadi Penjual', lengkapi syarat & ketentuan, lalu tunggu review tim verifikasi. Biasanya selesai dalam 1x24 jam."],
            ['q' => 'Metode pembayaran apa saja yang didukung?', 'a' => 'QRIS Realtime, Virtual Account (BCA), GoPay, OVO, DANA, ShopeePay, dan Saldo Rekber.'],
        ];

        return view('help.index', compact('faqs'));
    }
}
