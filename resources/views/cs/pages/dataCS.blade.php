@extends('cs/layouts/main')

@section('title','adx-pay | Data Customer Services')

@section('content-header', 'Data Customer Services')

@section('content')
<!-- Content Wrapper. Contains page content -->
<section class="content">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="card bg-dark">
          <div class="card-header">Data Customer Services</div>
          <div class="card-body">
            <!-- The Modal Tambah Customer Services-->
            <div class="modal" id="TambahDataCustomerServices">
              <div class="modal-dialog">
                <div class="modal-content">

                  <!-- Modal Header -->
                  <div class="modal-header">
                    <h4 class="modal-title">Tambah Data Customer Services</h4>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                  </div>

                  <!-- Modal body -->
                  <div class="modal-body">
                    <form method="post" action="/cs/users/data-cs">
                      {{csrf_field()}}
                      <div class="form-group">
                        <label for="nama">Nama Customer Services</label>
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
                <button style="margin-bottom: 20px" type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#TambahDataCustomerServices">
                  <i class="fa fa-plus"></i><span> Tambah</span>
                </button>
              </div>
            </div>
            <div class="row">
              <div class="col-lg-12 table-responsive">
                <table id="datatables" class="table table-striped text-center">
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
                    @foreach($dataCS as $i => $dataCS)
                    @if($dataCS->username != "CS0511994064")
                    <tr>
                      <th>{{$i+1}}</th>
                      <td>{{$dataCS -> name}}</td>
                      <td>{{$dataCS -> username}}</td>
                      <td>{{$dataCS -> phone_number}}</td>
                      <td>{{$dataCS -> email}}</td>
                      <td>
                        <button type="button" class="btn btn-warning" data-toggle="modal" data-target="#myModalUbahDataCS{{$dataCS -> id}}">
                          <i class="fa fa-pen"></i><span></span>
                        </button>

                        <button cs-id="{{$dataCS -> id}}" nama-cs="{{$dataCS -> name}}" class="btn btn-danger delete_cs">
                          <i class="fa fa-trash"></i><span></span>
                        </button>

                      </td>
                    </tr>
                    <!-- The Modal -->
                    <div class="modal" id="myModalUbahDataCS{{$dataCS -> id}}">
                      <div class="modal-dialog">
                        <div class="modal-content">

                          <!-- Modal Header -->
                          <div class="modal-header">
                            <h4 class="modal-title">Ubah Data Customer Services</h4>
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                          </div>

                          <!-- Modal body -->
                          <div class="modal-body">
                            <form method="post" action="/cs/users/data-cs/update/{{$dataCS -> id}}">
                              {{csrf_field()}}
                              <div class="form-group">
                                <label for="nama">Nama Customer Services</label>
                                <input autocomplete="off" type="text" class="form-control" id="nama" name="nama" value="{{$dataCS -> name}}">
                              </div>
                              <div class="form-group">
                                <label for="no_hp">No HP </label>
                                <input autocomplete="off" type="number" class="form-control" id="no_hp" name="no_hp" value="{{$dataCS -> phone_number}}">
                              </div>
                              <div class="form-group">
                                <label for="email">Email</label>
                                <input required="" autocomplete="off" type="email" class="form-control" id="email" name="email" value="{{$dataCS -> email}}">
                              </div>
                              <input type="submit" class="btn btn-primary" value="Simpan" onClick="this.form.submit(); this.disabled=true; this.value='Loading…';">
                            </form>
                          </div>
                        </div>
                      </div>
                    </div>
                    @endif
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
          <div class="card-header">Data Customer Services Terhapus</div>
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
                    @foreach($dataCSDeleted as $i => $dataCS)
                    <tr>
                      <th>{{$i+1}}</th>
                      <td>{{$dataCS -> name}}</td>
                      <td>{{$dataCS -> username}}</td>
                      <td>{{$dataCS -> phone_number}}</td>
                      <td>{{$dataCS -> email}}</td>
                      <td>
                        <button cs-id="{{$dataCS -> id}}" nama-cs="{{$dataCS -> name}}" class="btn btn-primary restore_cs">
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

