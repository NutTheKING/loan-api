<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->id();
            $table->string('full_name');
            $table->string('user_name')->unique();
            $table->enum('admin_type', ['admin_system', 'operations'])->default('operations');
            $table->string('email')->unique()->nullable();
            $table->string('password');
            $table->string('profile')->nullable();
            $table->foreignId('role_id')->constrained('roles')->onDelete('set null');
            $table->string('phone')->nullable();
            $table->boolean('is_active')->default(true);
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();  
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('admins');
    }
};