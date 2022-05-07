<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="shortcut icon" href="{{ asset('lte/dist/img/favicon.ico') }}">
  <title>@yield('title')</title>

  <link rel="manifest" href="{{asset('manifest/manifestcustomers.json')}}">
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
  <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/0.5.0-beta4/html2canvas.js" integrity="sha512-5XAS7mhslf6oGjLxzmY4iYfFwDGf8G1ZBeWdymR/+y8ZCvPWwI3Ff+WrS+kabqYdIEwYaLEnJhsuymZxgrneQg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
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
    div#preloader { position: fixed; left: 0; top: 0; z-index: 999; width: 100%; height: 100%; overflow: visible; background: #ffffff url('{{ asset("lte/plugins/loader/loader6.gif") }}') no-repeat center center; }
    .fireworks-container {
      position: fixed;
      left: 0;
      top: 0;
      width: 100%;
      height: 100%;
      
    }
  </style>
  <script type="text/javascript">
    // loader
    $(window).on('load',function(){
      $('#preloader').fadeOut('slow',function(){
        $(this).remove();
      });
    });
  </script>
</head>
<body id="body" class="hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed">
  <div class="wrapper">
    <div id="preloader"></div>
    <!--spesial thun baru-->
    <!-- <div class="fireworks-container" style="background-size: cover; background-position: 50% 50%; background-repeat: no-repeat;"></div> -->
    <!--spesial thun baru-->


    <!-- Navbar -->
    @include('customers/layouts/header')
    <!-- /.navbar -->

    <!-- Main Sidebar Container -->
    @include('customers/layouts/sidebar')

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
      <!-- Content Header (Page header) -->
      <div class="content-header">
        <div class="container-fluid">
          <div class="row mb-2">
            <div class="col-sm-6">
              <h1 class="m-0">@yield('content-header')</h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
              <ol class="breadcrumb float-sm-right">
                <li class="breadcrumb-item active">Page of @yield('content-header')</li>
              </ol>
            </div><!-- /.col -->
          </div><!-- /.row -->
        </div><!-- /.container-fluid -->
      </div>
      <!-- /.content-header -->

      <!-- Main content -->
      
      <div id="content">  
        @yield('content')
      </div>
      <!-- /.content -->
    </div>
    <!-- /.content-wrapper -->

    <!-- Control Sidebar -->
    <aside class="control-sidebar control-sidebar-dark">
      <!-- Control sidebar content goes here -->
    </aside>
    <!-- /.control-sidebar -->

    <!-- Main Footer -->
    @include('customers/layouts/footer')
  </div>
  <!-- ./wrapper -->
  <script>
    const toggleSwitch = document.querySelector('.theme-switch input[type="checkbox"]');
    function switchTheme(e) {
      if (e.target.checked) {
        document.getElementById("body").className = 'hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed';
        document.getElementById("sidebarr").className = 'main-sidebar sidebar-dark-primary elevation-4';
        document.getElementById("headerr").className = 'main-header navbar navbar-expand navbar-dark';
        document.getElementById("navbottom").className = 'navbar navbar-dark navbar-expand fixed-bottom d-md-none d-lg-none d-xl-none pt-0';
        localStorage.setItem('theme', 'hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed');
        localStorage.setItem('themeSideBar', 'main-sidebar sidebar-dark-primary elevation-4');
        localStorage.setItem('themeHeader', 'main-header navbar navbar-expand navbar-dark');
        localStorage.setItem('themeNavBottom', 'navbar navbar-dark navbar-expand fixed-bottom d-md-none d-lg-none d-xl-none pt-0');
      }
      else {
        document.getElementById("body").className = 'hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed';
        document.getElementById("sidebarr").className = 'main-sidebar sidebar-light-primary elevation-4';
        document.getElementById("headerr").className = 'main-header navbar navbar-expand navbar-light';
        document.getElementById("navbottom").className = 'navbar navbar-light navbar-expand fixed-bottom d-md-none d-lg-none d-xl-none pt-0';
        localStorage.setItem('theme', 'hold-transition sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed');
        localStorage.setItem('themeSideBar', 'main-sidebar sidebar-light-primary elevation-4');
        localStorage.setItem('themeHeader', 'main-header navbar navbar-expand navbar-light');
        localStorage.setItem('themeNavBottom', 'navbar navbar-light navbar-expand fixed-bottom d-md-none d-lg-none d-xl-none pt-0');
      }    
    }
    toggleSwitch.addEventListener('change', switchTheme, false);

    const currentTheme = localStorage.getItem('theme') ? localStorage.getItem('theme') : null;
    const currentThemeSideBar = localStorage.getItem('themeSideBar') ? localStorage.getItem('themeSideBar') : null;
    const currentThemeHeader = localStorage.getItem('themeHeader') ? localStorage.getItem('themeHeader') : null;
    const currentThemeNavBottom = localStorage.getItem('themeNavBottom') ? localStorage.getItem('themeNavBottom') : null;
    if (currentTheme) {
      document.getElementById("body").className = currentTheme;
      document.getElementById("sidebarr").className = currentThemeSideBar;
      document.getElementById("headerr").className = currentThemeHeader;
      document.getElementById("navbottom").className = currentThemeNavBottom;
      if (currentTheme === 'hold-transition dark-mode sidebar-mini layout-fixed layout-navbar-fixed layout-footer-fixed') {
        toggleSwitch.checked = true;
      }
    }
  </script>

  <!--spesial thun baru-->
  <script src="{{asset('lte/plugins/fireworks/fireworks.js')}}"></script>
  <script>
    const container = document.querySelector('.fireworks-container')

    const fireworks = new Fireworks({
      target: container,
      hue: 120,
      startDelay: 1,
      minDelay: 20,
      maxDelay: 30,
      speed: 4,
      acceleration: 1.05,
      friction: 0.98,
      gravity: 1,
      particles: 75,
      trace: 3,
      explosion: 5,
      boundaries: {
        top: 50,
        bottom: container.clientHeight,
        left: 50,
        right: container.clientWidth
      },
      sound: {
        enable: false,
        list: [
        'explosion0.mp3',
        'explosion1.mp3',
        'explosion2.mp3'
        ],
        min: 4,
        max: 8
      }
    })

