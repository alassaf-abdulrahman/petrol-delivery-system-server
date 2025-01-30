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
        Schema::create('orders', function (Blueprint $table) {
            $table->id("orderID");
            $table->unsignedBigInteger("customerID");
            $table->unsignedBigInteger("driverID")->nullable();
            $table->string("fuelType");
            $table->string("quantity");
            $table->string("status");
            $table->string("deliveryLocation");

            $table->foreign("customerID")->references("customerID")->on("customers")->onDelete("cascade");
            $table->foreign("driverID")->references("driverID")->on("drivers")->onDelete("cascade");
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
