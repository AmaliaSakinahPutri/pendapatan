<?php

namespace Modules\Pendapatan\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pegawai extends Model
{
    use HasFactory;

    protected $table = 'pegawais';
    protected $guarded = ['id'];

    // public function staff()
    // {
    //     return $this->belongsTo(Staff::class);
    // }
    
    protected static function newFactory()
    {
        return \Modules\Pendapatan\Database\factories\PegawaiFactory::new();
    }
}