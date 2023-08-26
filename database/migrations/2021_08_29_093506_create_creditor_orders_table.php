<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCreditorOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('creditor_orders', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('item_id');
            $table->integer('discount')->nullable();
            $table->string('payment_type');
            $table->string('invoice_number');
            $table->date('date_scheduled');
            $table->enum('status', ['hold','empty','unpaid','aborted']);
            $table->unsignedDecimal('amount');
            $table->unsignedDecimal('paid')->nullable();
            $table->unsignedDecimal('balance')->nullable();
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
        Schema::dropIfExists('creditor_orders');
    }
}
