@extends('cs/layouts/main')

@section('title','adx-pay | Manajemen Piutang')

@section('content-header', 'Manajemen Piutang')

@section('content')
<!-- Content Wrapper. Contains page content -->
<section class="content">
  <div class="container">
    <!-- The Modal Tambah Piutang-->
    <div class="modal" id="TambahPiutang">
      <div class="modal-dialog">
        <div class="modal-content">
          <!-- Modal Header -->
          <div class="modal-header">
            <h4 class="modal-title">Tambah Piutang</h4>
            <button type="button" class="close" data-dismiss="modal">&times;</button>
          </div>
          <!-- Modal body -->
          <div class="modal-body">
            <form method="post" action="/cs/piutang/tambah">
              {{csrf_field()}}
              <div class="form-group">
                <label for="customer">Customer</label>
                <select id="customer" name="customer" required class="form-control selectpicker">
                  <option value="">Pilih</option>
                  @foreach($customer as $c)
                  <option value="{{$c->id}}">{{$c->name}} | {{$c->username}}</option>
                  @endforeach
                </select>
              </div>
              <div class="form-group">
                <label for="nominal">Nominal</label>
                <input required=""  autocomplete="off" type="number" class="form-control" id="nominal" name="nominal">
              </div>
              <div class="form-group">
                <label for="keterangan">Keterangan</label>
                <input required="" autocomplete="off" type="text" class="form-control" id="keterangan" name="keterangan">
              </div>
              <button type="submit" class="btn btn-primary">Tambah</button>
            </form>
          </div>
        </div>
      </div>
    </div>
    <div class="row">
      <div class="col-md-12">
        <button style="margin-bottom: 20px" type="button" class="btn btn-primary float-right" data-toggle="modal" data-target="#TambahPiutang">
          <i class="fa fa-plus"></i><span> Tambah</span>
        </button>
      </div>
    </div>


    <div class="row">
      <div class="col-md-12">
        <div class="card bg-dark">
          <div class="card-header">Data Piutang</div>
          <div class="card-body">
            <div class="row">
              <div class="col-lg-4">
                <!-- small card -->
                <div class="small-box bg-light">
                  <div class="inner">
                    <h3>Rp. {{number_format($TotalHutangCustomer, 0, '', '.')}}</h3>

                    <p>Total Semua Piutang</p>
                  </div>
                  <div class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                  </div>
                </div>
              </div>
              <!-- ./col -->
            </div>
            <div class="row">
              <div class="col-md-12 table-responsive">
                <table id="datatables" class="table table-striped text-center">
                  <thead style="background-color: #343a40">
                    <tr>
                      <th>Username</th>
                      <th>Nama</th>
                      <th>Total Piutang</th>
                      <th width="80px">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($piutang as $p)
                    <tr>
                      <td>{{$p->first()->Customers->username}}</td>
                      <td>{{$p->first()->Customers->name}}</td>
                      <td>Rp. {{number_format($p->sum('sisa'), 0, '', '.')}}</td>
                      <td>
                        <a class="btn btn-primary" href="/cs/piutang/{{$p->first()->customer_id}}"><i class="fa fa-eye"></i></a>
                        <button class="btn btn-success lunaskan" cus-id="{{$p->first()->customer_id}}" nama-cus="{{$p->first()->Customers->name}}">Lunaskan</button>
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

    <div class="row">
      <div class="col-md-12">
        <div class="card bg-dark">
          <div class="card-header">History Pelunasan Piutang</div>
          <div class="card-body">
            <div class="row">
              <div class="col-lg-4">
                <!-- small card -->
                <div class="small-box bg-light">
                  <div class="inner">
                    <h3>Rp. {{number_format($TotalHutangCustomerLunas, 0, '', '.')}}</h3>

                    <p>Total Semua Piutang Dilunasi</p>
                  </div>
                  <div class="icon">
                    <i class="fas fa-file-invoice-dollar"></i>
                  </div>
                </div>
              </div>
              <!-- ./col -->
            </div>
            <div class="row">
              <div class="col-md-12 table-responsive">
                <table id="datatables2" class="table table-striped text-center">
                  <thead style="background-color: #343a40">
                    <tr>
                      <th>Username</th>
                      <th>Nama</th>
                      <th>Total Pelunasan</th>
                      <th width="80px">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($piutangTerlunaskan as $p)
                    <tr>
                      <td>{{$p->first()->Customers->username}}</td>
                      <td>{{$p->first()->Customers->name}}</td>
                      <td>Rp. {{number_format($p->sum('nominal'), 0, '', '.')}}</td>
                      <td>
                        <a class="btn btn-primary" href="/cs/piutang/{{$p->first()->customer_id}}"><i class="fa fa-eye"></i></a>
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

