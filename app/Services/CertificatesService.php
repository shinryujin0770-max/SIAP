<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade as PDF;

class CertificatesService
{
    public function getCertificate($data = [])
    {
        // $file = file_get_contents(app_path("Services/Mails/template/$name.php"));
        // $pdf = PDF::loadHTML($file)->setPaper('a4', 'portrait');
        // ->setOptions(['debugLayout' => true, 'debugLayoutPaddingBox' => true])
        $pdf = PDF::loadView('certificates.page', $data)->setPaper('legal', 'portrait');
        return $pdf->stream();
    }

    public function getSkrd($data = [])
    {
        // $file = file_get_contents(app_path("Services/Mails/template/$name.php"));
        // $pdf = PDF::loadHTML($file)->setPaper('a4', 'portrait');
        // ->setOptions(['debugLayout' => true, 'debugLayoutPaddingBox' => true])
        $customPaper = array(0,0,684,792);
        $pdf = PDF::loadView('certificates.skrd.page', $data)->setPaper($customPaper, 'portrait');
        return $pdf->stream();
    }
}
