@extends('cs/layouts/main')

@section('title','adx-pay | Data Customer')

@section('content-header', 'Data Customer')

@section('content')
<!-- Content Wrapper. Contains page content -->
<section class="content">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="card bg-dark">
          <div class="card-header">Data Customer</div>
          <div class="card-body">
            <!-- The Modal Tambah Customer-->
            <div class="modal" id="TambahDataCustomer">
              <div class="modal-dialog">
                <div class="modal-content">
                  <!-- Modal Header -->
                  <div class="modal-header">
                    <h4 class="modal-title">Tambah Data Customer</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>
                  <!-- Modal body -->
                  <div class="modal-body">
                    <form method="post" action="/cs/users/data-customers">
                      {{csrf_field()}}
                      <div class="form-group">
                        <label for="nama">Nama Customer</label>
                        <input required="" autocomplete="off" type="text" class="form-control" id="nama" name="nama">
                      </div>
                      <div class="form-group">
                        <label for="no_hp">No HP</label>
                        <input required="" autocomplete="off" type="number" class="form-control" id="no_hp" name="no_hp">
                      </div>
                      <div class="form-group">
                        <label for="email">Email</label>
                        <input required="" autocomplete="off" type="email" class="form-control" id="email" name="email">
                      </div>
                      <input type="submit" class="btn btn-primary" value="Tambah" onClick="this.form.submit(); this.disabled=true; this.value='Loading…';">
                    </form>
                  </div>
                </div>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12">
                <button style="margin-bottom: 20px" type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#TambahDataCustomer">
                  <i class="fa fa-plus"></i><span> Tambah</span>
                </button>
              </div>
            </div>
            <div class="row">
              <div class="col-md-12 table-responsive">
                <table id="datatables" class="table table-striped text-center">
                  <thead style="background-color: #343a40">
                    <tr>
                      <th>No</th>
                      <th>Nama</th>
                      <th>Saldo</th>
                      <th>Coin</th>
                      <th>Username</th>
                      <th>No HP</th>
                      <th>Email</th>
                      <th width="70px">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($dataCustomer as $i => $dataCustomer)
                    <tr>
                      <th>{{$i+1}}</th>
                      <td>{{$dataCustomer -> name}} @if($dataCustomer -> verified == 1)<i class="bi bi-patch-check-fill text-primary"></i>@else<i class="bi bi-patch-exclamation-fill text-warning"></i>@endif</td>
                      <td>{{$dataCustomer -> saldo}}</td>
                      <td>{{$dataCustomer -> point}}</td>
                      <td>{{$dataCustomer -> username}}</td>
                      <td>{{$dataCustomer -> phone_number}}</td>
                      <td>{{$dataCustomer -> email}}</td>
                      <td>
                        <button class="btn btn-success" data-toggle="modal" data-target="#depositSaldo{{$dataCustomer -> id}}">Deposit</button>

                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#myModalUbahDataCustomer{{$dataCustomer -> id}}">
                          <i class="fa fa-pen"></i><span></span>
                        </button>

                        <button cus-id="{{$dataCustomer -> id}}" nama-cus="{{$dataCustomer -> name}}" class="btn btn-danger delete_cus">
                          <i class="fa fa-trash"></i><span></span>
                        </button>

                      </td>
                    </tr>
                    <!-- The Modal Deposit Saldo-->
                    <div class="modal" id="depositSaldo{{$dataCustomer -> id}}">
                      <div class="modal-dialog">
                        <div class="modal-content">
                          <!-- Modal Header -->
                          <div class="modal-header">
                            <h4 class="modal-title">Deposit Saldo</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                          </div>
                          <!-- Modal body -->
                          <div class="modal-body">
                            <div class="row">
                              <div class="col-lg-12">
                                <!-- small card -->
                                <div class="small-box bg-light">
                                  <div class="inner">
                                    <h3>Rp. {{number_format($dataCustomer -> saldo, 0, '', '.')}}</h3>

                                    <p>Saldo {{$dataCustomer -> name}}</p>
                                  </div>
                                  <div class="icon">
                                    <i class="fas fa-file-invoice-dollar"></i>
                                  </div>
                                </div>
                              </div>
                              <!-- ./col -->
                            </div>
                            <form method="post" action="/cs/users/data-customers/deposit/{{$dataCustomer -> id}}">
                              {{csrf_field()}}
                              <div class="form-group">
                                <label for="nominal_deposit">Nominal Deposit</label>
                                <input required="" autocomplete="off" type="number" class="form-control" id="nominal_deposit" name="nominal_deposit">
                              </div>
                              <button type="submit" class="btn btn-primary float-right">Submit</button>
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>

                    <!-- The Modal -->
                    <div class="modal" id="myModalUbahDataCustomer{{$dataCustomer -> id}}">
                      <div class="modal-dialog">
                        <div class="modal-content">

                          <!-- Modal Header -->
                          <div class="modal-header">
                            <h4 class="modal-title">Ubah Data Customer</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                          </div>

                          <!-- Modal body -->
                          <div class="modal-body">
                            <form method="post" action="/cs/users/data-customers/update/{{$dataCustomer -> id}}">
                              {{csrf_field()}}
                              <div class="form-group">
                                <label for="nama">Nama Customer</label>
                                <input autocomplete="off" type="text" class="form-control" id="nama" name="nama" value="{{$dataCustomer -> name}}">
                              </div>
                              <div class="form-group">
                                <label for="no_hp">No HP </label>
                                <input autocomplete="off" type="number" class="form-control" id="no_hp" name="no_hp" value="{{$dataCustomer -> phone_number}}">
                              </div>
                              <div class="form-group">
                                <label for="email">Email</label>
                                <input autocomplete="off" type="email" class="form-control" id="email" name="email" value="{{$dataCustomer -> email}}">
                              </div>
                              <div class="form-group">
                                <label for="status">Status</label>
                                <select id="status" name="status" class="form-control">
                                  <option value="{{$dataCustomer -> verified}}">@if($dataCustomer -> verified ==1) Verified @else Not Verified @endif</option>
                                  @if($dataCustomer -> verified ==1)
                                  <option value="0">Not Verified</option>
                                  @else
                                  <option value="1">Verified</option>
                                  @endif
                                </select>
                              </div>
                              <input type="submit" class="btn btn-primary" value="Simpan" onClick="this.form.submit(); this.disabled=true; this.value='Loading…';">
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="col-md-12">
        <div class="card bg-dark">
          <div class="card-header">Data Customer Terhapus</div>
          <div class="card-body">
            <div class="row">
              <div class="col-md-12 table-responsive">
                <table id="datatables2" class="table table-striped text-center">
                  <thead style="background-color: #343a40">
                    <tr>
                      <th>No</th>
                      <th>Nama</th>
                      <th>Username</th>
                      <th>No HP</th>
                      <th>Email</th>
                      <th width="70px">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($dataCustomerDeleted as $i => $dataCustomer)
                    <tr>
                      <th>{{$i+1}}</th>
                      <td>{{$dataCustomer -> name}} <i class="bi bi-patch-minus-fill text-danger"></i></td>
                      <td>{{$dataCustomer -> username}}</td>
                      <td>{{$dataCustomer -> phone_number}}</td>
                      <td>{{$dataCustomer -> email}}</td>
                      <td>
                        <button cus-id="{{$dataCustomer -> id}}" nama-cus="{{$dataCustomer -> name}}" class="btn btn-primary restore_cus">
                          <i class="fa fa-trash-restore"></i><span></span>
                        </button>
                      </td>
                    </tr>
                    @endforeach
                  </tbody>
                </table>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>
<!-- /.content-wrapper -->
@endsection