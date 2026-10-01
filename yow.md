analisis sistem notifikasi WhatsApp pada project Laravel saya. Saat ini notifikasi pengajuan akun berhasil terkirim ke WhatsApp, tetapi notifikasi untuk jadwal baru, kegiatan baru, keuangan, dan laporan warga tidak masuk ke WhatsApp meskipun log Laravel menampilkan [Fonnte] Pesan terkirim dan broadcast selesai.

Yang saya inginkan:

Telusuri alur pengiriman WA dari Controller → NotifikasiService → FonnteService.
Temukan penyebab mengapa hanya notifikasi akun yang benar-benar terkirim.
Periksa apakah ada perbedaan payload, queue, event, query user, atau pemanggilan service.
Tambahkan logging yang diperlukan untuk mengetahui di tahap mana pengiriman gagal.
Berikan perbaikan kode yang diperlukan tanpa mengubah fitur yang sudah berjalan.
Tampilkan file yang diubah beserta alasan setiap perubahan.