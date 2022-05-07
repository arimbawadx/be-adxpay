@extends('customers/layouts/main')

@section('title','adx-pay | Dashboard')

@section('content-header', 'Dashboard')

@section('content')
<!-- Content Wrapper. Contains page content -->
<section class="content">
  <?php
  function singkat_aja($m, $presisi=1) {
    if ($m < 900) {
      $format_angka = number_format($m, $presisi);
      $simbol = '';
    } else if ($m < 900000) {
      $format_angka = number_format($m / 1000, $presisi);
      $simbol = 'K';
    } else if ($n < 900000000) {
      $format_angka = number_format($m / 1000000, $presisi);
      $simbol = 'Jt';
    } else if ($n < 900000000000) {
      $format_angka = number_format($m / 1000000000, $presisi);
      $simbol = 'M';
    } else {
      $format_angka = number_format($m / 1000000000000, $presisi);
      $simbol = 'T';
    }

    if ( $presisi > 0 ) {
      $pisah = '.' . str_repeat( '0', $presisi );
      $format_angka = str_replace( $pisah, '', $format_angka );
    }

    return $format_angka . $simbol;
  }
  ?>
  <div class="container">
    <div class="row">
     @if($point<=0)
     <div class="col-lg-12">
      <div class="alert alert-info alert-dismissible">
        <h5><i class="icon fas fa-info"></i> Info</h5>
        Silahkan melakukan transaksi untuk mendapatkan coin! 
      </div>
    </div>
    @endif
  </div>

  <div class="row justify-content-center">
    <div class="col-md-12">
      <div class="card mb-3">
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

            <a href="/cus/transaksi/PIU/1" class="col-4 col-md-3">
              <div class="card bg-light mb-3">
                <img style="background-color: white;" src="{{asset('lte/dist/img/logo/pulsa.png')}}" class="card-img-top rounded-top">
                <h6 class="text-center">Pulsa Isi Ulang</h6>
              </div>
            </a>

            <a href="/cus/transaksi/PD/1" class="col-4 col-md-3">
              <div class="card bg-light mb-3">
                <img style="background-color: white;" src="{{asset('lte/dist/img/logo/kuota.png')}}" class="card-img-top rounded-top">
                <h6 class="text-center">Data Internet</h6>
              </div>
            </a>

            <a href="/cus/transaksi/TL/1" class="col-4 col-md-3">
              <div class="card bg-light mb-3">
                <img style="background-color: white;" src="{{asset('lte/dist/img/logo/PLN.png')}}" class="card-img-top rounded-top">
                <h6 class="text-center">Token Listrik</h6>
              </div>
            </a>

            <a href="/cus/transaksi/Bank/1" class="col-4 col-md-3">
              <div class="card bg-light mb-3">
                <img style="background-color: white;" src="{{asset('lte/dist/img/logo/Bank.png')}}" class="card-img-top rounded-top">
                <h6 class="text-center">Transfer Bank</h6>
              </div>
            </a>

            <a href="/cus/transaksi/DANA/1" class="col-4 col-md-3">
              <div class="card bg-light mb-3">
                <img style="background-color: white;" src="{{asset('lte/dist/img/logo/dana.png')}}" class="card-img-top rounded-top">
                <h6 class="text-center">Topup DANA</h6>
              </div>
            </a>

            <a href="/cus/transaksi/OVO/1" class="col-4 col-md-3">
              <div class="card bg-light mb-3">
                <img style="background-color: white;" src="{{asset('lte/dist/img/logo/OVO.png')}}" class="card-img-top rounded-top">
                <h6 class="text-center">Topup OVO</h6>
              </div>
            </a>


            <a href="/cus/transaksi/ShopeePay/1" class="col-4 col-md-3">
              <div class="card bg-light mb-3">
                <img style="background-color: white;" src="{{asset('lte/dist/img/logo/ShopeePay.png')}}" class="card-img-top rounded-top">
                <h6 class="text-center">Topup ShopeePay</h6>
              </div>
            </a>

            <a href="/cus/transaksi/GoPay/1" class="col-4 col-md-3">
              <div class="card bg-light mb-3">
                <img style="background-color: white;" src="{{asset('lte/dist/img/logo/gopay.png')}}" class="card-img-top rounded-top">
                <h6 class="text-center">Topup GoPay</h6>
              </div>
            </a>

            <a href="/cus/transaksi/LinkAja/1" class="col-4 col-md-3">
              <div class="card bg-light mb-3">
                <img style="background-color: white;" src="{{asset('lte/dist/img/logo/linkaja.png')}}" class="card-img-top rounded-top">
                <h6 class="text-center">Topup LinkAja</h6>
              </div>
            </a>

            <a href="/cus/transaksi/wifi-id/1" class="col-4 col-md-3">
              <div class="card bg-light mb-3">
                <img style="background-color: white;" src="{{asset('lte/dist/img/logo/wifi-id.png')}}" class="card-img-top rounded-top">
                <h6 class="text-center">Akses Wifi ID</h6>
              </div>
            </a>

            <a href="/cus/transaksi/vgml/2" class="col-4 col-md-3">
              <div class="card bg-light mb-3">
                <img style="background-color: white;" src="{{asset('lte/dist/img/logo/mobile_legend.jpg')}}" class="card-img-top rounded-top">
                <h6 class="text-center">Diamond Mobile Legend</h6>
              </div>
            </a>
            
            <a href="/cus/transaksi/vgff/2" class="col-4 col-md-3">
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
    
  </section>
  <script type="text/javascript">
    $('#info_pembayaran_hutang').hide();
    function tampilkanInfoPembayaran() {
      $('#info_pembayaran_hutang').show();
    }
  </script>
  <!-- /.content-wrapper -->
  @endsection

