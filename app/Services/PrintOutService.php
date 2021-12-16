<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade as PDF;

class PrintOutService
{
    public function getPrint($kategori, $page, $data = [])
    {
        // $file = file_get_contents(app_path("Services/Mails/template/$name.php"));
        // $pdf = PDF::loadHTML($file)->setPaper('a4', 'portrait');
        // ->setOptions(['debugLayout' => true, 'debugLayoutPaddingBox' => true])
        $pdf = PDF::loadView("printouts.$kategori.$page", $data)->setPaper('legal', 'portrait');
        return $pdf->stream();
    }
}
