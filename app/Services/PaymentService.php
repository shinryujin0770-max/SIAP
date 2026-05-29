<?php

namespace App\Services;

class PaymentService
{
    protected $berita_acara = [
        [
            'title' => 'Tanggal STS',
            'value' => '',
        ],
        [
            'title' => 'Nomor',
            'value' => '',
        ],
        [
            'title' => 'Tanggal',
            'value' => '',
        ],
        [
            'title' => 'Nomor Penetapan PPK',
            'value' => '',
        ],
        [
            'title' => 'Tanggal Penetapan PPK',
            'value' => '',
        ],
    ];

    protected $bast = [
        [
            'title' => 'Tanggal STS',
            'value' => '',
        ],
        [
            'title' => 'Nomor',
            'value' => '',
        ],
        [
            'title' => 'Tanggal',
            'value' => '',
        ],
        [
            'title' => 'Nomor Penetapan PPK',
            'value' => '',
        ],
        [
            'title' => 'Tanggal Penetapan PPK',
            'value' => '',
        ],
    ];

    protected $pengantar = [
        [
            'title' => 'Nomor',
            'value' => '',
        ],
        [
            'title' => 'Tanggal',
            'value' => '',
        ],
    ];

    protected $ringkasan_kontrak = [
        [
            'title' => 'Nomor',
            'value' => '',
        ],
        [
            'title' => 'Tanggal',
            'value' => '',
        ],
    ];

    public function getDetailPayment()
    {
        return array_merge([
            'berita_acara' => $this->berita_acara,
            'bast' => $this->bast,
            'pengantar' => $this->pengantar,
            'ringkasan_kontrak' => $this->ringkasan_kontrak,
        ]);
    }
}
