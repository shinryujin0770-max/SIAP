<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kelurahan;

class AjaxController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function kelurahan($id_kecamatan)
    {
        $data = Kelurahan::select('*')->where('id_kecamatan', $id_kecamatan)->get();
        return $data;
    }
}
