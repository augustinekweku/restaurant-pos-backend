<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddNullableToCompanyNameAndDropAmountFieldInCreditorOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('creditor_orders', function (Blueprint $table) {
            $table->dropColumn('amount');
            $table->string('company_name')->nullable()->change();

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
