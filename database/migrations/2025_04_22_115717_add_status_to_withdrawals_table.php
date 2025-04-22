<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddStatusToWithdrawalsTable extends Migration
{
    public function up()
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->string('status')->default('pending');  // Adicionando a coluna 'status'
            $table->timestamp('rejected_at')->nullable(); // Coluna para 'rejected_at'
            $table->timestamp('approved_at')->nullable(); // Coluna para 'approved_at'
        });
    }

    public function down()
    {
        Schema::table('withdrawals', function (Blueprint $table) {
            $table->dropColumn('status');
            $table->dropColumn('rejected_at');
            $table->dropColumn('approved_at');
        });
    }
}
