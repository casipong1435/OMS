<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Business;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('requirement_images', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Business::class);
            $table->string('cedula')->nullable();
            $table->string('brgy_clearance')->nullable();
            $table->string('pmo_ceedo_clearance')->nullable();
            $table->string('dti_cert')->nullable();
            $table->string('medical_cert')->nullable();
            $table->string('business_permit')->nullable();
            //$table->string('bof_protection')->nullable();
            //$table->string('sanitary_cert')->nullable();
            //$table->string('garbage_cert')->nullable();
            //$table->string('bldg_cert')->nullable();
            //$table->string('inspection_cert')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('requirement_images');
    }
};
