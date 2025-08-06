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
        Schema::create('report_data_source', function (Blueprint $table) {
            $table->foreignId('report_id')->constrained()->onDelete('cascade');
            $table->foreignId('data_source_id')->constrained()->onDelete('cascade');
            $table->primary(['report_id', 'data_source_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('report_data_source');
    }
};
