<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->date('start_date')->nullable()->after('disbursed_at');
            $table->date('end_date')->nullable()->after('start_date');
            $table->decimal('remaining_balance', 12, 2)->default(0.00)->after('end_date');
        });
    }

    public function down(): void
    {
        Schema::table('loans', function (Blueprint $table) {
            $table->dropColumn(['start_date', 'end_date', 'remaining_balance']);
        });
    }
};