@extends('cs/layouts/main')

@section('title') adx-pay | Transaksi Pulsa Isi Ulang @endsection

@section('content-header') Transaksi Pulsa Isi Ulang @endsection

@section('content')
<!-- Content Wrapper. Contains page content -->
<section class="content">
  <div class="container">
    <div class="row">
      <div class="col-md-6 col-12">
        <form class="card card-body" method="post" action="/cs/transaksi/PIU/2">
          {{csrf_field()}}
          <div class="input-group">
            <input id="no_tujuan" required="" autocomplete="off" type="text" class="form-control" name="no_hp" minlength="11" maxlength="14" pattern="\d*" placeholder="No Telepon. Contoh : 085847801933">
            <div class="input-group-append">
              <button type="submit" class="btn btn-primary">Selanjutnya</button>
            </div>
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
            <div class=""><a href="/cs/transaksi/PIU/{{$mts->trxid_api}}" class="btn btn-primary btn-block">Lihat Bukti</a></div>
            @elseif($mts->status == "SUCCESS")
            <span class="float-right text-success text-uppercase"><strong>sukses</strong></span>
            <br><div>{{$mts -> phone}}</div>
            <div>({{$mts -> code}})</div>
            <div class="text-success text-justify">{{$mts -> note}}</div>
            <div class=""><a href="/cs/transaksi/PIU/{{$mts->trxid_api}}" class="btn btn-primary btn-block">Lihat Bukti</a></div>
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

