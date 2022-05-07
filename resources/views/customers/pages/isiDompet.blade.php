@extends('customers/layouts/main')

@section('title','adx-pay | Isi Dompet')

@section('content-header', 'Isi Dompet')

@section('content')
<!-- Content Wrapper. Contains page content -->
<section class="content">
  <div class="container">
    <div class="row">
      <div class="col-lg-6">
        <div class="small-box bg-info">
          <div class="inner">
            <h3>Rp. {{number_format($saldo, 0, '', '.')}}</h3>

            <p>Dompet Saya</p>
          </div>
          <div class="icon">
            <i class="fas fa-wallet"></i>
          </div>
        </div>
      </div>
    </div>
    <div class="row" id="info_pembayaran_deposit">
      <div class="col-lg-12">
        <div class="alert alert-warning alert-dismissible">
          <h5><i class="icon fas fa-info"></i>Info Pembayaran</h5>
          Silahkan Transfer sebesar Rp. <span id="jumlahIsiValue"></span> ke rekening BRI 011-401-021881505 a.n I Made Yoga Arimbawa, atau ke Dompet Digital 085847801933 (DANA/OVO/LinkAja/Gopay/Shopeepay). Kemudian kirim bukti transfer di sini:
          <a href="https://api.whatsapp.com/send?phone=6285847801933" class="badge badge-warning mb-3">Kirim</a> 
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-12 p-2">
        <form method="post" action="/customers/transaksi/isi-dompet/proses">
          {{csrf_field()}}
          <div class="input-group">
            <input required="" id="jumlah_isi" autocomplete="off" type="number" class="form-control" name="jumlah_isi" placeholder="Masukan Jumlah Pengisian">
            <div class="input-group-append">
              <button type="button" onclick="tampilkanInfoPembayaranDepo();" class="btn btn-primary">Confirm</button>
            </div>
          </div>     
        </form>
      </div>
      <div class="col-md-6 p-2">
        <div class="card">
          <div class="card-body p-2">
            <p class="text-center m-0"><strong>Transaksi</strong></p>
          </div>
        </div>

        @foreach($mutasi as $mts)
        <div class="card">
          <div class="card-body p-2">
            <span class="float-left">{{$mts->updated_at}}</span>

            @if($mts->status == 'FAILED') 
            <span class="float-right text-danger text-uppercase"><strong>gagal</strong></span>
            @elseif($mts->status == 'REFUND') 
            <span class="float-right text-primary text-uppercase"><strong>refund</strong></span>
            @elseif($mts->status == 'PENDING') 
            <span class="float-right text-warning text-uppercase"><strong>pending</strong></span>
            @elseif($mts->status == 'SUCCESS') 
            <span class="float-right text-success text-uppercase"><strong>sukses</strong></span>
            @endif

            <br><div>{{$mts -> phone}}</div>
            <div>({{$mts -> code}})</div>
            @if($mts->status == 'FAILED') 
            <div class="text-danger text-justify">{{$mts -> note}}</div>
            @elseif($mts->status == 'REFUND') 
            <div class="text-primary text-justify">{{$mts -> note}}</div>
            @elseif($mts->status == 'PENDING')
            @if($mts->bukti_transfer == null) 
            <form action="/customers/transaksi/isi-dompet/upload-bukti/{{$mts->id}}" method="post" enctype="multipart/form-data">
              @csrf
              <div class="text-warning text-justify">{{$mts -> note}} di sini:</div>
              <div class="custom-file">
                <input accept="image/*" required="" name="bukti_transfer" type="file" class="custom-file-input" id="customFile">
                <label class="custom-file-label" for="customFile">Upload Bukti Transfer <span class="text-danger">*berupa gambar saja</span></label>
              </div>
              <button type="submit" class="btn btn-primary mt-2">Confirm</button>
            </form>
            @else
            <div class="text-warning text-justify">Bukti transfer terupload, silahkan tunggu dompet terisi, terima kasih {{session()->get('dataLoginCustomers')['name']}}</div>
            <!-- The Modal -->
            <div class="modal" id="bukti_transfer">
              <div class="modal-dialog">
                <div class="modal-content">

                  <!-- Modal Header -->
                  <div class="modal-header">
                    <h4 class="modal-title">Bukti Transfer</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>

                  <!-- Modal body -->
                  <div class="modal-body">
                    <div class="text-center">  
                      <img width="100%" src="{{asset('bukti_transfer/'.$mts->bukti_transfer)}}">
                    </div>
                    <form action="/customers/transaksi/isi-dompet/upload-bukti/{{$mts->id}}" method="post" enctype="multipart/form-data">
                      @csrf
                      <div class="custom-file mt-3">
                        <input accept="image/*" required="" name="bukti_transfer" type="file" class="custom-file-input" id="customFile">
                        <label class="custom-file-label" for="customFile">Upload Ulang <span class="text-danger">*berupa gambar saja</span></label>
                      </div>
                    </div>

                    <!-- Modal footer -->
                    <div class="modal-footer">
                      <button type="submit" class="btn btn-primary">Confirm</button>
                    </form>
                  </div>

                </div>
              </div>
            </div>
            <button class="btn btn-primary" data-toggle="modal" data-target="#bukti_transfer">Lihat Bukti transfer</button>
            <a href="https://api.whatsapp.com/send?phone=6285847801933&text=Halo Yoga, Saya sudah mengajukan deposit saldo/isi dompet, silahkan dicek!" class="btn btn-warning">Ingatkan!</a>
            @endif
            @elseif($mts->status == 'SUCCESS') 
            <div class="text-success text-justify">{{$mts -> note}}</div>
            @endif
          </div>
        </div>
        @endforeach
      </div>
    </div>
  </div>
</section>
<script type="text/javascript">
  $('#info_pembayaran_deposit').hide();
  let jumlahIsi;
  $('#jumlah_isi').on('keyup', function(){
    jumlahIsi = $('#jumlah_isi').val();
    $('#jumlahIsiValue').html(jumlahIsi);
    if(jumlahIsi==""){
      $('#jumlah_isi').addClass('is-invalid');
    }else{
      $('#jumlah_isi').removeClass('is-invalid');
    }
    return jumlahIsi;
  });
  function tampilkanInfoPembayaranDepo() {
    if (jumlahIsi!=null) {
      $('#info_pembayaran_deposit').show();
    }else{
      Swal.fire({
        icon: 'error',
        title: 'Jumlah Pengisian Kosong!',
        text: 'Silahkan isi jumlah pengisian saldo'
      })
      return false;
    }
  }
</script>
<!-- /.content-wrapper -->
@endsection

