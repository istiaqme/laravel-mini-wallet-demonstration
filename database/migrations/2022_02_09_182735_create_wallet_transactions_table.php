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
            $table->string('from_wallet_id');
            $table->bigInteger('from_wallet_native_amount'); // x100
            $table->string('from_wallet_currency'); // purposely done duplication
            $table->bigInteger('from_wallet_current_balance'); // x100
            $table->bigInteger('conversion_rate'); // x100
            $table->dateTime('sent_at');
            $table->string('to_wallet_id');
            $table->bigInteger('to_wallet_converted_amount'); // x100
            $table->string('to_wallet_currency'); // purposely done duplication
            $table->bigInteger('to_wallet_current_balance'); // x100
            $table->dateTime('received_at');
            $table->string('purpose');
            $table->bigInteger('amount_in_base_currency');
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
