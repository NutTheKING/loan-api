<?php
// database/migrations/2024_01_01_000004_create_loan_repayments_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loan_repayments', function (Blueprint $table) {
            $table->id();
            
            // Foreign key to loans table
            $table->foreignId('loan_id')->constrained('loans');

            // Repayment details
            $table->integer('installment_number')->default(1);
            $table->decimal('amount_due', 15, 2)->default(0);
            $table->decimal('amount_paid', 15, 2)->default(0);
            $table->decimal('late_fee', 15, 2)->default(0);
            
            // Date fields
            $table->date('due_date');
            $table->date('paid_date')->nullable();
            
            // Status and payment info
            $table->enum('status', ['pending', 'paid', 'overdue'])->default('pending');
            $table->string('payment_method', 50)->nullable()->comment('cash,bank_transfer,mobile_money,etc');
            $table->string('transaction_reference', 100)->nullable();
            $table->text('notes')->nullable();
            
            // Optional: Keep existing financial columns if needed
            $table->decimal('begining_balance', 15, 2)->default(0);
            $table->decimal('interest', 15, 2)->default(0);
            $table->decimal('principal', 15, 2)->default(0);
            $table->decimal('ending_balance', 15, 2)->default(0);
            
            // Timestamps
            $table->timestamps();
            $table->softDeletes();
            
            // Indexes for performance
            $table->index('loan_id');
            $table->index('status');
            $table->index('due_date');
            $table->index('paid_date');
            $table->index(['loan_id', 'status']);
            $table->index(['loan_id', 'due_date']);
            $table->unique(['loan_id', 'installment_number'], 'loan_installment_unique');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loan_repayments');
    }
};