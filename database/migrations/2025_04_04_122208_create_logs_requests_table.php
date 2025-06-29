<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLogsRequestsTable extends Migration
{
    public function up()
    {
        Schema::create('log_requests', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asset_id')->constrained('assets')->onDelete('cascade');
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            $table->date('date')->index();
            $table->string('usage_type')->default('general');
            $table->text('notes')->nullable();

            $table->string('original_location')->nullable();
            $table->string('new_location');

            $table->string('brand')->nullable();
            $table->string('model')->nullable();

            $table->enum('action', ['create', 'update', 'delete']);
            $table->json('original_data')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamp('requested_at')->nullable();
            $table->timestamp('reviewed_at')->nullable();
            $table->timestamp('applied_at')->nullable();

            $table->uuid('swap_group_id')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('log_requests');
    }
}
