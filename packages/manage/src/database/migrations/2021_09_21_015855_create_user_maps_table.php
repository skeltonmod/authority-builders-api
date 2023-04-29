<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserMapsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('maps', function (Blueprint $table) {
            $table->id();
            $table->string('latitude');
            $table->string('longitude');
            $table->string('canonical_name')->nullable();
            $table->boolean('status')->default(false);

            // Reference the foreign key directly, no pivot needed
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('maps');
        // Schema::dropIfExists('user_maps');
    }
}
