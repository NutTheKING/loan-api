<?php
// database/migrations/2024_01_01_000003_create_loans_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('order_id')->unique()->nullable(); 
            $table->decimal('loan_amount', 12, 2);
            $table->integer('loan_period')->default(0);
            $table->decimal('principle', 12, 2)->default(0.00);
            $table->decimal('interest_rate', 3, 1)->default(5.0);
            $table->decimal('interest_amount', 12, 2)->default(0.00);
            $table->decimal('total_payment', 12, 2)->default(0.00);
            $table->date('front_remark')->nullable();
            $table->date('back_remark')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('admins');
            $table->timestamp('disbursed_at')->nullable();
            $table->timestamps();
            $table->softDeletes();
            $table->foreignId('updated_by')->nullable()->constrained('admins');

               // Indexes for performance
            $table->index('order_id');
            $table->index('status');
            $table->index(['user_id', 'status']);
            $table->index('disbursed_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loans');
    }
};