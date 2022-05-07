@extends('customers/layouts/main')

@section('title','adx-pay | Profile')

@section('content-header', 'Profile')

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
      @if(session()->get('dataLoginCustomers')['verified']==0)
      <?php 
      $nama = session()->get('dataLoginCustomers')['name'];
      $username = session()->get('dataLoginCustomers')['username'];
      ?>
      <div class="col-lg-12">
        <div class="alert alert-danger alert-dismissible">
          <h5><i class="icon fas fa-info"></i>Fitur Hutang Belum Aktif!</h5>
          Silahkan ajukan verifikasi untuk centang biru agar bisa mengaktifkan fitur hutang dan validasi data diri. <a class="badge badge-primary" href="https://api.whatsapp.com/send?phone=6285847801933&text=Halo Yoga, Saya {{$nama}} dengan username {{$username}} ingin mengajukan verifikasi diri.">Ajukan</a>
        </div>
      </div>
      @endif
    </div>
    <div class="row">
      <div class="col-lg-12">
        <!-- Widget: user widget style 1 -->
        <div class="card card-widget widget-user">
          <!-- Add the bg color to the header using any of the bg-* classes -->
          <div class="widget-user-header bg-primary">

          </div>
          <div class="widget-user-image">
            @if($profile == null)
            <img data-toggle="modal" data-target="#changeProfile" class="img-circle elevation-2" src="{{ asset('lte/dist/img/noprofil.jpg') }}" alt="User Avatar">
            @else
            <img style="width: 100px;
            height: 100px;
            background-position: center center;
            background-repeat: no-repeat;" data-toggle="modal" data-target="#changeProfile" class="img-circle elevation-2" src="{{ asset('lte/dist/img/profile/'.$profile) }}" alt="User Avatar">
            @endif
            <!-- The Modal -->
            <div class="modal" id="changeProfile">
              <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">

                  <!-- Modal Header -->
                  <div class="modal-header">
                    <h4 class="modal-title">Ganti Profil</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>

                  <!-- Modal body -->
                  <div class="modal-body">
                    <div class="text-center">  
                      @if($profile == null)
                      <img width="100%" src="{{ asset('lte/dist/img/noprofil.jpg') }}">
                      @else
                      <img width="100%" src="{{ asset('lte/dist/img/profile/'.$profile) }}">
                      @endif
                    </div>
                    <form action="/customers/ganti-profile/{{session()->get('dataLoginCustomers')['id']}}" method="post" enctype="multipart/form-data">
                      @csrf
                      <div class="custom-file mt-3">
                        <input accept="image/*" required="" name="profile" type="file" class="custom-file-input" id="customFile">
                        <label class="custom-file-label" for="customFile">Upload gambar</label>
                      </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-primary btn-block">Perbaharui</button>
                    </form>
                  </div>

                </div>
              </div>
            </div>
          </div>
          <div class="card-footer" bis_skin_checked="1">
            <div class="row justify-content-center text-center">
              <div class="col-md-12 mb-3">
                <h3 class="widget-user-username">{{session()->get('dataLoginCustomers')['name']}} @if(session()->get('dataLoginCustomers')['verified']==1)<i style="font-size: 18px;" class="bi bi-patch-check-fill text-primary"></i>@endif</h3>
                <p>({{session()->get('dataLoginCustomers')['username']}})</p>
              </div>
            </div>
            <div class="row mb-5" bis_skin_checked="1">
              <div class="col-4 border-right" bis_skin_checked="1">
                <div class="description-block" bis_skin_checked="1">
                  <h5 class="description-header">{{singkat_aja($saldo)}}</h5>
                  <span class="description-text">Dompet</span>
                  <a href="/customers/transaksi/isi-dompet" class="mt-2 btn btn-outline-primary btn-block btn-sm">Deposit</a>
                </div>
                <!-- /.description-block -->
              </div>
              <!-- /.col -->
              <div class="col-4 border-right" bis_skin_checked="1">
                <div class="description-block" bis_skin_checked="1">
                  <h5 class="description-header">{{singkat_aja($point)}}</h5>
                  <span class="description-text">Coin</span>
                  <a href="#" data-toggle="modal" data-target="#TarikPoint" class="mt-2 btn btn-outline-primary btn-block btn-sm">Tarik</a>
                  <!-- The Modal Tarik Point-->
                  <div class="modal" id="TarikPoint">
                    <div class="modal-dialog">
                      <div class="modal-content">

                        <!-- Modal Header -->
                        <div class="modal-header">
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>

                        <!-- Modal body -->
                        <div class="modal-body">
                          @if($point>0)
                          <div class="row">
                            <div class="col-12">
                              <div class="small-box bg-warning">
                                <div class="inner">
                                  <h3>C. {{number_format($point, 0, '', '.')}}</h3>
                                  <p>Coin Saya</p>
                                </div>
                                <div class="icon">
                                  <i class="fas fa-donate"></i>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="alert alert-warning alert-dismissible">
                            <h5><i class="icon fas fa-donate"></i> Info Coin Anda</h5>
                            <strong>C. {{number_format($point, 0, '', '.')}}</strong> setara dengan <strong>Rp. {{number_format($point/10, 0, '', '.')}}, 00</strong>. <br> Yakin tarik?
                          </div>
                          <form method="post" action="/customers/tarik-coin">
                            @csrf                      
                            <button type="submit" class="btn btn-primary">Tarik</button>
                          </form>
                          @else
                          <div class="alert alert-danger alert-dismissible">
                            <h5><i class="icon fas fa-donate"></i> Info Coin Anda</h5>
                            Jangan Halu, anda tidak punya coin! 
                          </div>
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /.description-block -->
              </div>
              <!-- /.col -->
              <div class="col-4" bis_skin_checked="1">
                <div class="description-block" bis_skin_checked="1">
                  <h5 class="description-header">{{singkat_aja($hutang)}}</h5>
                  <span class="description-text">Hutang</span>
                  <a href="#" data-toggle="modal" data-target="#HutangSaya" class="mt-2 btn btn-outline-primary btn-block btn-sm">Detail</a>
                  <!-- The Modal Hutang Saya-->
                  <div class="modal" id="HutangSaya">
                    <div class="modal-dialog">
                      <div class="modal-content">

                        <!-- Modal Header -->
                        <div class="modal-header">
                          <div class="modal-title">Hutang Saya</div>
                          <button type="button" class="close" data-dismiss="modal">&times;</button>
                        </div>

                        <!-- Modal body -->
                        <div class="modal-body">
                          @if($dataHutang->first() != null)
                          <div class="row">
                            <div class="col-12">
                              <!-- small card -->
                              <div class="small-box bg-danger">
                                <div class="inner">
                                  <h3>Rp. {{number_format($hutang, 0, '', '.')}}</h3>
                                  <p>Hutang Saya</p>
                                </div>
                                <div class="icon">
                                  <i class="fas fa-wallet"></i>
                                </div>
                              </div>
                            </div>
                          </div>
                          <div class="row" id="info_pembayaran_hutang">
                            <div class="col-lg-12">
                              <div class="alert alert-warning alert-dismissible">
                                <h5><i class="icon fas fa-info"></i> Info Pembayaran Hutang</h5>
                                Silahkan Transfer sebesar Rp. {{number_format($hutang, 0, '', '.')}} ke rekening BRI 011-401-021881505 a.n I Made Yoga Arimbawa, atau ke Dompet Digital 085847801933 (DANA/OVO/LinkAja/Gopay/Shopeepay). Kemudian kirim bukti transfer di sini:
                                <a href="https://api.whatsapp.com/send?phone=6285847801933" class="badge badge-warning mb-3">Kirim</a> 
                              </div>
                            </div>
                          </div>
                          <button type="button" onclick="tampilkanInfoPembayaran();" class="btn btn-success btn-sm btn-block mb-3">Bayar Hutang</button>
                          <table class="table table-responsive">
                            <thead class="thead-dark">
                              <tr>
                                <!-- <th scope="col">No</th> -->
                                <th scope="col">Tanggal</th>
                                <th scope="col">Hutang</th>
                                <th scope="col">Keterangan</th>
                                <th scope="col">Status</th>
                              </tr>
                            </thead>
                            <tbody>
                              @foreach($dataHutang as $i => $h)
                              <tr>
                                <!-- <td>{{$i+1}}</td> -->
                                <td>{{$h->created_at}}</td>
                                <td>Rp. {{number_format($h->sisa, 0, '', '.')}}</td>
                                <td>{{$h->keterangan}}</td>
                                <td class=<?php if ($h->status=="Belum Lunas"){echo "text-danger";}elseif($h->status=="Lunas"){echo "text-success";} ?>>{{$h->status}}</td>
                              </tr>
                              @endforeach
                            </tbody>
                          </table>
                          @elseif(session()->get('dataLoginCustomers')['verified']==0)
                          <div class="alert alert-danger alert-dismissible">
                            <h5><i class="icon fas fa-info"></i>Ajukan verifikasi diri!</h5>
                            Silahkan ajukan verifikasi untuk centang biru agar bisa mengaktifkan fitur hutang dan validasi data diri. <a class="badge badge-primary" href="https://api.whatsapp.com/send?phone=6285847801933&text=Halo Yoga, Saya {{$nama}} dengan username {{$username}} ingin mengajukan verifikasi diri.">Ajukan</a>
                          </div>
                          @else
                          <div class="alert alert-success alert-dismissible" bis_skin_checked="1">
                            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
                            <h5><i class="icon fas fa-check"></i>Belum Ada Hutang</h5>
                            Silahkan Belanja dan gunakan metode pembayaran "Bayar Nanti"
                          </div>
                          @endif
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
                <!-- /.description-block -->
              </div>
              <!-- /.col -->
            </div>
            <!-- /.row -->
          </div>
        </div>
        <!-- /.widget-user -->
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

