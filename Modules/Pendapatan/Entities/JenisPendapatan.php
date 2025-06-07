<?php
namespace Modules\Pendapatan\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JenisPendapatan extends Model
{
    use HasFactory;

    protected $table    = 'jenis_pendapatan';
    protected $fillable = ['nama_jenis_pendapatan'];

    public function pendapatan()
    {
        return $this->hasMany(Pendapatan::class, 'jenis_id');
    }
    protected static function newFactory()
    {
        return \Modules\Pendapatan\Database\factories\JenisPendapatanFactory::new ();
    }
}
