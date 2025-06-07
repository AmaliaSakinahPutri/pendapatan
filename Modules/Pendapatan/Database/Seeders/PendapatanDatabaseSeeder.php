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

        DB::table('pegawais')->insert([
            [
                'nip'                    => '199311252019031011',
                'nama'                   => 'Nova Victor Geral Dino, S.E.',
                'noid'                   => null,
                'id_staff'               => 18,
                'id_jurusan'             => null,
                'id_prodi'               => null,
                'jenis_kelamin'          => null,
                'agama'                  => null,
                'no_tlp'                 => null,
                'tgl_lahir'              => null,
                'gol_darah'              => null,
                'gelar_dpn'              => null,
                'gelar_blk'              => null,
                'status_kawin'           => null,
                'kelurahan'              => null,
                'kecamatan'              => null,
                'kota'                   => null,
                'provinsi'               => null,
                'askes'                  => null,
                'kode_dosen'             => null,
                'npwp'                   => null,
                'nidn'                   => null,
                'username'               => null,
                'id_dosen_feeder'        => null,
                'id_status_dosen_feeder' => null,
                'id_reg_dosen_feeder'    => null,
                'status_karyawan'        => null,
                'created_at'             => now(),
                'updated_at'             => now(),
            ],
            [
                'nip'                    => '198311052015041001',
                'nama'                   => 'Devit Suwardiyanto, S.Si., M.T.',
                'noid'                   => null,
                'id_staff'               => 4,
                'id_jurusan'             => null,
                'id_prodi'               => null,
                'jenis_kelamin'          => null,
                'agama'                  => null,
                'no_tlp'                 => null,
                'tgl_lahir'              => null,
                'gol_darah'              => null,
                'gelar_dpn'              => null,
                'gelar_blk'              => null,
                'status_kawin'           => null,
                'kelurahan'              => null,
                'kecamatan'              => null,
                'kota'                   => null,
                'provinsi'               => null,
                'askes'                  => null,
                'kode_dosen'             => null,
                'npwp'                   => null,
                'nidn'                   => null,
                'username'               => null,
                'id_dosen_feeder'        => null,
                'id_status_dosen_feeder' => null,
                'id_reg_dosen_feeder'    => null,
                'status_karyawan'        => null,
                'created_at'             => now(),
                'updated_at'             => now(),
            ],
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

        // Buat user Nova dan assign role pegawai
        $nova = User::create([
            'name'       => 'Nova Victor Geral Dino, SE',
            'email'      => 'nova.victor@example.com',
            'username'   => 'novavictor',
            'password'   => Hash::make('12345678'),
            'nip'        => '199311252019031011',
            'unit'       => 72,
            'staff'      => 22,
            'status'     => 2,
            'role_aktif' => 'pegawai',
        ]);
        $nova->assignRole($rolePegawai);

        // Buat user Devit dan assign role pegawai
        $devit = User::create([
            'name'       => 'Devit Suwardiyanto, S.Si., M.T.',
            'email'      => 'devit.suwardiyanto@example.com',
            'username'   => 'devitsuwardiyanto',
            'password'   => Hash::make('12345678'),
            'nip'        => '198311052015041001',
            'unit'       => 75,
            'staff'      => 25,
            'status'     => 2,
            'role_aktif' => 'pegawai',
        ]);
        $devit->assignRole($rolePegawai);

        $keuangan = User::create([
            'name'       => 'keuangan',
            'email'      => 'keuangan@example.com',
            'username'   => 'keuangan',
            'password'   => Hash::make('12345678'),
            'nip'        => '0',
            'unit'       => 75,
            'staff'      => 25,
            'status'     => 2,
            'role_aktif' => 'keuangan',
        ]);
        $keuangan->assignRole($roleKeuangan);
    }
}
