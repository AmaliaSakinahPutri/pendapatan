<?php
namespace Modules\Pendapatan\Entities;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pendapatan extends Model
{
    use HasFactory;

    protected $table    = 'pendapatan';
    protected $fillable = ['pegawai_id', 'jenis_id', 'bulan', 'tahun', 'nilai_bruto', 'pajak', 'potongan', 'nilai_netto', 'tanggal_masuk'];

    public function pegawai()
    {
        return $this->belongsTo(Pegawai::class);
    }

    public function jenisPendapatan()
    {
        return $this->belongsTo(JenisPendapatan::class, 'jenis_id');
    }
    protected static function newFactory()
    {
        return \Modules\Pendapatan\Database\factories\PendapatanFactory::new ();
    }
}