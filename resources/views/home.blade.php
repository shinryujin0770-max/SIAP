@extends('adminlte::page')

@section('title', 'Aplikasi Pembangungan Tulungagung')

@section('content_header')
<h1 class="m-0 text-dark">Aplikasi Pembangungan Tulungagung</h1>
@stop

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">Aplikasi Pembangungan Tulungagung</div>

                <div class="card-body">
                    @if (session('status'))
                    <div class="alert alert-success" role="alert">
                        {{ session('status') }}
                    </div>
                    @endif

                    You are logged in!
                </div>
            </div>
        </div>
    </div>
</div>
@endsection