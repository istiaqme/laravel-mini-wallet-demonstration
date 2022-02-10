<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuthTokensTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('auth_tokens', function (Blueprint $table) { 
            $table->id();
            $table->string('ip_address', 45);
            $table->integer('user_id')->nullable();
            $table->string('token')->nullable();
            $table->string('status')->nullable(); // "Active" or "Inactive"
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
        Schema::dropIfExists('auth_tokens');
    }
}
