<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BuatTabelSaranPenerimaan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('saran_penerimaan', function (Blueprint $table) {
            $table->increments('id_saran');
            $table->string('no_pendaftar', 12);
            $table->foreign('no_pendaftar')->references('no_pendaftar')->on('mahasiswa');
            $table->string('kode_prodi', 15);
            $table->string('periode', 4);
			      $table->foreign('kode_prodi')->references('kode_prodi')->on('prodi');
            $table->string('ranking', 5);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('saran_penerimaan');
    }
}
