<nav id="headerr" class="main-header navbar navbar-expand navbar-dark">
  <span class="navbar-text"><strong>AdxPay</strong></span>
  <!-- Left navbar links -->
  <!-- <ul class="navbar-nav">
    <li class="nav-item">
      <a class="nav-link" data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
    </li>
  </ul> -->
  <!-- Right navbar links -->
  <ul class="navbar-nav ml-auto">
    <!-- <li class="nav-item">
      <a class="nav-link" data-widget="fullscreen" href="#" role="button">
        <i class="fas fa-expand-arrows-alt"></i>
      </a>
    </li> -->
    <li class="nav-item">
      <a class="nav-link" href="#" role="button">
        <div class="custom-control custom-switch theme-switch">
          <input type="checkbox" class="custom-control-input" id="checkbox1">
          <label class="custom-control-label" for="checkbox1">Dark Mode <i class="fas fa-moon"></i></label>
        </div>
      </a>
    </li>
    <li class="nav-item">
      <?php 
      $nama = session()->get('dataLoginCustomers')['name'];
      $username = session()->get('dataLoginCustomers')['username'];
      ?>
      <a class="nav-link" href="https://api.whatsapp.com/send?phone=6285847801933&text=Nama : {{$nama}}%0AUsername : {{$username}}%0A%0A%0AHalo Yoga, Saya ingin menanyakan mengenai ..." role="button">
        <i class="fab fa-whatsapp"> Customer Care</i>
      </a>
    </li>
    <li class="nav-item">
      <a class="nav-link" href="/logout" role="button">
        <i class="fas fa-power-off"></i>
      </a>
    </li>
  </ul>
</nav>


