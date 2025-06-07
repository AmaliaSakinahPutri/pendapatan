@extends('adminlte::page')
@section('title', 'Pendapatan')

@section('content_header')
    <h1 class="m-0 text-dark">Detail Data Pendapatan</h1>
@stop

@section('content')
<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-body">

                @include('layouts.partials.messages')

                <table id="pendapatan-table" class="table table-bordered table-sm">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th>Pegawai</th>
                            <th>Jenis</th>
                            <th>Bulan</th>
                            <th>Tahun</th>
                            <th>Bruto</th>
                            <th>Pajak</th>
                            <th>Potongan</th>
                            <th>Netto</th>
                            <th>Tanggal Masuk</th>
                            @if(auth()->user()->role_aktif == 'keuangan')
                            <th>Aksi</th>
                            @endif
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($pendapatan as $item)
                        <tr>
                            <td>{{ $loop->iteration }}</td>
                            <td>{{ $item->pegawai->nama ?? '-' }}</td>
                            <td>{{ $item->jenisPendapatan->nama_jenis_pendapatan ?? '-' }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->bulan)->translatedFormat('F') }}</td>
                            <td>{{ $item->tahun }}</td>
                            <td>Rp{{ number_format($item->nilai_bruto, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($item->pajak, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($item->potongan, 0, ',', '.') }}</td>
                            <td>Rp{{ number_format($item->nilai_netto, 0, ',', '.') }}</td>
                            <td>{{ \Carbon\Carbon::parse($item->tanggal_masuk)->format('d-m-Y') }}</td>
                            @if(auth()->user()->role_aktif == 'keuangan')
                            <td>
                                <a href="{{ route('pendapatan.edit', $item->id) }}" class="btn btn-sm btn-primary">Edit</a>
                                {{-- <form action="{{ route('pendapatan.destroy', $item->id) }}" method="POST" class="d-inline"
                                    onsubmit="return confirm('Yakin ingin menghapus?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form> --}}
                                <button type="button" class="btn btn-sm btn-danger" data-toggle="modal" data-target="#confirmDeleteModal" data-id="{{ $item->id }}">
                                    Hapus
                                </button>

                            </td>
                            @endif
                        </tr>
                        @empty
                        <tr>
                            <td colspan="11" class="text-center">Data tidak tersedia.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>

            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" role="dialog" aria-labelledby="confirmDeleteLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <form method="POST" id="deleteForm">
            @csrf
            @method('DELETE')
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="confirmDeleteLabel">Konfirmasi Hapus</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Tutup">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    Apakah Anda yakin ingin menghapus data ini? Tindakan ini tidak dapat dibatalkan.
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">Ya, Hapus</button>
                </div>
            </div>
        </form>
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
<script>
    $('#confirmDeleteModal').on('show.bs.modal', function (event) {
        var button = $(event.relatedTarget);
        var id = button.data('id');
        var form = $('#deleteForm');
        form.attr('action', '/pendapatan/' + id);
    });
</script>


@endsection