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

        Schema::create($prefix . 'upazilas', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('district_id');
            $table->string('name');
            $table->string('name_bn');
            $table->timestamps();

            $table->foreign('district_id')
                  ->references('id')
                  ->on($prefix . 'districts')
                  ->onDelete('cascade');

            $table->index('district_id');
            $table->index('name');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $prefix = config('bd-geo.table_prefix', 'bd_');

        Schema::dropIfExists($prefix . 'upazilas');
    }
};
