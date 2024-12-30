<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Profile;
use App\Models\EstablishmentUnit;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('businesses', function (Blueprint $table) {
            $table->id();
            $table->foreignIdFor(Profile::class);
            $table->foreignIdFor(EstablishmentUnit::class);
            $table->foreignIdFor(Area::class);
            $table->string('kind_of_business')->nullable();
            $table->string('name')->nullable();
            $table->string('plate')->nullable();
            $table->string('permit_number')->nullable();
            $table->date('permit_expiration_date')->nullable();
            $table->string('dti_reg_number')->nullable();
            $table->tinyInteger('payment_cycle')->nullable();
            $table->string('cedula')->nullable();
            $table->text('remarks')->nullable();
            $table->date('date_approved')->nullable();
            $table->date('date_rejected')->nullable();
            $table->date('date_closed')->nullable();
            $table->tinyInteger('status')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('businesses');
    }
};
