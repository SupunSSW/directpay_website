<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tracking_no');
            $table->string('name')->nullable();
            $table->string('address')->nullable();
            $table->string('mobile', 10);
            $table->decimal('amount', 8, 2);
            $table->string('type', 15);
            $table->integer('agent_id');
            $table->string('agent_email');
            $table->string('status', 20);
            $table->integer('transaction_id');
            $table->string('transaction_reference');
            $table->dateTime('transaction_datetime');
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
        Schema::dropIfExists('transactions');
    }
}
