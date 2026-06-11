<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowing_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('borrowing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('item_id')->constrained()->cascadeOnDelete();
            $table->foreignId('asset_id')->nullable()->constrained()->nullOnDelete(); // assigned on handover via scan
            $table->unsignedInteger('quantity')->default(1);

            // Condition tracking
            $table->enum('condition_out', [
                'excellent', 'good', 'fair', 'poor',
            ])->nullable(); // set when handed over
            $table->enum('condition_in', [
                'excellent', 'good', 'fair', 'poor', 'damaged', 'lost',
            ])->nullable(); // set when returned
            $table->text('condition_out_note')->nullable();
            $table->text('condition_in_note')->nullable();
            $table->string('photo_out_path')->nullable(); // photo when handed out
            $table->string('photo_in_path')->nullable();  // photo when returned

            // Return tracking
            $table->boolean('is_returned')->default(false);
            $table->timestamp('returned_at')->nullable();
            $table->foreignId('returned_to')->nullable()->constrained('users')->nullOnDelete();

            // Damage & fine
            $table->boolean('is_damaged')->default(false);
            $table->decimal('fine_amount', 14, 2)->default(0);
            $table->decimal('damage_fee', 14, 2)->default(0);
            $table->text('damage_description')->nullable();

            $table->timestamps();

            $table->index('borrowing_id');
            $table->index('item_id');
            $table->index('asset_id');
            $table->index('is_returned');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowing_details');
    }
};
