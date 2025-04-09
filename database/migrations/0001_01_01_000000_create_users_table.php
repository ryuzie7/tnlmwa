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
            $table->id();  // Auto-incrementing primary key
            $table->string('name');  // Name of the user
            $table->string('email')->unique();  // Email, must be unique in the table
            $table->string('password');  // Password for the user (hashed)
            $table->string('staff_id')->unique()->nullable();  // Unique staff ID, can be nullable
            $table->string('phone')->nullable();  // Phone number (nullable)
            $table->boolean('approved')->default(true);  // Account approval status, default is true (approved)
            $table->rememberToken();  // For "remember me" functionality
            $table->timestamps();  // Timestamps (created_at, updated_at)
        });

        // Password reset tokens table
        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email');  // Email address for the password reset request
            $table->string('token');  // The token for password reset
            $table->timestamp('created_at')->nullable();  // Timestamp when the token was created
            $table->primary('email');  // Use email as the primary key
        });

        // Sessions table to track active sessions
        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();  // Session ID
            $table->foreignId('user_id')->nullable()->index();  // Foreign key for the user, nullable
            $table->string('ip_address', 45)->nullable();  // IP address of the user
            $table->text('user_agent')->nullable();  // User agent string (browser details)
            $table->longText('payload');  // Payload for the session data
            $table->integer('last_activity')->index();  // Timestamp for the last activity in the session
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop all three tables if rolling back
        Schema::dropIfExists('users');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('sessions');
    }
};
