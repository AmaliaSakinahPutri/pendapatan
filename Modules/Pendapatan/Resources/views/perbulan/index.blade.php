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

                {{-- Form Filter Bulan dan Tahun --}}
                <form method="GET" action="{{ route('pendapatan.perbulan') }}" class="row g-2 mb-3">
                    <div class="col-md-3">
                        <select name="bulan" class="form-control form-control-sm" required>
                            <option value="" disabled>-- Pilih Bulan --</option>
                            @foreach(range(1, 12) as $b)
                                <option value="{{ $b }}" {{ request('bulan') == $b ? 'selected' : '' }}>
                                    {{ \Carbon\Carbon::create()->month($b)->translatedFormat('F') }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <select name="tahun" class="form-control form-control-sm" required>
                            <option value="" disabled>-- Pilih Tahun --</option>
                            @foreach(range(now()->year, now()->year - 5) as $t)
                                <option value="{{ $t }}" {{ request('tahun') == $t ? 'selected' : '' }}>{{ $t }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-sm btn-primary">Filter</button>
                        <a href="{{ route('pendapatan.perbulan') }}" class="btn btn-sm btn-secondary">Reset</a>
                    </div>
                </form>

                @include('layouts.partials.messages')

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
                            <td colspan="11" class="text-center">Data tidak tersedia.</td>
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
