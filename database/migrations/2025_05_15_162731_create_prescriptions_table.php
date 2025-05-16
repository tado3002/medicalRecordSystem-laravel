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
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->id();
            $table->string('medicine_name');
            $table->string('dosage');
            $table->string('duration');
            $table->string('notes');
            $table->timestamps();

            $table->unsignedBigInteger('medical_record_id')->nullable(false);
            $table->foreign('medical_record_id')->on('medical_records')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('prescriptions');
    }
};
