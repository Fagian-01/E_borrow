<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('assets', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->string('asset_code')->unique(); // e.g. AST-LPT-001
            $table->string('barcode')->unique()->nullable(); // QR / barcode value
            $table->string('serial_number')->nullable();
            $table->enum('status', [
                'available',
                'borrowed',
                'maintenance',
                'damaged',
                'lost',
                'retired',
            ])->default('available');
            $table->enum('condition', [
                'excellent',
                'good',
                'fair',
                'poor',
            ])->default('excellent');
            $table->text('condition_note')->nullable();
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_price', 14, 2)->nullable();
            $table->string('location')->nullable(); // storage room/rack
            $table->timestamps();
            $table->softDeletes();

            $table->index('item_id');
            $table->index('status');
            $table->index('condition');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('assets');
    }
};
