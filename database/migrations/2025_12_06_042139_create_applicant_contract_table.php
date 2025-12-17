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
        Schema::create('applicant_contracts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_id')->constrained('loans')->onDelete('cascade');
            $table->string('signature_url');
            $table->boolean('terms_accepted')->default(false);
            $table->boolean('privacy_policy_accepted')->default(false);
            $table->timestamps();
            $table->foreignId('updated_by')->nullable()->constrained('admins');

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('applicant_contracts');
    }
};
