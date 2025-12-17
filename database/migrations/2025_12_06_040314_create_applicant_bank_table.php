<?php

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
        Schema::create('applicant_banks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->onDelete('cascade');
            $table->string('beneficiary_bank');
            $table->string('bank_acc_name');
            $table->string('bank_acc_num');
            $table->decimal('balance_amount', 8, 2);
            $table->timestamps();
            $table->foreignId('updated_by')->nullable()->constrained('admins');

             // Index Performance
            $table->index(['loan_id', 'bank_acc_name', 'bank_acc_num']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_banks');
    }
};
