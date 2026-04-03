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
        $prefix = config('bd-geo.table_prefix', 'bd_');

        Schema::create($prefix . 'divisions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('name');
            $table->string('name_bn');
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('long', 11, 8)->nullable();
            $table->timestamps();

            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $prefix = config('bd-geo.table_prefix', 'bd_');

        Schema::dropIfExists($prefix . 'divisions');
    }
};
