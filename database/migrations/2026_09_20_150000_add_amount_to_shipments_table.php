<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('shipments', function (Blueprint $table): void {
            // Parcel value / quote ("devis") shown to the recipient. The currency is
            // snapshotted at creation so a later change in Settings never rewrites history.
            $table->decimal('amount', 12, 2)->nullable()->after('weight');
            $table->string('currency', 3)->nullable()->after('amount');
        });
    }

    public function down(): void
    {
        Schema::table('shipments', function (Blueprint $table): void {
            $table->dropColumn(['amount', 'currency']);
        });
    }
};
