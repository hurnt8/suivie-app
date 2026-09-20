<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shipments', function (Blueprint $table): void {
            $table->id();
            $table->string('tracking_code', 32)->unique();
            $table->foreignId('sender_id')->constrained()->restrictOnDelete();
            $table->foreignId('recipient_id')->constrained()->restrictOnDelete();
            $table->text('description')->nullable();
            $table->decimal('weight', 8, 2)->nullable();
            $table->unsignedInteger('package_count')->default(1);
            $table->string('shipment_type');
            $table->string('service_type');
            $table->string('origin');
            $table->string('destination');
            $table->string('current_status')->default('pending')->index();
            $table->date('estimated_delivery_date')->nullable();
            $table->text('special_instructions')->nullable();
            $table->timestamp('shipped_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shipments');
    }
};
