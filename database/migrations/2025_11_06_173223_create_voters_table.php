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
   Schema::create('voters', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->nullable()->unique();
    $table->string('phone')->nullable();
    $table->string('token')->unique();

    // Track if the voter used or voted
    $table->boolean('token_used')->default(false);  // link opened once
    $table->boolean('has_voted')->default(false);   // finished voting

    // Track when
    $table->timestamp('voted_at')->nullable();
    $table->timestamp('link_used_at')->nullable();

    $table->timestamps();
});

}


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voters');
    }
};
