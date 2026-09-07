<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;

class LaporanSiswaExport implements FromArray, ShouldAutoSize, WithEvents, WithHeadings
{
    protected array $rows;

    protected string $namaSekolah;

    protected string $namaKelas;

    public function __construct(array $rows, string $namaSekolah = '', string $namaKelas = '')
    {
        $this->rows = $rows;
        $this->namaSekolah = $namaSekolah;
        $this->namaKelas = $namaKelas;
    }

    public function headings(): array
    {
        return [
            'No',
            'Nama Siswa',
            'Status Siswa',
            'Status Kas',
            'Kurang Kas (Bulan)',
            'Kurang Kas (Rp)',
            'Status Komite',
            'Dibayar Komite (Rp)',
            'Kurang Komite (Rp)',
            'Total Kurang (Rp)',
        ];
    }

    public function array(): array
    {
        $data = [];

        foreach ($this->rows as $i => $row) {
            $data[] = [
                $i + 1,
                $row['nama'],
                sprintf('%-9s', strtoupper($row['status'])),
                $row['kasStatus'],
                $row['kasStatus'] === 'Belum Lunas' ? $row['kasKurangBulan'] : 0,
                $row['kasStatus'] === 'Belum Lunas' ? $row['kasKurangRupiah'] : 0,
                $row['komiteStatus'],
                $row['komitePaid'],
                $row['komiteStatus'] === 'Belum Lunas' ? $row['komiteKurangRupiah'] : 0,
                $row['totalKurangRupiah'],
            ];
        }

        return $data;
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();

                $totalRows = count($this->rows);
                $lastRow = $totalRows + 4;
                $lastCol = 'J';

                $sheet->mergeCells('A1:'.$lastCol.'1');
                $sheet->setCellValue('A1', $this->namaSekolah ?: 'Laporan Kas & Komite Siswa');
                $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);

                $sheet->mergeCells('A2:'.$lastCol.'2');
                $sheet->setCellValue('A2', $this->namaKelas);
                $sheet->getStyle('A2')->getFont()->setSize(11);

                $sheet->mergeCells('A3:'.$lastCol.'3');
                $sheet->setCellValue('A3', 'Posisi per '.tglIndo(now()));
                $sheet->getStyle('A3')->getFont()->setSize(10);

                $sheet->getStyle('A4:'.$lastCol.'4')->getFont()->setBold(true);
                $sheet->getStyle('A4:'.$lastCol.'4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('A4:'.$lastCol.'4')->getFill()
                    ->setFillType(Fill::FILL_SOLID)
                    ->getStartColor()->setARGB('E5E7EB');

                $range = 'A4:'.$lastCol.$lastRow;
                $sheet->getStyle($range)->getBorders()->getAllBorders()
                    ->getColor()->setARGB(Color::COLOR_BLACK);

                if ($totalRows > 0) {
                    $sheet->setCellValue('A'.$lastRow, 'Total');
                    $sheet->setCellValue('F'.$lastRow, array_sum(array_column($this->rows, 'kasKurangRupiah')));
                    $sheet->setCellValue('J'.$lastRow, array_sum(array_column($this->rows, 'totalKurangRupiah')));
                    $sheet->getStyle('A'.$lastRow.':'.$lastCol.$lastRow)->getFont()->setBold(true);
                }
            },
        ];
    }
}
