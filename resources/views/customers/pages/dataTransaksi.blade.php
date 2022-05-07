@extends('customers/layouts/main')

@section('title','adx-pay | Data Transaksi')

@section('content-header', 'Data Transaksi')

@section('content')
<!-- Content Wrapper. Contains page content -->
<section class="content">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-6 col-12">
        <div class="card">
          <div class="card-body p-2">
            <p class="text-center m-0"><strong>Transaksi Hari Ini ({{$mutasiThisDay->count()}})</strong></p>
          </div>
        </div>
        @if($mutasiThisDay->first() == null)
        <div class="col-lg-12">
          <div class="alert alert-warning alert-dismissible">
            Tydak adaa transaksii hari ini.
          </div>
        </div>
        @endif

        @foreach($mutasiThisDay as $mts)
        <div class="card">
          <div class="card-body p-2">
            <?php $date = new DateTime($mts->created_at) ?>
            <span class="float-left">{{$date->format('d F Y   G:i')}} WITA</span>

            @if($mts->status == "FAILED") 
            <span class="float-right text-danger text-uppercase"><strong>gagal</strong></span>
            <br><div>{{$mts -> phone}}</div>
            <div>({{$mts -> code}}) <p class="badge badge-danger">from {{$mts -> username}}</p></div>
            <div class="text-danger text-justify">{{$mts -> note}}</div>
            <div class=""><a href="/cus/transaksi/{{$mts->trxid_api}}" class="btn btn-primary btn-block">Lihat Bukti</a></div>
            @elseif($mts->status == "SUCCESS")
            <span class="float-right text-success text-uppercase"><strong>sukses</strong></span>
            <br><div>{{$mts -> phone}}</div>
            <div>({{$mts -> code}}) <p class="badge badge-success">from {{$mts -> username}}</p></div>
            <div class="text-success text-justify">{{$mts -> note}}</div>
            <div class=""><a href="/cus/transaksi/{{$mts->trxid_api}}" class="btn btn-primary btn-block">Lihat Bukti</a></div>
            @else
            <span class="float-right text-warning text-uppercase"><strong>diproses</strong></span>
            <br><div>{{$mts -> phone}}</div>
            <div>({{$mts -> code}}) <p class="badge badge-warning">from {{$mts -> username}}</p></div>
            <div class="text-warning text-justify">{{$mts -> note}}</div>
            <div class=""><a href="/cus/transaksi/update/{{$mts->trxid_api}}" class="btn btn-warning btn-block">Refresh</a></div>
            @endif
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="card mt-5">
          <div class="card-body p-2">
            <p class="text-center m-0"><strong>Riwayat Transaksi ({{$mutasi->count()}})</strong></p>
          </div>
        </div>

        @foreach($mutasi as $mts)
        <div class="card">
          <div class="card-body p-2">
            <span class="float-left">{{$mts->created_at}}</span>

            @if($mts->status == "FAILED") 
            <span class="float-right text-danger text-uppercase"><strong>gagal</strong></span>
            <br><div>{{$mts -> phone}}</div>
            <div>({{$mts -> code}})</div>
            <div class="text-danger text-justify">{{$mts -> note}}</div>
            <div class=""><a href="/cus/transaksi/{{$mts->trxid_api}}" class="btn btn-primary btn-block">Lihat Bukti</a></div>
            @elseif($mts->status == "SUCCESS")
            <span class="float-right text-success text-uppercase"><strong>sukses</strong></span>
            <br><div>{{$mts -> phone}}</div>
            <div>({{$mts -> code}})</div>
            <div class="text-success text-justify">{{$mts -> note}}</div>
            <div class=""><a href="/cus/transaksi/{{$mts->trxid_api}}" class="btn btn-primary btn-block">Lihat Bukti</a></div>
            @elseif($mts->status == "KADALUARSA")
            <span class="float-right text-success text-uppercase"><strong>sukses</strong></span>
            <br><div>{{$mts -> phone}}</div>
            <div>({{$mts -> code}})</div>
            <div class="text-success text-justify">{{$mts -> note}}</div>
            @else
            <span class="float-right text-warning text-uppercase"><strong>diproses</strong></span>
            <br><div>{{$mts -> phone}}</div>
            <div>({{$mts -> code}})</div>
            <div class="text-warning text-justify">{{$mts -> note}}</div>
            <div class=""><a href="/cus/transaksi/update/{{$mts->trxid_api}}" class="btn btn-warning btn-block">Refresh</a></div>
            @endif
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</section>
<!-- /.content-wrapper -->
@endsection

