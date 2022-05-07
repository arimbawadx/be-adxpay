<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="{{ asset('lte/dist/img/favicon.ico') }}">
  <title>adx-pay | Daftar</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome Icons -->
  <link rel="stylesheet" href="{{ asset('lte/plugins/fontawesome-free/css/all.min.css') }}">
  <!-- overlayScrollbars -->
  <link rel="stylesheet" href="{{ asset('lte/plugins/overlayScrollbars/css/OverlayScrollbars.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('lte/dist/css/adminlte.min.css') }}">
  <!-- datatables -->
  <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.3/css/dataTables.bootstrap4.min.css">
  <link rel="icon" type="image/png" href="{{asset('lte/dist/img/bit.png')}}">
  <!-- <link rel="manifest" href="{{asset('manifest/manifest.json')}}"> -->
  <!-- Bootstrap CSS -->  
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.3/css/bootstrap.min.css" integrity="sha384-MCw98/SFnGE8fJT3GXwEOngsV7Zt27NXFoaoApmYm81iuXoPkFOJwJ8ERdknLPMO" crossorigin="anonymous">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css">
  <!-- REQUIRED SCRIPTS -->
  <!-- jQuery first, then Popper.js, then Bootstrap JS -->
  <script src="{{ asset('lte/plugins/jquery/jquery.min.js') }}"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.3/umd/popper.min.js" integrity="sha384-ZMP7rVo3mIykV+2+9J3UJ46jBk0WLaUAdn689aCwoqbBJiSnjAK/l8WvCWPIPm49" crossorigin="anonymous"></script>
  <script src="{{ asset('lte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <!-- overlayScrollbars -->
  <script src="{{ asset('lte/plugins/overlayScrollbars/js/jquery.overlayScrollbars.min.js') }}"></script>
  <!-- AdminLTE App -->
  <script src="{{ asset('lte/dist/js/adminlte.js') }}"></script>
  <!-- html2canvas -->
  <script src="http://html2canvas.hertzen.com/dist/html2canvas.js"></script>
  <!-- datatables -->
  <script src="https://cdn.datatables.net/1.11.3/js/jquery.dataTables.min.js"></script>
  <script src="https://cdn.datatables.net/1.11.3/js/dataTables.bootstrap4.min.js"></script>
  <!-- Select2 -->
  <link rel="stylesheet" href="{{ asset('lte/plugins/select2/css/select2.css')}}">
  <link rel="stylesheet" href="{{ asset('lte/plugins/select2/css//select2-bootstrap4.min.css')}}">
  <script src="{{ asset('lte/plugins/select2/js/select2.js') }}"></script>
  <!-- ChartJS -->
  <script src="{{ asset('lte/plugins/chart.js/Chart.min.js') }}"></script>
  <!-- Sweet Alert 2-->
  <script src="{{ asset('lte/plugins/sweetalert2/sweetalert2.all.js') }}"></script>
  <!-- Loader CSS -->
  <style type="text/css">
    div#preloader { position: fixed; left: 0; top: 0; z-index: 999; width: 100%; height: 100%; overflow: visible; background: #ffffff url('https://manifest.arimbawadx.com/loader/loader2.gif') no-repeat center center; }
  </style>
</head>
<body class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
  <!-- <div id="preloader"></div> -->
  <div class="container">
    <div id="html-content-holder" class="row justify-content-center">
      <div class="col-md-6 col-12">
        <div class="card mt-5">
          <div class="card-body">
            <div class="row text-center justify-content-center">
              <div class="col-md-6">
                <i id="btnConvert" style="font-size: 5em; border: 2px solid white; border-radius: 50%; margin-top:-250px;" class="icon fa fa-check-circle bg-white text-primary"></i>
              </div>
            </div>
            <h4 class="text-center mt-3">Pendaftaran Berhasil</h4>
            <div class="container mt-3">
              <div class="row">
                <div class="col-12">
                  <div class="alert alert-warning alert-dismissible">
                    <h5><i class="icon fas fa-info"></i> Info</h5>
                    Salin dan simpan code akses dengan baik untuk melakukan login pada sistem atau screenshot halaman ini!
                  </div>
                </div>
              </div>
              <div class="row">
                <div class="col-6 text-left"><p>Tanggal</p></div>
                <div class="col-6 text-right"><p>{{date('Y-m-d')}}</p></div>
              </div>
              <div class="row">
                <div class="col-6 text-left"><p>Nama</p></div>
                <div class="col-6 text-right"><p>{{$name}}</p></div>
              </div>
              <div class="row">
                <div class="col-6 text-left"><p>No HP</p></div>
                <div class="col-6 text-right"><p>{{$phone_number}}</p></div>
              </div>
              <div class="row">
                <div class="col-12"><hr style="border:1px dashed white;">
                </div>
              </div>
              <div class="row">
                <div class="col-12 text-center"><h6>Code Akses : {{$random}}</h6>
                </div>
              </div>
              <div class="row">
                <div class="col-12"><hr style="border:1px dashed white;">
                </div>
              </div>
              <div class="row">
                <div class="col-12">
                  <a href="/" class="btn btn-primary btn-block">Login</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
  <script type="text/javascript">
    $(document).ready( function () {
       // loader
       $(window).on('load',function(){
        $('#preloader').fadeOut('slow',function(){
          $(this).remove();
        });
      });
    </script>
    <script type="text/javascript">
     $("#btnConvert").on('click', function () {
      var trxid_api = <?= $random ?>;
      var filename = "Code_" + trxid_api;
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
  @include('sweetalert::alert')
</body>
</html>
