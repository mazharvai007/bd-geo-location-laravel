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

        Schema::create($prefix . 'districts', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('division_id');
            $table->string('name');
            $table->string('name_bn');
            $table->decimal('lat', 10, 8)->nullable();
            $table->decimal('long', 11, 8)->nullable();
            $table->timestamps();

            $table->foreign('division_id')
                  ->references('id')
                  ->on($prefix . 'divisions')
                  ->onDelete('cascade');

            $table->index('division_id');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $prefix = config('bd-geo.table_prefix', 'bd_');

        Schema::dropIfExists($prefix . 'districts');
    }
};
