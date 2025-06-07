<?php
namespace Modules\Pendapatan\Http\Controllers;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Modules\Pendapatan\Entities\JenisPendapatan;
use Modules\Pendapatan\Entities\Pegawai;
use Modules\Pendapatan\Entities\Pendapatan;
use Modules\Pendapatan\Imports\PendapatanImport;

class PendapatanController extends Controller
{
    public function cetakbulan($pegawai_id, $bulan, $tahun)
    {
        $pendapatan = Pendapatan::with('jenisPendapatan')
            ->where('pegawai_id', $pegawai_id)
            ->whereMonth('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();

        $pegawai = DB::table('pegawais')
        ->join('staffs', 'pegawais.id_staff', '=', 'staffs.id')
        ->where('pegawais.id', $pegawai_id)
        ->select('pegawais.*', 'staffs.nama as nama_staff')
        ->first();
        if ($pendapatan->isEmpty() || ! $pegawai) {
            return redirect()->back()->with('error', 'Data pendapatan tidak ditemukan.');
        }

        $pdf = Pdf::loadView('pendapatan::pendapatan.cetak', compact('pendapatan', 'pegawai', 'bulan', 'tahun'));

        return $pdf->stream('laporan-pendapatan-' . $pegawai->nama . '-' . $bulan . '-' . $tahun . '.pdf');
    }
    public function cetaktahun($pegawai_id, $tahun)
    {
        $pendapatan = Pendapatan::where('pegawai_id', $pegawai_id)
            ->where('tahun', $tahun)
            ->get();

        $pegawai = DB::table('pegawais')
            ->join('staffs', 'pegawais.id_staff', '=', 'staffs.id')
            ->where('pegawais.id', $pegawai_id)
            ->select('pegawais.*', 'staffs.nama as nama_staff')
            ->first();

        if ($pendapatan->isEmpty() || ! $pegawai) {
            return redirect()->back()->with('error', 'Data pendapatan tidak ditemukan.');
        }

        $pdf = Pdf::loadView('pendapatan::pendapatan.cetak_tahun', compact('pendapatan', 'pegawai', 'tahun'));

        return $pdf->stream('laporan-pendapatan-' . $pegawai->nama . '-' . $tahun . '.pdf');
    }

    public function importExcel(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv',
        ]);

        $import = new PendapatanImport();
        Excel::import($import, $request->file('file'));

