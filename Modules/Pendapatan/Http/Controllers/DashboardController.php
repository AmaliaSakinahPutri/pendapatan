<?php
namespace Modules\Pendapatan\Http\Controllers;

use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Modules\Pendapatan\Entities\Pegawai;
use Modules\Pendapatan\Entities\Pendapatan;

class DashboardController extends Controller
{
    /**
     * Display a listing of the resource.
     * @return Renderable
     */
    public function index()
    {
        if (isset(auth()->user()->role_aktif) && ! empty(auth()->user()->role_aktif)) {
                $view = 'pendapatan::dashboard.' . auth()->user()->role_aktif;

                // if (View::exists($view)) {
                    // Jika role keuangan, kirim data grafik
                    if (auth()->user()->role_aktif == 'keuangan') {
                        $data = Pendapatan::selectRaw('DATE_FORMAT(bulan, "%Y-%m") as bulan, SUM(nilai_netto) as total')
                            ->groupByRaw('DATE_FORMAT(bulan, "%Y-%m")')
                            ->orderByRaw('DATE_FORMAT(bulan, "%Y-%m")')
                            ->get();

                        $labels = $data->pluck('bulan');
                        $totals = $data->pluck('total');

                        return view($view, compact('labels', 'totals'));
                    }
                    // Jika role pegawai, kirim data grafik
                    if (auth()->user()->role_aktif == 'pegawai') {
                        $user = Pegawai::where('nip', auth()->user()->nip)->first();
                        $data = Pendapatan::selectRaw('DATE_FORMAT(bulan, "%Y-%m") as bulan, SUM(nilai_netto) as total')
                            ->where('pegawai_id', $user->id)
                            ->groupByRaw('DATE_FORMAT(bulan, "%Y-%m")')
                            ->orderByRaw('DATE_FORMAT(bulan, "%Y-%m")')
                            ->get();

                        $labels = $data->pluck('bulan');
                        $totals = $data->pluck('total');

                        return view($view, compact('labels', 'totals'));
                    }

                    return view($view);
                // }
            }

            // return view('home');
    }

    /**
     * Show the form for creating a new resource.
     * @return Renderable
     */
    public function create()
    {
        return view('pendapatan::create');
    }

    /**
     * Store a newly created resource in storage.
     * @param Request $request
     * @return Renderable
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Show the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function show($id)
    {
        return view('pendapatan::show');
    }

    /**
     * Show the form for editing the specified resource.
     * @param int $id
     * @return Renderable
     */
    public function edit($id)
    {
        return view('pendapatan::edit');
    }

    /**
     * Update the specified resource in storage.
     * @param Request $request
     * @param int $id
     * @return Renderable
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     * @param int $id
     * @return Renderable
     */
    public function destroy($id)
    {
        //
    }
}
