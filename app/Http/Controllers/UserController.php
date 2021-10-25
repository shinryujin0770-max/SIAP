<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Jenis_dagangan;
use App\Models\Kios;
use App\Models\Pasar;
use App\Models\Pedagang;
use App\Models\Situ;
use App\Models\User;
use App\Services\CertificatesService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class UserController extends Controller
{

    public function __construct()
    {
        $this->middleware('auth');

        Carbon::setLocale('id');
    }

    public function index()
    {
        $data['page_title'] = "SITU";
        $data['page_subtitle'] = "";

        return view('pages/user/browse', $data);
    }

    public function browse(Request $request)
    {
        if ($request->ajax()) {
            $data = User::all();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $id = $row['id'];
                    $btn = "<button onclick='read($id)' class='edit btn btn-block btn-info btn-sm'><i class='fa fa-eye'></i> Detail</button>";
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return;
    }

    public function read($id)
    {
        $data['page_title'] = "SITU";
        $data['page_subtitle'] = "Detail SITU";
        $data['user'] = User::findOrFail($id);

        return view('pages/user/read', $data);
    }

    public function add()
    {
        $data['page_title'] = "SITU";
        $data['page_subtitle'] = "Tambah SITU";
        $data['pasars'] = Pasar::all();
        $data['kioss'] = Kios::all();
        $data['pedagangs'] = Pedagang::all();
        $data['jenis_dagangans'] = Jenis_dagangan::all();

        return view('pages/user/add', $data);
    }

    public function store(Request $request)
    {
        $attr = $request->all();
        $validator = Validator::make(array_merge($attr['dt'], $attr), [
            'nip' => 'required',
            'name' => 'required',
            'jabatan' => 'required',
            'pangkat' => 'required',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'signature' => 'required',
        ], [
            'required' => ':attribute harus diisi.',
        ]);
        if ($validator->fails()) {
            $errors = [];
            $i = 0;
            foreach ($validator->getMessageBag()->toArray() as $key => $value) {
                $errors[$i]['code'] = $key;
                $errors[$i]['message'] = $value[0];
                $i++;
            }
            return Response::json(array(
                'success' => false,
                'errors' => $errors
            ), 400); // 400 being the HTTP code for an invalid request.
        }

        $data = $attr['dt'];
        $data['password'] = Hash::make($data['password']);
        $store = User::create($data);

        if (!$store) {
            toastr()->error('Gagal Menambah');
            return;
        }

        if (isset($attr['foto'])) {
            $store->addMedia($attr['foto'])
                ->usingFileName('foto-' . $store['id'] . '.' . pathinfo($attr['foto']->getClientOriginalName(), PATHINFO_EXTENSION))
                ->toMediaCollection('foto');
        }

        toastr()->success('Berhasil Menambah');
        return redirect('user');
    }

    public function edit($id)
    {
        $data['page_title'] = "SITU";
        $data['page_subtitle'] = "Ubah SITU";
        $data['user'] = User::findOrFail($id);

        return view('pages/user/edit', $data);
    }

    public function update(Request $request)
    {
        $attr = $request->all();
        $validator = Validator::make(array_merge($attr['dt'], $attr), [
            'nip' => 'required',
            'name' => 'required',
            'jabatan' => 'required',
            'pangkat' => 'required',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'signature' => 'required',
        ], [
            'required' => ':attribute harus diisi.',
        ]);
        if ($validator->fails()) {
            $errors = [];
            $i = 0;
            foreach ($validator->getMessageBag()->toArray() as $key => $value) {
                $errors[$i]['code'] = $key;
                $errors[$i]['message'] = $value[0];
                $i++;
            }
            return Response::json(array(
                'success' => false,
                'errors' => $errors
            ), 400); // 400 being the HTTP code for an invalid request.
        }

        $id = $attr['id'];
        $data = $attr['dt'];

        $findUser = User::find($id);
        $store = $findUser->update($data);

        if (!$store) {
            toastr()->error('Gagal Mengubah');
            return;
        }

        if (isset($attr['signature'])) {
            $findUser->addMedia($attr['signature'])
                ->usingFileName('signature-' . $id . '.' . pathinfo($attr['signature']->getClientOriginalName(), PATHINFO_EXTENSION))
                ->toMediaCollection('signature');
        }

        toastr()->success('Berhasil Mengubah');
        return redirect('user');
    }

    public function modalDelete($id)
    {
        $data['page_title'] = "SITU";
        $data['page_subtitle'] = "Hapus SITU";
        $data['user'] = User::findOrFail($id);

        return view('pages/user/delete', $data);
    }

    public function delete($id)
    {
        $store = User::where('id', $id)->delete();

        if (!$store) {
            toastr()->error('Gagal Menghapus');
            return;
        }

        toastr()->success('Berhasil Menghapus');
        return redirect('situ');
    }
}