        $pendapatan = DB::table('pendapatan')
            ->join('pegawais', 'pendapatan.pegawai_id', '=', 'pegawais.id')
            ->selectRaw('
                pendapatan.pegawai_id,
                pegawais.nama,
                pegawais.no_tlp,
                DATE_FORMAT(pendapatan.bulan, "%Y-%m") as bulan,
                YEAR(pendapatan.bulan) as tahun,
                SUM(pendapatan.nilai_bruto) as total_bruto,
                SUM(pendapatan.pajak) as total_pajak,
                SUM(pendapatan.potongan) as total_potongan,
                SUM(pendapatan.nilai_netto) as total_netto
            ')
            ->groupBy('pendapatan.pegawai_id', 'pegawais.nama', 'pegawais.no_tlp', DB::raw('DATE_FORMAT(pendapatan.bulan, "%Y-%m")'), DB::raw('YEAR(pendapatan.bulan)'))
            ->get();

        foreach ($pendapatan as $item) {
            if (! empty($item->no_tlp)) {
                $message = "Rincian Gaji Anda Bulan {$item->bulan}:\n"
                . "Nama: {$item->nama}\n"
                . "Total Bruto: Rp" . number_format($item->total_bruto, 0, ',', '.') . "\n"
                . "Pajak: Rp" . number_format($item->total_pajak, 0, ',', '.') . "\n"
                . "Potongan: Rp" . number_format($item->total_potongan, 0, ',', '.') . "\n"
                . "Total Netto: Rp" . number_format($item->total_netto, 0, ',', '.');

                $payload = json_encode([
                    'phone_number' => $item->no_tlp,
                    'message'      => $message,
                ]);

                $curl = curl_init();
                curl_setopt_array($curl, [
                    CURLOPT_URL            => 'https://sit.poliwangi.ac.id/v2/api/v1/sitapi/wa',
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_CUSTOMREQUEST  => 'POST',
                    CURLOPT_POSTFIELDS     => $payload,
                    CURLOPT_HTTPHEADER     => [
                        'Content-Type: application/json',
                    ],
                ]);

                $response = curl_exec($curl);
                if (curl_errno($curl)) {
                    Log::error('WA API Error: ' . curl_error($curl));
                }
                curl_close($curl);

                Log::info('WA API Response: ' . $response);
            }
        }

        // Jika tidak ada error saat import, tampilkan pesan sukses
        if (empty($import->errors)) {
            return redirect()->route('pendapatan.index')->with('success', 'Import berhasil!');
        }

        return view('pendapatan::pendapatan.index', [
            'pendapatan'   => $pendapatan,
            'importErrors' => $import->errors, // kirim error custom ke view
        ]);
    }
    // Menampilkan semua data pendapatan
    public function index()
    {
        $pendapatan = DB::table('pendapatan')
            ->join('pegawais', 'pendapatan.pegawai_id', '=', 'pegawais.id')
            ->selectRaw('
                pendapatan.pegawai_id,
                pegawais.nama,
                DATE_FORMAT(pendapatan.bulan, "%Y-%m") as bulan,
                YEAR(pendapatan.bulan) as tahun,
                SUM(pendapatan.nilai_bruto) as total_bruto,
                SUM(pendapatan.pajak) as total_pajak,
                SUM(pendapatan.potongan) as total_potongan,
                SUM(pendapatan.nilai_netto) as total_netto
            ')
            ->groupBy(
                'pendapatan.pegawai_id',
                'pegawais.nama',
                DB::raw('DATE_FORMAT(pendapatan.bulan, "%Y-%m")'),
                DB::raw('YEAR(pendapatan.bulan)')
            )
            ->orderByDesc(DB::raw('DATE_FORMAT(pendapatan.bulan, "%Y-%m")'))
            ->get();

        return view('pendapatan::pendapatan.index', compact('pendapatan'));
    }

    // Menampilkan form input pendapatan
    public function create()
    {
        $pegawai         = Pegawai::all();
        $jenisPendapatan = JenisPendapatan::all();
        return view('pendapatan::pendapatan.create', compact('pegawai', 'jenisPendapatan'));
    }

    // Menyimpan data pendapatan
    public function store(Request $request)
    {
        // Ambil nilai input
        $bruto    = $request->nilai_bruto;
        $pajak    = $request->pajak;
        $potongan = $request->potongan;

        // Validasi tambahan: pajak + potongan tidak boleh lebih besar dari bruto
        if ($pajak > $bruto) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['pajak' => 'Pajak tidak boleh lebih besar dari nilai bruto.']);
        }

