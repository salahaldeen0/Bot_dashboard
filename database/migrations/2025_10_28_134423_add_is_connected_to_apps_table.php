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
        Schema::table('apps', function (Blueprint $table) {
            $table->boolean('is_connected')->default(false)->after('password');
            
            // Make database fields nullable
            $table->string('database_type')->nullable()->change();
            $table->string('database_name')->nullable()->change();
            $table->integer('port')->nullable()->change();
            $table->string('host')->nullable()->change();
            $table->string('username')->nullable()->change();
            $table->string('password')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('apps', function (Blueprint $table) {
            $table->dropColumn('is_connected');
            
            // Revert database fields to not nullable
            $table->string('database_type')->nullable(false)->change();
            $table->string('database_name')->nullable(false)->change();
            $table->integer('port')->nullable(false)->change();
            $table->string('host')->nullable(false)->change();
            $table->string('username')->nullable(false)->change();
            $table->string('password')->nullable(false)->change();
        });
    }
};
