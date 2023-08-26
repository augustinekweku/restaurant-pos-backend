<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddQtyAddedRenameNewQtyInItemInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('items_inventories', function (Blueprint $table) {
            $table->renameColumn('new_qty', 'new_qty_left');
            $table->bigInteger('qty_added');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('items_inventories', function (Blueprint $table) {
            //
        });
    }
}
