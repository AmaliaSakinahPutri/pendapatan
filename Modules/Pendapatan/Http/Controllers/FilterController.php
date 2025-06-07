<?php
namespace Modules\Pendapatan\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Modules\Pendapatan\Entities\Pegawai;

class FilterController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function perbulan(Request $request)
    {
        $query = DB::table('pendapatan')
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
            ');

        // Jika role pegawai, filter data hanya untuk pegawai tersebut
        if (auth()->user()->role_aktif == 'pegawai') {
            $user = Pegawai::where('nip', auth()->user()->nip)->first();
            if ($user) {
                $query->where('pendapatan.pegawai_id', $user->id);
            }
        }

        if ($request->filled('bulan')) {
            $query->whereMonth('pendapatan.bulan', $request->bulan);
        }

        if ($request->filled('tahun')) {
            $query->whereYear('pendapatan.bulan', $request->tahun);
        }

        $query->groupBy(
            'pendapatan.pegawai_id',
            'pegawais.nama',
            DB::raw('DATE_FORMAT(pendapatan.bulan, "%Y-%m")'),
            DB::raw('YEAR(pendapatan.bulan)')
        );

        $pendapatan = $query->get();

        return view('pendapatan::perbulan.index', compact('pendapatan'));
    }

    public function pertahun(Request $request)
    {
        $query = DB::table('pendapatan')
            ->join('pegawais', 'pendapatan.pegawai_id', '=', 'pegawais.id')
            ->selectRaw('
                pendapatan.pegawai_id,
                pegawais.nama,
                YEAR(pendapatan.bulan) as tahun,
                SUM(pendapatan.nilai_bruto) as total_bruto,
                SUM(pendapatan.pajak) as total_pajak,
                SUM(pendapatan.potongan) as total_potongan,
                SUM(pendapatan.nilai_netto) as total_netto
            ');

        // Jika role pegawai, filter data hanya untuk pegawai tersebut
        if (auth()->user()->role_aktif == 'pegawai') {
            $user = Pegawai::where('nip', auth()->user()->nip)->first();
            if ($user) {
                $query->where('pendapatan.pegawai_id', $user->id);
            }
        }

        if ($request->filled('tahun')) {
            $query->whereYear('pendapatan.bulan', $request->tahun);
        }

        $query->groupBy(
            'pendapatan.pegawai_id',
            'pegawais.nama',
            DB::raw('YEAR(pendapatan.bulan)')
        );

        $pendapatan = $query->get();

        return view('pendapatan::pertahun.index', compact('pendapatan'));
    }
}
