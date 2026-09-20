<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('destination_rates', function (Blueprint $table): void {
            $table->id();
            // Country (or zone) name matched against a shipment's free-text destination.
            $table->string('name')->unique();
            $table->decimal('base_amount', 10, 2)->default(0);
            $table->decimal('per_kg_amount', 10, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('destination_rates');
    }
};
