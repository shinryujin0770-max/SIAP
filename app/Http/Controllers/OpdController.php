<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OpdController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['page_title'] = "OPD";
        $data['page_subtitle'] = "";

        return view('pages/opd/browse', $data);
    }

    public function browse(Request $request)
    {
        if ($request->ajax()) {
            $data = Opd::orderBy('id', 'desc');
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
        $data['page_title'] = "OPD";
        $data['page_subtitle'] = "Tambah OPD";

        return view('pages/opd/add', $data);
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
        $store = Opd::create($data);

        if (!$store) {
            toastr()->error('Gagal Menambah');
            return redirect('opd');
        }

        toastr()->success('Berhasil Menambah');
        return redirect('opd');
    }

    public function edit($id)
    {
        $data['page_title'] = "OPD";
        $data['page_subtitle'] = "Ubah OPD";
        $data['opd'] = Opd::find($id);
        return view('pages/opd/edit', $data);
    }

    public function update(Request $request)
    {
        $attr = $request->all();

        $id = $attr['id'];
        $data = $attr['dt'];
        $store = Opd::where('id', $id)->update($data);

        if (!$store) {
            toastr()->error('Gagal Mengubah');
            return redirect('opd');
        }

        toastr()->success('Berhasil Mengubah');
        return redirect('opd');
    }

    public function modalDelete($id)
    {
        $data['page_title'] = "OPD";
        $data['page_subtitle'] = "Hapus OPD";
        $data['opd'] = Opd::findOrFail($id);

        return view('pages/opd/delete', $data);
    }

    public function delete($id)
    {
        $store = Opd::where('id', $id)->delete();

        if (!$store) {
            toastr()->error('Gagal Menghapus');
            return redirect('opd');
        }

        toastr()->success('Berhasil Menghapus');
        return redirect('opd');
    }

    public function ajax($id)
    {
        $data = Opd::select('*')->where('name', $id)->first();
        return $data;
    }
}
