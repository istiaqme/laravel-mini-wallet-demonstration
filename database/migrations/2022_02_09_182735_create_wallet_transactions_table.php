<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWalletTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wallet_transactions', function (Blueprint $table) {
            $table->id();
            $table->string('from_wallet_id')->nullable();
            $table->bigInteger('from_wallet_native_amount')->nullable(); // x100
            $table->string('from_wallet_currency')->nullable(); // purposely done duplication
            $table->bigInteger('from_wallet_current_balance')->nullable(); // x100
            $table->bigInteger('conversion_rate')->nullable(); // x100
            $table->dateTime('sent_at')->nullable();
            $table->string('to_wallet_id')->nullable();
            $table->bigInteger('to_wallet_converted_amount')->nullable(); // x100
            $table->string('to_wallet_currency')->nullable(); // purposely done duplication
            $table->bigInteger('to_wallet_current_balance')->nullable(); // x100
            $table->dateTime('received_at')->nullable();
            $table->string('purpose')->nullable();
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
        Schema::dropIfExists('wallet_transactions');
    }
}
