<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('asset_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('brand');
            $table->string('model');
            $table->string('type');
            $table->string('condition');
            $table->string('location');
            $table->string('building_name')->nullable();
            $table->string('fund')->nullable();
            $table->decimal('price', 10, 2)->nullable();
            $table->string('property_number')->nullable();
            $table->string('previous_custodian')->nullable();
            $table->string('custodian')->nullable();
            $table->text('notes')->nullable();
            $table->enum('action', ['create', 'edit']);
            $table->json('original_data')->nullable();
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->timestamp('requested_at')->useCurrent();
            $table->timestamp('approved_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('asset_requests');
    }
};
