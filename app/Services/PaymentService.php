<?php

namespace App\Services;

class PaymentService
{
    protected $laporan_selesai = [
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

    protected $iktisar_pembayaran = [
        [
            'title' => 'Nomor',
            'value' => '',
        ],
        [
            'title' => 'Tanggal',
            'value' => '',
        ],
    ];

    public function getDetailPayment($type)
    {
        if ($type == 'Langsung 100%') {
            return $this->detailPaid();
        }
        if ($type == 'Uang Muka') {
            return $this->detailUangMuka();
        }
        return $this->detailAngsuran();
    }

    private function detailPaid()
    {
        return array_merge([
            'laporan_selesai' => $this->laporan_selesai,
            'bast' => $this->bast,
            'pengantar' => $this->pengantar,
            'iktisar_pembayaran' => $this->iktisar_pembayaran,
        ]);
    }

    private function detailUangMuka()
    {
        return array_merge([
            'pengantar' => $this->pengantar,
            'iktisar_pembayaran' => $this->iktisar_pembayaran,
        ]);
    }

    private function detailAngsuran()
    {
        return array_merge([
            'laporan_selesai' => $this->laporan_selesai,
            'bast' => $this->bast,
            'pengantar' => $this->pengantar,
            'iktisar_pembayaran' => $this->iktisar_pembayaran,
        ]);
    }
}
