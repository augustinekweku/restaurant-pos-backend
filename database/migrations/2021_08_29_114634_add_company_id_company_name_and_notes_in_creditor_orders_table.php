<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyIdCompanyNameAndNotesInCreditorOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('creditor_orders', function (Blueprint $table) {
            $table->unsignedBigInteger('company_id');
            $table->string('company_name');
            $table->text('notes')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('creditor_orders', function (Blueprint $table) {
            //
        });
    }
}
