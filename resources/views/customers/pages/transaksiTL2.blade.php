@extends('customers/layouts/main')

@section('title') adx-pay | Transaksi Token Listrik @endsection

@section('content-header') Transaksi Token Listrik @endsection

@section('content')
<!-- Content Wrapper. Contains page content -->
<section class="content">
  <div class="container">
    <div class="row">
      <div class="col-md-6 col-12">
        <form class="card card-body" id="form-confirm" method="post" action="/cus/transaksi/TL/3">
          {{csrf_field()}}
          <input name="no_hp" value="{{$phone}}" type="hidden">
          <input name="operatorProduk" value="{{$idProduk}}" type="hidden">
          <div class="form-group">
            <label for="produk">Produk @if(session()->get('dataLoginCustomers')['verified']==0) <span class="badge badge-danger">Saldo : Rp. {{number_format($saldo->saldo, 0, '', '.')}}</span> @endif</label>
            <select id="produk" name="produk" required class="form-control">
              <option value="">Pilih</option>
              @foreach($GetPrabayarProduct as $pp)
              @if($pp->status=="ACTIVE")
              <option value="{{$pp->product_id}}">{{$pp->operator_name}} {{$pp->product_name}} | Harga Rp. {{number_format($pp->product_price+2000)}}</option>
              @endif
              @endforeach
            </select>
          </div>
          @if(session()->get('dataLoginCustomers')['verified']==0)
          <input type="hidden" id="metode_pembayaran" name="metode_pembayaran" required value="Dompet">
          @else
          <div class="form-group">
            <label for="metode_pembayaran">Metode Pembayaran</label>
            <select id="metode_pembayaran" name="metode_pembayaran" required class="form-control">
              <option value="">Pilih</option>
              <option value="Hutang">Bayar Nanti</option>
              <option value="Dompet">Dompet | Saldo Anda Rp. {{number_format($saldo->saldo, 0, '', '.')}}</option>
            </select>
          </div>
          @endif
          <button id="btn-trx-confirm" type="button" class="btn btn-primary">Kirim</button>
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
            <div class=""><a href="/cus/transaksi/TL/{{$mts->trxid_api}}" class="btn btn-primary btn-block">Lihat Bukti</a></div>
            @elseif($mts->status == "SUCCESS")
            <span class="float-right text-success text-uppercase"><strong>sukses</strong></span>
            <br><div>{{$mts -> phone}}</div>
            <div>({{$mts -> code}})</div>
            <div class="text-success text-justify">{{$mts -> note}}</div>
            <div class=""><a href="/cus/transaksi/TL/{{$mts->trxid_api}}" class="btn btn-primary btn-block">Lihat Bukti</a></div>
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

