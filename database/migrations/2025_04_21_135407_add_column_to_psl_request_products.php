<?php

use App\Models\BloodBag;
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
        Schema::table('psl_request_products', function (Blueprint $table) {
            $table->foreignIdFor(BloodBag::class)->nullable()->constrained()->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('psl_request_products', function (Blueprint $table) {
            //
        });
    }
};
