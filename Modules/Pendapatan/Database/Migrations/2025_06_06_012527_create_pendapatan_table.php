<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePendapatanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pendapatan', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->constrained('pegawais')->onDelete('cascade');
            $table->foreignId('jenis_id')->constrained('jenis_pendapatan')->onDelete('cascade');
            $table->date('bulan');
            $table->year('tahun');
            $table->decimal('nilai_bruto', 15, 2);
            $table->decimal('pajak', 15, 2);
            $table->decimal('potongan', 15, 2);
            $table->decimal('nilai_netto', 15, 2);
            $table->date('tanggal_masuk');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pendapatan');
    }
}