<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConsultationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('consultations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned();
            $table->foreign('user_id')
                ->references('id')->on('users');
            $table->integer('user_doctor_detail_id')->unsigned();
            $table->foreign('user_doctor_detail_id')
                ->references('id')->on('user_doctor_details');
            $table->enum('status', ['Menunggu Pembayaran', 'Menunggu Konfirmasi Pembayaran', 'Dibatalkan','Pembayaran Ditolak', 'Sesi Konsultasi', 'Selesai'])->default('Menunggu Pembayaran');
            $table->date('approved_at')->nullable();
            $table->date('rejected_at')->nullable();
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
        Schema::dropIfExists('consultations');
    }
}
