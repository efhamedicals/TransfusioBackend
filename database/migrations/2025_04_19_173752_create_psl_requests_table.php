<?php

use App\Models\User;
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
        Schema::create('psl_requests', function (Blueprint $table) {
            $table->id();
            $table->string('reference');
            $table->string('first_name');
            $table->string('last_name');
            $table->foreignIdFor(User::class)->constrained()->cascadeOnDelete();
            $table->string('prescription')->nullable();
            $table->string('blood_report')->nullable();
            $table->timestamp('end_verification')->nullable();
            $table->string('prescription_date')->nullable();
            $table->string('prescription_fullname')->nullable();
            $table->string('prescription_birth_date')->nullable();
            $table->string('prescription_age')->nullable();
            $table->string('prescription_gender')->nullable();
            $table->string('prescription_blood_type')->nullable();
            $table->string('prescription_blood_rh')->nullable();
            $table->string('prescription_diagnostic')->nullable();
            $table->boolean('prescription_substitution')->nullable();
            $table->enum('status', ['processing', 'found', 'not_found', 'waiting_payment', 'paid'])->default('processing');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('psl_requests');
    }
};
