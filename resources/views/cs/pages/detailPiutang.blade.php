@extends('cs/layouts/main')

@section('title','adx-pay | Detail Piutang')

@section('content-header', 'Detail Piutang')

@section('content')
<!-- Content Wrapper. Contains page content -->
<section class="content">
  <div class="container">
    <div class="row">
      <div class="col-md-12">
        <div class="card bg-dark">
          <div class="card-header">Detail Piutang {{$customer->name}}</div>
          <div class="card-body">
            <div class="row">
              <div class="col-lg-4">
                <!-- small card -->
                <div class="small-box bg-light">
                  <div class="inner">
                    <h3>Rp. {{number_format($hutang->sum('sisa'), 0, '', '.')}}</h3>

                    <p>Total Piutang {{$customer->name}}</p>
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
                      <th>Tanggal</th>
                      <th>Piutang</th>
                      <th>Keterangan</th>
                      <th>Status</th>
                      <th width="80px">Aksi</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($hutang as $h)
                    <tr>
                      <td>{{$h->created_at}}</td>
                      <td>{{$h->sisa}}</td>
                      <td>{{$h->keterangan}}</td>
                      <td class=<?php if ($h->status=="Belum Lunas"){echo "text-danger";}elseif($h->status=="Lunas"){echo "text-success";} ?>>{{$h->status}}</td>
                      <!-- The Modal ubah Piutang-->
                      <div class="modal" id="ubahPiutang{{$h->id}}">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <!-- Modal Header -->
                            <div class="modal-header">
                              <h4 class="modal-title">Ubah Piutang</h4>
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <!-- Modal body -->
                            <div class="modal-body">
                              <form method="post" action="/cs/piutang/ubah/{{$h->id}}">
                                {{csrf_field()}}
                                <div class="form-group">
                                  <label for="nominal">Nominal</label>
                                  <input required="" min="0" value="{{$h->nominal}}" autocomplete="off" type="number" class="form-control" id="nominal" name="nominal">
                                </div>
                                <div class="form-group">
                                  <label for="keterangan">Keterangan</label>
                                  <input required="" value="{{$h->keterangan}}" autocomplete="off" type="text" class="form-control" id="keterangan" name="keterangan">
                                </div>
                                <div class="form-group">
                                  <label for="sisa">Sisa Piutang</label>
                                  <input required="" min="0" value="{{$h->sisa}}" autocomplete="off" type="number" class="form-control" id="sisa" name="sisa">
                                </div>
                                <button type="submit" class="btn btn-primary">Submit</button>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>

                      <!-- The Modal bayar Piutang-->
                      <div class="modal" id="bayarPiutang{{$h->id}}">
                        <div class="modal-dialog">
                          <div class="modal-content">
                            <!-- Modal Header -->
                            <div class="modal-header">
                              <h4 class="modal-title">Bayar Piutang</h4>
                              <button type="button" class="close" data-dismiss="modal">&times;</button>
                            </div>
                            <!-- Modal body -->
                            <div class="modal-body">
                              <div class="row">
                                <div class="col-lg-12">
                                  <!-- small card -->
                                  <div class="small-box bg-light">
                                    <div class="inner">
                                      <h3>Rp. {{number_format($h->sisa, 0, '', '.')}}</h3>

                                      <p>{{$h->keterangan}}</p>
                                    </div>
                                    <div class="icon">
                                      <i class="fas fa-file-invoice-dollar"></i>
                                    </div>
                                  </div>
                                </div>
                                <!-- ./col -->
                              </div>
                              <form method="post" action="/cs/piutang/bayar/{{$h->customer_id}}/{{$h->id}}">
                                {{csrf_field()}}
                                <div class="form-group">
                                  <label for="bayar">Bayar</label>
                                  <input required="" min="0" max="{{$h->sisa}}" autocomplete="off" type="number" class="form-control" id="bayar" name="bayar">
                                </div>
                                <button type="submit" class="btn btn-primary float-right">Submit</button>
                              </form>
                            </div>
                          </div>
                        </div>
                      </div>

                      <td>
                        <button class="btn btn-warning" data-toggle="modal" data-target="#ubahPiutang{{$h->id}}"><i class="fa fa-pen"></i></button>
                        <button class="btn btn-danger hapus-hutang" cus-id="{{$h->customer_id}}" hutang-id="{{$h->id}}" nama-hutang="{{$h->keterangan}}"><i class="fa fa-trash"></i></button>
                        <button class="btn btn-primary" data-toggle="modal" data-target="#bayarPiutang{{$h->id}}">Bayar</button>
                        <button class="btn btn-success lunaskan-detail" cus-id="{{$h->customer_id}}" hutang-id="{{$h->id}}" nama-hutang="{{$h->keterangan}}">Lunaskan</button>
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
          <div class="card-header">Detail Piutang {{$customer->name}} telah Dilunaskan</div>
          <div class="card-body">
            <div class="row">
              <div class="col-lg-4">
                <!-- small card -->
                <div class="small-box bg-light">
                  <div class="inner">
                    <h3>Rp. {{number_format($hutangTerlunaskan->sum('nominal'), 0, '', '.')}}</h3>

                    <p>Total Lunas</p>
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
                      <th>Tanggal</th>
                      <th>Nominal</th>
                      <th>Keterangan</th>
                      <th>Status</th>
                    </tr>
                  </thead>
                  <tbody>
                    @foreach($hutangTerlunaskan as $h)
                    <tr>
                      <td>{{$h->created_at}}</td>
                      <td>{{$h->nominal}}</td>
                      <td>{{$h->keterangan}}</td>
                      <td class=<?php if ($h->status=="Belum Lunas"){echo "text-danger";}elseif($h->status=="Lunas"){echo "text-success";} ?>>{{$h->status}}</td>
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

