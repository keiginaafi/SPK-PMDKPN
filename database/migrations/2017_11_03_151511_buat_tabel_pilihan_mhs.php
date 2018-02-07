<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BuatTabelPilihanMhs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pilihan_mhs', function (Blueprint $table) {
            $table->increments('id_pilihan');
            $table->string('no_pendaftar', 12);
            $table->foreign('no_pendaftar')->references('no_pendaftar')->on('mahasiswa');
            $table->char('pilihan_ke', 1);
            //$table->string('pilihan_poltek', 60);
            $table->string('pilihan_prodi', 6);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('pilihan_mhs');
    }
}
