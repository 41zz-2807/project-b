<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Laporan Kas &amp; Komite</title>
    <style>
        * { box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #111827; margin: 0; padding: 24px; }
        h1 { font-size: 16px; margin: 0 0 2px; }
        .subtitle { font-size: 11px; color: #4b5563; margin: 0 0 2px; }
        .meta { font-size: 10px; color: #6b7280; margin-bottom: 16px; }
        table { width: 100%; border-collapse: collapse; margin-top: 8px; }
        th, td { border: 1px solid #d1d5db; padding: 5px 6px; text-align: left; vertical-align: top; }
        th { background: #e5e7eb; font-weight: bold; text-align: center; }
        td.num, th.num { text-align: right; white-space: nowrap; }
        td.center, th.center { text-align: center; }
        .status-lunas { color: #047857; font-weight: bold; }
        .status-kurang { color: #b91c1c; font-weight: bold; }
        .status-info { color: #6b7280; }
        tfoot td { background: #f3f4f6; font-weight: bold; }
        .footer { margin-top: 24px; font-size: 10px; color: #6b7280; text-align: right; }
    </style>
</head>
<body>
    <h1>{{ $namaSekolah ?: 'Laporan Kas &amp; Komite Siswa' }}</h1>
    <p class="subtitle">{{ $namaKelas }}</p>
    <p class="meta">Rekap Status Pembayaran Iuran Kas &amp; Komite per Siswa · Posisi per {{ tglIndo(now()) }}</p>

    <table>
        <thead>
            <tr>
                <th class="center" width="4%">No</th>
                <th width="20%">Nama Siswa</th>
                <th class="center" width="9%">Status Kas</th>
                <th class="num" width="9%">Kurang Kas (bln)</th>
                <th class="num" width="12%">Kurang Kas (Rp)</th>
                <th class="center" width="10%">Status Komite</th>
                <th class="num" width="13%">Kurang Komite (Rp)</th>
                <th class="num" width="14%">Total Kurang (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($rows as $i => $row)
                <tr>
                    <td class="center">{{ $i + 1 }}</td>
                    <td>{{ $row['nama'] }}<br><span style="font-size:8px;color:#6b7280">{{ $row['status'] === 'aktif' ? 'Aktif' : 'Nonaktif' }}</span></td>
                    <td class="center">
                        @if ($row['kasStatus'] === 'Lunas')
                            <span class="status-lunas">Lunas</span>
                        @elseif ($row['kasStatus'] === 'Belum Lunas')
                            <span class="status-kurang">Belum Lunas</span>
                        @else
                            <span class="status-info">Belum Diatur</span>
                        @endif
                    </td>
                    <td class="num">{{ $row['kasStatus'] === 'Belum Lunas' ? $row['kasKurangBulan'] : '0' }}</td>
                    <td class="num">{{ $row['kasStatus'] === 'Belum Lunas' ? rupiah($row['kasKurangRupiah']) : 'Rp 0' }}</td>
                    <td class="center">
                        @if ($row['komiteStatus'] === 'Lunas')
                            <span class="status-lunas">Lunas</span>
                        @elseif ($row['komiteStatus'] === 'Lebih')
                            <span class="status-lunas">Lunas (Lebih)</span>
                        @elseif ($row['komiteStatus'] === 'Belum Lunas')
                            <span class="status-kurang">Belum Lunas</span>
                        @else
                            <span class="status-info">Belum Diatur</span>
                        @endif
                    </td>
                    <td class="num">{{ $row['komiteStatus'] === 'Belum Lunas' ? rupiah($row['komiteKurangRupiah']) : 'Rp 0' }}</td>
                    <td class="num">{{ rupiah($row['totalKurangRupiah']) }}</td>
                </tr>
            @endforeach
        </tbody>
        @if (count($rows) > 0)
            <tfoot>
                <tr>
                    <td colspan="3">Total Kurang</td>
                    <td class="num">{{ array_sum(array_column($rows, 'kasKurangBulan')) }} bulan</td>
                    <td class="num">{{ rupiah(array_sum(array_column($rows, 'kasKurangRupiah'))) }}</td>
                    <td></td>
                    <td class="num">{{ rupiah(array_sum(array_column($rows, 'komiteKurangRupiah'))) }}</td>
                    <td class="num">{{ rupiah(array_sum(array_column($rows, 'totalKurangRupiah'))) }}</td>
                </tr>
            </tfoot>
        @endif
    </table>

    <p class="footer">Iuran Kas {{ $iuranKas > 0 ? rupiah($iuranKas) . '/bulan' : 'belum diatur' }} · Iuran Komite {{ $iuranKomite > 0 ? rupiah($iuranKomite) . '/tahun ajaran' : 'belum diatur' }}</p>
</body>
</html>