<?php
namespace Modules\Pendapatan\Database\Seeders;

use App\Models\Core\Menu;
use App\Models\Core\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PendapatanDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Model::unguard();
        // Hapus dulu menu modul 'Pendapatan' kalau ada
        Menu::where('modul', 'Pendapatan Pegawai')->delete();
        Menu::where('modul', 'Pendapatan Keuangan')->delete();

        // === Pendapatan untuk Pegawai ===
        $menuPegawai = Menu::create([
            'modul'     => 'Pendapatan Pegawai',
            'label'     => 'Pendapatan',
            'url'       => 'pendapatan/dashboard',
            'can'       => serialize(['pegawai']),
            'icon'      => 'fas fa-dollar-sign',
            'urut'      => 4,
            'parent_id' => 0,
            'active'    => serialize(['pendapatan/dashboard']),
        ]);

        if ($menuPegawai) {
            Menu::create([
                'modul'     => 'Pendapatan Pegawai',
                'label'     => 'Dashboard',
                'url'       => 'pendapatan/dashboard',
                'can'       => serialize(['pegawai']),
                'icon'      => 'fas fa-tachometer-alt',
                'urut'      => 1,
                'parent_id' => $menuPegawai->id,
                'active'    => serialize(['pendapatan/dashboard*']),
            ]);
            Menu::create([
                'modul'     => 'Pendapatan Pegawai',
                'label'     => 'Per Bulan',
                'url'       => 'pendapatan/perbulan',
                'can'       => serialize(['pegawai']),
                'icon'      => 'fas fa-calendar-alt',
                'urut'      => 2,
                'parent_id' => $menuPegawai->id,
                'active'    => serialize(['pendapatan/perbulan*']),
            ]);
            Menu::create([
                'modul'     => 'Pendapatan Pegawai',
                'label'     => 'Per Tahun',
                'url'       => 'pendapatan/pertahun',
                'can'       => serialize(['pegawai']),
                'icon'      => 'fas fa-calendar',
                'urut'      => 3,
                'parent_id' => $menuPegawai->id,
                'active'    => serialize(['pendapatan/pertahun*']),
            ]);
        }

        // === Pendapatan untuk Keuangan ===
        $menuKeuangan = Menu::create([
            'modul'     => 'Pendapatan Keuangan',
            'label'     => 'Pendapatan',
            'url'       => 'pendapatan/index',
            'can'       => serialize(['keuangan']),
            'icon'      => 'fas fa-dollar-sign',
            'urut'      => 5,
            'parent_id' => 0,
            'active'    => serialize(['pendapatan/index']),
        ]);

        if ($menuKeuangan) {
            Menu::create([
                'modul'     => 'Pendapatan Keuangan',
                'label'     => 'Dashboard',
                'url'       => 'pendapatan/dashboard',
                'can'       => serialize(['keuangan']),
                'icon'      => 'fas fa-tachometer-alt',
                'urut'      => 1,
                'parent_id' => $menuKeuangan->id,
                'active'    => serialize(['pendapatan/dashboard*']),
            ]);
            Menu::create([
                'modul'     => 'Pendapatan Keuangan',
                'label'     => 'Per Bulan',
                'url'       => 'pendapatan/perbulan',
                'can'       => serialize(['keuangan']),
                'icon'      => 'fas fa-calendar-alt',
                'urut'      => 2,
                'parent_id' => $menuKeuangan->id,
                'active'    => serialize(['pendapatan/perbulan*']),
            ]);
            Menu::create([
                'modul'     => 'Pendapatan Keuangan',
                'label'     => 'Per Tahun',
                'url'       => 'pendapatan/pertahun',
                'can'       => serialize(['keuangan']),
                'icon'      => 'fas fa-calendar',
                'urut'      => 3,
                'parent_id' => $menuKeuangan->id,
                'active'    => serialize(['pendapatan/pertahun*']),
            ]);
            Menu::create([
                'modul'     => 'Pendapatan Keuangan',
                'label'     => 'Pendapatan',
                'url'       => 'pendapatan/index',
                'can'       => serialize(['keuangan']),
                'icon'      => 'fas fa-file-invoice-dollar',
                'urut'      => 4,
                'parent_id' => $menuKeuangan->id,
                'active'    => serialize(['pendapatan/index*']),
            ]);
        }

       
    }
}
