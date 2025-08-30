<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInvoicesTable extends Migration
{
    public function up()
    {
        Schema::create('invoices', function (Blueprint $table) {
            $table->id(); // Primary key (auto-incremented)
            $table->unsignedBigInteger('user_id'); // Foreign key to users table
            $table->decimal('amount', 10, 2); // Total amount of the invoice
            $table->string('status'); // Invoice status
            $table->timestamps(); // Created at, updated at
        });
    }

    public function down()
    {
        Schema::dropIfExists('invoices');
    }
}
