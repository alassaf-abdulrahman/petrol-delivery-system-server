<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('payments', function (Blueprint $table) {
            $table->id("paymentID");
            $table->unsignedBigInteger("orderID");
            $table->decimal("amount", 8, 2);
            $table->string("paymentMethod");
            $table->string("status");
            $table->string("cardNumber")->nullable();
            $table->string("expiryDate")->nullable();
            $table->string("cvv")->nullable();
            $table->string("address")->nullable();
            $table->string("zipCode")->nullable();
            $table->foreign("orderID")->references("orderID")->on("orders")->onDelete("cascade");
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
