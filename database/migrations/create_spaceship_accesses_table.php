<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('spaceship_accesses', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->foreignId('spaceship_space_id');
            $table->foreignId('user_id');
            $table->foreignId('spaceship_role_id');
            $table->boolean('is_active')->default(1);
            $table->timestamp('punched_at')->nullable()->default(null);
            $table->timestamps();

            // Relations
            $table->foreign('spaceship_space_id')->references('id')->on('spaceship_spaces')->onDelete('cascade');
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('spaceship_role_id')->references('id')->on('spaceship_roles')->onDelete('cascade');

            // Indices
            $table->unique('uuid');
            $table->unique([ 'spaceship_space_id', 'user_id' ]);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('spaceship_accesses');
    }
};
