<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('username')->nullable();
            $table->string('email')->nullable()->unique();
            $table->timestampTz('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('photo', 255)->nullable();
            $table->rememberToken()->nullable();
            $table->tinyInteger('verified_email')->default(0)->nullable()->index();
            $table->string('email_token', 32)->nullable();
            $table->tinyInteger('blocked')->default(0)->nullable();
            $table->tinyInteger('status')->default(0)->nullable();
            $table->string('cpf')->nullable();
            $table->text('theme_config')->nullable();
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