        if ($potongan > $bruto) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['potongan' => 'Potongan tidak boleh lebih besar dari nilai bruto.']);
        }

        $request->validate([
            'pegawai_id'    => 'required|exists:pegawai,id',
            'jenis_id'      => 'required|exists:jenis_pendapatan,id',
            'bulan'         => 'required',
            'nilai_bruto'   => 'required|numeric|min:0',
            'pajak'         => 'required|numeric|min:0',
            'potongan'      => 'required|numeric|min:0',
            'nilai_netto'   => 'required|numeric|min:0',
            'tanggal_masuk' => 'required|date',
        ], [
            'pegawai_id.required'    => 'Pegawai wajib dipilih.',
            'pegawai_id.exists'      => 'Pegawai tidak ditemukan.',
            'jenis_id.required'      => 'Jenis pendapatan wajib dipilih.',
            'jenis_id.exists'        => 'Jenis pendapatan tidak ditemukan.',
            'bulan.required'         => 'Bulan wajib diisi.',
            'nilai_bruto.required'   => 'Nilai bruto wajib diisi.',
            'nilai_bruto.numeric'    => 'Nilai bruto harus berupa angka.',
            'nilai_bruto.min'        => 'Nilai bruto minimal 0.',
            'pajak.required'         => 'Pajak wajib diisi.',
            'pajak.numeric'          => 'Pajak harus berupa angka.',
            'pajak.min'              => 'Pajak minimal 0.',
            'potongan.required'      => 'Potongan wajib diisi.',
            'potongan.numeric'       => 'Potongan harus berupa angka.',
            'potongan.min'           => 'Potongan minimal 0.',
            'nilai_netto.required'   => 'Nilai netto wajib diisi.',
            'nilai_netto.numeric'    => 'Nilai netto harus berupa angka.',
            'nilai_netto.min'        => 'Nilai netto minimal 0.',
            'tanggal_masuk.required' => 'Tanggal masuk wajib diisi.',
            'tanggal_masuk.date'     => 'Format tanggal masuk tidak valid.',
        ]);

        $data = $request->all();

        // Format bulan agar valid untuk date: YYYY-MM -> YYYY-MM-01
        $data['bulan'] = $request->bulan . '-01';

        // Ambil tahun dari input bulan
        $data['tahun'] = date('Y', strtotime($data['bulan']));

        // Cek duplikasi
        $exists = Pendapatan::where('pegawai_id', $data['pegawai_id'])
            ->where('jenis_id', $data['jenis_id'])
            ->where('bulan', $data['bulan'])
            ->where('tahun', $data['tahun'])
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['duplicate' => 'Data pendapatan untuk kombinasi pegawai, jenis, bulan, dan tahun ini sudah ada.']);
        }

        Pendapatan::create($data);

        // Ambil data pegawai
        $pegawai = Pegawai::find($request->pegawai_id);

        // Kirim WhatsApp jika nomor ada
        if ($pegawai && ! empty($pegawai->no_tlp)) {
            $bulan   = date('Y-m', strtotime($data['bulan']));
            $message = "Rincian Gaji Anda Bulan {$bulan}:\n"
            . "Nama: {$pegawai->nama}\n"
            . "Jenis: " . JenisPendapatan::find($request->jenis_id)->nama_jenis_pendapatan . "\n"
            . "Total Bruto: Rp" . number_format($bruto, 0, ',', '.') . "\n"
            . "Pajak: Rp" . number_format($pajak, 0, ',', '.') . "\n"
            . "Potongan: Rp" . number_format($potongan, 0, ',', '.') . "\n"
            . "Total Netto: Rp" . number_format($request->nilai_netto, 0, ',', '.');

            $payload = json_encode([
                'phone_number' => $pegawai->no_tlp,
                'message'      => $message,
            ]);

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL            => 'https://sit.poliwangi.ac.id/v2/api/v1/sitapi/wa',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_CUSTOMREQUEST  => 'POST',
                CURLOPT_POSTFIELDS     => $payload,
                CURLOPT_HTTPHEADER     => [
                    'Content-Type: application/json',
                ],
            ]);

            $response = curl_exec($curl);
            if (curl_errno($curl)) {
                Log::error('WA API Error: ' . curl_error($curl));
            }
            curl_close($curl);

            Log::info('WA API Response: ' . $response);
        }

        return redirect()->route('pendapatan::pendapatan.index')->with('success', 'Pendapatan berhasil ditambahkan.');
    }

    // Menampilkan form edit pendapatan
    public function edit($id)
    {
        $pendapatan      = Pendapatan::findOrFail($id);
        $pegawai         = Pegawai::all();
        $jenisPendapatan = JenisPendapatan::all();
        return view('pendapatan::pendapatan.edit', compact('pendapatan', 'pegawai', 'jenisPendapatan'));
    }

    // Update data pendapatan
    public function update(Request $request, $id)
    {

        // Ambil nilai input
        $bruto    = $request->nilai_bruto;
        $pajak    = $request->pajak;
        $potongan = $request->potongan;

        // Validasi tambahan: pajak + potongan tidak boleh lebih besar dari bruto
        if ($pajak > $bruto) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['pajak' => 'Pajak tidak boleh lebih besar dari nilai bruto.']);
        }

        if ($potongan > $bruto) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['potongan' => 'Potongan tidak boleh lebih besar dari nilai bruto.']);
        }

        $request->validate([
            'pegawai_id'    => 'required|exists:pegawais,id',
            'jenis_id'      => 'required|exists:jenis_pendapatan,id',
            'bulan'         => 'required',
            'nilai_bruto'   => 'required|numeric|min:0',
            'pajak'         => 'required|numeric|min:0',
            'potongan'      => 'required|numeric|min:0',
            'nilai_netto'   => 'required|numeric|min:0',
            'tanggal_masuk' => 'required|date',
        ], [
            'pegawai_id.required'    => 'Pegawai wajib dipilih.',
            'pegawai_id.exists'      => 'Pegawai tidak ditemukan.',
            'jenis_id.required'      => 'Jenis pendapatan wajib dipilih.',
            'jenis_id.exists'        => 'Jenis pendapatan tidak ditemukan.',
            'bulan.required'         => 'Bulan wajib diisi.',
            'nilai_bruto.required'   => 'Nilai bruto wajib diisi.',
            'nilai_bruto.numeric'    => 'Nilai bruto harus berupa angka.',
            'nilai_bruto.min'        => 'Nilai bruto minimal 0.',
            'pajak.required'         => 'Pajak wajib diisi.',
            'pajak.numeric'          => 'Pajak harus berupa angka.',
            'pajak.min'              => 'Pajak minimal 0.',
            'potongan.required'      => 'Potongan wajib diisi.',
            'potongan.numeric'       => 'Potongan harus berupa angka.',
            'potongan.min'           => 'Potongan minimal 0.',
            'nilai_netto.required'   => 'Nilai netto wajib diisi.',
            'nilai_netto.numeric'    => 'Nilai netto harus berupa angka.',
            'nilai_netto.min'        => 'Nilai netto minimal 0.',
            'tanggal_masuk.required' => 'Tanggal masuk wajib diisi.',
            'tanggal_masuk.date'     => 'Format tanggal masuk tidak valid.',
        ]);

        $pendapatan = Pendapatan::findOrFail($id);

        $data          = $request->all();
        $data['bulan'] = $request->bulan . '-01';
        $data['tahun'] = date('Y', strtotime($data['bulan']));

        $pendapatan->update($data);

        return redirect()->route('pendapatan.index')->with('success', 'Pendapatan berhasil diperbarui.');
    }

    public function detail($pegawai_id, $bulan, $tahun)
    {
        $pendapatan = Pendapatan::with('jenisPendapatan')
            ->where('pegawai_id', $pegawai_id)
            ->whereMonth('bulan', $bulan)
            ->where('tahun', $tahun)
            ->get();
        $pegawai = Pegawai::find($pegawai_id);
        return view('pendapatan::pendapatan.detail', compact('pendapatan'));
    }

    public function detailPertahun($pegawai_id, $tahun)
    {
        $pendapatan = Pendapatan::with('jenisPendapatan')
            ->where('pegawai_id', $pegawai_id)
            ->where('tahun', $tahun)
            ->orderBy('bulan', 'asc')
            ->get();

        $pegawai = Pegawai::findOrFail($pegawai_id);

        return view('pendapatan::pendapatan.detail', compact('pendapatan'));
    }

    // Hapus data pendapatan
    public function destroy($id)
    {
        $pendapatan = Pendapatan::findOrFail($id);
        $pendapatan->delete();

        return redirect()->route('pendapatan::pendapatan.index')->with('success', 'Pendapatan berhasil dihapus.');
    }
}
