<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithDefaultStyles;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Style;

class LaporanRFK2 implements FromView, ShouldAutoSize, WithDefaultStyles, WithStyles
{
    protected $data;

    function __construct($data)
    {
        $this->data = $data;
    }

    public function view(): View
    {
        return view('exports.laporan_rfk_2', [
            'data' => $this->data,
        ]);
    }

    public function defaultStyles(Style $defaultStyle)
    {
        return [
            'font' => ['size' => 12],
        ];
    }

    public function styles(Worksheet $sheet)
    {
        $keuangan = [];
        $i = 1;
        foreach ($this->data as $item) {
            if ($item['keuangan'] < 35) $keuangan['J' . ($i + 2)] =  ['font' => ['color' => ['argb' => 'EB2B02']]];
            $i++;
        }

        return [
            1    => [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ],
            2    => [
                'font' => ['bold' => true],
                'alignment' => [
                    'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER,
                ],
            ]
        ] + $keuangan;
    }
}
