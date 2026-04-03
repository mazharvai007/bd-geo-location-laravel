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

        Schema::create($prefix . 'unions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('upazila_id');
            $table->string('name');
            $table->string('name_bn');
            $table->timestamps();

            $table->foreign('upazila_id')
                  ->references('id')
                  ->on($prefix . 'upazilas')
                  ->onDelete('cascade');

            $table->index('upazila_id');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $prefix = config('bd-geo.table_prefix', 'bd_');

        Schema::dropIfExists($prefix . 'unions');
    }
};
