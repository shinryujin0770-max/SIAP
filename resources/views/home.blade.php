@extends('adminlte::page')

@section('title', 'Aplikasi Pembangungan Tulungagung')

@section('content_header')
<h1 class="m-0 text-dark">Aplikasi Pembangungan Tulungagung</h1>
@stop

@section('content')
<div class="row justify-content-center">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Jenis Pekerjaan</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        {{-- <p class="text-center">
                            <strong>Total Pelayanan: Januari 2023 - Juli 2023</strong>
                        </p> --}}
                        <div class="chart">
                            <div class="position-relative mb-4">
                                <canvas id="project-chart" height="200"></canvas>
                            </div>
                            <div class="d-flex flex-row justify-content-end">
                                <span class="mr-2">
                                    <i class="fas fa-square text-primary"></i> Jumlah
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Jumlah Nilai Realisasi</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        <div class="chart">
                            <div class="position-relative mb-4">
                                <canvas id="avarage-chart" height="200"></canvas>
                            </div>
                            <div class="d-flex flex-row justify-content-end">
                                <span class="mr-2">
                                    <i class="fas fa-square" style="color: #FF5733"></i> Nilai Pagu Anggaran
                                </span>
                                <span class="mr-2">
                                    <i class="fas fa-square" style="color: #007bff"></i> Nilai HPS
                                </span>
                                <span class="mr-2">
                                    <i class="fas fa-square" style="color: #33FFA2"></i> Nilai Kontrak
                                </span>
                                <span class="mr-2">
                                    <i class="fas fa-square" style="color: #FF3368"></i> Realisasi Anggaran
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title">Prosentase Nilai Kontrak Berdasarkan HPS</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-12">
                        {{-- <p class="text-center">
                            <strong>Total Pelayanan: Januari 2023 - Juli 2023</strong>
                        </p> --}}
                        <div class="chart">
                            <div class="position-relative mb-4">
                                <canvas id="hps-chart" height="200"></canvas>
                            </div>
                            {{-- <div class="d-flex flex-row justify-content-end">
                                <span class="mr-2">
                                    <i class="fas fa-square text-primary"></i> Jumlah
                                </span>
                            </div> --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    var projectChart = new Chart(
        $('#project-chart'),
        {
            type:'bar',
            data: {
                labels:['Konstruksi','Pengawasan','Perencanaan'],
                datasets:[
                    {
                        backgroundColor:'#007bff',
                        borderColor:'#007bff',
                        data: [10,15,22],
                        fill: false,
                    },
                ]
            },
            options:{
                maintainAspectRatio:false,
                tooltips:{
                    mode:'index',
                    intersect:true
                },
                hover:{
                    mode:'index',
                    intersect:true
                },
                legend:{
                    display:false
                },
                scales:{
                    yAxes:[
                        {
                            gridLines:{
                                display:true,
                                lineWidth:'4px',
                                color:'rgba(0, 0, 0, .2)',
                                zeroLineColor:'transparent'
                            },
                            ticks:$.extend({
                                beginAtZero:true,
                                callback:function(value){
                                    // if(value>=1000){
                                    //     value/=1000
                                    //     value+='k'
                                    // }
                                    return value;
                                }
                            }, {fontColor:'#495057',fontStyle:'bold'} )
                        }
                    ],
                    xAxes:[{display:true,gridLines:{display:false},ticks:{fontColor:'#495057',fontStyle:'bold'}}]
                }
            }
        }
    )
    var avarageChart = new Chart(
        $('#avarage-chart'),
        {
            type:'bar',
            data: {
                labels:['Januari','Februari','Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                datasets:[
                    {
                        backgroundColor:'#FF5733',
                        borderColor:'#FF5733',
                        data: [10,15,22],
                        fill: false,
                    },
                    {
                        backgroundColor:'#007bff',
                        borderColor:'#007bff',
                        data: [10,15,22],
                        fill: false,
                    },
                    {
                        backgroundColor:'#33FFA2',
                        borderColor:'#33FFA2',
                        data: [10,15,22],
                        fill: false,
                    },
                    {
                        backgroundColor:'#FF3368',
                        borderColor:'#FF3368',
                        data: [10,15,22],
                        fill: false,
                    },
                ]
            },
            options:{
                maintainAspectRatio:false,
                tooltips:{
                    mode:'index',
                    intersect:true
                },
                hover:{
                    mode:'index',
                    intersect:true
                },
                legend:{
                    display:false
                },
                scales:{
                    yAxes:[
                        {
                            gridLines:{
                                display:true,
                                lineWidth:'4px',
                                color:'rgba(0, 0, 0, .2)',
                                zeroLineColor:'transparent'
                            },
                            ticks:$.extend({
                                beginAtZero:true,
                                callback:function(value){
                                    // if(value>=1000){
                                    //     value/=1000
                                    //     value+='k'
                                    // }
                                    return value;
                                }
                            }, {fontColor:'#495057',fontStyle:'bold'} )
                        }
                    ],
                    xAxes:[{display:true,gridLines:{display:false},ticks:{fontColor:'#495057',fontStyle:'bold'}}]
                }
            }
        }
    )
    var hpsChart = new Chart(
        $('#hps-chart'),
        {
            type:'line',
            data: {
                labels:['Januari','Februari','Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'],
                datasets:[
                    {
                        backgroundColor:'#007bff',
                        borderColor:'#007bff',
                        data: [10,15,22,21,25,59,40],
                        fill: false,
                    },
                ]
            },
            options:{
                maintainAspectRatio:false,
                tooltips:{
                    mode:'index',
                    intersect:true
                },
                hover:{
                    mode:'index',
                    intersect:true
                },
                legend:{
                    display:false
                },
                scales:{
                    yAxes:[
                        {
                            gridLines:{
                                display:true,
                                lineWidth:'4px',
                                color:'rgba(0, 0, 0, .2)',
                                zeroLineColor:'transparent'
                            },
                            ticks:$.extend({
                                beginAtZero:true,
                                callback:function(value){
                                    // if(value>=1000){
                                    //     value/=1000
                                    //     value+='k'
                                    // }
                                    return value + '%'
                                }
                            }, {fontColor:'#495057',fontStyle:'bold'} )
                        }
                    ],
                    xAxes:[{display:true,gridLines:{display:false},ticks:{fontColor:'#495057',fontStyle:'bold'}}]
                }
            }
        }
    )
</script>
@endsection