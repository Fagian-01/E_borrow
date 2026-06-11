<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('borrowings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('transaction_code')->unique(); // e.g. BRW-20240101-XXXX
            $table->enum('status', [
                'pending',      // waiting for admin approval
                'approved',     // approved, waiting pickup
                'active',       // items handed over
                'partially_returned', // some items returned
                'returned',     // all items returned
                'overdue',      // past due date
                'rejected',     // rejected by admin
                'cancelled',    // cancelled by user
            ])->default('pending');
            $table->date('borrow_date');
            $table->date('expected_return_date');
            $table->date('actual_return_date')->nullable();
            $table->text('purpose')->nullable(); // borrowing purpose/reason
            $table->text('notes')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->decimal('total_fine', 14, 2)->default(0);
            $table->boolean('fine_paid')->default(false);
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('handed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('handed_at')->nullable();
            $table->foreignId('received_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('received_at')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('user_id');
            $table->index('status');
            $table->index('borrow_date');
            $table->index('expected_return_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('borrowings');
    }
};
