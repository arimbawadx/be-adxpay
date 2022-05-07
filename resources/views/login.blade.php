<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="{{ asset('lte/dist/img/favicon.ico') }}">
  <title>adx-pay | Log in</title>

  <!-- Google Font: Source Sans Pro -->
  <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700&display=fallback">
  <!-- Font Awesome -->
  <link rel="stylesheet" href="{{ asset('lte/plugins/fontawesome-free/css/all.min.css') }}">
  <!-- icheck bootstrap -->
  <link rel="stylesheet" href="{{ asset('lte/plugins/icheck-bootstrap/icheck-bootstrap.min.css') }}">
  <!-- Theme style -->
  <link rel="stylesheet" href="{{ asset('lte/dist/css/adminlte.min.css') }}">
  <link rel="manifest" href="{{asset('manifest/manifest.json')}}">
  <script src="//code.jquery.com/jquery-1.11.3.min.js"></script>
  <style type="text/css">
    div#preloader { position: fixed; left: 0; top: 0; z-index: 999; width: 100%; height: 100%; overflow: visible; background: #ffffff url('https://manifest.arimbawadx.com/loader/loader2.gif') no-repeat center center; }
  </style>
</head>
<body class="hold-transition login-page">
  <div id="preloader"></div>
  
  <div class="login-box">
    <!-- /.login-logo -->
    <div class="card card-outline card-default">
      <div class="card-header text-center">
        <a href="#" class="h1"><b>adx</b> - pay</a>
      </div>
      <div class="card-body">
        <!-- <p class="login-box-msg">Silahkan login sayangg</p> -->
        <form action="/login-check" method="post">
          {{ csrf_field() }}
          <div class="input-group mb-3">
            <input required type="password" class="form-control username" name="username" autocomplete="off" placeholder="Masukan Kode Akses">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-user"></span>
              </div>
            </div>
          </div>
          <!-- <div class="input-group mb-3">
            <input required type="password" class="form-control" name="password" autocomplete="off" placeholder="Password">
            <div class="input-group-append">
              <div class="input-group-text">
                <span class="fas fa-lock"></span>
              </div>
            </div>
          </div> -->
          <div class="row">
            <div class="col-12 mb-3">
              <button type="submit" name="login" class="btn btn-primary btn-block">Submit</button>
            </div>
            <!-- /.col -->
            <div class="col-12">
              <span>Belum punya kode Akses?  </span><a class="badge badge-primary" href="/customers/daftar"><span>Buat Kode</span></a>  
            </div>
            <!-- /.col -->
          </div>
        </form>
      </div>
      <!-- /.card-body -->
    </div>
    <!-- /.card -->
  </div>
  <!-- /.login-box -->

  <script type="text/javascript">
    jQuery(document).ready(function($) {
      $(window).load(function(){
        $('#preloader').fadeOut('slow',function(){$(this).remove();});
      });
    });
  </script>
  <!-- jQuery -->
  <script src="{{ asset('lte/plugins/jquery/jquery.min.js') }}"></script>
  <!-- Bootstrap 4 -->
  <script src="{{ asset('lte/plugins/bootstrap/js/bootstrap.bundle.min.js') }}"></script>
  <!-- AdminLTE App -->
  <script src="{{ asset('lte/dist/js/adminlte.min.js') }}"></script>
  @include('sweetalert::alert')
</body>
</html>
