<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->string('nik')->unique();
            $table->string('nama');
            $table->string('tempatLahir');
            $table->date('tanggalLahir');
            $table->enum('jenisKelamin', ['L', 'P']);
            $table->string('golDarah')->nullable();
            $table->string('alamat');
            $table->string('rt');
            $table->string('rw');
            $table->string('kel');
            $table->string('desa');
            $table->string('kecamatan');
            $table->string('agama');
            $table->string('statusPekerjaan');
            $table->string('statusPerkawinan');
            $table->string('pekerjaan');
            $table->string('kewarganegaraan');
            $table->date('berlakuHingga');
            $table->string('gambarKtp');
            $table->string('kodeBank')->nullable();
            $table->string('noRekening')->nullable();
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
