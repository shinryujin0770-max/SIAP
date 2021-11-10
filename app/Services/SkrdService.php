<?php

namespace App\Services;

class SkrdService
{
    public function calculate($kios, $dagangan, $kelas, $menghadap = null, $luas)
    {
        $data = $this->tarifRetribusi();

        if ($kios == 'kios') {
            $tarif = $data[$menghadap][$kios][$dagangan][$kelas];
        } else {
            $tarif =  $data[$kios][$dagangan][$kelas];
        }
        $luas = strtolower($luas);
        $luas = str_replace('x', '*', 'luas');
        $luas = @eval($luas);
        if (!$luas) {
            $luas = 1;
        }

        return $tarif * $luas;
    }

    public function tarifRetribusi()
    {
        return [
            'kios' => [
                'ML' => [
                    'A' => [
                        '1' => 11000,
                        '2' => 9000,
                        '3' => 8000,
                    ],
                    'B' => [
                        '1' => 10500,
                        '2' => 8500,
                        '3' => 7500,
                    ],
                    'C' => [
                        '1' => 10000,
                        '2' => 8000,
                        '3' => 7000,
                    ],
                ],
                'MD' => [
                    'A' => [
                        '1' => 9000,
                        '2' => 7000,
                        '3' => 6500,
                    ],
                    'B' => [
                        '1' => 8500,
                        '2' => 6500,
                        '3' => 6000,
                    ],
                    'C' => [
                        '1' => 8000,
                        '2' => 6000,
                        '3' => 5500,
                    ],
                ],
            ],
            'los' => [
                'A' => [
                    '1' => 500,
                    '2' => 400,
                    '3' => 400,
                ],
                'B' => [
                    '1' => 400,
                    '2' => 400,
                    '3' => 300,
                ],
                'C' => [
                    '1' => 300,
                    '2' => 250,
                    '3' => 200,
                ],
            ],
            'pelataran' => [
                'A' => [
                    '1' => 400,
                    '2' => 300,
                    '3' => 300,
                ],
                'B' => [
                    '1' => 300,
                    '2' => 250,
                    '3' => 250,
                ],
                'C' => [
                    '1' => 200,
                    '2' => 200,
                    '3' => 200,
                ],
            ],
        ];
    }
}
