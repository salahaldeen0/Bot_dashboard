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
        Schema::create('apps', function (Blueprint $table) {
            $table->id();
            
            // App Details Section
            $table->string('app_name');
            $table->text('description')->nullable();
            $table->string('phone_number')->nullable();
            
            // Database Connection Section
            $table->string('database_type');
            $table->string('database_name');
            $table->integer('port');
            $table->string('host');
            $table->string('username');
            $table->string('password');
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apps');
    }
};
