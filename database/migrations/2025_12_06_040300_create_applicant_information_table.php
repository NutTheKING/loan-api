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
        Schema::create('applicant_information', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans');
            $table->string('actual_name');
            $table->string('id_card_num');
            $table->string('current_job');
            $table->enum('gender', ['male', 'female']);
            $table->decimal('stable_income', 8, 2)->default(0.00);
            $table->string('loan_purpose')->nullable();
            $table->text('current_address');
            $table->text('guarantor_name');
            $table->text('guarantor_phone');
            $table->timestamps();
            $table->foreignId('updated_by')->nullable()->constrained('admins');
            
            // Index Performance
            $table->index('loan_id');
            $table->index('actual_name');
            $table->index('id_card_num');
            $table->index('guarantor_name');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_information');
    }
};
