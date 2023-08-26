<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddReadyFieldAndDropCompanyNameInCreditorOrdersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('creditor_orders', function (Blueprint $table) {
            $table->dropColumn('company_name');
            $table->boolean('ready')->nullable();
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
