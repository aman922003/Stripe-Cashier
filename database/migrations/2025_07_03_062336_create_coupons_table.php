<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_id')->nullable()->unique(); // <- Add Stripe ID
            $table->string('promo_id')->nullable(); // <- Add Promo Code ID
            $table->string('name');
            $table->string('code')->nullable();
            $table->enum('type', ['percentage', 'fixed']);
            $table->decimal('amount_off', 8, 2)->nullable();
            $table->integer('percent_off')->nullable();
            $table->json('currencies')->nullable();
            $table->dateTime('redeem_by')->nullable();
            $table->integer('max_redemptions')->nullable();
            $table->boolean('generate_code')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('coupons');
    }
};
