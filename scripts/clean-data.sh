#!/usr/bin/env bash
#
# scripts/clean-data.sh
# Membersihkan data untuk production: transaksi + bukti pembayaran + rekap kas &
# komite + laporan + siswa (praktis reset data transaksi & siswa; saldo kembali nol).
# Pengaturan dan akun pengguna TIDAK ikut terhapus.
#
# Contoh pemakaian:
#   bash scripts/clean-data.sh          # konfirmasi interaktif
#   bash scripts/clean-data.sh --force  # langsung hapus tanpa konfirmasi
#
# Catatan: bila aplikasi berjalan di dalam container Docker (project-bendahara-app),
# skrip otomatis mengeksekusi perintah di dalam container tersebut.
set -euo pipefail

echo "Menghapus seluruh data transaksi, bukti pembayaran, rekap kas & komite, laporan, dan siswa (saldo kembali nol) ..."

if command -v docker >/dev/null 2>&1 \
    && docker ps --format '{{.Names}}' 2>/dev/null | grep -qx 'project-bendahara-app'; then
    echo "Mengeksekusi artisan clean:transaksi pada container project-bendahara-app ..."
    TTY_FLAG=""
    if [[ -t 0 ]] && [[ -t 1 ]]; then
        TTY_FLAG="-t"
    fi
    exec docker exec -i $TTY_FLAG project-bendahara-app php artisan clean:transaksi "$@"
fi

echo "Mengeksekusi artisan clean:transaksi secara lokal ..."
exec php artisan clean:transaksi "$@"