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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid');
            $table->string('first_name');
            $table->string('last_name');
            $table->boolean('is_admin')->default(false);
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->foreignUuid('avatar')->nullable()->constrained('files', 'uuid')->onDelete('set null');
            $table->string('address')->nullable();
            $table->string('phone_number')->nullable();
            $table->boolean('is_marketing')->default(false)->comment('Enable marketing preferences');
            $table->rememberToken();
            $table->timestamps();
            $table->timestamp('last_login_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
