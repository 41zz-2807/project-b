#!/usr/bin/env bash
#
# scripts/create-user.sh
# Membuat pengguna baru untuk aplikasi bendahara.
#
# Contoh pemakaian:
#   bash scripts/create-user.sh
#   bash scripts/create-user.sh "Nama Pengguna" "nama@email.com" "password123"
#
# Catatan: bila aplikasi berjalan di dalam container Docker (project-bendahara-app),
# skrip otomatis mengeksekusi perintah di dalam container tersebut.
set -euo pipefail

NAME="${1:-}"
EMAIL="${2:-}"
PASSWORD="${3:-}"

ARGS=()
[[ -n "$NAME" ]] && ARGS+=("$NAME")
[[ -n "$EMAIL" ]] && ARGS+=("$EMAIL")
[[ -n "$PASSWORD" ]] && ARGS+=("$PASSWORD")

if command -v docker >/dev/null 2>&1 \
    && docker ps --format '{{.Names}}' 2>/dev/null | grep -qx 'project-bendahara-app'; then
    echo "Mengeksekusi artisan user:create pada container project-bendahara-app ..."
    exec docker exec -it project-bendahara-app php artisan user:create "${ARGS[@]}"
fi

echo "Mengeksekusi artisan user:create secara lokal ..."
exec php artisan user:create "${ARGS[@]}"