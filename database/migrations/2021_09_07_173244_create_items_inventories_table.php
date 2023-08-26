<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateItemsInventoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('items_inventories', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('old_stock');
            $table->bigInteger('new_stock');
            $table->bigInteger('old_qty');
            $table->bigInteger('new_qty');
            $table->unsignedBigInteger('item_id');
            $table->string('date');
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
        Schema::dropIfExists('items_inventories');
    }
}
