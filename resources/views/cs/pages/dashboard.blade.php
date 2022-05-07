@extends('cs/layouts/main')

@section('title','adx-pay | Dashboard')

@section('content-header', 'Dashboard')

@section('content')
<!-- Content Wrapper. Contains page content -->
<section class="content">
  <div class="container">
    <div class="row">
      <div class="col-lg-6">
        <!-- small card -->
        <div class="small-box bg-info">
          <div class="inner">
            <h3>Rp. {{number_format($saldo, 0, '', '.')}}</h3>

            <p>Total Saldo Utama</p>
          </div>
          <div class="icon">
            <i class="fas fa-wallet"></i>
          </div>
          <a href="#" class="small-box-footer">
            More info <i class="fas fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
      <!-- ./col -->

      <div class="col-lg-6">
        <!-- small card -->
        <div class="small-box bg-success">
          <div class="inner">
            <h3>Rp. {{number_format($TotalsaldoCustomer, 0, '', '.')}}</h3>

            <p>Total Saldo Semua Customer</p>
          </div>
          <div class="icon">
            <i class="fas fa-wallet"></i>
          </div>
          <a href="#" class="small-box-footer">
            More info <i class="fas fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
      <!-- ./col -->

      <div class="col-lg-6">
        <!-- small card -->
        <div class="small-box bg-warning">
          <div class="inner">
            <h3>Rp. {{number_format($AkumulasiSaldoCS, 0, '', '.')}}</h3>

            <p>Saldo Customer Services</p>
          </div>
          <div class="icon">
            <i class="fas fa-wallet"></i>
          </div>
          <a href="#" class="small-box-footer">
            More info <i class="fas fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
      <!-- ./col -->

      <div class="col-lg-6">
        <!-- small card -->
        <div class="small-box bg-danger">
          <div class="inner">
            <h3>Rp. {{number_format($TotalHutangCustomer, 0, '', '.')}}</h3>

            <p>Total Piutang</p>
          </div>
          <div class="icon">
            <i class="fas fa-file-invoice-dollar"></i>
          </div>
          <a href="/cs/piutang" class="small-box-footer">
            More info <i class="fas fa-arrow-circle-right"></i>
          </a>
        </div>
      </div>
      <!-- ./col -->
    </div>

    <div class="container">
      <div class="row justify-content-center">
        <div class="col-md-8">
          <div class="card text-white bg-dark mb-3">
            <div class="card-header text-center"><h3>Transaksi Digital</h3></div>
            <div class="card-body">
              <div class="row">
                <!-- The Modal Comming Soon-->
                <div class="modal" id="CommingSoon">
                  <div class="modal-dialog">
                    <div class="modal-content">
                      <!-- Modal body -->
                      <div class="modal-body text-center">
                        <h1>COMMING SOON...</h1>
                        <img width="400px" src="{{asset('lte/dist/img/commingsoon.jpg')}}">
                      </div>
                    </div>
                  </div>
                </div>
                
                <a href="/cs/transaksi/PIU/1" class="col-6 col-md-3">
                  <div class="card bg-light mb-3">
                    <img style="background-color: white;" src="{{asset('lte/dist/img/logo/pulsa.png')}}" class="card-img-top rounded-top">
                    <h6 class="text-center">Pulsa Isi Ulang</h6>
                  </div>
                </a>

                <a href="/cs/transaksi/PD/1" class="col-6 col-md-3">
                  <div class="card bg-light mb-3">
                    <img style="background-color: white;" src="{{asset('lte/dist/img/logo/kuota.png')}}" class="card-img-top rounded-top">
                    <h6 class="text-center">Data Internet</h6>
                  </div>
                </a>

                <a href="/cs/transaksi/TL/1" class="col-6 col-md-3">
                  <div class="card bg-light mb-3">
                    <img style="background-color: white;" src="{{asset('lte/dist/img/logo/PLN.png')}}" class="card-img-top rounded-top">
                    <h6 class="text-center">Token Listrik</h6>
                  </div>
                </a>

                <a href="/cs/transaksi/Bank/1" class="col-6 col-md-3">
                  <div class="card bg-light mb-3">
                    <img style="background-color: white;" src="{{asset('lte/dist/img/logo/Bank.png')}}" class="card-img-top rounded-top">
                    <h6 class="text-center">Transfer Bank</h6>
                  </div>
                </a>

                <a href="/cs/transaksi/DANA/1" class="col-6 col-md-3">
                  <div class="card bg-light mb-3">
                    <img style="background-color: white;" src="{{asset('lte/dist/img/logo/dana.png')}}" class="card-img-top rounded-top">
                    <h6 class="text-center">Topup DANA</h6>
                  </div>
                </a>

                <a href="/cs/transaksi/OVO/1" class="col-6 col-md-3">
                  <div class="card bg-light mb-3">
                    <img style="background-color: white;" src="{{asset('lte/dist/img/logo/OVO.png')}}" class="card-img-top rounded-top">
                    <h6 class="text-center">Topup OVO</h6>
                  </div>
                </a>
                

                <a href="/cs/transaksi/ShopeePay/1" class="col-6 col-md-3">
                  <div class="card bg-light mb-3">
                    <img style="background-color: white;" src="{{asset('lte/dist/img/logo/ShopeePay.png')}}" class="card-img-top rounded-top">
                    <h6 class="text-center">Topup ShopeePay</h6>
                  </div>
                </a>

                <a href="/cs/transaksi/GoPay/1" class="col-6 col-md-3">
                  <div class="card bg-light mb-3">
                    <img style="background-color: white;" src="{{asset('lte/dist/img/logo/gopay.png')}}" class="card-img-top rounded-top">
                    <h6 class="text-center">Topup GoPay</h6>
                  </div>
                </a>

                <a href="/cs/transaksi/LinkAja/1" class="col-6 col-md-3">
                  <div class="card bg-light mb-3">
                    <img style="background-color: white;" src="{{asset('lte/dist/img/logo/linkaja.png')}}" class="card-img-top rounded-top">
                    <h6 class="text-center">Topup LinkAja</h6>
                  </div>
                </a>

                <a href="/cs/transaksi/wifi-id/1" class="col-6 col-md-3">
                  <div class="card bg-light mb-3">
                    <img style="background-color: white;" src="{{asset('lte/dist/img/logo/wifi-id.png')}}" class="card-img-top rounded-top">
                    <h6 class="text-center">Akses Wifi ID</h6>
                  </div>
                </a>

                <a href="/cs/transaksi/vgml/2" class="col-6 col-md-3">
                  <div class="card bg-light mb-3">
                    <img style="background-color: white;" src="{{asset('lte/dist/img/logo/mobile_legend.jpg')}}" class="card-img-top rounded-top">
                    <h6 class="text-center">Diamond Mobile Legend</h6>
                  </div>
                </a>

                <a href="/cs/transaksi/vgff/2" class="col-6 col-md-3">
                  <div class="card bg-light mb-3">
                    <img style="background-color: white;" src="{{asset('lte/dist/img/logo/free_fire.jpg')}}" class="card-img-top rounded-top">
                    <h6 class="text-center">Diamond Free Fire</h6>
                  </div>
                </a>
                

                <!-- comming soon -->
                <!-- <a data-toggle="modal" data-target="#CommingSoon" style="opacity: 10%;" href="#" class="col-6 col-md-3">
                  <div class="card bg-light mb-3">
                    <img style="background-color: white;" src="{{asset('lte/dist/img/logo/gopay.png')}}" class="card-img-top rounded-top">
                    <h6 class="text-center">Topup Saldo Gopay</h6>
                  </div>
                </a> -->
                <!-- end comming soon -->

              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="container mt-3">
      <div class="row justify-content-center">
        <div class="col-md-6 col-12">
          <div class="card">
            <div class="card-body p-2">
              <p class="text-center m-0"><strong>Transaksi Hari Ini ({{$jtrxThisDay}})</strong></p>
            </div>
          </div>

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
              <div class=""><a href="/cs/transaksi/{{$mts->trxid_api}}" class="btn btn-primary btn-block">Lihat Bukti</a></div>
              @elseif($mts->status == "SUCCESS")
              <span class="float-right text-success text-uppercase"><strong>sukses</strong></span>
              <br><div>{{$mts -> phone}}</div>
              <div>({{$mts -> code}}) <p class="badge badge-success">from {{$mts -> username}}</p></div>
              <div class="text-success text-justify">{{$mts -> note}}</div>
              <div class=""><a href="/cs/transaksi/{{$mts->trxid_api}}" class="btn btn-primary btn-block">Lihat Bukti</a></div>
              @else
              <span class="float-right text-warning text-uppercase"><strong>diproses</strong></span>
              <br><div>{{$mts -> phone}}</div>
              <div>({{$mts -> code}}) <p class="badge badge-warning">from {{$mts -> username}}</p></div>
              <div class="text-warning text-justify">{{$mts -> note}}</div>
              @endif
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>

    <div class="container mt-3">
      <div class="row justify-content-center">
        <div class="col-md-6 col-12">
          <div class="card">
            <div class="card-body p-2">
              <p class="text-center m-0"><strong>Riwayat Transaksi</strong></p>
            </div>
          </div>

          @foreach($mutasi as $mts)
          <div class="card">
            <div class="card-body p-2">
              <?php $date = new DateTime($mts->created_at) ?>
              <span class="float-left">{{$date->format('d F Y   G:i')}} WITA</span>

              @if($mts->status == "FAILED") 
              <span class="float-right text-danger text-uppercase"><strong>gagal</strong></span>
              <br><div>{{$mts -> phone}}</div>
              <div>({{$mts -> code}}) <p class="badge badge-danger">from {{$mts -> username}}</p></div>
              <div class="text-danger text-justify">{{$mts -> note}}</div>
              <div class=""><a href="/cs/transaksi/{{$mts->trxid_api}}" class="btn btn-primary btn-block">Lihat Bukti</a></div>
              @elseif($mts->status == "SUCCESS")
              <span class="float-right text-success text-uppercase"><strong>sukses</strong></span>
              <br><div>{{$mts -> phone}}</div>
              <div>({{$mts -> code}}) <p class="badge badge-success">from {{$mts -> username}}</p></div>
              <div class="text-success text-justify">{{$mts -> note}}</div>
              <div class=""><a href="/cs/transaksi/{{$mts->trxid_api}}" class="btn btn-primary btn-block">Lihat Bukti</a></div>
              @else
              <span class="float-right text-warning text-uppercase"><strong>diproses</strong></span>
              <br><div>{{$mts -> phone}}</div>
              <div>({{$mts -> code}}) <p class="badge badge-warning">from {{$mts -> username}}</p></div>
              <div class="text-warning text-justify">{{$mts -> note}}</div>
              @endif
            </div>
          </div>
          @endforeach
        </div>
      </div>
    </div>
  </div>
</section>
<!-- /.content-wrapper -->
@endsection

