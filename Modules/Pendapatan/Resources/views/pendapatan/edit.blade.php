@extends('adminlte::page')
@section('title', 'Edit Pendapatan')

@section('content_header')
    <h1 class="m-0 text-dark">Edit Pendapatan</h1>
@stop

@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card card-primary">
            <div class="card-body">
                <form action="{{ route('pendapatan.update', $pendapatan->id) }}" method="POST" id="pendapatanForm">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="pegawai_id">Pegawai</label>
                            <select name="pegawai_id" class="form-control @error('pegawai_id') is-invalid @enderror">
                                <option value="">-- Pilih Pegawai --</option>
                                @foreach($pegawai as $p)
                                    <option value="{{ $p->id }}" {{ old('pegawai_id', $pendapatan->pegawai_id) == $p->id ? 'selected' : '' }}>
                                        {{ $p->nama }}
                                    </option>
                                @endforeach
                            </select>
                            @error('pegawai_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="bulan">Bulan</label>
                            <input type="month" name="bulan" class="form-control @error('bulan') is-invalid @enderror"
                                value="{{ old('bulan', \Carbon\Carbon::parse($pendapatan->bulan)->format('Y-m')) }}">
                            @error('bulan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="nilai_bruto">Nilai Bruto</label>
                            <input type="text" id="brutoFormatted" class="form-control @error('nilai_bruto') is-invalid @enderror"
                                value="{{ number_format(old('nilai_bruto', $pendapatan->nilai_bruto), 0, ',', '.') }}">
                            <input type="hidden" name="nilai_bruto" id="bruto" value="{{ old('nilai_bruto', $pendapatan->nilai_bruto) }}">
                            @error('nilai_bruto')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="potongan">Potongan</label>
                            <input type="text" id="potonganFormatted" class="form-control @error('potongan') is-invalid @enderror"
                                value="{{ number_format(old('potongan', $pendapatan->potongan), 0, ',', '.') }}">
                            <input type="hidden" name="potongan" id="potongan" value="{{ old('potongan', $pendapatan->potongan) }}">
                            @error('potongan')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="jenis_id">Jenis Pendapatan</label>
                            <select name="jenis_id" class="form-control @error('jenis_id') is-invalid @enderror">
                                <option value="">-- Pilih Jenis --</option>
                                @foreach($jenisPendapatan as $jenis)
                                    <option value="{{ $jenis->id }}" {{ old('jenis_id', $pendapatan->jenis_id) == $jenis->id ? 'selected' : '' }}>
                                        {{ $jenis->nama_jenis_pendapatan }}
                                    </option>
                                @endforeach
                            </select>
                            @error('jenis_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="pajak">Pajak</label>
                            <input type="text" id="pajakFormatted" class="form-control @error('pajak') is-invalid @enderror"
                                value="{{ number_format(old('pajak', $pendapatan->pajak), 0, ',', '.') }}">
                            <input type="hidden" name="pajak" id="pajak" value="{{ old('pajak', $pendapatan->pajak) }}">
                            @error('pajak')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="nilai_netto">Nilai Netto</label>
                            <input type="text" id="nettoFormatted" class="form-control" readonly
                                value="{{ number_format(old('nilai_netto', $pendapatan->nilai_netto), 0, ',', '.') }}">
                            <input type="hidden" name="nilai_netto" id="netto" value="{{ old('nilai_netto', $pendapatan->nilai_netto) }}">
                        </div>

                        <div class="form-group">
                            <label for="tanggal_masuk">Tanggal Masuk</label>
                            <input type="date" name="tanggal_masuk" class="form-control @error('tanggal_masuk') is-invalid @enderror"
                                value="{{ old('tanggal_masuk', $pendapatan->tanggal_masuk) }}">
                            @error('tanggal_masuk')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary">Update</button>
                <a href="{{ route('pendapatan.index') }}" class="btn btn-secondary">Kembali</a>
            </form>

            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<script>
    const formatter = new Intl.NumberFormat('id-ID');

    function toNumber(str) {
        return parseFloat(str.replace(/\./g, '').replace(',', '.')) || 0;
    }

    function updateNetto() {
        const bruto = toNumber(document.getElementById('brutoFormatted').value);
        const pajak = toNumber(document.getElementById('pajakFormatted').value);
        const potongan = toNumber(document.getElementById('potonganFormatted').value);
        const netto = bruto - pajak - potongan;

        document.getElementById('nettoFormatted').value = formatter.format(netto);
        document.getElementById('netto').value = netto;
    }

    function formatField(formattedId, realId) {
        const formattedInput = document.getElementById(formattedId);
        const realInput = document.getElementById(realId);

        formattedInput.addEventListener('input', function() {
            let value = this.value.replace(/\D/g, '');
            value = value.replace(/^0+/, '');
            if (value === '') value = '0';
            this.value = formatter.format(value);
            realInput.value = value;
            updateNetto();
        });
    }

    formatField('brutoFormatted', 'bruto');
    formatField('pajakFormatted', 'pajak');
    formatField('potonganFormatted', 'potongan');
    updateNetto();
</script>
@endsection
