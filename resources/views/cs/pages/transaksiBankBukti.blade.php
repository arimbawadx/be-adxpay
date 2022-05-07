@extends('cs/layouts/main')

@section('title') adx-pay | Bukti Transaksi @endsection

@section('content-header') Bukti Transaksi @endsection

@section('content')
<!-- Content Wrapper. Contains page content -->
<section class="content">
  <div class="container">
    <div id="html-content-holder" class="row justify-content-center">
      <div class="col-md-6 col-12">
        <div class="card mt-5">
          <div class="card-body">
            @if($GetPrabayarHistory['status'] == "SUCCESS")
            <div class="row text-center justify-content-center">
              <div class="col-md-6">
                <i id="btnConvert" style="font-size: 5em; border: 2px solid white; border-radius: 50%; margin-top:-250px;" class="icon fa fa-check-circle bg-white text-primary"></i>
              </div>
            </div>
            <h4 class="text-center mt-3">Transaksi Berhasil</h4>
            @elseif($GetPrabayarHistory['status'] == "PENDING")
            <div class="row text-center justify-content-center">
              <div class="col-md-6">
                <i id="btnConvert" style="font-size: 5em; border: 2px solid white; border-radius: 50%; margin-top:-250px;" class="icon fa fa-sync-alt bg-white text-warning"></i>
              </div>
            </div>
            <h4 class="text-center mt-3">Transaksi Diproses</h4>
            @elseif($GetPrabayarHistory['status'] == "FAILED")
            <div class="row text-center justify-content-center">
              <div class="col-md-6">
                <i id="btnConvert" style="font-size: 5em; border: 2px solid white; border-radius: 50%; margin-top:-250px;" class="icon fa fa-times-circle bg-white text-danger"></i>
              </div>
            </div>
            <h4 class="text-center mt-3">Transaksi Gagal</h4>
            @endif
            <div class="container mt-3">
              <div class="row">
                <div class="col-6 text-left"><p>Tanggal</p></div>
                <div class="col-6 text-right"><p>{{$GetPrabayarHistory['created_at']}}</p></div>
              </div>
              <div class="row">
                <div class="col-6 text-left"><p>No Ref</p></div>
                <div class="col-6 text-right"><p>{{$GetPrabayarHistory['ref_id']}}</p></div>
              </div>
              <div class="row">
                <div class="col-12"><hr style="border:1px dashed white;">
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <p class="text-center"><strong>{{$GetPrabayarHistory['product_name']}}</strong></p>
                </div>
              </div>
              <div class="row">
                <div class="col-12"><hr style="border:1px dashed white;">
                </div>
              </div>
              <div class="row">
                <div class="col-6 text-left"><p>ID Produk</p></div>
                <div class="col-6 text-right"><p>{{$GetPrabayarHistory['product_id']}}</p></div>
              </div>
              <div class="row">
                <div class="col-6 text-left"><p>No HP</p></div>
                <div class="col-6 text-right"><p>{{$GetPrabayarHistory['destination']}}</p></div>
              </div>
              <?php 
              $message=explode(". ", $GetPrabayarHistory['message']); 
              $keterangan=explode("Keterangan : ", $message[1]); 
              ?>
              @if($GetPrabayarHistory['status'] == "SUCCESS")
              <div class="row">
                <div class="col-6 text-left"><p>SN</p></div>
                <div class="col-6 text-right"><p>{{$GetPrabayarHistory['serial_number']}}</p></div>
              </div>
              @else
              <div class="row">
                <div class="col-6 text-left"><p>Keterangan</p></div>
                <div class="col-6 text-right"><p>{{$keterangan[1]}}</p></div>
              </div>
              @endif
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<script type="text/javascript">
 $("#btnConvert").on('click', function () {
  var trxid_api = <?= $trxid_api ?>;
  var filename = "BuktiTransaksi_" + trxid_api;
  html2canvas(document.getElementById("html-content-holder")).then(function (canvas) {                   
   var anchorTag = document.createElement("a");
   document.body.appendChild(anchorTag);
   anchorTag.download = filename+".png";
   anchorTag.href = canvas.toDataURL();
   anchorTag.target = '_blank';
   anchorTag.click();
 });
});
</script>
<!-- /.content-wrapper -->
@endsection

