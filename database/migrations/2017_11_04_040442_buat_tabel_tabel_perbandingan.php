<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BuatTabelTabelPerbandingan extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tabel_perbandingan', function (Blueprint $table) {
            $table->increments('id_perbandingan');
            $table->integer('id_kriteria_1')->unsigned();
            $table->integer('id_kriteria_2')->unsigned();            
            $table->float('nilai_banding', 2, 2);
            $table->float('normalisasi', 2, 2);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tabel_perbandingan');
    }
}
