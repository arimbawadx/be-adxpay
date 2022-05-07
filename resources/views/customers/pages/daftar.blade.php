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
</head>
<body class="hold-transition dark-mode login-page">
  <div class="container login-box">
    <div class="row justify-content-center">
      <div class="col-md-4 col-12">
        <div class="card card-outline card-default">
          <div class="card-header text-center">
            <a href="#" class="h1"><b>adx</b> - pay</a>
            <p class="badge badge-warning">Daftar dulu yaa, Isi formulir berikut</p>
          </div>
          <div class="card-body">
            <form id="daftar-form" action="/customers/registering" method="post">
              {{ csrf_field() }}
              <div class="form-group">
                <input required="" autocomplete="off" type="text" class="form-control" id="nama" name="nama" placeholder="Masukan Nama">
              </div>
              <div class="form-group">
                <input required="" autocomplete="off" type="number" class="form-control" id="no_hp" name="no_hp" placeholder="Masukan No HP">
              </div>
              <div class="row">
                <div class="col-12 mb-3">
                  <button id="btn-daftar" type="button" class="btn btn-primary btn-block">Daftar</button>
                </div>
                <!-- /.col -->
                <div class="col-12">
                  <span>Sudah daftar? </span><a href="/"><span class="badge badge-primary">Masuk</span></a>
                </div>
                <!-- /.col -->
              </div>
            </form>
          </div>
          <!-- /.card-body -->
        </div>
      </div>
    </div>
  </div>

  <script type="text/javascript">
    // btn kirim validation
    $('#btn-daftar').on('click', function() {
      let selectJumlahPulsa = document.forms["daftar-form"]["nama"].value;
      let selectMetodePembayaran = document.forms["daftar-form"]["no_hp"].value;
      if (selectJumlahPulsa == "") {
        Swal.fire({
          icon: 'error',
          title: 'Nama Kosong!',
          text: 'Silahkan masukan Nama Anda!'
        })
        return false;
      }else if(selectMetodePembayaran == ""){
        Swal.fire({
          icon: 'error',
          title: 'No HP Kosong',
          text: 'Silahkan masukan no hp aktif'
        })
        return false;
      }else{
        document.getElementById("daftar-form").submit();
      }
    });
    // end btn kirim validation
  </script>
  @include('sweetalert::alert')
</body>
</html>
