<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTransactionsTable extends Migration
{
    public function up()
    {
        // Create the transactions table
        Schema::create('transactions', function (Blueprint $table) {
            // Primary key
            $table->id();

            // Foreign keys
            $table->unsignedBigInteger('user_id'); // User who made the transaction
            $table->unsignedBigInteger('invoice_id'); // Related invoice ID

            // Transaction details
            $table->decimal('amount_refunded', 10, 2)->default(0); // Refund amount
            $table->string('refund_status')->nullable(); // Status of the refund (e.g. 'pending', 'completed')
            $table->string('refund_reason')->nullable();  // Reason for the refund (user defined)
            $table->timestamp('refund_date')->nullable(); // Timestamp when the refund was processed

            // Timestamps for created and updated records
            $table->timestamps();

            // Foreign key constraints to ensure referential integrity
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });
    }

    public function down()
    {
        // Drop the transactions table
        Schema::dropIfExists('transactions');
    }
}
