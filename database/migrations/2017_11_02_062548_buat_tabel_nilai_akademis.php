<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BuatTabelNilaiAkademis extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nilai_akademis', function (Blueprint $table) {
            $table->increments('id_nilai_akademis');
            $table->string('no_pendaftar', 12);
            $table->foreign('no_pendaftar')->references('no_pendaftar')->on('mahasiswa');
            $table->char('semester', 1);
            $table->char('jenis_nilai', 3);
            $table->string('mapel', 30);
            $table->decimal('nilai_mapel', 4, 2);
            $table->decimal('nilai_mapel_koreksi', 4, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nilai_akademis');
    }
}
