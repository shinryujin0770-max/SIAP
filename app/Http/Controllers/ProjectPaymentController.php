<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Partner;
use App\Models\Payment_detail;
use App\Models\Project;
use App\Models\Project_payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ProjectPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        Carbon::setLocale('id');
    }

    public function detail($id)
    {
        $data['payment_detail'] = Payment_detail::where('id_payment', $id)->get();

        return view('pages/project/payments/browse', $data);
    }

    public function edit($id)
    {
        $data['payment_detail'] = Payment_detail::find($id);

        return view('pages/project/payments/edit', $data);
    }
}
