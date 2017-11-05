<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BuatTabelNonAkademis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nilai_non_akademis', function (Blueprint $table) {
            $table->increments('id_prestasi');
            $table->string('no_pendaftar', 12);
            $table->foreign('no_pendaftar')->references('no_pendaftar')->on('mahasiswa');
            $table->string('nama_prestasi', 60);
            $table->string('skala_prestasi', 13);
            $table->string('jenis_prestasi', 8);
            $table->string('juara_prestasi', 7);
            $table->string('tahun_prestasi', 4);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nilai_non_akademis');
    }
}
