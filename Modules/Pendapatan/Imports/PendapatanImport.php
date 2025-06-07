<?php
namespace Modules\Pendapatan\Imports;

use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Modules\Pendapatan\Entities\JenisPendapatan;
use Modules\Pendapatan\Entities\Pegawai;
use Modules\Pendapatan\Entities\Pendapatan;

class PendapatanImport implements ToModel, SkipsOnFailure, WithHeadingRow
{
    use SkipsFailures;

    public $errors = [];

    public function model(array $row)
    {
        $pegawai = Pegawai::where('nip', $row['nip'])->first();

        if (! $pegawai) {
            $this->errors[] = [
                'nama'  => $row['nip'],
                'jenis' => $row['jenis_pendapatan'],
                'pesan' => 'NIP tidak ditemukan',
            ];
            return null;
        }

        if (empty($row['jenis_pendapatan'])) {
            $this->errors[] = [
                'nama'  => $pegawai->nama,
                'jenis' => '-',
                'pesan' => 'Jenis pendapatan tidak boleh kosong',
            ];
            return null;
        }

        $jenisPendapatan = trim($row['jenis_pendapatan'] ?? '');

        $jenis = JenisPendapatan::firstOrCreate([
            'nama_jenis_pendapatan' => $jenisPendapatan,
        ]);

        // Validasi tanggal_pendapatan
        try {
            $tanggal = Carbon::createFromFormat('d-m-Y', $row['tanggal_pendapatan']);
        } catch (\Exception $e) {
            $this->errors[] = [
                'nama'  => $pegawai->nama,
                'jenis' => $row['jenis_pendapatan'],
                'pesan' => 'Format tanggal pendapatan tidak valid',
            ];
            return null;
        }

        // Validasi tanggal_masuk_rekening
        try {
            $tanggalMasuk = Carbon::createFromFormat('d-m-Y', $row['tanggal_masuk_rekening']);
        } catch (\Exception $e) {
            $this->errors[] = [
                'nama'  => $pegawai->nama,
                'jenis' => $row['jenis_pendapatan'],
                'pesan' => 'Format tanggal masuk rekening tidak valid',
            ];
            return null;
        }

        // Validasi nilai angka
        $nilaiBruto = is_numeric($row['bruto']) ? $row['bruto'] : null;
        $pajak      = is_numeric($row['pajak']) ? $row['pajak'] : null;
        $potongan   = is_numeric($row['potongan']) ? $row['potongan'] : null;

        // Validasi masing-masing nilai
        if (is_null($nilaiBruto)) {
            $this->errors[] = [
                'nama'  => $pegawai->nama,
                'jenis' => $row['jenis_pendapatan'],
                'pesan' => 'Nilai bruto kosong',
            ];
        }

        if (is_null($pajak)) {
            $this->errors[] = [
                'nama'  => $pegawai->nama,
                'jenis' => $row['jenis_pendapatan'],
                'pesan' => 'Nilai pajak kosong',
            ];
        }

        if (is_null($potongan)) {
            $this->errors[] = [
                'nama'  => $pegawai->nama,
                'jenis' => $row['jenis_pendapatan'],
                'pesan' => 'Nilai potongan kosong',
            ];
        }

        // Jika semua nilai valid, baru cek logika bruto vs pajak + potongan
        if (! is_null($nilaiBruto) && ! is_null($pajak) && ! is_null($potongan)) {
            if ($nilaiBruto < ($pajak + $potongan)) {
                $this->errors[] = [
                    'nama'  => $pegawai->nama,
                    'jenis' => $row['jenis_pendapatan'],
                    'pesan' => 'Bruto tidak boleh lebih kecil dari jumlah pajak dan potongan',
                ];
            }
        }

        $nilaiBruto = is_numeric($row['bruto']) ? $row['bruto'] : 0;
        $pajak      = is_numeric($row['pajak']) ? $row['pajak'] : 0;
        $potongan   = is_numeric($row['potongan']) ? $row['potongan'] : 0;

        $existing = Pendapatan::where('pegawai_id', $pegawai->id)
            ->where('jenis_id', $jenis->id)
            ->where('bulan', $tanggal->format('Y-m-d'))
            ->where('tahun', $tanggal->format('Y'))
            ->first();

        if ($existing) {
            return $existing; // Jangan buat baru, return yang lama
        }

        return new Pendapatan([
            'pegawai_id'    => $pegawai->id,
            'jenis_id'      => $jenis->id,
            'bulan'         => $tanggal,
            'tahun'         => $tanggal->format('Y'),
            'tanggal_masuk' => $tanggalMasuk,
            'nilai_bruto'   => $nilaiBruto,
            'pajak'         => $pajak,
            'potongan'      => $potongan,
            'nilai_netto'   => $nilaiBruto - $pajak - $potongan,
        ]);

    }
}
