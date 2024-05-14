<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('name_client'); // could be "client_name"
            $table->string('cpf'); // could be "client_cpf"
            $table->string('description');
            $table->integer('amount');
            $table->integer('fee');
            $table->enum('status', ['pending', 'paid', 'expired', 'failed']);
            $table->unsignedInteger('merchant_id');
            $table->string('payment_method_slug'); // not sure why not use "payment_method_id" instead or just have an enum column with the values
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->foreign('merchant_id')->references('id')->on('merchants');
            $table->foreign('payment_method_slug')->references('slug')->on('payment_methods');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
