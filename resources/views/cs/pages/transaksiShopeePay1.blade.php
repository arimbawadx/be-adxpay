@extends('cs/layouts/main')

@section('title') adx-pay | Transaksi ShopeePay @endsection

@section('content-header') Transaksi ShopeePay @endsection

@section('content')
<!-- Content Wrapper. Contains page content -->
<section class="content">
  <div class="container">
    <div class="row">
      <div class="col-md-6 col-12">
        <form class="card card-body" method="post" action="/cs/transaksi/ShopeePay/2">
          {{csrf_field()}}
          <div class="form-group">
            <input required="" autocomplete="off" type="text" class="form-control" id="no_pelanggan" name="no_pelanggan" pattern="\d*" placeholder="No ShopeePay. Contoh : 085847801933">
          </div>
          <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block">Selanjutnya</button>
          </div>
        </form>
      </div>
      <div class="col-md-6 col-12">
        <div class="card">
          <div class="card-body p-2">
            <p class="text-center m-0"><strong>Riwayat Transaksi</strong></p>
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
            <div class=""><a href="/cs/transaksi/{{$mts->trxid_api}}" class="btn btn-primary btn-block">Lihat Bukti</a></div>
            @elseif($mts->status == "SUCCESS")
            <span class="float-right text-success text-uppercase"><strong>sukses</strong></span>
            <br><div>{{$mts -> phone}}</div>
            <div>({{$mts -> code}})</div>
            <div class="text-success text-justify">{{$mts -> note}}</div>
            <div class=""><a href="/cs/transaksi/{{$mts->trxid_api}}" class="btn btn-primary btn-block">Lihat Bukti</a></div>
            @else
            <span class="float-right text-warning text-uppercase"><strong>diproses</strong></span>
            <br><div>{{$mts -> phone}}</div>
            <div>({{$mts -> code}})</div>
            <div class="text-warning text-justify">{{$mts -> note}}</div>
            <div class=""><a href="/cs/transaksi/update/{{$mts->trxid_api}}" class="btn btn-warning btn-block">Refresh</a></div>
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