// start fireworks
fireworks.start()
</script>
<!--spesial thun baru-->

<script type="text/javascript">
  $(document).ready( function () {
      // selectpicker
      $('.selectpicker').select2({
       theme: 'bootstrap4',
     });
      // end selectpicker

    // datatables
    $('#datatables').DataTable();
    // end datatables

    $(".custom-file-input").on("change", function() {
      var fileName = $(this).val().split("\\").pop();
      $(this).siblings(".custom-file-label").addClass("selected").html(fileName);
    });


    // konfirm-trx
    // btn kirim validation
    $('#btn-trx-confirm').on('click', function() {
      let selectJumlahPulsa = document.forms["form-confirm"]["produk"].value;
      let selectMetodePembayaran = document.forms["form-confirm"]["metode_pembayaran"].value;
      if (selectJumlahPulsa == "") {
        Swal.fire({
          icon: 'error',
          title: 'Pilih Produk!',
          text: 'Silahkan pilih produk yang dibeli'
        })
        return false;
      }else if(selectMetodePembayaran == ""){
        Swal.fire({
          icon: 'error',
          title: 'Pilih Metode Pembayaran',
          text: 'Silahkan pilih metode pembayaran yang digunakan'
        })
        return false;
      }else{
        Swal.fire({
          title: 'Kamu Yakin Untuk Transaksi?',
          showDenyButton: true,
          confirmButtonText: 'Yakin',
          denyButtonText: 'Batal',
        }).then((result) => {
          if (result.isConfirmed) {
            Swal.fire('Diproses!', 'Transaksi diproses', 'success');
            document.getElementById("form-confirm").submit();
          } else if (result.isDenied) {
            Swal.fire('Batal', 'Transaksi dibatalkan', 'error')
          }
        })
      }
    });
    // end btn kirim validation

    // btn kirim validation
    $('#btn-trx-confirm-vg').on('click', function() {
      let nohp = document.forms["form-confirm"]["no_hp"].value;
      let selectJumlahPulsa = document.forms["form-confirm"]["produk"].value;
      let selectMetodePembayaran = document.forms["form-confirm"]["metode_pembayaran"].value;
      if (nohp == "") {
        Swal.fire({
          icon: 'error',
          title: 'ID Game Kosong',
          text: 'Silahkan masukan ID Game'
        })
        return false;
      }else if (selectJumlahPulsa == "") {
        Swal.fire({
          icon: 'error',
          title: 'Pilih Produk!',
          text: 'Silahkan pilih produk yang dibeli'
        })
        return false;
      }else if(selectMetodePembayaran == ""){
        Swal.fire({
          icon: 'error',
          title: 'Pilih Metode Pembayaran',
          text: 'Silahkan pilih metode pembayaran yang digunakan'
        })
        return false;
      }else{
        Swal.fire({
          title: 'Kamu Yakin Untuk Transaksi?',
          showDenyButton: true,
          confirmButtonText: 'Yakin',
          denyButtonText: 'Batal',
        }).then((result) => {
          if (result.isConfirmed) {
            Swal.fire('Diproses!', 'Transaksi diproses', 'success');
            document.getElementById("form-confirm").submit();
          } else if (result.isDenied) {
            Swal.fire('Batal', 'Transaksi dibatalkan', 'error')
          }
        })
      }
    });
    // end btn kirim validation
  });
</script>
@include('sweetalert::alert')
</body>
</html>
