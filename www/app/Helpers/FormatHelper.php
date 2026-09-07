<?php

use Illuminate\Support\Carbon;

if (! function_exists('rupiah')) {
    function rupiah($value): string
    {
        return 'Rp '.number_format((float) $value, 0, ',', '.');
    }
}

if (! function_exists('namaBulan')) {
    function namaBulan(?int $month = null): string
    {
        $month ??= now()->month;

        $names = [
            1 => 'Januari',
            2 => 'Februari',
            3 => 'Maret',
            4 => 'April',
            5 => 'Mei',
            6 => 'Juni',
            7 => 'Juli',
            8 => 'Agustus',
            9 => 'September',
            10 => 'Oktober',
            11 => 'November',
            12 => 'Desember',
        ];

        return $names[(int) $month] ?? '';
    }
}

if (! function_exists('namaBulanSingkat')) {
    function namaBulanSingkat(?int $month = null): string
    {
        $month ??= now()->month;

        $names = [
            1 => 'Jan',
            2 => 'Feb',
            3 => 'Mar',
            4 => 'Apr',
            5 => 'Mei',
            6 => 'Jun',
            7 => 'Jul',
            8 => 'Agu',
            9 => 'Sep',
            10 => 'Okt',
            11 => 'Nov',
            12 => 'Des',
        ];

        return $names[(int) $month] ?? '';
    }
}

if (! function_exists('tglIndo')) {
    function tglIndo($date): string
    {
        $d = Carbon::parse($date);

        return $d->format('d').' '.namaBulanSingkat($d->month).' '.$d->format('Y');
    }
}

if (! function_exists('bulanOptionsTahunAjaran')) {
    /**
     * Daftar bulan dalam satu tahun ajaran (Juli - Juni), urut dari paling baru.
     *
     * @param  int|null  $tahunAjaran  Tahun awal tahun ajaran, misal 2026 untuk 2026/2027.
     */
    function bulanOptionsTahunAjaran(?int $tahunAjaran = null): array
    {
        $tahun = $tahunAjaran ?? tahunAjaranTahun(now());
        $awal = \Carbon\CarbonImmutable::create($tahun, 7, 1);

        $options = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = $awal->addMonthsNoOverflow($i);

            $options[] = [
                'value' => $date->format('Y-m'),
                'label' => namaBulan($date->month).' '.$date->format('Y'),
            ];
        }

        return $options;
    }
}

if (! function_exists('setting')) {
    function setting(string $key, $default = null)
    {
        $value = \App\Models\Setting::where('key', $key)->value('value');

        return $value === null ? $default : $value;
    }
}
