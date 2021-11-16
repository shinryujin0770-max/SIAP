<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PartnerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['page_title'] = "Pelaksana";
        $data['page_subtitle'] = "";

        return view('pages/partner/browse', $data);
    }

    public function browse(Request $request)
    {
        if ($request->ajax()) {
            $data = Partner::orderBy('id', 'desc');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $id = $row['id'];
                    $btn = "<button onclick='edit($id)' class='edit btn btn-block btn-warning btn-sm'><i class='fa fa-edit'></i> Ubah</button>";
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return;
    }

    public function add()
    {
        $data['page_title'] = "Pelaksana";
        $data['page_subtitle'] = "Tambah Pelaksana";

        return view('pages/partner/add', $data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $attr = $request->all();

        $data = $attr['dt'];
        $store = Partner::create($data);

        if (!$store) {
            toastr()->error('Gagal Menambah');
            return redirect('partner');
        }

        toastr()->success('Berhasil Menambah');
        return redirect('partner');
    }

    public function edit($id)
    {
        $data['page_title'] = "Pelaksana";
        $data['page_subtitle'] = "Ubah Pelaksana";
        $data['partner'] = Partner::find($id);

        return view('pages/partner/edit', $data);
    }

    public function update(Request $request)
    {
        $attr = $request->all();

        $id = $attr['id'];
        $data = $attr['dt'];
        $store = Partner::where('id', $id)->update($data);

        if (!$store) {
            toastr()->error('Gagal Mengubah');
            return redirect('partner');
        }

        toastr()->success('Berhasil Mengubah');
        return redirect('partner');
    }

    public function modalDelete($id)
    {
        $data['page_title'] = "Pelaksana";
        $data['page_subtitle'] = "Hapus Pelaksana";
        $data['partner'] = Partner::findOrFail($id);

        return view('pages/partner/delete', $data);
    }

    public function delete($id)
    {
        $store = Partner::where('id', $id)->delete();

        if (!$store) {
            toastr()->error('Gagal Menghapus');
            return redirect('partner');
        }

        toastr()->success('Berhasil Menghapus');
        return redirect('partner');
    }

    public function ajax($id)
    {
        $data = Partner::select('*')->where('name', $id)->first();
        return $data;
    }
}
