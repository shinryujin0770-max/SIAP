<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade as PDF;

class CertificatesService
{
    public function getSurat($data = [])
    {
        // $file = file_get_contents(app_path("Services/Mails/template/$name.php"));
        // $pdf = PDF::loadHTML($file)->setPaper('a4', 'portrait');
        // ->setOptions(['debugLayout' => true, 'debugLayoutPaddingBox' => true])
        $pdf = PDF::loadView('certificates.page', $data)->setPaper('legal', 'portrait');
        return $pdf->stream();
    }

    public function loadSurat($url)
    {
        // dd(\public_path(), $url);
        $pdf = PDF::loadFile(public_path() . $url)->save('/download/my_stored_file.pdf')->stream('my_stored_file.pdf');;
        return $pdf->stream();
    }
}
