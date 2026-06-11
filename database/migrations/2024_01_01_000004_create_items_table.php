<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->text('specifications')->nullable(); // JSON-encoded specs
            $table->string('image')->nullable();
            $table->unsignedInteger('total_stock')->default(0);
            $table->unsignedInteger('available_stock')->default(0);
            $table->unsignedInteger('max_borrow_days')->default(7); // max loan duration
            $table->unsignedInteger('max_qty_per_user')->default(1); // max qty per transaction
            $table->decimal('fine_per_day', 12, 2)->default(0); // overdue fine rate
            $table->boolean('is_active')->default(true);
            $table->boolean('requires_approval')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->index('category_id');
            $table->index('is_active');
            $table->index('available_stock');
            $table->index('name'); // fullText index can be used on MySQL
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
