<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class UpdateCashbacksForeignKey extends Migration
{
    public function up()
    {
        Schema::table('cashbacks', function (Blueprint $table) {
            // Remove a foreign key atual
            $table->dropForeign(['donation_id']);

            // Adiciona novamente com cascade
            $table->foreign('donation_id')
                  ->references('id')->on('donations')
                  ->onDelete('cascade');
        });
    }

    public function down()
    {
        Schema::table('cashbacks', function (Blueprint $table) {
            // Reverte para a foreign key padrão (sem cascade)
            $table->dropForeign(['donation_id']);
            $table->foreign('donation_id')
                  ->references('id')->on('donations');
        });
    }
}
