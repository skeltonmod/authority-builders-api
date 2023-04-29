<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrganizationsUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('organizations_users', function (Blueprint $table) {
            // Organization Pivot
            $table->unsignedBigInteger('organizations_id');
            $table->foreign('organizations_id')
                ->references('id')
                ->on('organizations')
                ->onDelete('cascade')
                ->primary();

            // User Pivot
            $table->unsignedBigInteger('users_id');
            $table->foreign('users_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');

            $table->primary('users_id');
            $table->index('users_id');
            $table->index('organizations_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('organizations_users');
    }
}
