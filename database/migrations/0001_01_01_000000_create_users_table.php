<?php
// database/migrations/2024_01_01_000001_create_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('order_id')->unique()->nullable();
            $table->string('full_name')->nullable()->index();;
            $table->string('user_name')->unique();
            $table->string('email')->unique()->nullable();
            $table->string('password');
            $table->string('profile')->nullable();                     
            $table->string('ip_address')->index()->nullable();
            $table->string('device')->index()->nullable();
            $table->integer('credit_score')->default(0)->index();
            $table->enum('account_status', allowed: ['normal', 'blacklist'])->default('normal');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->timestamps();
            $table->softDeletes();

        });
        
      Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
}
        

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};