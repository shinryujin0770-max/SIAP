<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Pedagang;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class PedagangController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $data['page_title'] = "Pedagang";
        $data['page_subtitle'] = "";

        return view('pages/pedagang/browse', $data);
    }

    public function browse(Request $request)
    {
        if ($request->ajax()) {
            $data = Pedagang::orderBy('id', 'desc');
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
        $data['page_title'] = "Pedagang";
        $data['page_subtitle'] = "Tambah Pedagang";

        return view('pages/pedagang/add', $data);
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
        $store = Pedagang::create($data);

        if (!$store) {
            toastr()->error('Gagal Menambah');
            return redirect('pedagang');
        }

        toastr()->success('Berhasil Menambah');
        return redirect('pedagang');
    }

    public function edit($id)
    {
        $data['page_title'] = "Pedagang";
        $data['page_subtitle'] = "Ubah Pedagang";
        $data['pedagang'] = Pedagang::find($id);

        return view('pages/pedagang/edit', $data);
    }

    public function update(Request $request)
    {
        $attr = $request->all();

        $id = $attr['id'];
        $data = $attr['dt'];
        $store = Pedagang::where('id', $id)->update($data);

        if (!$store) {
            toastr()->error('Gagal Mengubah');
            return redirect('pedagang');
        }

        toastr()->success('Berhasil Mengubah');
        return redirect('pedagang');
    }

    public function modalDelete($id)
    {
        $data['page_title'] = "Pedagang";
        $data['page_subtitle'] = "Hapus Pedagang";
        $data['pedagang'] = Pedagang::findOrFail($id);

        return view('pages/pedagang/delete', $data);
    }

    public function delete($id)
    {
        $store = Pedagang::where('id', $id)->delete();

        if (!$store) {
            toastr()->error('Gagal Menghapus');
            return redirect('pedagang');
        }

        toastr()->success('Berhasil Menghapus');
        return redirect('pedagang');
    }

    public function ajax($id)
    {
        $data = Pedagang::select('*')->where('nik', $id)->first();
        return $data;
    }
}
