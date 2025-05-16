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
        Schema::create('medical_records', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date');
            $table->string('diagnosis');
            $table->string('treatment');
            $table->timestamps();

            $table->unsignedBigInteger('patient_id')->nullable(false);
            $table->unsignedBigInteger('docter_id')->nullable(false);
            $table->foreign('patient_id')->on('patients')->references('id');
            $table->foreign('docter_id')->on('docters')->references('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('medical_records');
    }
};
