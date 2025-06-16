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
         Menu::create([
                'modul'     => 'Pendapatan Pegawai',
                'label'     => 'Dashboard',
                'url'       => 'pendapatan/dashboard',
                'can'       => serialize(['pegawai']),
                'icon'      => 'fas fa-tachometer-alt',
                'urut'      => 3,
                'parent_id' => 0,
                'active'    => serialize(['pendapatan/dashboard*']),
            ]);
            Menu::create([
                'modul'     => 'Pendapatan Pegawai',
                'label'     => 'Per Bulan',
                'url'       => 'pendapatan/pegawai_perbulan',
                'can'       => serialize(['pegawai']),
                'icon'      => 'fas fa-calendar-alt',
                'urut'      => 2,
                'parent_id' => 0,
                'active'    => serialize(['pendapatan/pegawai_perbulan*']),
            ]);
            Menu::create([
                'modul'     => 'Pendapatan Pegawai',
                'label'     => 'Per Tahun',
                'url'       => 'pendapatan/pegawai_pertahun',
                'can'       => serialize(['pegawai']),
                'icon'      => 'fas fa-calendar',
                'urut'      => 2,
                'parent_id' => 0,
                'active'    => serialize(['pendapatan/pegawai_pertahun*']),
            ]);

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
        Menu::create([
            'modul'     => 'Pendapatan Keuangan',
            'label'     => 'Dashboard',
            'url'       => 'pendapatan/dashboard',
            'can'       => serialize(['keuangan']),
            'icon'      => 'fas fa-tachometer-alt',
            'urut'      => 4,
            'parent_id' => 0,
            'active'    => serialize(['pendapatan/dashboard*']),
        ]);
        Menu::create([
            'modul'     => 'Pendapatan Keuangan',
            'label'     => 'Per Bulan',
            'url'       => 'pendapatan/perbulan',
            'can'       => serialize(['keuangan']),
            'icon'      => 'fas fa-calendar-alt',
            'urut'      => 2,
            'parent_id' => 0,
            'active'    => serialize(['pendapatan/perbulan*']),
        ]);
        Menu::create([
            'modul'     => 'Pendapatan Keuangan',
            'label'     => 'Per Tahun',
            'url'       => 'pendapatan/pertahun',
            'can'       => serialize(['keuangan']),
            'icon'      => 'fas fa-calendar',
            'urut'      => 1,
            'parent_id' => 0,
            'active'    => serialize(['pendapatan/pertahun*']),
        ]);
        Menu::create([
            'modul'     => 'Pendapatan Keuangan',
            'label'     => 'Pendapatan',
            'url'       => 'pendapatan/index',
            'can'       => serialize(['keuangan']),
            'icon'      => 'fas fa-file-invoice-dollar',
            'urut'      => 3,
            'parent_id' => 0,
            'active'    => serialize(['pendapatan/index*']),
        ]);

        // Buat role pegawai dan keuangan jika belum ada
        $rolePegawai  = Role::firstOrCreate(['name' => 'pegawai']);
        $roleKeuangan = Role::firstOrCreate(['name' => 'keuangan']);

        // Daftar permission sesuai route
        $permissions = [
            'pendapatan.dashboard',
            'pendapatan.index',
            'pendapatan.create',
            'pendapatan.store',
            'pendapatan.edit',
            'pendapatan.update',
            'pendapatan.destroy',
            'pendapatan.import',
            'pendapatan.perbulan',
            'pendapatan.pertahun',
            'pendapatan.detail',
            'pendapatan.detail_pertahun',
            'pendapatan.cetak.bulan',
            'pendapatan.cetak.tahun',
            'logout.perform',
            'login.show',
            'login.perform',
            'home.index',
            'adminlte.darkmode.toggle',
        ];

        // Buat permission jika belum ada
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission]);
        }

        // Sinkronisasi permission ke role pegawai dan keuangan
        $rolePegawai->syncPermissions($permissions);
        $roleKeuangan->syncPermissions($permissions);

       
    }
}
