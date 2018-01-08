<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class BuatTabelMahasiswa extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mahasiswa', function (Blueprint $table) {
            $table->string('no_pendaftar', 12)->index()->primary();
            $table->string('nisn', 28);
            $table->string('nama', 60);
            $table->char('jenis_kelamin', 1);
            $table->string('agama', 20);
            $table->string('tgl_lahir', 10);
            $table->string('kecamatan', 25);
            $table->string('kota', 50);
            $table->string('provinsi', 35);
            $table->string('npsn', 8);
            $table->string('tipe_sekolah', 7);
            $table->string('jenis_sekolah', 6);
            $table->char('akreditasi_sekolah', 1);
            $table->string('jurusan_asal', 60);
            $table->string('pekerjaan_ayah', 60);
            $table->string('pendapatan_ayah', 12);
            $table->string('pekerjaan_ibu', 60);
            $table->string('pendapatan_ibu', 12);
            $table->string('jumlah_tanggungan', 12);
            $table->string('bidik_misi', 3);
            $table->decimal('nilai_akademis', 6, 3)->default('0');
            $table->decimal('nilai_non_akademis', 6, 3)->default('0');
            $table->decimal('nilai_peringkat', 6, 4)->default('0');
            $table->decimal('nilai_akhir', 8, 5)->default('0');
            $table->string('periode', 4);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::drop('mahasiswa');
    }
}
