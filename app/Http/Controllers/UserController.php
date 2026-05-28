<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Opd;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        $data['page_title'] = "Pengguna";
        $data['page_subtitle'] = "";

        return view('pages/user/browse', $data);
    }

    public function browse(Request $request)
    {
        if ($request->ajax()) {
            $data = User::select('users.*', 'roles.name as role_name', 'opd.name as opd_name')->orderBy('id', 'desc')
                ->leftJoin('model_has_roles', 'users.id', '=', 'model_has_roles.model_id', 'left')
                ->leftJoin('roles', 'model_has_roles.role_id', '=', 'roles.id', 'left')
                ->leftJoin('opd', 'users.id_opd', '=', 'opd.id', 'left');
            $data = DB::table($data)->select('*');
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('action', function ($row) {
                    $id = $row->id;
                    $btn = "<button onclick='read($id)' class='edit btn btn-block btn-info btn-sm'><i class='fa fa-edit'></i> Edit</button>";
                    $btn .= "<button onclick='hapus($id)' class='edit btn btn-block btn-danger btn-sm'><i class='fa fa-trash'></i> Delete</button>";
                    return $btn;
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return;
    }

    public function read($id)
    {
        $data['page_title'] = "Pengguna";
        $data['page_subtitle'] = "Detail Pengguna";
        $data['user'] = User::findOrFail($id);

        return view('pages/user/read', $data);
    }

    public function add()
    {
        $data['page_title'] = "Pengguna";
        $data['page_subtitle'] = "Tambah Pengguna";
        $data['opds'] = Opd::all();

        return view('pages/user/add', $data);
    }

    public function store(Request $request)
    {
        $attr = $request->all();
        $validator = Validator::make(array_merge($attr['dt'], $attr), [
            'username' => ['required', 'string', 'unique:users'],
            'name' => 'required',
            'id_opd' => 'required',
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'nip' => 'required',
            'sk_date' => 'required',
            'sk_number' => 'required',
            'jabatan' => 'required',
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

        $role = $attr['role'];
        $store->assignRole($role);

        if (!$store) {
            toastr()->error('Gagal Menambah');
            return;
        }

        toastr()->success('Berhasil Menambah');
        return 'true';
    }

    public function edit($id)
    {
        $data['page_title'] = "Pengguna";
        $data['page_subtitle'] = "Ubah Pengguna";
        $data['user'] = User::findOrFail($id);
        $data['opds'] = Opd::all();

        $data['user']['role'] = count($data['user']->roles()->get()) ? $data['user']->roles()->get()[0]->name : 'superadmin';

        return view('pages/user/edit', $data);
    }

    public function update(Request $request)
    {
        $attr = $request->all();
        $id = $attr['id'];
        $data = $attr['dt'];

        $findUser = User::find($id);
        $dataValidation = $attr['password'] ? array_merge($attr['dt'], $attr) : $attr['dt'];
        $validationUsername = [
            'username' => $findUser['username'] != $data['username'] ? ['required', 'string', 'unique:users'] : ['required', 'string']
        ];
        $validationEmail = [
            'email' => $findUser['email'] != $data['email'] ? ['required', 'string', 'email', 'max:255', 'unique:users'] : ['required', 'string', 'email', 'max:255']
        ];

        $validationPassword = isset($dataValidation['password']) ? [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ] : [];
        $validator = Validator::make($dataValidation, array_merge($validationEmail, $validationUsername, $validationPassword, [
            'name' => 'required',
            'id_opd' => 'required',
            'nip' => 'required',
            'sk_date' => 'required',
            'sk_number' => 'required',
            'jabatan' => 'required',
        ]), [
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

        if ($attr['password']) {
            $data['password'] = Hash::make($attr['password']);
        }
        $store = $findUser->update($data);

        $role = $attr['role'];
        $findUser->syncRoles($role);

        if (!$store) {
            toastr()->error('Gagal Mengubah');
            return;
        }

        toastr()->success('Berhasil Mengubah');
        return 'true';
    }

    public function modalDelete($id)
    {
        $data['page_title'] = "Pengguna";
        $data['page_subtitle'] = "Hapus Pengguna";
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
        return redirect('Pengguna');
    }
}
