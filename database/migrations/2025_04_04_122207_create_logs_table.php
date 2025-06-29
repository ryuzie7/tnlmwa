<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsTable extends Migration
{
    public function up()
{
    Schema::create('logs', function (Blueprint $table) {
        $table->id();

        // Nullable foreign keys for flexibility
        $table->foreignId('asset_id')->nullable()->constrained('assets')->onDelete('set null');
        $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');

        // Log metadata
        $table->string('action')->default('log'); // e.g., 'created', 'updated', 'deleted'
        $table->string('model_type')->nullable(); // polymorphic
        $table->unsignedBigInteger('model_id')->nullable();
        $table->json('changes')->nullable();

        // Usage-specific fields
        $table->date('date')->nullable()->index();
        $table->string('usage_type')->default('general');
        $table->text('notes')->nullable();

        // NEW FIELDS
        $table->string('status')->default('approved'); // 'pending', 'approved', 'rejected'
        $table->timestamp('applied_at')->nullable(); // when approved/rejected

        $table->timestamps();

        $table->index(['asset_id', 'user_id', 'date']);
    });
}


    public function down()
    {
        Schema::dropIfExists('logs');
    }
}
