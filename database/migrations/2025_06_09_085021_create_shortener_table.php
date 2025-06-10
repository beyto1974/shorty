<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('shorteners', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->string('original_url');
            $table->string('handle')->unique();

            $table->unsignedInteger('hits')->default(0);

            $table->unsignedBigInteger('created_by_user_id')->index();
            $table->foreign('created_by_user_id')->references('id')->on('users');

            $table->unsignedBigInteger('updated_by_user_id')->nullable()->default(null)->index();
            $table->foreign('updated_by_user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shorteners');
    }
};
