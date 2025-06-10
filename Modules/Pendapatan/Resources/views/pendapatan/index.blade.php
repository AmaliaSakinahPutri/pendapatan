@extends('adminlte::page')
@section('title', 'Pendapatan')

@section('content_header')
    <h1 class="m-0 text-dark">Data Pendapatan</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                <div class="d-flex justify-content-between mb-3">
                    <a href="{{ route('pendapatan.create') }}" class="btn btn-primary btn-sm">+ Tambah Pendapatan</a>
                    <form action="{{ route('pendapatan.import') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <div class="input-group">
                            <input type="file" name="file" class="form-control form-control-sm" style="height: 37px;" required>
                            <button type="submit" class="btn btn-success btn-sm">Import Excel</button>
                        </div>
                    </form>
                </div>

                @include('layouts.partials.messages')
                @if(!empty($importErrors))
                    <div class="alert alert-danger">
                        <h4>Data yang Gagal Diproses:</h4>
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Nama Pegawai / NIP</th>
                                    <th>Jenis Pendapatan</th>
                                    <th>Pesan Error</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($importErrors as $error)
                                    <tr>
                                        <td>{{ $error['nama'] }}</td>
                                        <td>{{ $error['jenis'] }}</td>
                                        <td>{{ $error['pesan'] }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif

                <table id="pendapatan-table" class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pegawai</th>
                            <th>Bulan</th>
                            <th>Tahun</th>
                            <th>Bruto</th>
                            <th>Pajak</th>
                            <th>Potongan</th>
                            <th>Netto</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($pendapatan as $item)
                            <tr>
                                <td>{{ $loop->iteration }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ \Carbon\Carbon::parse($item->bulan)->translatedFormat('F') }}</td>
                                <td>{{ $item->tahun }}</td>
                                <td>Rp{{ number_format($item->total_bruto, 0, ',', '.') }}</td>
                                <td>Rp{{ number_format($item->total_pajak, 0, ',', '.') }}</td>
                                <td>Rp{{ number_format($item->total_potongan, 0, ',', '.') }}</td>
                                <td>Rp{{ number_format($item->total_netto, 0, ',', '.') }}</td>
                                <td>
                                    <a href="{{ route('pendapatan.detail', [$item->pegawai_id, \Carbon\Carbon::parse($item->bulan)->format('m'), $item->tahun]) }}" class="btn btn-sm btn-primary">Detail</a>
                                    <a href="{{ route('pendapatan.cetak.bulan', [$item->pegawai_id, \Carbon\Carbon::parse($item->bulan)->format('m'), $item->tahun]) }}" class="btn btn-sm btn-danger" target="_blank">Cetak</a>
                                </td>
                            </tr>
                        @endforeach
                        @if($pendapatan->isEmpty())
                        <tr>
                            <td colspan="9" class="text-center">Data tidak tersedia.</td>
                        </tr>
                        @endif
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- DataTables CDN -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css"/>

<script>
    $(document).ready(function () {
        $('#pendapatan-table').DataTable({
            responsive: true,
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json'
            }
        });
    });
</script>
@endsection
