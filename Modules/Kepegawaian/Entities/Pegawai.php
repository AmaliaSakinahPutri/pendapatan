<?php

namespace Modules\Kepegawaian\Entities;

use App\Models\Core\User;
use Modules\Pengadaan\Entities\Unit;
use Illuminate\Database\Eloquent\Model;
use Modules\Pengadaan\Entities\SubPerencanaan;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pegawai extends Model
{
    use HasFactory;
    protected $connection = 'mysql';
    protected $guarded = ['id'];
    
    protected static function newFactory()
    {
        return \Modules\Kepegawaian\Database\factories\PegawaiFactory::new();
    }

    // Relasi ke SubPerencanaan sebagai PIC
    public function subPerencanaansAsPic()
    {
        return $this->hasMany(SubPerencanaan::class, 'pic_id');
    }

    // Relasi ke SubPerencanaan sebagai PPK
    public function subPerencanaansAsPpk()
    {
        return $this->hasMany(SubPerencanaan::class, 'ppk_id');
    }

    // Relasi ke SubPerencanaan sebagai PP
    public function subPerencanaansAsPp()
    {
        return $this->hasMany(SubPerencanaan::class, 'pp_id');
    }

    // Relasi ke Model Unit
    public function unit()
    {
        return $this->belongsTo(Unit::class, 'unit_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'pegawais_id');
    }
}
