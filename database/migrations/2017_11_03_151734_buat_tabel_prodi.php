<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BuatTabelProdi extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prodi', function (Blueprint $table) {
            $table->string('kode_prodi', 15)->primary()->index();
            $table->string('nama_prodi', 50);
            $table->unsignedTinyInteger('kuota_sma');
            $table->unsignedTinyInteger('kuota_smk');
            //$table->unsignedTinyInteger('kuota_cadangan');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('prodi');
    }
}
